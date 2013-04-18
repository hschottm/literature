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
 * @author     Helmut Schottmüller <helmut.schottmueller@aurealis.de>
 * @package    literature
 * @license    LGPL
 * @filesource
 */

$GLOBALS['TL_LANG']['tl_module']['lit_template']      = array('Reference style', 'Please choose a layout for the literature output. You can add custom <em>litref_</em> layouts to folder <em>templates</em>.');
$GLOBALS['TL_LANG']['tl_module']['lit_categories'] = array('Categories', 'Please select one ore more categories to build the literature list.');
$GLOBALS['TL_LANG']['tl_module']['lit_listtitle'] = array('List title', 'Please enter a list title for the literature list.');
$GLOBALS['TL_LANG']['tl_module']['lit_showsort'] = array('Sort options', 'Choose this option to sort the literature list.');
$GLOBALS['TL_LANG']['tl_module']['lit_sort'] = array('Sort by', 'Please select the sorting colum for the literature list.');
$GLOBALS['TL_LANG']['tl_module']['lit_sort']['title'] = array('Sort by title', 'Choose this option to sort the literature list by title.');
$GLOBALS['TL_LANG']['tl_module']['lit_sort']['released'] = array('Sort by date', 'Choose this option to sort the literature list by release date.');
$GLOBALS['TL_LANG']['tl_module']['lit_sort']['author'] = array('Sort by author', 'Choose this option to sort the literature list by author.');
$GLOBALS['TL_LANG']['tl_module']['lit_sortorder'] = array('Sort order', 'Please select the sort order of the literature list.');
$GLOBALS['TL_LANG']['tl_module']['lit_sortorder']['asc'] = array('Ascending', 'Choose this option to use ascending sort order of the selected sorting colum.');
$GLOBALS['TL_LANG']['tl_module']['lit_sortorder']['desc'] = array('Descending', 'Choose this option to use descending sort order of the selected sorting colum.');
$GLOBALS['TL_LANG']['tl_module']['lit_tags'] = array('Tags', 'Please enter one or more comma separated tags to show only literature references which are tagged with one of these tags.');
$GLOBALS['TL_LANG']['tl_module']['pagetype']['literature']      = 'Literature list';
$GLOBALS['TL_LANG']['tl_module']['litsort_legend'] = 'Sort options';
$GLOBALS['TL_LANG']['tl_module']['literature_legend'] = 'Literature list';

?>