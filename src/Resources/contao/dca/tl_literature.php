<?php

/**
 * @copyright  Helmut Schottmüller 2009-2020 <http://github.com/hschottm>
 * @author     Helmut Schottmüller (hschottm)
 * @package    hschottm/contao-literature
 * @license    LGPL-3.0+, CC-BY-NC-3.0
 * @see	      https://github.com/hschottm/literature
 */

use \Hschottm\LiteratureBundle\LiteraturePreview;

/**
 * Load tl_content language file
 */
\Contao\System::loadLanguageFile('tl_content');

/**
 * Table tl_literature
 */
$GLOBALS['TL_DCA']['tl_literature'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => \Contao\DC_Table::class,
		'ptable'                      => 'tl_literature_category',
		'ctable'                      => array('tl_literature_author', 'tl_literature_editor'),
		'ondelete_callback'           => array(array('tl_literature', 'deleteLiterature')),
		'enableVersioning'            => true,
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'pid' => 'index'
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 4,
			'filter'                  => true,
			'fields'                  => array('released','title'),
			'panelLayout'             => 'sort,filter;search,limit',
			'headerFields'            => array('title', 'tstamp', 'description'),
			'child_record_callback'   => array('tl_literature', 'compilePreview')
		),
		'global_operations' => array
		(
			'import' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_literature']['import'],
				'href'                => 'key=import',
				'class'               => 'header_import',
				'attributes'          => 'data-action="contao--scroll-offset#store"'
			),
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'data-action="contao--scroll-offset#store"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_literature']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.svg'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_literature']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.svg'
			),
			'cut' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_literature']['cut'],
				'href'                => 'act=paste&amp;mode=cut',
				'icon'                => 'cut.svg',
				'attributes'          => 'data-action="contao--scroll-offset#store"'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_literature']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.svg',
				'attributes'          => 'onclick="if (!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? '') . '\')) return false;"'
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_literature']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.svg'
			)
		)
	),

	'subpalettes' => array
	(
		'tagpalette'                    => 'tags',
		'addImage'                      => 'singleSRC,alt,size,imagemargin,imageUrl,fullsize,caption,floating',
		'addDownloads'                  => 'downtitle,multiSRC,sortBy'
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'authors' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_literature']['authors'],
			'inputType'               => 'multitextWizard',
			'save_callback'           => array(array('tl_literature', 'saveAuthors')),
			'load_callback'           => array(array('tl_literature', 'loadAuthors')),
			'eval'                    =>
				array(
					'mandatory' => false,
					'doNotSaveEmpty'=>true,
					'style' => 'width: 100%;',
					'columns' => array
					(
						array
						(
							'label' => &$GLOBALS['TL_LANG']['tl_literature']['firstname'],
							'width' => '180px'
						),
						array
						(
							'label' => &$GLOBALS['TL_LANG']['tl_literature']['lastname'],
						)
					),
					'buttonTitles' => array(
						'rnew' => &$GLOBALS['TL_LANG']['tl_literature']['buttontitle_author_new'],
						'rcopy' => &$GLOBALS['TL_LANG']['tl_literature']['buttontitle_author_copy'],
						'rdelete' => &$GLOBALS['TL_LANG']['tl_literature']['buttontitle_author_delete']
					)
				),
			'sql'                     => "blob NULL"
		),
		'authorssort' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_literature']['authors'],
			'search'                  => true,
			'sorting'                 => true,
			'filter'                  => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'editors' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_literature']['editors'],
			'inputType'               => 'multitextWizard',
			'save_callback'           => array(array('tl_literature', 'saveEditors')),
			'load_callback'           => array(array('tl_literature', 'loadEditors')),
			'eval'                    =>
				array(
					'mandatory' => false,
					'doNotSaveEmpty'=>true,
					'style'=>'width:100%;',
					'columns' => array
					(
						array
						(
							'label' => &$GLOBALS['TL_LANG']['tl_literature']['firstname'],
							'width' => '180px'
						),
						array
						(
							'label' => &$GLOBALS['TL_LANG']['tl_literature']['lastname'],
						)
					),
					'tl_class'=>'full',
					'buttonTitles' => array(
						'rnew' => &$GLOBALS['TL_LANG']['tl_literature']['buttontitle_editor_new'],
						'rcopy' => &$GLOBALS['TL_LANG']['tl_literature']['buttontitle_editor_copy'],
						'rdelete' => &$GLOBALS['TL_LANG']['tl_literature']['buttontitle_editor_delete']
					)
				),
			'sql'                     => "blob NULL"
		),
		'literature_type' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_literature']['literature_type'],
			'default'                 => 'periodic',
			'filter'                  => true,
			'inputType'               => 'select',
			'options'                 => array('periodic', 'nonperiodic', 'nonperiodicpart'),
			'reference'               => &$GLOBALS['TL_LANG']['tl_literature']['literature_type'],
			'eval'                    => array('helpwizard'=>true, 'submitOnChange'=>true, 'tl_class'=>'w50'),
			'sql'                     => "varchar(15) NOT NULL default ''"
		),
		'titlesort' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_literature']['title'],
			'search'                  => true,
			'sorting'                 => true,
			'filter'                  => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'save_callback'           => array(array('tl_literature', 'saveTitleSort')),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_literature']['title'],
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'save_callback'           => array(array('tl_literature', 'saveTitleSort')),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'title_periodic' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_literature']['title'],
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'save_callback'           => array(array('tl_literature', 'saveTitleSort')),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'title_nonperiodicpart' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_literature']['title'],
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'save_callback'           => array(array('tl_literature', 'saveTitleSort')),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'title_info' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_literature']['title_info'],
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>150, 'tl_class'=>'w50'),
			'sql'                     => "varchar(150) NOT NULL default ''"
		),
		'title_source' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_literature']['title_source'],
			'sorting'                 => true,
			'filter'                  => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>30, 'tl_class'=>'w50'),
			'sql'                     => "varchar(30) NOT NULL default ''"
		),
		'isbn' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_literature']['isbn'],
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>18, 'rgxp'=>'isbn', 'tl_class'=>'w50'),
			'sql'                     => "varchar(18) NOT NULL default ''"
		),
		'issn' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_literature']['issn'],
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>18, 'rgxp'=>'issn', 'tl_class'=>'w50'),
			'sql'                     => "varchar(9) NOT NULL default ''"
		),
		'location' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_literature']['location'],
			'search'                  => true,
			'sorting'                 => true,
			'filter'                  => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>false, 'maxlength'=>100, 'tl_class'=>'w50'),
			'sql'                     => "varchar(100) NOT NULL default ''"
		),
		'publisher' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_literature']['publisher'],
			'search'                  => true,
			'sorting'                 => true,
			'filter'                  => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>false, 'maxlength'=>100, 'tl_class'=>'w50'),
			'sql'                     => "varchar(100) NOT NULL default ''"
		),
		'uri' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_literature']['uri'],
			'default'                 => '',
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('tl_class'=>'w50'),
			'sql'                     => "text NOT NULL"
		),
		'uri_date' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_literature']['uri_date'],
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>32, 'rgxp' => 'date', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
			'sql'                     => "varchar(10) NOT NULL default ''"
		),
		'released' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_literature']['released'],
			'search'                  => true,
			'sorting'                 => true,
			'filter'                  => true,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>false, 'maxlength'=>30, 'tl_class'=>'w50'),
			'sql'                     => "varchar(30) NOT NULL default ''"
		),
		'title_act' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_literature']['title_act'],
			'search'                  => true,
			'sorting'                 => true,
			'filter'                  => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'title_act_info' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_literature']['title_act_info'],
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>150, 'tl_class'=>'w50'),
			'sql'                     => "varchar(100) NOT NULL default ''"
		),
		'title_journal' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_literature']['title_journal'],
			'search'                  => true,
			'sorting'                 => true,
			'filter'                  => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'volume' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_literature']['volume'],
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>5, 'tl_class'=>'w50'),
			'sql'                     => "varchar(5) NOT NULL default ''"
		),
		'issue' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_literature']['issue'],
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>30, 'tl_class'=>'w50'),
			'sql'                     => "varchar(30) NOT NULL default ''"
		),
		'pages' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_literature']['pages'],
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>false, 'maxlength'=>20, 'tl_class'=>'w50'),
			'sql'                     => "varchar(20) NOT NULL default ''"
		),
		'tags' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_literature']['tags'],
			'inputType'               => 'tag',
			'eval'                    => array(),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'addImage' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_literature']['addImage'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('submitOnChange'=>true),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'singleSRC' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content']['singleSRC'],
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => array('fieldType'=>'radio', 'files'=>true, 'filesOnly'=>true, 'mandatory'=>true),
			'sql'                     => "binary(16) NULL"
		),
		'alt' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content']['alt'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'long'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'size' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content']['size'],
			'exclude'                 => true,
			'inputType'               => 'imageSize',
			'options'                 => array('crop', 'proportional', 'box'),
			'reference'               => &$GLOBALS['TL_LANG']['MSC'],
			'eval'                    => array('rgxp'=>'digit', 'nospace'=>true, 'tl_class'=>'w50'),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'imagemargin' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content']['imagemargin'],
			'exclude'                 => true,
			'inputType'               => 'trbl',
			'options'                 => array('px', '%', 'em', 'pt', 'pc', 'in', 'cm', 'mm'),
			'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
			'sql'                     => "varchar(128) NOT NULL default ''"
		),
		'imageUrl' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content']['imageUrl'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('dcaPicker'=>true, 'rgxp'=>'url', 'decodeEntities'=>true, 'maxlength'=>255, 'tl_class'=>'w50 wizard'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'fullsize' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content']['fullsize'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'caption' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content']['caption'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'floating' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content']['floating'],
			'exclude'                 => true,
			'inputType'               => 'radioTable',
			'options'                 => array('above', 'left', 'right', 'below'),
			'eval'                    => array('cols'=>4, 'tl_class'=>'w50'),
			'reference'               => &$GLOBALS['TL_LANG']['MSC'],
			'sql'                     => "varchar(32) NOT NULL default ''"
		),
		'addDownloads' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_literature']['addDownloads'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('submitOnChange'=>true),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'downtitle' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_literature']['downtitle'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'long'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'multiSRC' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content']['multiSRC'],
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => array('multiple'=>true, 'fieldType'=>'checkbox', 'files'=>true, 'mandatory'=>true),
			'sql'                     => "blob NULL",
		),
		'sortBy' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content']['sortBy'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options'                 => array('name_asc', 'name_desc', 'date_asc', 'date_desc', 'meta', 'random'),
			'reference'               => &$GLOBALS['TL_LANG']['tl_content'],
			'eval'                    => array('tl_class'=>'w50'),
			'sql'                     => "varchar(32) NOT NULL default ''"
		),
		'abstract' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_literature']['abstract'],
			'search'                  => true,
			'inputType'               => 'textarea',
			'eval'                    => array('allowHtml'=>false, 'cols' => 40, 'tl_class'=>'long'),
			'sql'                     => "blob NULL"
		),
		'literaturefile' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_literature']['literaturefile'],
			'eval'                    => array('fieldType'=>'radio', 'files'=>true, 'filesOnly'=>true, 'extensions'=>'bib')
		)
	)
);

