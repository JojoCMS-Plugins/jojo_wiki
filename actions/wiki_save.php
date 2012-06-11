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
$url_old  = Jojo::getFormData('oldurl', false);
$bodycode = Jojo::getFormData('body','');

$frajax = new frajax();
$frajax->title = 'Save wiki content- ' . _SITETITLE;
$frajax->sendHeader();

/* error checking */

$id = Util::getFormData('arg1', 0);
if ($url === false) {
    $url = ($url_old) ? $url_old : Jojo::cleanUrl($title);
}
if ($id == 'undefined') $id = 0;

/* Convert BBCode to HTML */
$body = Jojo::bb2html($bodycode);

$prefix = JOJO_Plugin_Jojo_wiki::getPrefix();
$wiki = new JOJO_Plugin_Jojo_wiki();
if ($id) {
    Jojo::updateQuery("UPDATE {wiki} SET wk_title=wk_title, wk_url=wk_url, wk_bodycode=?, wk_body=?, wk_title=? WHERE wikiid=?", array($bodycode, $body, $title, $id));
} else {
    Jojo::insertQuery("INSERT INTO {wiki} SET wk_title=?, wk_url=?, wk_bodycode=?, wk_body=?", array($title, $url, $bodycode, $body));
    if (!$url_old || ($url_old == $url)) {
        $frajax->script('parent.$(".create-wiki .create").slideUp("slow");');
        $frajax->script('parent.$(".create-wiki .edit").slideDown("slow");');
        $frajax->script('parent.$(".create-wiki").addClass("edit-wiki").removeClass("create-wiki");');
    }
}

// Redirect if the URL has changed
if ($url_old && ($url_old !== $url)) {
    $frajax->redirect(_SITEURL.'/'.$prefix.'/'.$url.'/');
    $frajax->sendFooter();
    exit;
}

$frajax->script('parent.$("#wiki-edit-status").html("Saved...").fadeIn("slow").fadeTo(5000, 1).fadeOut("slow");');
$frajax->assign('wiki-view', 'innerHTML', $wiki->renderWikiBody($body));
$frajax->assign('body_code', 'value', $bodycode);
$frajax->script('parent.$("#wiki-edit").slideUp("slow");');
$frajax->script('parent.$("#wiki-view").slideDown("slow");');

$frajax->sendFooter();
