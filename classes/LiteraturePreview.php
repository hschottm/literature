<?php

/**
 * @copyright  Helmut Schottmüller 2009-2013
 * @author     Helmut Schottmüller <https://github.com/hschottm/literature>
 * @package    literature 
 * @license    LGPL 
 * @filesource
 */

namespace Contao;

/**
 * Class LiteraturePreview
 *
 * @copyright  Helmut Schottmüller 2009-2013
 * @author     Helmut Schottmüller <https://github.com/hschottm/literature>
 * @package    Controller
 */
class LiteraturePreview extends Frontend
{
	/**
	* Array data of a tl_literature entry
	*/
	protected $arrData;

	/**
	* List of authors
	*/
	protected $arrAuthors = array();

	/**
	* List of editors
	*/
	protected $arrEditors = array();
	
	/**
	* Frontend template for the literature output
	*/
	protected $template;

	function __construct($data = null, $template = "") 
	{
		parent::__construct();
		if (is_array($data)) 
		{
			$this->arrData = $data;
			$this->arrData['multiSRC'] = deserialize($this->arrData['multiSRC']);
			$this->loadAuthors($this->arrData['id']);
			$this->loadEditors($this->arrData['id']);
		}
		$this->template = (strlen($template)) ? $template : $this->getDefaultTemplate();
	}
	
	/**
	 * Return the name of the default literature template
	 * @return string
	 */
	protected function getDefaultTemplate()
	{
		return "litref_standard";
	}
	
	/**
	 * Load a literature entry into the object
	 * @param integer
	 */
	public function loadLiterature($id)
	{
		$objLiterature = $this->Database->prepare("SELECT * FROM tl_literature WHERE id = ?")
			->execute($id);
		if ($objLiterature->numRows == 1)
		{
			$this->arrData = $objLiterature->row();
			$this->loadAuthors($this->arrData['id']);
			$this->loadEditors($this->arrData['id']);
		}
	}
	
	/**
	 * Load the literature authors into an array
	 * @param integer
	 * @return array
	 */
	protected function loadAuthors($id)
	{
		$this->import('Database');
		$objAuthor = $this->Database->prepare("SELECT * FROM tl_literature_author WHERE pid = ? ORDER BY sequence")
			->execute($id);
		$this->arrAuthors = array();
		if ($objAuthor->numRows)
		{
			while ($objAuthor->next())
			{
				array_push($this->arrAuthors, array($objAuthor->firstname, $objAuthor->lastname));
			}
		}
	}

	/**
	 * Load the literature editors into an array
	 * @param integer
	 * @return array
	 */
	protected function loadEditors($id)
	{
		$this->import('Database');
		$objEditor = $this->Database->prepare("SELECT * FROM tl_literature_editor WHERE pid = ? ORDER BY sequence")
			->execute($id);
		$this->arrEditors = array();
		if ($objEditor->numRows)
		{
			while ($objEditor->next())
			{
				array_push($this->arrEditors, array($objEditor->firstname, $objEditor->lastname));
			}
		}
	}

	/**
	 * Return the preview of a literature entry
	 * @param array
	 * @param boolean
	 * @return string
	 */
	public function getPreview()
	{
		$objTemplate = new \FrontendTemplate($this->template);
		$objTemplate->authors = $this->arrAuthors;
		$objTemplate->editors = $this->arrEditors;
		$objTemplate->released = $this->arrData["released"];
		$objTemplate->literature_type = $this->arrData["literature_type"];
		$objTemplate->title_periodic = $this->arrData["title_periodic"];
		$objTemplate->title = $this->arrData["title"];
		$objTemplate->title_info = $this->arrData["title_info"];
		$objTemplate->title_journal = $this->arrData["title_journal"];
		$objTemplate->volume = $this->arrData["volume"];
		$objTemplate->issue = $this->arrData["issue"];
		$objTemplate->pages = $this->arrData["pages"];
		$objTemplate->abstract = $this->arrData["abstract"];
		$objTemplate->uri = $this->arrData["uri"];
		$objTemplate->urishort = $this->arrData["urishort"];
		$objTemplate->uri_date = $this->arrData["uri_date"];
		$objTemplate->location = $this->arrData["location"];
		$objTemplate->publisher = $this->arrData["publisher"];
		$objTemplate->isbn = $this->arrData["isbn"];
		$objTemplate->title_nonperiodicpart = $this->arrData["title_nonperiodicpart"];
		$objTemplate->title_act = $this->arrData["title_act"];
		$objTemplate->title_act_info = $this->arrData["title_act_info"];
		$objTemplate->title_source = $this->arrData["title_source"];
		$objTemplate->strAvailableAt = $GLOBALS['TL_LANG']['tl_literature']['availableat'];
		$objTemplate->strDateFormat = $GLOBALS['TL_CONFIG']['dateFormat'];
		$objTemplate->strIn = $GLOBALS['TL_LANG']['tl_literature']['in'];
		$objTemplate->strEditorShort = $GLOBALS['TL_LANG']['tl_literature']['editorShort'];
		$objTemplate->strEditorsShort = $GLOBALS['TL_LANG']['tl_literature']['editorsShort'];
		$objTemplate->strPageShort = $GLOBALS['TL_LANG']['tl_literature']['pageshort'];
		$objTemplate->addDownloads = $this->arrData["addDownloads"];
		$objTemplate->downtitle = $this->arrData["downtitle"];
		if ($this->arrData["addDownloads"])
		{
			$objTemplate->files = $this->prepareDownloads();
		}
		if ($this->arrData['addImage'] && is_file(TL_ROOT . '/' . $this->arrData['singleSRC']))
		{
			$this->addImageToTemplate($objTemplate, $this->arrData);
		}
		return $objTemplate->parse();
	}

