<?php

/**
 * @copyright  Helmut Schottmüller 2009-2020 <http://github.com/hschottm>
 * @author     Helmut Schottmüller (hschottm)
 * @package    hschottm/contao-literature
 * @license    LGPL-3.0+, CC-BY-NC-3.0
 * @see	      https://github.com/hschottm/literature
 */

namespace Hschottm\LiteratureBundle;

use RenanBr\BibTexParser\Listener;
use RenanBr\BibTexParser\Parser;
use RenanBr\BibTexParser\Processor;

class LiteratureTools extends \Backend
{
	protected $blnSave = true;

	public function importLiterature()
	{
		if (\Input::get('key') != 'import')
		{
			$this->redirect(str_replace('&key=import', '', \Environment::get('request')));
		}

		$this->import('BackendUser', 'User');
		$class = $this->User->uploader;

		// See #4086
		if (!class_exists($class))
		{
			$class = 'FileUpload';
		}

		$objUploader = new $class();

		$this->Template = new \BackendTemplate('be_import_literature');

		$this->Template->literaturefile = $this->getFileTreeWidget(\Input::post('literaturefile'));
		$this->Template->hrefBack = ampersand(str_replace('&key=import', '', \Environment::get('request')));
		$this->Template->goBack = $GLOBALS['TL_LANG']['MSC']['goBack'];
		$this->Template->headline = $GLOBALS['TL_LANG']['tl_literature']['import'][1];
		$this->Template->request = ampersand(\Environment::get('request'), ENCODE_AMPERSANDS);
		$this->Template->request_token = REQUEST_TOKEN;
		$this->Template->max_file_size = $GLOBALS['TL_CONFIG']['maxFileSize'];
		$this->Template->submit = specialchars($GLOBALS['TL_LANG']['MSC']['continue']);
		$this->Template->message = \Message::generate();
		$this->import('BackendUser', 'User');
		$class = $this->User->uploader;

		// See #4086
		if (!class_exists($class))
		{
			$class = 'FileUpload';
		}

		$objUploader = new $class();
		$this->Template->markup = $objUploader->generateMarkup();

		if (\Input::post('FORM_SUBMIT') == 'tl_import_literature')
		{
			$arrUploaded = $objUploader->uploadTo('system/tmp');
			if (empty($arrUploaded))
			{
				\Message::addError($GLOBALS['TL_LANG']['ERR']['all_fields']);
				$this->reload();
			}

			$arrFiles = array();

			foreach ($arrUploaded as $strFile)
			{
				// Skip folders
				if (is_dir(TL_ROOT . '/' . $strFile))
				{
					\Message::addError(sprintf($GLOBALS['TL_LANG']['ERR']['importFolder'], basename($strFile)));
					continue;
				}

				$objFile = new \File($strFile, true);

				// Skip anything but .cto files
				if ($objFile->extension != 'bib')
				{
					\Message::addError(sprintf($GLOBALS['TL_LANG']['ERR']['filetype'], $objFile->extension));
					continue;
				}

				$arrFiles[] = $strFile;
			}
			if (empty($arrFiles))
			{
				\Message::addError($GLOBALS['TL_LANG']['ERR']['all_fields']);
				$this->reload();
			}
			else
			{
				foreach ($arrFiles as $file)
				{
					$objFile = new \File($file, true);
					$this->importBibTeX($objFile);
				}
				$this->redirect(str_replace('&key=import', '', \Environment::get('request')));
			}
		}
		return $this->Template->parse();
	}

