<?php
namespace Hschottm\LiteratureBundle;

use Contao\{Backend, BackendTemplate, FileUpload, File, Input, Message, Environment, System, Controller, Database, Config};
use RenanBr\BibTexParser\{Listener, Parser, Processor};

class LiteratureTools extends Backend
{
    public function importLiterature()
    {
        System::loadLanguageFile('tl_literature');
        $uploader = new FileUpload();
        $files = [];
        if (Input::post('FORM_SUBMIT') === 'tl_literature_import') {
            $success = false;
            $directory = 'system/tmp/literature-'.bin2hex(random_bytes(12));
            $filesystem = new \Symfony\Component\Filesystem\Filesystem();
            $absoluteDirectory = System::getContainer()->getParameter('kernel.project_dir').'/'.$directory;
            $filesystem->mkdir($absoluteDirectory);
            $uploadTypes = Config::get('uploadTypes');
            try {
                Config::set('uploadTypes', 'bib');
                $files = $uploader->uploadTo($directory);
                if (!$files) throw new \RuntimeException($GLOBALS['TL_LANG']['ERR']['all_fields']);
                foreach ($files as $file) {
                    if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) !== 'bib') throw new \RuntimeException($GLOBALS['TL_LANG']['tl_literature']['invalidBibtex']);
                }
                $this->importBibTeX($files);
                $success = true;
            } catch (\RenanBr\BibTexParser\Exception\ExceptionInterface|\RuntimeException $e) {
                Message::addError($GLOBALS['TL_LANG']['tl_literature']['invalidBibtex']);
            } finally {
                Config::set('uploadTypes', $uploadTypes);
                $filesystem->remove($absoluteDirectory);
            }
            if ($success) $this->redirect(str_replace('&key=import', '', Environment::get('request')));
        }
        $template = new BackendTemplate('be_literature_import');
        $template->messages = Message::generate();
        $template->upload = $uploader->generateMarkup();
        $template->title = $GLOBALS['TL_LANG']['tl_literature']['import'][1];
        $template->label = $GLOBALS['TL_LANG']['tl_literature']['literaturefile'][0];
        $template->submit = $GLOBALS['TL_LANG']['tl_literature']['import'][0];
        return $template->parse();
    }

    protected function importBibTeX($files)
    {
        $entries = [];
        foreach ($files as $file) {
            // A fresh listener avoids importing previous files again.
            $listener = new Listener();
            $listener->addProcessor(new Processor\TagNameCaseProcessor(CASE_LOWER));
            $listener->addProcessor(new Processor\NamesProcessor());
            $parser = new Parser();
            $parser->addListener($listener);
            $parser->parseFile(System::getContainer()->getParameter('kernel.project_dir').'/'.$file);
            array_push($entries, ...$listener->export());
        }
        $this->importEntries($entries, (int) Input::get('id'));
    }

    public function importEntries(array $entries, int $category): void
    {
        $db = System::getContainer()->get('database_connection');
        if (!$db->fetchOne('SELECT id FROM tl_literature_category WHERE id=?', [$category])) throw new \RuntimeException('Unknown category');
        $hasTags = Database::getInstance()->tableExists('tl_tag');
        $db->transactional(function () use ($db, $entries, $category, $hasTags): void {
            foreach ($entries as $entry) {
                $type = match (strtolower($entry['_type'] ?? $entry['type'] ?? 'misc')) {
                    'article','proceedings','inproceedings','incollection' => 'periodic',
                    'inbook' => 'nonperiodicpart', default => 'nonperiodic',
                };
                $fields = ['title','title_periodic','title_nonperiodicpart','title_info','title_source','title_act','title_act_info','title_journal','pages','volume','issue','location','publisher','released','isbn','issn','uri','uri_date'];
                $data = array_fill_keys($fields, '');
                $data += ['pid'=>$category,'literature_type'=>$type,'tstamp'=>time(),'abstract'=>(string)($entry['abstract'] ?? '')];
                $titleField = match ($type) {'periodic'=>'title_periodic','nonperiodicpart'=>'title_nonperiodicpart',default=>'title'};
                $data[$titleField] = $data['titlesort'] = (string) ($entry['title'] ?? '');
                foreach (['series'=>'title_info','pages'=>'pages','volume'=>'volume','number'=>'issue','address'=>'location','publisher'=>'publisher','year'=>'released','isbn'=>'isbn','issn'=>'issn','url'=>'uri'] as $source=>$target) $data[$target] = (string) ($entry[$source] ?? '');
                $data['volume'] = mb_substr($data['volume'], 0, 5);
                $data[$type === 'nonperiodicpart' ? 'title_act' : 'title_journal'] = (string) ($entry['booktitle'] ?? $entry['journal'] ?? '');
                $authors = is_array($entry['author'] ?? null) ? $entry['author'] : [];
                $editors = is_array($entry['editor'] ?? null) ? $entry['editor'] : [];
                if ($type === 'nonperiodicpart' && !$editors) $editors = $authors;
                $data['authorssort'] = implode('', array_map(static fn($author) => ($author['last'] ?? '').','.($author['first'] ?? ''), $authors));
                $db->insert('tl_literature', $data);
                $id = (int) $db->lastInsertId();
                foreach (['author'=>$authors,'editor'=>$editors] as $kind=>$people) {
                    foreach ($people as $index=>$person) {
                        $db->insert('tl_literature_'.$kind, ['pid'=>$id,'sequence'=>$index+1,'tstamp'=>time(),'firstname'=>$person['first'] ?? '', 'lastname'=>trim(($person['von'] ?? '').' '.($person['last'] ?? '').' '.($person['jr'] ?? ''))]);
                    }
                }
                if ($hasTags) foreach (array_filter(array_map('trim', explode(',', (string) ($entry['keywords'] ?? '')))) as $tag) {
                    $db->insert('tl_tag', ['tid'=>$id,'tag'=>$tag,'from_table'=>'tl_literature']);
                }
            }
        });
    }
}
