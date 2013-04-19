<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @package Literature
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'Contao\Literature'             => 'system/modules/literature/classes/Literature.php',
	'Contao\LiteraturePersonalPage' => 'system/modules/literature/classes/LiteraturePersonalPage.php',
	'Contao\LiteraturePreview'      => 'system/modules/literature/classes/LiteraturePreview.php',
	'Contao\LiteratureTools'        => 'system/modules/literature/classes/LiteratureTools.php',

	// Modules
	'Contao\ModuleLiteratureList'   => 'system/modules/literature/modules/ModuleLiteratureList.php',
	'Contao\ModuleLiteratureSearch' => 'system/modules/literature/modules/ModuleLiteratureSearch.php',

	// Vendor
	'PARSECREATORS'                 => 'system/modules/literature/vendor/bibtexParse/PARSECREATORS.php',
	'PARSEENTRIES'                  => 'system/modules/literature/vendor/bibtexParse/PARSEENTRIES.php',
	'PARSEMONTH'                    => 'system/modules/literature/vendor/bibtexParse/PARSEMONTH.php',
	'PARSEPAGE'                     => 'system/modules/literature/vendor/bibtexParse/PARSEPAGE.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'be_import_literature'         => 'system/modules/literature/templates',
	'litlist_available_literature' => 'system/modules/literature/templates',
	'litlist_personal'             => 'system/modules/literature/templates',
	'litlist_personal_editor'      => 'system/modules/literature/templates',
	'litref_standard'              => 'system/modules/literature/templates',
	'mod_literaturelist'           => 'system/modules/literature/templates',
	'mod_literaturesearch'         => 'system/modules/literature/templates',
));
