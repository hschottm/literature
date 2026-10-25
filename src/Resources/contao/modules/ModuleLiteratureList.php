<?php
namespace Hschottm\LiteratureBundle;

use Contao\{Module, BackendTemplate, Database, Input, StringUtil, System};
use Contao\CoreBundle\Pagination\{PaginationConfig, LegacyTemplatePaginationProxy};
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\CoreBundle\Exception\PageOutOfRangeException;

class ModuleLiteratureList extends Module
{
    protected $strTemplate = 'mod_literaturelist';
    protected $arrCategories = [];
    public const SEARCH_FIELDS = ['titlesort','authorssort','title_journal','location','publisher','released'];

    public function generate()
    {
        $request = System::getContainer()->get('request_stack')->getCurrentRequest();
        if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request)) {
            $template = new BackendTemplate('be_wildcard');
            $template->wildcard = '### LITERATURE LIST ###';
            return $template->parse();
        }
        $this->arrCategories = array_filter(array_map('intval', StringUtil::deserialize($this->lit_categories, true)));
        return $this->arrCategories ? parent::generate() : '';
    }

    protected function compile()
    {
        System::loadLanguageFile('tl_literature');
        $this->listLiterature();
    }

    protected function listLiterature()
    {
        $where = 'pid IN ('.implode(',', $this->arrCategories).')';
        $params = [];
        if (class_exists(\Hschottm\TagsBundle\Service\TagFilter::class)) {
            $ids = System::getContainer()->get(\Hschottm\TagsBundle\Service\TagFilter::class)->findIds('tl_literature', (string) $this->lit_tags);
            if ($ids !== null) $where .= $ids ? ' AND id IN ('.implode(',', array_map('intval', $ids)).')' : ' AND 1=0';
        }
        $search = Input::get('search');
        $term = Input::get('for');
        if (in_array($search, self::SEARCH_FIELDS, true) && is_string($term) && $term !== '') {
            $where .= ' AND '.$search.' LIKE ?';
            $params[] = '%'.$term.'%';
        }
        $direction = strtolower((string) $this->lit_sortorder) === 'desc' ? 'DESC' : 'ASC';
        $field = match ($this->lit_sort) { 'title' => 'titlesort', 'author' => 'authorssort', default => 'released' };
        $order = $field.' '.$direction.', titlesort, id';
        $db = Database::getInstance();
        $total = (int) $db->prepare('SELECT COUNT(*) AS total FROM tl_literature WHERE '.$where)->execute(...$params)->total;
        $perPage = max(0, (int) $this->perPage);
        $requested = Input::get('per_page');
        if (is_scalar($requested) && ctype_digit((string) $requested) && (int) $requested > 0) $perPage = min(500, (int) $requested);
        $offset = 0;
        $this->Template->pagination = '';
        if ($perPage > 0 && $total > 0) {
            try {
                $pagination = System::getContainer()->get('contao.pagination.factory')->create(new PaginationConfig('page_l'.$this->id, $total, $perPage));
            } catch (PageOutOfRangeException $e) {
                throw new PageNotFoundException('Literature page out of range', previous: $e);
            }
            $offset = $pagination->getOffset();
            $this->Template->pagination = new LegacyTemplatePaginationProxy(System::getContainer()->get('twig'), $pagination);
        }
        $statement = $db->prepare('SELECT * FROM tl_literature WHERE '.$where.' ORDER BY '.$order);
        if ($perPage) $statement->limit($perPage, $offset);
        $entries = $statement->execute(...$params);
        $rows = [];
        while ($entries->next()) {
            $data = $entries->row();
            $index = count($rows);
            $class = 'row_'.$index.($index === 0 ? ' row_first' : '').($index === $entries->numRows-1 ? ' row_last' : '').($index % 2 ? ' odd' : ' even');
            $preview = new LiteraturePreview($data, $this->lit_template ?: 'litref_standard');
            $rows[$class] = ['raw'=>$data, 'content'=>$preview->getPreview(), 'class'=>'col_first', 'id'=>$data['id']];
        }
        $this->Template->tbody = $rows;
        $this->Template->listtitle = $this->lit_listtitle;
        $this->Template->per_page = $perPage;
    }
}