	protected function importBibTeX(File $f)
	{
		$listener = new Listener();
		$listener->addProcessor(new Processor\TagNameCaseProcessor(CASE_LOWER));
		$listener->addProcessor(new Processor\NamesProcessor());
		// $listener->addProcessor(new Processor\KeywordsProcessor());
		// $listener->addProcessor(new Processor\DateProcessor());
		// $listener->addProcessor(new Processor\FillMissingProcessor([/* ... */]));
		// $listener->addProcessor(new Processor\TrimProcessor());
		// $listener->addProcessor(new Processor\UrlFromDoiProcessor());
		$listener->addProcessor(new Processor\LatexToUnicodeProcessor());
		
		// Create a Parser and attach the listener
		$parser = new Parser();
		$parser->addListener($listener);
		
		// Parse the content, then read processed data from the Listener
		$parser->parseFile(TL_ROOT . '/' . $f->value); // or parseFile('/path/to/file.bib')
		$entries = $listener->export();
		$literature_type = '';
		if (is_array($entries))
		{
			foreach ($entries as $entry)
			{
				$authors = $entry['author'];
				$editors = $entry['editor'];
				switch ($entry['_type'])
				{
					case 'article':
					case 'proceedings':
					case 'inproceedings':
					case 'incollection':
						$literature_type = 'periodic';
						break;
					case 'book':
					case 'booklet':
					case 'conference':
					case 'manual':
					case 'mastersthesis':
					case 'misc':
					case 'phdthesis':
					case 'techreport':
					case 'unpublished':
						$literature_type = 'nonperiodic';
						break;
					case 'inbook':
						$literature_type = 'nonperiodicpart';
						break;
					default:
						break;
				}

				$stmt = null;
				$pub = preg_split('/,/', $entry['publisher']);
				$publisher = (array_key_exists('publisher', $entry)) ? $entry['publisher'] : '';
				$location = '';
				if (is_array($pub) && count($pub) > 1)
				{
					$publisher = trim($pub[0]);
					unset($pub[0]);
					$location = trim(join($pub, ','));
				}
				// filter out some things
				foreach ($entry as $key => $value)
				{
					if (is_string($value))
					{
						$value = preg_replace("/[\\-]+/is", '-', $value);
						$value = preg_replace("/^(\\{)+(.*?)(\\})+$/is", "\\2", $value);
						$entry[$key] = $value;
					}
				}
				switch ($literature_type)
				{
					case 'periodic':
						$titlesort = (array_key_exists('title', $entry)) ? $entry['title'] : '';
						$title_journal = (array_key_exists('journal', $entry)) ? $entry['journal'] : '';
						if (array_key_exists('booktitle', $entry)) $title_journal = $entry['booktitle'];
						$title_info = (array_key_exists('series', $entry)) ? $entry['series'] : '';
						$stmt = $this->Database->prepare("INSERT INTO tl_literature (pid, literature_type, titlesort, title, title_periodic, title_nonperiodicpart, title_info, title_source, title_act, title_act_info, title_journal, pages, volume, issue, location, publisher, released, isbn, issn, uri, uri_date, authors, editors, abstract, tstamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")
							->execute(
								\Input::get('id'),
								$literature_type,
								$titlesort,
								'',
								(array_key_exists('title', $entry)) ? $entry['title'] : '',
								'',
								$title_info,
								'',
								'',
								'',
								$title_journal,
								(array_key_exists('pages', $entry)) ? $entry['pages'] : '',
								(array_key_exists('volume', $entry)) ? substr($entry['volume'],0,5) : '',
								(array_key_exists('number', $entry)) ? $entry['number'] : '',
								$location,
								$publisher,
								(array_key_exists('year', $entry)) ? $entry['year'] : '',
								(array_key_exists('isbn', $entry)) ? $entry['isbn'] : '',
								(array_key_exists('issn', $entry)) ? $entry['issn'] : '',
								(array_key_exists('url', $entry)) ? $entry['url'] : '',
								'',
								null,
								null,
								(array_key_exists('abstract', $entry)) ? $entry['abstract'] : '',
								time()
						);
						break;
					case 'nonperiodic':
						$titlesort = (array_key_exists('title', $entry)) ? $entry['title'] : '';
						$title_journal = (array_key_exists('journal', $entry)) ? $entry['journal'] : '';
						if (array_key_exists('booktitle', $entry)) $title_journal = $entry['booktitle'];
						$title_info = (array_key_exists('series', $entry)) ? $entry['series'] : '';
						$stmt = $this->Database->prepare("INSERT INTO tl_literature (pid, literature_type, titlesort, title, title_periodic, title_nonperiodicpart, title_info, title_source, title_act, title_act_info, title_journal, pages, volume, issue, location, publisher, released, isbn, issn, uri, uri_date, authors, editors, abstract, tstamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")
							->execute(
								\Input::get('id'),
								$literature_type,
								$titlesort,
								(array_key_exists('title', $entry)) ? $entry['title'] : '',
								'',
								'',
								$title_info,
								'',
								'',
								'',
								$title_journal,
								(array_key_exists('pages', $entry)) ? $entry['pages'] : '',
								(array_key_exists('volume', $entry)) ? $entry['volume'] : '',
								(array_key_exists('number', $entry)) ? $entry['number'] : '',
								$location,
								$publisher,
								(array_key_exists('year', $entry)) ? $entry['year'] : '',
								(array_key_exists('isbn', $entry)) ? $entry['isbn'] : '',
								(array_key_exists('issn', $entry)) ? $entry['issn'] : '',
								(array_key_exists('url', $entry)) ? $entry['url'] : '',
								'',
								null,
								null,
								(array_key_exists('abstract', $entry)) ? $entry['abstract'] : '',
								time()
						);
						break;
					case 'nonperiodicpart':
						if (count($editors) == 0) $editors = $authors;
						$titlesort = (array_key_exists('title', $entry)) ? $entry['title'] : '';
						$title_journal = (array_key_exists('journal', $entry)) ? $entry['journal'] : '';
						if (array_key_exists('booktitle', $entry)) $title_journal = $entry['booktitle'];
						$title_info = (array_key_exists('series', $entry)) ? $entry['series'] : '';
						$stmt = $this->Database->prepare("INSERT INTO tl_literature (pid, literature_type, titlesort, title, title_periodic, title_nonperiodicpart, title_info, title_source, title_act, title_act_info, title_journal, pages, volume, issue, location, publisher, released, isbn, issn, uri, uri_date, authors, editors, abstract, tstamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")
							->execute(
								\Input::get('id'),
								$literature_type,
								$titlesort,
								'',
								'',
								(array_key_exists('title', $entry)) ? $entry['title'] : '',
								$title_info,
								'',
								$title_journal,
								'',
								'',
								(array_key_exists('pages', $entry)) ? $entry['pages'] : '',
								(array_key_exists('volume', $entry)) ? $entry['volume'] : '',
								(array_key_exists('number', $entry)) ? $entry['number'] : '',
								$location,
								$publisher,
								(array_key_exists('year', $entry)) ? $entry['year'] : '',
								(array_key_exists('isbn', $entry)) ? $entry['isbn'] : '',
								(array_key_exists('issn', $entry)) ? $entry['issn'] : '',
								(array_key_exists('url', $entry)) ? $entry['url'] : '',
								'',
								null,
								null,
								(array_key_exists('abstract', $entry)) ? $entry['abstract'] : '',
								time()
						);
						break;
				}

				$literature_id = $stmt->insertId;
				$position = 1;
				$sortstring = '';
				$this->Database->prepare("DELETE FROM tl_literature_author WHERE pid = ?")
					->execute($literature_id);
				foreach ($authors as $author)
				{
					if (is_array($author))
					{
						$this->Database->prepare("INSERT INTO tl_literature_author (pid, sequence, lastname, firstname) VALUES (?, ?, ?, ?)")
							->execute($literature_id, $position, trim(trim($author[2]) . ' ' . trim($author[3])), trim(trim($author[0]) . ' ' . trim($author[1])));
						$sortstring .= trim(trim($author[2]) . ' ' . trim($author[3])) . ',' . trim(trim($author[0]) . ' ' . trim($author[1]));
						$position++;
					}
				}
				$this->Database->prepare("DELETE FROM tl_literature_editor WHERE pid = ?")
					->execute($literature_id);
				$position = 1;
				foreach ($editors as $editor)
				{
					if (is_array($editor))
					{
						$this->Database->prepare("INSERT INTO tl_literature_editor (pid, sequence, lastname, firstname) VALUES (?, ?, ?, ?)")
							->execute($literature_id, $position, trim(trim($editor[2]) . ' ' . trim($editor[3])), trim(trim($editor[0]) . ' ' . trim($editor[1])));
						$position++;
					}
				}
				if (strlen($sortstring))
				{
					$this->Database->prepare("UPDATE tl_literature SET authorssort = ? WHERE id = ?")
						->execute($sortstring, $literature_id);
				}
				if (strlen($entry['keywords']))
				{
					$arrKeywords = preg_split('/,/', $entry['keywords']);
					foreach ($arrKeywords as $keyword)
					{
						$keyword = trim($keyword);
						if (strlen($keyword))
						{
							$this->Database->prepare("INSERT INTO tl_tag (id, tag, from_table) VALUES (?, ?, ?)")
								->execute($literature_id, $keyword, 'tl_literature');
						}
					}
				}
			}
		}
	}

