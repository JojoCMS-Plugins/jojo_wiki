<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2007 Harvey Kane <code@ragepank.com>
 * Copyright 2007 Michael Holt <code@gardyneholt.co.nz>
 * Copyright 2007 Melanie Schulz <mel@gardyneholt.co.nz>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

$table = 'wiki';
$query = "
CREATE TABLE {wiki} (
      `wikiid` int(11) NOT NULL auto_increment,
      `wk_title` varchar(255) NOT NULL default '',
      `wk_url` varchar(255) NOT NULL default '',
      `wk_desc` varchar(255) NOT NULL default '',
      `wk_bodycode` text NULL,
      `wk_body` text NULL,
      `wk_date` date default NULL,
      `wk_seotitle` varchar(255) NOT NULL default '',
      `wk_metadesc` varchar(255) NOT NULL default '',
      `wk_tags` text NULL,
      PRIMARY KEY  (`wikiid`),
      FULLTEXT KEY `title` (`wk_title`),
      FULLTEXT KEY `body` (`wk_title`,`wk_desc`,`wk_body`)
    ) TYPE=MyISAM ;";

/* Check table structure */
$result = JOJO::checkTable($table, $query);

/* Output result */
if (isset($result['created'])) {
    echo sprintf("jojo_wiki: Table <b>%s</b> Does not exist - created empty table.<br />", $table);
}

if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("jojo_wiki: Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}

if (isset($result['different'])) JOJO::printTableDifference($table,$result['different']);