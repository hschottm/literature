<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 * Copyright (C) 2005 Leo Feyer
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at http://www.gnu.org/licenses/.
 *
 * PHP version 5
 * @copyright  Helmut Schottmüller 2008 
 * @author     Helmut Schottmüller <typolight@aurealis.de>
 * @package    literature 
 * @license    LGPL 
 * @filesource
 */


/**
 * Class ModuleLiteratureList
 *
 * @copyright  Helmut Schottmüller 2008 
 * @author     Helmut Schottmüller <typolight@aurealis.de>
 * @package    Controller
 */
class ModuleLiteratureList extends Module
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_literaturelist';

	/**
	 * Categories
	 * @var array
	 */
	protected $arrCategories = array();

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new BackendTemplate('be_wildcard');
			$objTemplate->wildcard = '### LITERATURELIST ###';

			return $objTemplate->parse();
		}

		$file = $this->Input->get('file', true);

		// Send the file to the browser
		if ($file != '' && !preg_match('/^meta(_[a-z]{2})?\.txt$/', basename($file)))
		{
			$this->sendFileToBrowser($file);
			return '';
		}


		$this->arrCategories = deserialize($this->lit_categories, true);

		if (count($this->arrCategories) < 1)
		{
			return '';
		}

		return parent::generate();
	}


	/**
	 * Generate module
	 */
	protected function compile()
	{
		$this->import('String');
		$this->loadDataContainer('tl_literature');
		$this->loadLanguageFile('tl_literature');
		$this->listLiterature();
	}

	/**
	 * List literature entries
	 */
	protected function listLiterature()
	{
		$strWhere = "";
		$arrValues = array();

		// Split results
		$page = $this->Input->get('page') ? $this->Input->get('page') : 1;
		$per_page = $this->Input->get('per_page') ? $this->Input->get('per_page') : $this->perPage;

		$sortdirection = (strlen($this->lit_sortorder)) ? $this->lit_sortorder : "ASC";
		switch ($this->lit_sort)
		{
			case "released":
				$order_by = "released $sortdirection, titlesort ";
				break;
			case "title":
				$order_by = "titlesort $sortdirection, released ";
				break;
			case "author":
				$order_by = "authorssort $sortdirection, released, titlesort ";
				break;
			default:
				$order_by = "released $sortdirection, titlesort ";
				break;
		}

		$this->Template->listtitle = $this->lit_listtitle;

		$tags = (strlen($this->lit_tags)) ? array_filter(trimsplit(",", $this->lit_tags), 'strlen') : array();
		$relatedlist = (strlen($this->Input->get('related'))) ? split(",", $this->Input->get('related')) : array();
		$searchtags = array_merge(array($this->Input->get('tag')), $relatedlist);
		if (count($tags))
		{
			$alltags = array();
			foreach ($searchtags as $tag) if (in_array($tag, $tags)) array_push($alltags, $tag);
		} else $alltags = $searchtags;
		$tagids = array();
		foreach ($alltags as $tag)
		{
			if (count($tagids))
			{
				$tagids = $this->Database->prepare("SELECT id FROM tl_tag WHERE from_table = ? AND tag = ? AND id IN (" . join($tagids, ",") . ")")
					->execute('tl_literature', $tag)
					->fetchEach('id');
			}
			else
			{
				$tagids = $this->Database->prepare("SELECT id FROM tl_tag WHERE from_table = ? AND tag = ?")
					->execute('tl_literature', $tag)
					->fetchEach('id');
			}
		}
		if (count($tagids) == 0)
		{
			if (strlen($this->Input->get('tag')))
			{
				$this->Template->tbody = array();
				return;
			}
			if (count($tags))
			{
				foreach ($tags as $tag)
				{
					$arrIds = $this->Database->prepare("SELECT id FROM tl_tag WHERE from_table = ? AND tag = ?")
						->execute('tl_literature', $tag)
						->fetchEach('id');
					$tagids = array_merge($tagids, $arrIds);
				}
			}
		}
		$strWhere = "";
		if (count($tagids))
		{
			$strWhere = " AND id IN (" . join($tagids, ",") . ")";
		}

		$searchfilter = '';
		$params = array();
		if (strlen($this->Input->get('search')) && strlen($this->Input->get('for')))
		{
			$searchfilter = ' AND ' . $this->Input->get('search') . ' LIKE ?';
			array_push($params, '%' . $this->Input->get('for') . '%');
		}

		// Get total number of literature entries
		$objTotal = $this->Database->prepare("SELECT COUNT(*) AS count FROM tl_literature WHERE pid IN (" . join($this->arrCategories, ",") . ")$strWhere$searchfilter ORDER BY " . $order_by)->execute($params);

		$objLiteratureStmt = $this->Database->prepare("SELECT * FROM tl_literature WHERE pid IN (" . join($this->arrCategories, ",") . ")$strWhere$searchfilter ORDER BY " . $order_by);
		// Limit
		if ($per_page)
		{
			$objLiteratureStmt->limit($per_page, (($page - 1) * $per_page));
		}

		$objLiterature = $objLiteratureStmt->execute($params);

		$start = -1;
		$limit = $objLiterature->numRows;
		$lit_template = (strlen($this->lit_template)) ? $this->lit_template : "litref_standard";
		$arrData = array();

		while ($objLiterature->next())
		{
			$class = 'row_' . ++$start . (($start == 0) ? ' row_first' : '') . ((($start + 1) == $limit) ? ' row_last' : '') . ((($start % 2) == 0) ? ' even' : ' odd');
			$litdata = $objLiterature->row();
			$litdata['urishort'] = $litdata['uri'];
			if (strlen($litdata['uri']))
			{
				if (preg_match("/^([a-zA-Z]+:\\/\\/.*?\\/).*$/", $litdata['uri'], $matches))
				{
					$litdata['urishort'] = $matches[1];
				}
			}
			$preview = new LiteraturePreview($litdata, $lit_template);
			$arrData[$class] = array(
				'raw' => $litdata,
				'content' => $preview->getPreview(),
				'class' => 'col_first',
				'id' => $objLiterature->id
			);
		}

		$this->Template->tbody = $arrData;
		// Pagination
		$objPagination = new Pagination($objTotal->count, $per_page);
		$this->Template->pagination = $objPagination->generate("\n  ");
		$this->Template->per_page = $per_page;
	}
}

?>