	protected function prepareDownloads()
	{
		$files = array();
		$auxDate = array();

		$allowedDownload = trimsplit(',', strtolower($GLOBALS['TL_CONFIG']['allowedDownload']));

		// Get all files
		foreach ($this->arrData['multiSRC'] as $file)
		{
			if (isset($files[$file]) || !file_exists(TL_ROOT . '/' . $file))
			{
				continue;
			}

			// Single files
			if (is_file(TL_ROOT . '/' . $file))
			{
				$objFile = new \File($file);

				if (in_array($objFile->extension, $allowedDownload) && !preg_match('/^meta(_[a-z]{2})?\.txt$/', basename($file)))
				{
					$this->parseMetaFile(dirname($file), true);
					$arrMeta = $this->arrMeta[$objFile->basename];

					if ($arrMeta[0] == '')
					{
						$arrMeta[0] = specialchars($objFile->basename);
					}

					$files[$file] = array
					(
						'link' => $arrMeta[0],
						'title' => $arrMeta[0],
						'href' => \Environment::get('request') . (($GLOBALS['TL_CONFIG']['disableAlias'] || strpos(\Environment::get('request'), '?') !== false) ? '&' : '?') . 'file=' . $this->urlEncode($file),
						'caption' => $arrMeta[2],
						'filesize' => $this->getReadableSize($objFile->filesize, 1),
						'icon' => 'system/themes/' . $this->getTheme() . '/images/' . $objFile->icon,
						'meta' => $arrMeta
					);

					$auxDate[] = $objFile->mtime;
				}

				continue;
			}

			$subfiles = scan(TL_ROOT . '/' . $file);
			$this->parseMetaFile($file);

			// Folders
			foreach ($subfiles as $subfile)
			{
				if (is_dir(TL_ROOT . '/' . $file . '/' . $subfile))
				{
					continue;
				}

				$objFile = new \File($file . '/' . $subfile);

				if (in_array($objFile->extension, $allowedDownload) && !preg_match('/^meta(_[a-z]{2})?\.txt$/', basename($subfile)))
				{
					$arrMeta = $this->arrMeta[$objFile->basename];

					if ($arrMeta[0] == '')
					{
						$arrMeta[0] = specialchars($objFile->basename);
					}

					$files[$file . '/' . $subfile] = array
					(
						'link' => $arrMeta[0],
						'title' => $arrMeta[0],
						'href' => \Environment::get('request') . (($GLOBALS['TL_CONFIG']['disableAlias'] || strpos(\Environment::get('request'), '?') !== false) ? '&' : '?') . 'file=' . $this->urlEncode($file . '/' . $subfile),
						'caption' => $arrMeta[2],
						'filesize' => $this->getReadableSize($objFile->filesize, 1),
						'icon' => 'system/themes/' . $this->getTheme() . '/images/' . $objFile->icon,
						'meta' => $arrMeta
					);

					$auxDate[] = $objFile->mtime;
				}
			}
		}

		// Sort array
		switch ($this->arrData['sortBy'])
		{
			default:
			case 'name_asc':
				uksort($files, 'basename_natcasecmp');
				break;

			case 'name_desc':
				uksort($files, 'basename_natcasercmp');
				break;

			case 'date_asc':
				array_multisort($files, SORT_NUMERIC, $auxDate, SORT_ASC);
				break;

			case 'date_desc':
				array_multisort($files, SORT_NUMERIC, $auxDate, SORT_DESC);
				break;

			case 'meta':
				$arrFiles = array();
				foreach ($this->arrAux as $k)
				{
					if (strlen($k))
					{
						$arrFiles[] = $files[$k];
					}
				}
				$files = $arrFiles;
				break;
		}

		return array_values($files);
	}

	/**
	 * Replace literature specific insert tags
	 * @param string
	 * @return string
	 */
	public function replaceLiteratureInsertTags($strTag)
	{
		if (preg_match("/insert_literature::(\\d+)/", $strTag, $matches))
		{
			$objLiterature = $this->Database->prepare("SELECT * FROM tl_literature WHERE id = ?")
				->execute($matches[1]);
			if ($objLiterature->numRows == 1)
			{
				$this->arrData = $objLiterature->row();
				return $this->getPreview();
			}
		}
		return false;
	}
}

?>