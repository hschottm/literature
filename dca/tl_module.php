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
 * Class tl_module_literaturelist
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Helmut Schottmüller 2008 
 * @author     Helmut Schottmüller <typolight@aurealis.de>
 * @package    Controller
 */
class tl_module_literaturelist extends Backend
{
	public function getLitrefTemplates(DataContainer $dc)
	{
		if (version_compare(VERSION.BUILD, '2.9.0', '>=')) 
		{
			return $this->getTemplateGroup('litref_', $dc->activeRecord->pid);
		} else {
			return $this->getTemplateGroup('litref_');
		}
	}  
}


/**
 * Add palettes to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['literaturelist'] = '{title_legend},name,type,headline;{literature_legend},lit_listtitle,lit_categories,perPage,lit_tags,lit_template;{litsort_legend},lit_showsort;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['literaturesearch'] = '{title_legend},name,type,headline;{redirect_legend},jumpTo;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['lit_showsort'] = 'lit_sort,lit_sortorder';

array_push($GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'], 'lit_showsort');


/**
 * Add fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['lit_categories'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_module']['lit_categories'],
	'inputType'     => 'checkbox',
	'foreignKey'    => 'tl_literature_category.title',
	'eval'          => array('multiple'=>true)
);


$GLOBALS['TL_DCA']['tl_module']['fields']['lit_listtitle'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_module']['lit_listtitle'],
	'inputType'     => 'text',
	'eval'          => array('mandatory'=>true, 'maxlength' => 100)
);

$GLOBALS['TL_DCA']['tl_module']['fields']['lit_showsort'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['lit_showsort'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true)
);

$GLOBALS['TL_DCA']['tl_module']['fields']['lit_template'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['lit_template'],
	'default'                 => 'litref_standard',
	'exclude'                 => true,
	'inputType'               => 'select',
  'options_callback'        => array('tl_module_literaturelist', 'getLitrefTemplates')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['lit_sort'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['lit_sort'],
	'default'                 => 'released',
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'                 => array('released', 'title', 'author'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_module']['lit_sort'],
	'eval'                    => array('tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['lit_sortorder'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['lit_sortorder'],
	'default'                 => 'asc',
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'                 => array('asc', 'desc'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_module']['lit_sortorder'],
	'eval'                    => array('tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['lit_tags'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['lit_tags'],
	'inputType'               => 'tag',
	'eval'                    => array('table' => 'tl_literature', 'isTag' => FALSE, 'tl_class'=>'clr long')
)

?>