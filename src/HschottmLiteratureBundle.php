<?php

declare(strict_types=1);

/*
 * @copyright  Helmut Schottmüller 2009-2020 <http://github.com/hschottm>
 * @author     Helmut Schottmüller (hschottm)
 * @package    hschottm/contao-literature
 * @license    LGPL-3.0+, CC-BY-NC-3.0
 * @see	      https://github.com/hschottm/literature
 */

namespace Hschottm\LiteratureBundle;

use Hschottm\LiteratureBundle\DependencyInjection\LiteratureExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HschottmLiteratureBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new LiteratureExtension();
    }
}
