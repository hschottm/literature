<?php

/**
 * @copyright  Helmut Schottmüller 2009-2017
 * @author     Helmut Schottmüller <https://github.com/hschottm/literature>
 * @package    literature
 * @license    LGPL
 * @filesource
 */

namespace Contao;

/**
 * Class LiteraturePersonalPage
 *
 * @copyright  Helmut Schottmüller 2009-2013
 * @author     Helmut Schottmüller <https://github.com/hschottm/literature>
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
		$objTemplate = new \FrontendTemplate('litlist_available_literature');
		$objEntries = $this->Database->prepare("SELECT tl_literature_category.title as category, tl_literature.* FROM tl_literature_category, tl_literature WHERE tl_literature_category.id = tl_literature.pid ORDER BY category, tl_literature.released")
			->execute();
		$literature_entries = array();
		if ($objEntries->numRows)
		{
			while ($objEntries->next())
			{
				if (!array_key_exists($objEntries->category, $literature_entries)) $literature_entries[$objEntries->category] = array();
				$data = $objEntries->row();
				$preview = new \LiteraturePreview($data);
				$literature_entries[$objEntries->category][$data["id"]] = $preview->getPreview();
			}
		}
		$listarr = \Input::post("addLiteratureEntry");
		$objTemplate->listname = key($listarr);
		$objTemplate->strAddSelection = $GLOBALS['TL_LANG']['tl_literature']['addSelection'];
		$objTemplate->strCancel = $GLOBALS['TL_LANG']['tl_literature']['cancel'];
		$objTemplate->literatureEntries = $literature_entries;
		return $objTemplate->parse();
	}

	public function editPersonalLiteratureList(&$pageArray, $caller)
	{
		$this->import('FrontendUser', 'User');
		if (is_array(\Input::post("addLiteratureEntry")))
		{
			$caller->setShowPageHead(FALSE);
			return $this->showLiteratureSelection();
		}
		if (is_array(\Input::post("removeList")))
		{
			$listname = key(\Input::post("removeList"));
			unset($pageArray["content"]["lists"][$listname]);
		}

		$references = array();
		if (strlen(\Input::post("removeLiteraturePersonalPage")))
		{
			foreach ($pageArray["content"]["lists"] as $listname => $listarray)
			{
				$remove = \Input::post("remove");
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
		if (strlen(\Input::post("selectLiteratureEntries")))
		{
			$selected = \Input::post('literatureEntry');
			if (is_array($selected))
			{
				foreach ($selected as $id)
				{
					if (!in_array($id, $pageArray["content"]["lists"][\Input::post("listname")]))
					{
						$pageArray["content"]["lists"][\Input::post("listname")][] = $id;
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
					$preview = new \LiteraturePreview();
					$preview->loadLiterature($id);
					$references[$id] = $preview->getPreview();
				}
			}
		}
		else
		{
			$pageArray["content"] = array();
		}

		if (strlen(\Input::post("add_list")))
		{
			$pageArray["content"]["lists"][\Input::post("listadd")] = array();
		}
		if (strlen(\Input::post("saveLiteraturePersonalPage")) ||
			strlen(\Input::post("removeLiteraturePersonalPage")) ||
			strlen(\Input::post("removeList")) ||
			strlen(\Input::post("add_list"))
		)
		{
			$this->Database->prepare("UPDATE tl_member_pages SET title=?, content=?, is_visible=? WHERE position=? AND id IN (" . implode(",", deserialize($this->User->member_pages, TRUE)). ")")
				->execute(\Input::post("pageTitle"), serialize($pageArray["content"]), \Input::post("is_visible"), $pageArray["position"]);
			$pageArray["title"] = \Input::post("pageTitle");
			$pageArray["is_visible"] = \Input::post("is_visible");
		}
		if (strlen(\Input::post("selectLiteratureEntries")))
		{
			$this->Database->prepare("UPDATE tl_member_pages SET content=? WHERE position=? AND id IN (" . implode(",", deserialize($this->User->member_pages, TRUE)). ")")
				->execute(serialize($pageArray["content"]), $pageArray["position"]);
		}
		$objTemplate = new \FrontendTemplate('litlist_personal_editor');
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
				$preview = new \LiteraturePreview();
				$preview->loadLiterature($id);
				$references[$id] = $preview->getPreview();
			}
		}
		$objTemplate = new \FrontendTemplate('litlist_personal');
		$objTemplate->lists = is_array($pageArray["content"]["lists"]) ? $pageArray["content"]["lists"] : array();
		$objTemplate->references = $references;
		return $objTemplate->parse();
	}
}
