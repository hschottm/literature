<?php
namespace Hschottm\LiteratureBundle;
use Contao\{Module, BackendTemplate, System, PageModel, Environment, Input};

class ModuleLiteratureSearch extends Module
{
    protected $strTemplate = 'mod_literaturesearch';
    public function generate()
    {
        $request = System::getContainer()->get('request_stack')->getCurrentRequest();
        if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request)) {
            $template = new BackendTemplate('be_wildcard');
            $template->wildcard = '### LITERATURE SEARCH ###';
            return $template->parse();
        }
        return parent::generate();
    }
    protected function compile()
    {
        System::loadLanguageFile('tl_literature');
        $page = $this->jumpTo ? PageModel::findByPk($this->jumpTo) : null;
        $this->Template->action = $page ? $page->getFrontendUrl() : strtok(Environment::get('requestUri'), '?');
        $fields = [];
        foreach (ModuleLiteratureList::SEARCH_FIELDS as $field) {
            $label = match ($field) { 'titlesort'=>'title', 'authorssort'=>'authors', default=>$field };
            $fields[$field] = $GLOBALS['TL_LANG']['tl_literature'][$label][0] ?? $label;
        }
        $this->Template->search_fields = $fields;
        $this->Template->search = is_string(Input::get('search')) ? Input::get('search') : '';
        $this->Template->for = is_string(Input::get('for')) ? Input::get('for') : '';
        $this->Template->fields_label = $GLOBALS['TL_LANG']['MSC']['all_fields'][0] ?? 'Field';
        $this->Template->keywords_label = $GLOBALS['TL_LANG']['MSC']['keywords'] ?? 'Keywords';
        $this->Template->search_label = $GLOBALS['TL_LANG']['MSC']['search'] ?? 'Search';
    }
}
