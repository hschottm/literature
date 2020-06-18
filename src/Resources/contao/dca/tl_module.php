<?php

/**
 * @copyright  Helmut Schottmüller 2009-2020 <http://github.com/hschottm>
 * @author     Helmut Schottmüller (hschottm)
 * @package    hschottm/contao-literature
 * @license    LGPL-3.0+, CC-BY-NC-3.0
 * @see	      https://github.com/hschottm/literature
 */

/**
 * Class tl_module_literaturelist
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Helmut Schottmüller 2009-2013
 * @author     Helmut Schottmüller <https://github.com/hschottm/literature>
 * @package    Controller
 */
class tl_module_literaturelist extends Backend
{
	public function getLitrefTemplates(DataContainer $dc)
	{
		return $this->getTemplateGroup('litref_', $dc->activeRecord->pid);
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
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['lit_categories'],
	'inputType'               => 'checkbox',
	'foreignKey'              => 'tl_literature_category.title',
	'eval'                    => array('multiple'=>true),
	'sql'                     => "blob NULL"
);


$GLOBALS['TL_DCA']['tl_module']['fields']['lit_listtitle'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['lit_listtitle'],
	'inputType'               => 'text',
	'eval'                    => array('mandatory'=>true, 'maxlength' => 100),
	'sql'                     => "varchar(100) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['lit_showsort'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['lit_showsort'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true),
	'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['lit_template'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['lit_template'],
	'default'                 => 'litref_standard',
	'exclude'                 => true,
	'inputType'               => 'select',
  'options_callback'        => array('tl_module_literaturelist', 'getLitrefTemplates'),
	'sql'                     => "varchar(32) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['lit_sort'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['lit_sort'],
	'default'                 => 'released',
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'                 => array('released', 'title', 'author'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_module']['lit_sort'],
	'eval'                    => array('tl_class'=>'w50'),
	'sql'                     => "varchar(30) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['lit_sortorder'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['lit_sortorder'],
	'default'                 => 'asc',
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'                 => array('asc', 'desc'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_module']['lit_sortorder'],
	'eval'                    => array('tl_class'=>'w50'),
	'sql'                     => "varchar(4) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['lit_tags'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['lit_tags'],
	'inputType'               => 'tag',
	'eval'                    => array('table' => 'tl_literature', 'isTag' => FALSE, 'tl_class'=>'clr long'),
	'sql'                     => "blob NULL"
);

?>