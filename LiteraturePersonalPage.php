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
 * @copyright  Leo Feyer 2008 
 * @author     Leo Feyer 
 * @package    literature 
 * @license    LGPL 
 * @filesource
 */


/**
 * Class LiteraturePersonalPage
 *
 * @copyright  Helmut Schottmüller 2008 
 * @author     Helmut Schottmüller <typolight@aurealis.de>
 * @package    Controller
 */
class LiteraturePersonalPage extends Frontend
{
	function __construct()
	{
		parent::__construct();
		$this->loadLanguageFile('tl_literature');
	}
	
	protected function showLiteratureSelection()
	{
		$objTemplate = new FrontendTemplate('litlist_available_literature');
		$objEntries = $this->Database->prepare("SELECT tl_literature_category.title as category, tl_literature.* FROM tl_literature_category, tl_literature WHERE tl_literature_category.id = tl_literature.pid ORDER BY category, tl_literature.released")
			->execute();
		$literature_entries = array();
		if ($objEntries->numRows)
		{
			while ($objEntries->next())
			{
				if (!array_key_exists($objEntries->category, $literature_entries)) $literature_entries[$objEntries->category] = array();
				$data = $objEntries->row();
				$preview = new LiteraturePreview($data);
				$literature_entries[$objEntries->category][$data["id"]] = $preview->getPreview();
			}
		}
		$listarr = $this->Input->post("addLiteratureEntry");
		$objTemplate->listname = key($listarr);
		$objTemplate->strAddSelection = $GLOBALS['TL_LANG']['tl_literature']['addSelection'];
		$objTemplate->strCancel = $GLOBALS['TL_LANG']['tl_literature']['cancel'];
		$objTemplate->literatureEntries = $literature_entries;
		return $objTemplate->parse();
	}
	
	public function editPersonalLiteratureList(&$pageArray, $caller)
	{
		$this->import('FrontendUser', 'User');
		if (is_array($this->Input->post("addLiteratureEntry")))
		{
			$caller->setShowPageHead(FALSE);
			return $this->showLiteratureSelection();
		}
		if (is_array($this->Input->post("removeList")))
		{
			$listname = key($this->Input->post("removeList"));
			unset($pageArray["content"]["lists"][$listname]);
		}
		
		$references = array();
		if (strlen($this->Input->post("removeLiteraturePersonalPage")))
		{
			foreach ($pageArray["content"]["lists"] as $listname => $listarray)
			{
				$remove = $this->Input->post("remove");
				if (is_array($remove) && is_array($remove[$listname]) && count($remove[$listname]))
				{
					foreach ($remove[$listname] as $id)
					{
						$key = array_search($id, $pageArray["content"]["lists"][$listname]);
						unset($pageArray["content"]["lists"][$listname][$key]);
					}
					$pageArray["content"]["lists"][$listname] = array_values($pageArray["content"]["lists"][$listname]);
				}
			}
		}
		if (strlen($this->Input->post("selectLiteratureEntries")))
		{
			$selected = $this->Input->post('literatureEntry');
			if (is_array($selected))
			{
				foreach ($selected as $id)
				{
					if (!in_array($id, $pageArray["content"]["lists"][$this->Input->post("listname")]))
					{
						$pageArray["content"]["lists"][$this->Input->post("listname")][] = $id;
					}
				}
			}
		}
		
		if (is_array($pageArray["content"]["lists"]))
		{
			foreach ($pageArray["content"]["lists"] as $listname => $listarray)
			{
				foreach ($listarray as $sequence => $id)
				{
					$preview = new LiteraturePreview();
					$preview->loadLiterature($id);
					$references[$id] = $preview->getPreview();
				}
			}
		}
		else
		{
			$pageArray["content"] = array();
		}
		
		if (strlen($this->Input->post("add_list")))
		{
			$pageArray["content"]["lists"][$this->Input->post("listadd")] = array();
		}
		if (strlen($this->Input->post("saveLiteraturePersonalPage")) || 
			strlen($this->Input->post("removeLiteraturePersonalPage")) || 
			strlen($this->Input->post("removeList")) || 
			strlen($this->Input->post("add_list"))
		)
		{
			$this->Database->prepare("UPDATE tl_member_pages SET title=?, content=?, is_visible=? WHERE position=? AND id IN (" . implode(",", deserialize($this->User->member_pages, TRUE)). ")")
				->execute($this->Input->post("pageTitle"), serialize($pageArray["content"]), $this->Input->post("is_visible"), $pageArray["position"]);
			$pageArray["title"] = $this->Input->post("pageTitle");
			$pageArray["is_visible"] = $this->Input->post("is_visible");
		}
		if (strlen($this->Input->post("selectLiteratureEntries")))
		{
			$this->Database->prepare("UPDATE tl_member_pages SET content=? WHERE position=? AND id IN (" . implode(",", deserialize($this->User->member_pages, TRUE)). ")")
				->execute(serialize($pageArray["content"]), $pageArray["position"]);
		}
		$objTemplate = new FrontendTemplate('litlist_personal_editor');
		$objTemplate->strNewList = $GLOBALS['TL_LANG']['tl_literature']['newList'];
		$objTemplate->strAdd = $GLOBALS['TL_LANG']['tl_literature']['add'];
		$objTemplate->strAddLiterature = $GLOBALS['TL_LANG']['tl_literature']['addLiterature'];
		$objTemplate->strRemoveList = $GLOBALS['TL_LANG']['tl_literature']['removeList'];
		$objTemplate->strRemoveSelected = $GLOBALS['TL_LANG']['tl_literature']['removeSelected'];
		$objTemplate->strConfirmRemoveList = $GLOBALS['TL_LANG']['tl_literature']['confirmRemoveList'];
		$objTemplate->strSave = $GLOBALS['TL_LANG']['MSC']['save'];
		$objTemplate->lists = is_array($pageArray["content"]["lists"]) ? $pageArray["content"]["lists"] : array();
		$objTemplate->references = $references;
		return $objTemplate->parse();
	}
	
	public function showPersonalLiteratureList(&$pageArray)
	{
		$references = array();
		foreach ($pageArray["content"]["lists"] as $listname => $listarray)
		{
			foreach ($listarray as $sequence => $id)
			{
				$preview = new LiteraturePreview();
				$preview->loadLiterature($id);
				$references[$id] = $preview->getPreview();
			}
		}		
		$objTemplate = new FrontendTemplate('litlist_personal');
		$objTemplate->lists = is_array($pageArray["content"]["lists"]) ? $pageArray["content"]["lists"] : array();
		$objTemplate->references = $references;
		return $objTemplate->parse();
	}
}