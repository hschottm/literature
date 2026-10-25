<?php
namespace Hschottm\LiteratureBundle;
use Contao\{Frontend, FrontendTemplate, Database, System, StringUtil, Config, ContentModel, ContentDownloads};
use Contao\CoreBundle\File\Metadata;

class LiteraturePreview extends Frontend
{
    protected $arrData = [];
    protected $arrAuthors = [];
    protected $arrEditors = [];
    protected $template;
    public function __construct($data = null, $template = '')
    {
        parent::__construct();
        $this->template = $template ?: 'litref_standard';
        if (is_array($data)) $this->setData($data);
    }
    private function setData(array $data): void
    {
        $this->arrData = $data;
        foreach (['author'=>'arrAuthors','editor'=>'arrEditors'] as $type=>$property) {
            $people = Database::getInstance()->prepare('SELECT firstname, lastname FROM tl_literature_'.$type.' WHERE pid=? ORDER BY sequence')->execute($data['id']);
            $this->$property = [];
            while ($people->next()) $this->{$property}[] = [$people->firstname, $people->lastname];
        }
    }
    public function loadLiterature($id)
    {
        $this->arrData = [];
        $this->arrAuthors = $this->arrEditors = [];
        $record = Database::getInstance()->prepare('SELECT * FROM tl_literature WHERE id=?')->execute((int) $id);
        if ($record->numRows) $this->setData($record->row());
    }
    public function getPreview()
    {
        if (!$this->arrData) return '';
        System::loadLanguageFile('tl_literature');
        $template = new FrontendTemplate($this->template);
        $template->setData($this->arrData);
        $template->authors = $this->arrAuthors;
        $template->editors = $this->arrEditors;
        foreach (['strAvailableAt'=>'availableat','strIn'=>'in','strEditorShort'=>'editorShort','strEditorsShort'=>'editorsShort','strPageShort'=>'pageshort'] as $key=>$label) $template->$key = $GLOBALS['TL_LANG']['tl_literature'][$label] ?? '';
        $template->strDateFormat = Config::get('dateFormat');
        $uri = (string) ($this->arrData['uri'] ?? '');
        // Only link web URLs. Other content is still available as plain template data.
        $template->uri = preg_match('~^https?://~i', $uri) ? $uri : '';
        $template->urishort = preg_replace('~^(https?://[^/]+/).*~i', '$1', $uri);
        $template->downloads = '';
        if (!empty($this->arrData['addDownloads'])) {
            // Delegate file expansion, permitted downloads and file delivery to Contao.
            // Legacy ContentDownloads remains supported in 5.7; no core code is copied.
            $model = new ContentModel();
            $model->setRow(['id'=>(int)$this->arrData['id'], 'type'=>'downloads', 'multiSRC'=>$this->arrData['multiSRC'] ?? null, 'sortBy'=>$this->arrData['sortBy'] ?? 'name_asc', 'headline'=>'', 'cssID'=>null]);
            $template->downloads = (new ContentDownloads($model))->generate();
        }
        $template->figure = null;
        $floating = $this->arrData['floating'] ?? '';
        $template->figureClass = 'image_container'.(in_array($floating, ['above', 'below', 'left', 'right'], true) ? ' float_'.$floating : '');
        $styles = in_array($floating, ['left', 'right'], true) ? ['float:'.$floating] : [];
        $margin = StringUtil::deserialize($this->arrData['imagemargin'] ?? null, true);
        $unit = $margin['unit'] ?? 'px';
        if (in_array($unit, ['px', '%', 'em', 'pt', 'pc', 'in', 'cm', 'mm'], true)) {
            foreach (['top', 'right', 'bottom', 'left'] as $side) {
                if (isset($margin[$side]) && is_numeric($margin[$side])) $styles[] = 'margin-'.$side.':'.(float) $margin[$side].$unit;
            }
        }
        $template->figureStyle = implode(';', $styles);
        if (!empty($this->arrData['addImage']) && !empty($this->arrData['singleSRC'])) {
            $builder = System::getContainer()->get('contao.image.studio')->createFigureBuilder()->fromUuid($this->arrData['singleSRC'])->setSize(StringUtil::deserialize($this->arrData['size'] ?? null, true));
            $builder->setOverwriteMetadata(new Metadata(['alt'=>$this->arrData['alt'] ?? '', 'caption'=>$this->arrData['caption'] ?? '']));
            if (!empty($this->arrData['imageUrl'])) $builder->setLinkHref($this->arrData['imageUrl']);
            $builder->enableLightbox(!empty($this->arrData['fullsize']));
            $template->figure = $builder->buildIfResourceExists();
        }
        return $template->parse();
    }
}
