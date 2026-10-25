<?php

use Hschottm\LiteratureBundle\Literature;
use Hschottm\LiteratureBundle\LiteraturePreview;
use Hschottm\LiteratureBundle\LiteratureTools;
use Hschottm\LiteratureBundle\ModuleLiteratureList;
use Hschottm\LiteratureBundle\ModuleLiteratureSearch;
use Hschottm\LiteratureBundle\EventListener\InsertTagsListener;

\Contao\ArrayUtil::arrayInsert($GLOBALS['BE_MOD']['content'], 3, array
(
	'literature' => array(
		"tables" => array(
				"tl_literature_category", "tl_literature"
			),
		'import' => array(LiteratureTools::class, 'importLiterature'),
		'icon' => 'bundles/hschottmliterature/images/literature.png',
		'stylesheet' => 'bundles/hschottmliterature/css/literature.css'
	)
));

/**
 * Front end modules
 */
\Contao\ArrayUtil::arrayInsert($GLOBALS['FE_MOD']['miscellaneous'], 3, array
(
	'literaturelist'    => ModuleLiteratureList::class,
	'literaturesearch'  => ModuleLiteratureSearch::class
));

/**
 * Register page content handlers
 */
$GLOBALS['TL_HOOKS']['addCustomRegexp'][] = array(Literature::class, 'checkISBN');

$GLOBALS['tags_extension']['sourcetable'][] = 'tl_literature';
