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
use Contao\StringUtil;

class LiteratureTools extends \Backend
{
	protected $blnSave = true;

	public function importLiterature()
	{
		/** @var FileUpload $objUploader */
		$objUploader = new \FileUpload();

		if (\Input::post('FORM_SUBMIT') == 'tl_literature_import')
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
				if (is_dir($this->strRootDir . '/' . $strFile))
				{
					\Message::addError(sprintf($GLOBALS['TL_LANG']['ERR']['importFolder'], basename($strFile)));
					continue;
				}

				$objFile = new \File($strFile);

				// Skip anything but .bib files
				if ($objFile->extension != 'bib')
				{
					\Message::addError(sprintf($GLOBALS['TL_LANG']['ERR']['filetype'], $objFile->extension));
					continue;
				}

				$arrFiles[] = $strFile;
			}

			// Check whether there are any files
			if (empty($arrFiles))
			{
				\Message::addError($GLOBALS['TL_LANG']['ERR']['all_fields']);
				$this->reload();
			}
			$this->importBibTeX($arrFiles);
			$this->redirect(str_replace('&key=import', '', \Environment::get('request')));
		}

		// Return the form
		return \Message::generate() . '<div id="tl_buttons">
<a href="' . ampersand(str_replace('&key=import', '', \Environment::get('request'))) . '" class="header_back" title="' . StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']) . '" accesskey="b">' . $GLOBALS['TL_LANG']['MSC']['backBT'] . '</a>
</div>
<h2 class="sub_headline">'.$GLOBALS['TL_LANG']['tl_literature']['import'][1].'</h2>
<form id="tl_literature_import" class="tl_form tl_edit_form" method="post" enctype="multipart/form-data">
<div class="tl_formbody_edit">
<input type="hidden" name="FORM_SUBMIT" value="tl_literature_import">
<input type="hidden" name="REQUEST_TOKEN" value="' . REQUEST_TOKEN . '">
<input type="hidden" name="MAX_FILE_SIZE" value="' . \Config::get('maxFileSize') . '">
<div class="tl_tbox">
  <div class="widget">
    <h3>' . $GLOBALS['TL_LANG']['tl_literature']['literaturefile'][0] . '</h3>' . $objUploader->generateMarkup() . (isset($GLOBALS['TL_LANG']['tl_literature']['literaturefile'][1]) ? '
    <p class="tl_help tl_tip">' . $GLOBALS['TL_LANG']['tl_literature']['literaturefile'][1] . '</p>' : '') . '
  </div>
</div>
</div>
<div class="tl_formbody_submit">
<div class="tl_submit_container">
  <button type="submit" name="save" id="save" class="tl_submit" accesskey="s">' . $GLOBALS['TL_LANG']['tl_literature']['import'][0] . '</button>
</div>
</div>
</form>';
	}

	protected function importBibTeX($arrFiles)
	{
		$listener = new Listener();
		$listener->addProcessor(new Processor\TagNameCaseProcessor(CASE_LOWER));
		$listener->addProcessor(new Processor\NamesProcessor());
		// $listener->addProcessor(new Processor\KeywordsProcessor());
		// $listener->addProcessor(new Processor\DateProcessor());
		// $listener->addProcessor(new Processor\FillMissingProcessor([/* ... */]));
		// $listener->addProcessor(new Processor\TrimProcessor());
		// $listener->addProcessor(new Processor\UrlFromDoiProcessor());
		//$listener->addProcessor(new Processor\LatexToUnicodeProcessor());
		
		// Create a Parser and attach the listener
		$parser = new Parser();
		$parser->addListener($listener);

		foreach ($arrFiles as $strFile)
		{
			// Parse the content, then read processed data from the Listener
			$parser->parseFile(TL_ROOT . "/" . $strFile); // or parseFile('/path/to/file.bib')
			$entries = $listener->export();
			$literature_type = '';
			if (is_array($entries))
			{
				foreach ($entries as $entry)
				{
					$authors = $entry['author'];
					$editors = $entry['editor'];
					switch (strtolower($entry['type']))
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
								->execute($literature_id, $position, ((strlen($author['von'])>0) ? $author['von'] . ' ' : '') . $author['last'] . ((strlen($author['jr'])>0) ? ' ' . $author['jr']  : '') , $author['first']);
							$sortstring .= $author['last'] . ',' . $author['first'];
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
								->execute($literature_id, $position, ((strlen($editor['von'])>0) ? $editor['von'] . ' ' : '') . $editor['last'] . ((strlen($editor['jr'])>0) ? ' ' . $editor['jr']  : '') , $editor['first']);
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
	}

}
