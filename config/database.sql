-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the TYPOlight *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************

-- 
-- Table `tl_literature`
-- 

CREATE TABLE `tl_literature` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `literature_type` varchar(15) NOT NULL default '',
  `titlesort` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `title_periodic` varchar(255) NOT NULL default '',
  `title_nonperiodicpart` varchar(255) NOT NULL default '',
  `title_info` varchar(150) NOT NULL default '',
  `title_source` varchar(30) NOT NULL default '',
  `title_act` varchar(255) NOT NULL default '',
  `title_act_info` varchar(100) NOT NULL default '',
  `title_journal` varchar(255) NOT NULL default '',
  `pages` varchar(20) NOT NULL default '',
  `volume` varchar(5) NOT NULL default '',
  `issue` varchar(30) NOT NULL default '',
  `location` varchar(100) NOT NULL default '',
  `publisher` varchar(100) NOT NULL default '',
  `released` varchar(30) NOT NULL default '',
  `isbn` varchar(18) NOT NULL default '',
  `issn` varchar(9) NOT NULL default '',
  `uri` text NOT NULL,
  `uri_date` varchar(10) NOT NULL default '',
  `authors` blob NULL,
  `authorssort` varchar(255) NOT NULL default '',
  `editors` blob NULL,
  `tags` char(1) NOT NULL default '',
  `addImage` char(1) NOT NULL default '',
  `singleSRC` varchar(255) NOT NULL default '',
  `alt` varchar(255) NOT NULL default '',
  `size` varchar(64) NOT NULL default '',
  `imagemargin` varchar(128) NOT NULL default '',
  `imageUrl` varchar(255) NOT NULL default '',
  `fullsize` char(1) NOT NULL default '',
  `caption` varchar(255) NOT NULL default '',
  `floating` varchar(32) NOT NULL default '',
  `addDownloads` char(1) NOT NULL default '',
  `downtitle` varchar(255) NOT NULL default '',
  `multiSRC` blob NULL,
  `abstract` blob NULL,
  `sortBy` varchar(32) NOT NULL default '',
  `tstamp` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Table `tl_literature_category`
-- 

CREATE TABLE `tl_literature_category` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `tstamp` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- 
-- Table `tl_literature_author`
-- 

CREATE TABLE `tl_literature_author` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `sequence` int(10) unsigned NOT NULL default '0',
  `firstname` varchar(100) NOT NULL default '',
  `lastname` varchar(100) NOT NULL default '',
  `tstamp` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Table `tl_literature_editor
-- 

CREATE TABLE `tl_literature_editor` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `sequence` int(10) unsigned NOT NULL default '0',
  `firstname` varchar(100) NOT NULL default '',
  `lastname` varchar(100) NOT NULL default '',
  `tstamp` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Table `tl_module`
-- 

CREATE TABLE `tl_module` (
  `lit_categories` blob NULL,
  `lit_listtitle` varchar(100) NOT NULL default '',
  `lit_showsort` char(1) NOT NULL default '',
  `lit_template` varchar(32) NOT NULL default '',
  `lit_sort` varchar(30) NOT NULL default '',
  `lit_sortorder` varchar(4) NOT NULL default '',
  `lit_tags` blob NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
