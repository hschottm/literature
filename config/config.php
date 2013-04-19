<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 * Copyright (C) 2005 Leo Feyer
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at http://www.gnu.org/licenses/.
 *
 * PHP version 5
 * @copyright  Helmut Schottmüller 2008 
 * @author     Helmut Schottmüller <typolight@aurealis.de>
 * @package    literature
 * @license    LGPL
 * @filesource
 */

/**
 * Back end modules
 */

array_insert($GLOBALS['BE_MOD']['content'], 3, array
(
	'literature' => array(
		"tables" => array(
				"tl_literature_category", "tl_literature"
			),
		'import' => array('LiteratureTools', 'importLiterature'),
		'icon' => 'system/modules/literature/assets/literature.png',
		'stylesheet' => 'system/modules/literature/assets/literature.css'
	)
));

/**
 * Front end modules
 */
array_insert($GLOBALS['FE_MOD']['miscellaneous'], 3, array
(
	'literaturelist'    => 'ModuleLiteratureList',
	'literaturesearch'  => 'ModuleLiteratureSearch'
));

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('LiteraturePreview', 'replaceLiteratureInsertTags');

/**
 * Register page content handlers
 */
$GLOBALS['TL_PERSONALDATA_EDITOR']['literature'] = array('LiteraturePersonalPage', 'editPersonalLiteratureList');
$GLOBALS['TL_PERSONALDATA']['literature'] = array('LiteraturePersonalPage', 'showPersonalLiteratureList');
$GLOBALS['TL_HOOKS']['addCustomRegexp'][] = array('Literature', 'checkISBN');

$GLOBALS['tags_extension']['sourcetable'][] = 'tl_literature';

?>