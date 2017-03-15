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
 * Class ModuleLiteratureSearch
 *
 * @copyright  Helmut Schottmüller 2009-2017
 * @author     Helmut Schottmüller <https://github.com/hschottm/literature>
 * @package    Fontend
 */
class ModuleLiteratureSearch extends \Module
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_literaturesearch';

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
			$objTemplate = new \BackendTemplate('be_wildcard');
			$objTemplate->wildcard = '### LITERATURESEARCH ###';

			return $objTemplate->parse();
		}

		return parent::generate();
	}


	/**
	 * Generate module
	 */
	protected function compile()
	{
		$this->loadDataContainer('tl_literature');
		$this->loadLanguageFile('tl_literature');
		$this->showSearchField();
	}

	protected function showSearchField()
	{
		$request = $this->getIndexFreeRequest();
		if ($this->jumpTo > 0)
		{
			$objPage = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE id=?")
										->limit(1)
										->execute($this->jumpTo);

			if ($objPage->numRows)
			{
				$request = $this->generateFrontendUrl($objPage->row());
			}
		}

		$strOptions = '';
		$arrFields = array(
			'titlesort' => $GLOBALS['TL_DCA']['tl_literature']['fields']['title']['label'][0],
			'authorssort' => $GLOBALS['TL_DCA']['tl_literature']['fields']['authors']['label'][0],
			'title_journal' => $GLOBALS['TL_DCA']['tl_literature']['fields']['title_journal']['label'][0],
			'location' => $GLOBALS['TL_DCA']['tl_literature']['fields']['location']['label'][0],
			'publisher' => $GLOBALS['TL_DCA']['tl_literature']['fields']['publisher']['label'][0],
			'released' => $GLOBALS['TL_DCA']['tl_literature']['fields']['released']['label'][0],
		);
		foreach ($arrFields as $k=>$v)
		{
			$strOptions .= '  <option value="' . $k . '"' . (($k == \Input::get('search')) ? ' selected="selected"' : '') . '>' . $v . '</option>' . "\n";
		}

		$this->Template->search_fields = $strOptions;
		$this->Template->action = $request;
		$this->Template->fields_label = $GLOBALS['TL_LANG']['MSC']['all_fields'][0];
		$this->Template->keywords_label = $GLOBALS['TL_LANG']['MSC']['keywords'];
		$this->Template->search_label = specialchars($GLOBALS['TL_LANG']['MSC']['search']);
		$this->Template->search = \Input::get('search');
		$this->Template->for = \Input::get('for');
		if ($this->jumpTo > 0)
		{
			$this->Template->id = $this->jumpTo;
		}
		else
		{
			$this->Template->id = \Input::get('id');
		}
		$this->Template->order_by = \Input::get('order_by');
		$this->Template->sort = \Input::get('sort');
	}
}
