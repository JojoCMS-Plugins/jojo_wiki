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

/* Add a page to the menu */
$details = array();
$details['title']         = 'Wiki';
$details['pluginfile']    = 'JOJO_Plugin_Jojo_wiki';
$details['url']           = 'wiki';
$details['mainnav']       = 'yes';
$details['secondarynav']  = 'no';
$details['breadcrumbnav'] = 'yes';
$details['footernav']     = 'no';
$details['sitemapnav']    = 'yes';
$details['xmlsitemapnav'] = 'yes';
$details['index']         = 'yes';

$data = Jojo::selectQuery("SELECT * FROM {page} WHERE pg_link=?", $details['pluginfile']);
if (!count($data)) {
    echo "Wiki: Adding <b>Wiki</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title=?, pg_link=?, pg_url=?, pg_mainnav=?, pg_secondarynav=?, pg_breadcrumbnav=?, pg_footernav=?, pg_sitemapnav=?, pg_xmlsitemapnav=?, pg_index=?",
        array(
            $details['title'],
            $details['pluginfile'],
            $details['url'],
            $details['mainnav'],
            $details['secondarynav'],
            $details['breadcrumbnav'],
            $details['footernav'],
            $details['sitemapnav'],
            $details['xmlsitemapnav'],
            $details['index']
        )
    );
}

/* make sure there is a wiki entry for the homepage */
if (Jojo::tableExists('wiki')) {
    $data = Jojo::selectQuery("SELECT count(*) AS numrecords FROM {wiki} WHERE wk_url=''");
    if (!$data[0]['numrecords']) {
        Jojo::insertQuery("INSERT INTO {wiki} SET wk_title='Wiki homepage', wk_url=''");
    }
}