<?php
$app = getenv('LITERATURE_TEST_APP') ?: '/var/www/html';
require $app.'/vendor/autoload.php';
$root=dirname(__DIR__);
$map=[];
foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root.'/src')) as $file) {
    if($file->getExtension()==='php' && preg_match('/namespace ([^;]+);/', file_get_contents($file), $m)) $map[trim($m[1]).'\\'.$file->getBasename('.php')]=(string)$file;
}
spl_autoload_register(static function($class)use($map){if(isset($map[$class])) require $map[$class];});
class LiteratureTestKernel extends Contao\ManagerBundle\HttpKernel\ContaoKernel {
    public function registerBundles(): array { return [...parent::registerBundles(), new Hschottm\LiteratureBundle\HschottmLiteratureBundle()]; }
    public function getCacheDir(): string { return '/tmp/contao-literature-test-cache'; }
}
$kernel=LiteratureTestKernel::fromInput($app,new Symfony\Component\Console\Input\ArrayInput(['--env'=>'prod']));
$kernel->boot();
$c=$kernel->getContainer();
$request=Symfony\Component\HttpFoundation\Request::create('http://localhost/contao');
$request->attributes->set('_scope','backend');
$c->get('request_stack')->push($request);
$c->get('contao.framework')->initialize();


function check(bool $condition, string $message): void { if (!$condition) throw new RuntimeException($message); }
Contao\System::loadLanguageFile('default', 'en');
Contao\System::loadLanguageFile('tl_literature', 'en');
set_error_handler(static function ($severity, $message, $file, $line) {
    if ($severity & (E_WARNING | E_NOTICE | E_USER_WARNING)) throw new ErrorException($message, 0, $severity, $file, $line);
    return false;
});
foreach (['tl_module','tl_literature_category','tl_literature','tl_literature_author','tl_literature_editor'] as $table) {
    Contao\Controller::loadDataContainer($table);
    check(isset($GLOBALS['TL_DCA'][$table]), 'Load '.$table);
}
foreach ($map as $class=>$path) check(class_exists($class), 'Load '.$class);
foreach (glob($root.'/src/Resources/contao/templates/*.twig') as $path) $c->get('twig')->load('@Contao/'.basename($path));
echo "Bundle, DCA, classes and Twig compilation passed.\n";
$db = $c->get('database_connection');
foreach (['tl_literature_category','tl_literature','tl_literature_author','tl_literature_editor'] as $table) {
    $columns=[];
    foreach ($GLOBALS['TL_DCA'][$table]['fields'] as $name=>$field) {
        if (is_string($field['sql'] ?? null)) $columns[]='`'.$name.'` '.$field['sql'];
    }
    $db->executeStatement('CREATE TEMPORARY TABLE '.$table.' ('.implode(',', $columns).', PRIMARY KEY (id))');
}
$db->insert('tl_literature_category',['id'=>1,'title'=>'Test','description'=>'']);
$db->executeStatement('CREATE TEMPORARY TABLE tl_tag (id INT AUTO_INCREMENT PRIMARY KEY, tid INT, tag VARCHAR(255), from_table VARCHAR(255))');
spl_autoload_register(static function ($class) {
    $prefix='RenanBr\\BibTexParser\\';
    if (str_starts_with($class,$prefix)) require (getenv('LITERATURE_TEST_PARSER') ?: '/tmp/literature-bibtex-parser/src').'/'.str_replace('\\','/',substr($class,strlen($prefix))).'.php';
});
$listener = new RenanBr\BibTexParser\Listener();
$listener->addProcessor(new RenanBr\BibTexParser\Processor\NamesProcessor());
$parser = new RenanBr\BibTexParser\Parser();
$parser->addListener($listener);
$parser->parseString('@book{one, title={Book <unsafe>}, author={Doe, Jane and Smith, John}, year={2026}, keywords={php,test}} @article{two, title={Article}, journal={Journal}, year={2025}} @inbook{three, title={Chapter}, booktitle={Collected}, editor={Jones, June}}');
$tools = new Hschottm\LiteratureBundle\LiteratureTools();
$tools->importEntries($listener->export(), 1);
check((int)$db->fetchOne('SELECT COUNT(*) FROM tl_literature')===3,'All BibTeX types imported');
check((int)$db->fetchOne('SELECT COUNT(*) FROM tl_literature_author')===2,'Authors imported');
check((int)$db->fetchOne('SELECT COUNT(*) FROM tl_tag')===2,'Keywords use tid');
$records=$db->fetchAllAssociative('SELECT * FROM tl_literature ORDER BY id');
foreach($records as $record) {
    $html=(new Hschottm\LiteratureBundle\LiteraturePreview($record))->getPreview();
    check($html!=='','Preview '.$record['literature_type']);
    if($record['id']==1) check(str_contains($html,'&lt;unsafe&gt;') && str_contains($html,'Doe, Jane'),'Escaped book and author');
}
$preview=new Hschottm\LiteratureBundle\LiteraturePreview();
$preview->loadLiterature(99999);check($preview->getPreview()==='','Missing reference');
$callback=new tl_literature();
$dc=(object)['id'=>1,'table'=>'tl_literature'];
$callback->saveAuthors(serialize([['Updated','Person']]),$dc);
check(str_contains($callback->loadAuthors('', $dc),'Updated'),'Author round trip');
$callback->saveAuthors(serialize([]),$dc);
check($db->fetchOne('SELECT authorssort FROM tl_literature WHERE id=1')==='','Clear stale author sort');
$db->insert('tl_literature_author', ['pid'=>3, 'sequence'=>1, 'firstname'=>'Migration', 'lastname'=>'Test']);
$db->update('tl_literature', ['authorssort'=>''], ['id'=>3]);
$migration = new Hschottm\LiteratureBundle\Migration\AuthorSortMigration($db);
$migration->run();
$sort = $db->fetchOne('SELECT authorssort FROM tl_literature WHERE id=3');
check($sort !== '', 'Migration restores author sort');
$migration->run();
check($db->fetchOne('SELECT authorssort FROM tl_literature WHERE id=3') === $sort, 'Migration is idempotent');
foreach (['0306406152','9780306406157','0-8044-2957-x'] as $isbn) {
    $widget=new Contao\TextField(['label'=>'ISBN']);
    (new Hschottm\LiteratureBundle\Literature())->checkISBN('isbn',$isbn,$widget);
    check(!$widget->hasErrors(),'Valid ISBN '.$isbn);
}
$widget=new Contao\TextField(['label'=>'ISBN']);
(new Hschottm\LiteratureBundle\Literature())->checkISBN('isbn','9780306406158',$widget);
check($widget->hasErrors(),'Invalid ISBN');
$insert=$c->get('contao.insert_tag.parser')->replace('{{insert_literature::2}}');
check(str_contains($insert,'Article'),'Registered insert tag');
echo "BibTeX, reference rendering, authors, ISBN and insert tags passed.\n";
class TestLiteratureList extends Hschottm\LiteratureBundle\ModuleLiteratureList {
    public function run(array $data): array {
        foreach($data as $key=>$value) $this->$key=$value;
        $this->arrCategories=[1];
        $this->Template=new Contao\FrontendTemplate('mod_literaturelist');
        $this->compile();
        return $this->Template->getData();
    }
}
$list=(new ReflectionClass(TestLiteratureList::class))->newInstanceWithoutConstructor();
$data=['id'=>731,'perPage'=>1,'lit_sort'=>'title','lit_sortorder'=>'asc','lit_template'=>'litref_standard','lit_listtitle'=>'List','lit_tags'=>''];
Contao\Input::setGet('tag',null);Contao\Input::setGet('related',null);
$result=$list->run($data);
check(count($result['tbody'])===1 && array_values($result['tbody'])[0]['id']==2,'Sorted first page');
$request->query->set('page_l731',2);
$result=$list->run($data);
check(array_values($result['tbody'])[0]['id']==1,'Second page');
$request->query->remove('page_l731');
Contao\Input::setGet('search','titlesort');Contao\Input::setGet('for','Chapter');
$result=$list->run($data);check(count($result['tbody'])===1 && array_values($result['tbody'])[0]['id']==3,'Search allowed field');
Contao\Input::setGet('search','id) OR 1=1 --');
$result=$list->run(array_replace($data,['perPage'=>0]));check(count($result['tbody'])===3,'Untrusted field ignored');
Contao\Input::setGet('search',null);Contao\Input::setGet('for',null);
if ($c->has(Hschottm\TagsBundle\Service\TagFilter::class)) {
    $result=$list->run(array_replace($data,['lit_tags'=>'does-not-exist']));check(!$result['tbody'],'No-match tags stay empty');
}
$request->query->set('page_l731',999);
try { $list->run($data); throw new RuntimeException('Expected 404'); }
catch (Contao\CoreBundle\Exception\PageNotFoundException $e) {}
$request->query->remove('page_l731');
class TestLiteratureSearch extends Hschottm\LiteratureBundle\ModuleLiteratureSearch {
    public function run(): string {
        $this->id=732;$this->jumpTo=0;
        $this->Template=new Contao\FrontendTemplate('mod_literaturesearch');
        $this->compile();
        return $this->Template->parse();
    }
}
$search=(new ReflectionClass(TestLiteratureSearch::class))->newInstanceWithoutConstructor();
check(str_contains($search->run(),'name="search"'),'Search form renders');
echo "List sorting, search whitelist, pagination, 404 and tag filtering passed.\n";
// Media fixtures are removed even if a check fails; database rows are temporary.
$fileSchema = $db->fetchAssociative('SHOW CREATE TABLE tl_files');
$db->executeStatement(preg_replace('/^CREATE TABLE/', 'CREATE TEMPORARY TABLE', array_values($fileSchema)[1]));
$fixtureDir='system/tmp/literature-test-'.bin2hex(random_bytes(6));
$project=$c->getParameter('kernel.project_dir');
mkdir($project.'/'.$fixtureDir);
try {
    file_put_contents($project.'/'.$fixtureDir.'/sample.pdf', "%PDF-1.4\nTest\n");
    file_put_contents($project.'/'.$fixtureDir.'/sample.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+a0ioAAAAASUVORK5CYII='));
    $uuids=[];
    foreach(['pdf','png'] as $ext) {
        $uuid=Symfony\Component\Uid\Uuid::v4()->toBinary();$uuids[$ext]=$uuid;
        $db->insert('tl_files',['uuid'=>$uuid,'type'=>'file','path'=>$fixtureDir.'/sample.'.$ext,'name'=>'sample.'.$ext,'extension'=>$ext,'hash'=>md5_file($project.'/'.$fixtureDir.'/sample.'.$ext),'found'=>'1','meta'=>serialize(['en'=>['title'=>'Test file']])]);
    }
    $record=$records[0];
    $record['addImage']='1';$record['singleSRC']=$uuids['png'];$record['size']=serialize([10,10,'proportional']);$record['alt']='Image test';
    $html=(new Hschottm\LiteratureBundle\LiteraturePreview($record))->getPreview();
    check(str_contains($html,'Image test') && str_contains($html,'<img'),'UUID image rendering');
    $record['addImage']='';$record['addDownloads']='1';$record['multiSRC']=serialize([$uuids['pdf']]);$record['sortBy']='name_asc';
    $html=(new Hschottm\LiteratureBundle\LiteraturePreview($record))->getPreview();
    check(str_contains($html,'sample.pdf'),'Assigned download renders');
    $record['multiSRC']=serialize([Symfony\Component\Uid\Uuid::v4()->toBinary()]);
    $html=(new Hschottm\LiteratureBundle\LiteraturePreview($record))->getPreview();
    check(!str_contains($html,'sample.pdf'),'Missing download handled');
} finally {
    (new Symfony\Component\Filesystem\Filesystem())->remove($project.'/'.$fixtureDir);
}
echo "UUID image and assigned/missing download checks passed.\n";
