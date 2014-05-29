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

$GLOBALS['TL_LANG']['tl_literature']['literature_type']   = array('Literature type', 'Please select the literature type.');
$GLOBALS['TL_LANG']['tl_literature']['literature_type']['periodic']     = array('Periodical journals etc.','Author, A. A., Author, B. B. & Author, C. C. (1994). Article title. <u>Journal, xx</u>, xxx-xxx.');
$GLOBALS['TL_LANG']['tl_literature']['literature_type']['nonperiodic']     = array('Non-periodical literature','Author, A. A. (1994). <u>Literature title</u>. Location: Publisher.');
$GLOBALS['TL_LANG']['tl_literature']['literature_type']['nonperiodicpart']     = array('Parts of non-periodical literature (e.g. book chapter)','Author, A. A. & Author, B. B. (1994). Chapter title. In: A. Editor, B. Editor & C. Editor (Editor), <u>Literature title</u> (p. xxx-xxx). Location: Publisher.');
$GLOBALS['TL_LANG']['tl_literature']['title']   = array('Title', 'Please enter the title.');
$GLOBALS['TL_LANG']['tl_literature']['title_info']   = array('Additional information', 'Please enter additional information (e.g. edition).');
$GLOBALS['TL_LANG']['tl_literature']['title_source']   = array('Translation/Media', 'Please enter translations or media.');
$GLOBALS['TL_LANG']['tl_literature']['location']   = array('Location', 'Please enter the location.');
$GLOBALS['TL_LANG']['tl_literature']['publisher']   = array('Publisher', 'Please enter the publisher.');
$GLOBALS['TL_LANG']['tl_literature']['uri']   = array('Retrieved from', 'Please enter a web page from which the literature is available.');
$GLOBALS['TL_LANG']['tl_literature']['uri_date']    = array('Access date', 'Please enter the web page access date.');
$GLOBALS['TL_LANG']['tl_literature']['released']     = array('Year of publication', 'Please enter the year of publication.');
$GLOBALS['TL_LANG']['tl_literature']['title_act']     = array('Literature title', 'Please enter the literature title.');
$GLOBALS['TL_LANG']['tl_literature']['title_act_info']     = array('Additional information', 'Please enter additional information of the literature.');
$GLOBALS['TL_LANG']['tl_literature']['title_journal']     = array('Journal', 'Please enter the journal title.');
$GLOBALS['TL_LANG']['tl_literature']['volume']     = array('Volume', 'Please enter the volume of the journal.');
$GLOBALS['TL_LANG']['tl_literature']['issue']     = array('Issue', 'Please enter the issue of the journal.');
$GLOBALS['TL_LANG']['tl_literature']['pages']     = array('Page references', 'Please enter the page references.');
$GLOBALS['TL_LANG']['tl_literature']['authors']   = array('Author', 'Please enter one or more authors.');
$GLOBALS['TL_LANG']['tl_literature']['editors']   = array('Editor', 'Please enter one or more editors.');
$GLOBALS['TL_LANG']['tl_literature']['isbn']   = array('ISBN', 'Please enter the ISBN number.');
$GLOBALS['TL_LANG']['tl_literature']['issn']   = array('ISSN', 'Please enter the ISSN number.');
$GLOBALS['TL_LANG']['tl_literature']['tags']     = array('Tags', 'Please enter one or more comma separated tags to categorize the literature reference.');
$GLOBALS['TL_LANG']['tl_literature']['abstract']     = array('Abstract', 'Please enter an abstract of the publication.');

$GLOBALS['TL_LANG']['tl_literature']['new']    = array('Add literature', 'Add new literature');
$GLOBALS['TL_LANG']['tl_literature']['show']   = array('Details', 'Show details of literature ID %s');
$GLOBALS['TL_LANG']['tl_literature']['edit']   = array('Edit literature', 'Edit literature ID %s');
$GLOBALS['TL_LANG']['tl_literature']['editheader'] = array('Edit category', 'Edit the parent category');
$GLOBALS['TL_LANG']['tl_literature']['copy']   = array('Duplicate literature', 'Duplicate literature ID %s');
$GLOBALS['TL_LANG']['tl_literature']['cut']   = array('Move literature', 'Move literature ID %s');
$GLOBALS['TL_LANG']['tl_literature']['delete'] = array('Delete literature', 'Delete literature ID %s');

