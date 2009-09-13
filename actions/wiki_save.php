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

$title    = Jojo::getFormData('title','');
$url      = Jojo::getFormData('url', false);
$bodycode = Jojo::getFormData('body','');

$frajax = new frajax();
$frajax->title = 'Save wiki content- ' . _SITETITLE;
$frajax->sendHeader();

/* error checking */

$id = Util::getFormData('arg1', 0);
if ($url === false) $url = Jojo::cleanUrl($title);
if ($id == 'undefined') $id = 0;

/* Convert BBCode to HTML */
$body = Jojo::bb2html($bodycode);

$prefix = JOJO_Plugin_Jojo_wiki::getPrefix();

if ($id) {
    Jojo::updateQuery("UPDATE {wiki} SET wk_title=wk_title, wk_url=wk_url, wk_bodycode=?, wk_body=? WHERE wikiid=?", array($bodycode, $body, $id));
    $redirect = empty($url) ? _SITEURL.'/'.$prefix.'/' : _SITEURL.'/'.$prefix.'/'.$url.'/';
    $frajax->redirect($redirect);
} else {
    Jojo::insertQuery("INSERT INTO {wiki} SET wk_title=?, wk_url=?, wk_bodycode=?, wk_body=?", array($title, $url, $bodycode, $body));
    $frajax->redirect(_SITEURL.'/'.$prefix.'/'.$url.'/');
}

$frajax->sendFooter();