<?php

/*
 * @copyright  Helmut Schottmüller 2020 <http://github.com/hschottm>
 * @author     Helmut Schottmüller (hschottm)
 * @package    contao-literature
 * @license    LGPL-3.0+, CC-BY-NC-3.0
 * @see	      https://github.com/hschottm/literature
 */

namespace Hschottm\LiteratureBundle\EventListener;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Hschottm\LiteratureBundle\LiteraturePreview;

class InsertTagsListener
{
    /**
     * @var ContaoFrameworkInterface
     */
    private $framework;

    /**
     * @var array
     */
    private $supportedTags = [
      'insert_literature',
    ];

    /**
     * Constructor.
     *
     * @param ContaoFrameworkInterface $framework
     */
    public function __construct(ContaoFrameworkInterface $framework)
    {
        $this->framework = $framework;
    }

    /**
     * Replaces FAQ insert tags.
     *
     * @param string $tag
     *
     * @return string|false
     */
    public function onReplaceInsertTags($tag)
    {
        $elements = explode('::', $tag);
        $key = strtolower($elements[0]);
        if (!\in_array($key, $this->supportedTags, true)) {
            return false;
        }

        $this->framework->initialize();

        if (strcmp($key, "insert_literature") == 0)
        {
            $preview = new LiteraturePreview();
            $preview->loadLiterature($elements[1]);
            return $preview->getPreview();
        }
        return false;
    }
}