if (!class_exists(\Hschottm\TagsBundle\TagList::class))
{
	// Palettes
	$GLOBALS['TL_DCA']['tl_literature']['palettes'] = array
	(
		'__selector__'            => array('literature_type', 'addImage', 'addDownloads'),
		'default'                 => '{type_legend},literature_type',
		'nonperiodic'             => '{type_legend},literature_type;{authors_legend},authors;{nonperiodic_legend},released,title,title_info,title_source,location,publisher;{isbn_legend},isbn,issn;{uri_legend},uri,uri_date;{image_legend},addImage;{downloads_legend},addDownloads;{abstract_legend},abstract',
		'periodic'                => '{type_legend},literature_type;{authors_legend},authors;{periodic_legend},released,title_periodic,title_info,title_journal,volume,issue,pages;{tags_legend},tags;{isbn_legend},isbn,issn;{uri_legend},uri,uri_date;{image_legend},addImage;{downloads_legend},addDownloads;{abstract_legend},abstract',
		'nonperiodicpart'         => '{type_legend},literature_type;{authors_legend},authors;{nonperiodicpart_legend},released,title_nonperiodicpart,title_info;{editors_legend},editors;{book_legend},title_act,title_act_info,title_source,pages,location,publisher;{tags_legend},tags;{isbn_legend},isbn,issn;{uri_legend},uri,uri_date;{image_legend},addImage;{downloads_legend},addDownloads;{abstract_legend},abstract',
	);
} else {
	// Palettes
	$GLOBALS['TL_DCA']['tl_literature']['palettes'] = array
	(
		'__selector__'            => array('literature_type', 'addImage', 'addDownloads'),
		'default'                 => '{type_legend},literature_type',
		'nonperiodic'             => '{type_legend},literature_type;{authors_legend},authors;{nonperiodic_legend},released,title,title_info,title_source,location,publisher;{tags_legend},tags;{isbn_legend},isbn,issn;{uri_legend},uri,uri_date;{image_legend},addImage;{downloads_legend},addDownloads;{abstract_legend},abstract',
		'periodic'                => '{type_legend},literature_type;{authors_legend},authors;{periodic_legend},released,title_periodic,title_info,title_journal,volume,issue,pages;{tags_legend},tags;{isbn_legend},isbn,issn;{uri_legend},uri,uri_date;{image_legend},addImage;{downloads_legend},addDownloads;{abstract_legend},abstract',
		'nonperiodicpart'         => '{type_legend},literature_type;{authors_legend},authors;{nonperiodicpart_legend},released,title_nonperiodicpart,title_info;{editors_legend},editors;{book_legend},title_act,title_act_info,title_source,pages,location,publisher;{tags_legend},tags;{isbn_legend},isbn,issn;{uri_legend},uri,uri_date;{image_legend},addImage;{downloads_legend},addDownloads;{abstract_legend},abstract',
	);
}

