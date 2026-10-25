<?php
namespace Hschottm\LiteratureBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsInsertTag;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\InsertTag\InsertTagResult;
use Contao\CoreBundle\InsertTag\OutputType;
use Contao\CoreBundle\InsertTag\ResolvedInsertTag;
use Hschottm\LiteratureBundle\LiteraturePreview;

class InsertTagsListener
{
    public function __construct(private readonly ContaoFramework $framework) {}

    #[AsInsertTag('insert_literature')]
    public function __invoke(ResolvedInsertTag $tag): InsertTagResult
    {
        $this->framework->initialize();
        $id = $tag->getParameters()->get(0);
        $preview = new LiteraturePreview();
        if (is_string($id) && ctype_digit($id)) $preview->loadLiterature((int) $id);
        return new InsertTagResult($preview->getPreview(), OutputType::html);
    }
}
