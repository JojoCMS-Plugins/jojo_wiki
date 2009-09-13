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

/* Register URI patterns */
$prefix = JOJO_Plugin_Jojo_wiki::getPrefix();
Jojo::registerURI("$prefix/[url:string]", 'JOJO_Plugin_Jojo_wiki'); // "wiki/name-of-article/"
if ($prefix != 'wiki') {
    Jojo::registerURI("wiki/[url:string]", 'JOJO_Plugin_Jojo_wiki'); // "wiki/name-of-article/"
}

$_provides['pluginClasses'] = array(
        'JOJO_Plugin_Jojo_wiki' => 'Wiki - Wiki pages'
        );