/**
 * Class tl_literature
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Helmut Schottmüller 2008
 * @author     Helmut Schottmüller <typolight@aurealis.de>
 * @package    Controller
 */
class tl_literature extends \Contao\Backend
{
	/**
	 * Compile format definitions and return them as string
	 * @param array
	 * @param boolean
	 * @return string
	 */
	public function compilePreview($row, $blnWriteToFile=false)
	{
		$preview = new \Hschottm\LiteratureBundle\LiteraturePreview($row);
		return $preview->getPreview();
	}

	/**
	 * Delete the tags of a literature entry
	 */
	public function deleteLiterature($dc)
	{
        if (!\Contao\Database::getInstance()->tableExists('tl_tag')) return;
		\Contao\Database::getInstance()->prepare("DELETE FROM tl_tag WHERE from_table = ? AND tid = ?")
			->execute($dc->table, $dc->id);
	}

	/**
	 * Callback on save the title sort field for literature entries
	 * @param string
	 * @param object
	 * @return string
	 */
	public function saveTitleSort($value, $dc)
	{
		if (strlen($value))
		{
			\Contao\Database::getInstance()->prepare("UPDATE tl_literature SET titlesort = ? WHERE id = ?")
				->execute($value, $dc->id);
		}
		return $value;
	}

	/**
	 * Callback on save the literature authors
	 * @param string
	 * @param object
	 * @return string
	 */
	public function saveAuthors($value, $dc)
	{
		$authors = \Contao\StringUtil::deserialize($value, TRUE);
		$position = 1;
		$sortstring = '';
		\Contao\Database::getInstance()->prepare("DELETE FROM tl_literature_author WHERE pid = ?")
			->execute($dc->id);
		foreach ($authors as $author)
		{
			if (is_array($author))
			{
				\Contao\Database::getInstance()->prepare("INSERT INTO tl_literature_author (pid, sequence, firstname, lastname) VALUES (?, ?, ?, ?)")
					->execute($dc->id, $position, $author[0] ?? '', $author[1] ?? '');
				$sortstring .= ($author[1] ?? '') . ',' . ($author[0] ?? '');
				$position++;
			}
		}
		\Contao\Database::getInstance()->prepare("UPDATE tl_literature SET authorssort = ? WHERE id = ?")
			->execute($sortstring, $dc->id);
		return NULL;
	}