	/**
	 * Return the file tree widget as object
	 * @param mixed
	 * @return object
	 */
	protected function getFileTreeWidget($value=null)
	{
		$widget = new \FileTree();

		$widget->id = 'literaturefile';
		$widget->name = 'literaturefile';
		$widget->mandatory = true;
		$GLOBALS['TL_DCA']['tl_member']['fields']['literaturefile']['eval']['fieldType'] = 'radio';
		$GLOBALS['TL_DCA']['tl_member']['fields']['literaturefile']['eval']['files'] = true;
		$GLOBALS['TL_DCA']['tl_member']['fields']['literaturefile']['eval']['filesOnly'] = true;
		$GLOBALS['TL_DCA']['tl_member']['fields']['literaturefile']['eval']['extensions'] = 'bib';
		$widget->strTable = 'tl_literature';
		$widget->strField = 'literaturefile';
		$widget->value = $value;

		$widget->label = $GLOBALS['TL_LANG']['tl_literature']['literaturefile'][0];

		if ($GLOBALS['TL_CONFIG']['showHelp'] && strlen($GLOBALS['TL_LANG']['tl_literature']['literaturefile'][1]))
		{
			$widget->help = $GLOBALS['TL_LANG']['tl_literature']['literaturefile'][1];
		}

		// Valiate input
		if (\Input::post('FORM_SUBMIT') == 'tl_import_literature_fileselection')
		{
			$widget->validate();
			if ($widget->hasErrors())
			{
				$this->blnSave = false;
			}
		}

		return $widget;
	}
}