$GLOBALS['TL_LANG']['tl_literature']['availableat']    = "Retrieved from";
$GLOBALS['TL_LANG']['tl_literature']['in']    = "In";
$GLOBALS['TL_LANG']['tl_literature']['pageshort']    = "p.";
$GLOBALS['TL_LANG']['tl_literature']['firstname']    = "Firstname";
$GLOBALS['TL_LANG']['tl_literature']['lastname']    = "Lastname";
$GLOBALS['TL_LANG']['tl_literature']['editorShort']    = "Ed.";
$GLOBALS['TL_LANG']['tl_literature']['editorsShort']    = "Eds.";
$GLOBALS['TL_LANG']['tl_literature']['addSelection']  = 'Add selection';
$GLOBALS['TL_LANG']['tl_literature']['cancel']  = 'Cancel';
$GLOBALS['TL_LANG']['tl_literature']['newList']  = 'Add list';
$GLOBALS['TL_LANG']['tl_literature']['add']  = 'Add';
$GLOBALS['TL_LANG']['tl_literature']['addLiterature']  = 'Add literature';
$GLOBALS['TL_LANG']['tl_literature']['removeList']  = 'Remove list';
$GLOBALS['TL_LANG']['tl_literature']['confirmRemoveList']  = 'Do you really want to remove the list?';
$GLOBALS['TL_LANG']['tl_literature']['removeSelected']  = 'Remove selected';
$GLOBALS['TL_LANG']['tl_literature']['errNoISBN']  = 'Field %s should be an ISBN number (either ISBN-10 or ISBN-13)';
$GLOBALS['TL_LANG']['tl_literature']['errWrongISSN']  = 'Checksum error! Field %s should be a correct ISSN number';
$GLOBALS['TL_LANG']['tl_literature']['errWrongISSNFormat'] = "Field %s should be a valid ISSN number (format xxxx-xxxx where x is a number between 0 and 9)";
$GLOBALS['TL_LANG']['tl_literature']['errWrongISBN10']  = 'Checksum error! Field %s should be a correct ISBN-10 number';
$GLOBALS['TL_LANG']['tl_literature']['errWrongISBN13']  = 'Checksum error! Field %s should be a correct ISBN-13 number';
$GLOBALS['TL_LANG']['tl_literature']['addImage'] = array('Add an image', 'Add an image to the literature entry.');
$GLOBALS['TL_LANG']['tl_literature']['addDownloads'] = array('Add downloads', 'Add downloads to the literature entry.');
$GLOBALS['TL_LANG']['tl_literature']['downtitle']   = array('Headline', 'Please enter a headline for the downloads.');

$GLOBALS['TL_LANG']['tl_literature']['literaturefile'] = array('File source', 'Please choose one or more files containing the literature entries you want to import from your device. Allowed file types are BibTeX (*.bib) files.');
$GLOBALS['TL_LANG']['tl_literature']['import']   = array('Import', 'Import literature entries');

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_literature']['type_legend']     = 'Literature Type';
$GLOBALS['TL_LANG']['tl_literature']['authors_legend']     = 'Authors';
$GLOBALS['TL_LANG']['tl_literature']['editors_legend']     = 'Editors';
$GLOBALS['TL_LANG']['tl_literature']['book_legend']     = 'Book Data and Publisher';
$GLOBALS['TL_LANG']['tl_literature']['nonperiodic_legend']     = 'Release Date, Literature Data and Publisher';
$GLOBALS['TL_LANG']['tl_literature']['periodic_legend']     = 'Release Date, Journal Data and Publisher';
$GLOBALS['TL_LANG']['tl_literature']['nonperiodicpart_legend']     = 'Release Date and Literature Data';
$GLOBALS['TL_LANG']['tl_literature']['tags_legend']     = 'Tags';
$GLOBALS['TL_LANG']['tl_literature']['isbn_legend']     = 'Identification';
$GLOBALS['TL_LANG']['tl_literature']['uri_legend']     = 'Web Reference';
$GLOBALS['TL_LANG']['tl_literature']['image_legend']     = 'Image settings';
$GLOBALS['TL_LANG']['tl_literature']['downloads_legend'] = 'Download settings';
$GLOBALS['TL_LANG']['tl_literature']['abstract_legend'] = 'Abstract';

?>