	/**
	 * Load the literature authors into an array an return a serialized string
	 * @param string
	 * @param object
	 * @return string
	 */
	public function loadAuthors($value, $dc)
	{
		$objAuthor = \Contao\Database::getInstance()->prepare("SELECT * FROM tl_literature_author WHERE pid = ? ORDER BY sequence")
			->execute($dc->id);
		$authors = array();
		if ($objAuthor->numRows)
		{
			while ($objAuthor->next())
			{
				array_push($authors, array($objAuthor->firstname, $objAuthor->lastname));
			}
		}
		return serialize($authors);
	}

	public function loadAuthorsSort($value, $dc)
	{
		$objAuthor = \Contao\Database::getInstance()->prepare("SELECT * FROM tl_literature_author WHERE pid = ? ORDER BY sequence")
			->execute($dc->id);
		$authorssort = '';
		if ($objAuthor->numRows)
		{
			while ($objAuthor->next())
			{
				$authorssort .= $objAuthor->lastname . $objAuthor->firstname;
			}
		}
		return $authorssort;
	}

	/**
	 * Callback on save the literature editors
	 * @param string
	 * @param object
	 * @return string
	 */
	public function saveEditors($value, $dc)
	{
		$editors = \Contao\StringUtil::deserialize($value, TRUE);
		$position = 1;
		\Contao\Database::getInstance()->prepare("DELETE FROM tl_literature_editor WHERE pid = ?")
			->execute($dc->id);
		foreach ($editors as $editor)
		{
			if (is_array($editor))
			{
				\Contao\Database::getInstance()->prepare("INSERT INTO tl_literature_editor (pid, sequence, firstname, lastname) VALUES (?, ?, ?, ?)")
					->execute($dc->id, $position, $editor[0] ?? '', $editor[1] ?? '');
				$position++;
			}
		}
		return NULL;
	}

	/**
	 * Load the literature editors into an array an return a serialized string
	 * @param string
	 * @param object
	 * @return string
	 */
	public function loadEditors($value, $dc)
	{
		$objEditor = \Contao\Database::getInstance()->prepare("SELECT * FROM tl_literature_editor WHERE pid = ? ORDER BY sequence")
			->execute($dc->id);
		$editors = array();
		if ($objEditor->numRows)
		{
			while ($objEditor->next())
			{
				array_push($editors, array($objEditor->firstname, $objEditor->lastname));
			}
		}
		return serialize($editors);
	}

}

if (!class_exists(\Hschottm\TagsBundle\TagList::class)) {
    foreach ($GLOBALS['TL_DCA']['tl_literature']['palettes'] as &$palette) {
        if (is_string($palette)) $palette = str_replace(';{tags_legend},tags', '', $palette);
    }
    unset($palette);
}
