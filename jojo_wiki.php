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

class JOJO_Plugin_Jojo_wiki extends JOJO_Plugin
{
    private $_headings = array();

    /**
     * Get the url prefix for this plugin
     */
    function getPrefix($for = 'JOJO_Plugin_Jojo_wiki') {
        static $_cache;

        if (!isset($_cache[$for])) {
            $query = 'SELECT pg_url FROM {page} WHERE pg_link = ?;';
            $values = array($for);

            if ($values) {
                $res = Jojo::selectQuery($query, $values);
                if (isset($res[0])) {
                    $_cache[$for] = $res[0]['pg_url'];
                    return $_cache[$for];
                }
            }
            $_cache[$for] = '';
        }

        return $_cache[$for];
    }

    function _getContent()
    {
        global $smarty, $_USERGROUPS;
        $content = array();

        /* Only admins can edit articles */
        if (in_array('admin', $_USERGROUPS)) {
            $smarty->assign('wiki_can_edit', true);
        }
        $smarty->assign('wikiprefix', JOJO_Plugin_Jojo_wiki::getPrefix());

        $url = strtolower(Util::getFormData('url', ''));
        if (!$url) {
            /* wiki homepage */
            $wikis = JOJO::selectQuery("SELECT * FROM {wiki}");
            $smarty->assign('wikis', $wikis);
            //$content['content'] = $smarty->fetch('jojo_wiki.tpl');
            //return $content;
        }

        $wikis = Jojo::selectQuery("SELECT * FROM {wiki} WHERE wk_url=?", $url);
        if (count($wikis)) {
            $wiki = $wikis[0];
            $content['title'] = $wiki['wk_title'];
            $content['seotitle'] = $wiki['wk_title'];
            $wiki['body'] = $this->renderWikiBody($wiki['wk_body']);

            $smarty->assign('wiki', $wiki);

            /* Add wiki breadcrumb */
            $breadcrumbs = $this->_getBreadCrumbs();
            $breadcrumb = array();
            $breadcrumb['name'] = $wiki['wk_title'];
            $breadcrumb['rollover'] = $wiki['wk_desc'];
            $breadcrumb['url'] =  JOJO_Plugin_Jojo_wiki::getPrefix() . '/' . $wiki['wk_url'] . '/';
            $breadcrumbs[count($breadcrumbs)] = $breadcrumb;
        } else {
            /* non-existant page */
            $smarty->assign('pg_noindex', 'yes'); //make the page noindex

            /* set titles */
            $title = ucwords(str_replace(array('-', '_'), ' ', $url));
            $content['title'] = $title;
            $content['seotitle'] = $title . ' - New Page';

            $smarty->assign('wikititle', $title);
            $smarty->assign('wikiurl', $url);

            /* Add wiki breadcrumb */
            $breadcrumbs = $this->_getBreadCrumbs();
            $breadcrumb = array();
            $breadcrumb['name'] = $title;
            $breadcrumb['rollover'] = '';
            $breadcrumb['url'] =  JOJO_Plugin_Jojo_wiki::getPrefix() . '/' . $url . '/';
            $breadcrumbs[count($breadcrumbs)] = $breadcrumb;
        }

        $content['breadcrumbs'] = $breadcrumbs;
        $content['head']        = $smarty->fetch('jojo_wiki_head.tpl');
        $content['content']     = $smarty->fetch('jojo_wiki.tpl');

        return $content;
    }

    function getCorrectUrl()
    {
        //Assume the URL is correct
        return _PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    function renderWikiBody($wikicode) {
        $wikicode = "\n" . trim($wikicode);

        /* Add headings eg == heading == */
        $wikicode = preg_replace_callback('%(<br \/>)?\n(={1,6}) (.*) \\2\n<br \/>%U',
            array('JOJO_Plugin_Jojo_wiki', '_formatHeadings'),
            $wikicode);

        /* Add bold eq ''bold text'' */
        $wikicode = preg_replace_callback("%'''(()|[^'].*)'''%U",
            array('JOJO_Plugin_Jojo_wiki', '_formatBold'),
            $wikicode);

        /* Add italic eq ''italic text'' */
        $wikicode = preg_replace_callback("%''(()|[^'].*)''%U",
            array('JOJO_Plugin_Jojo_wiki', '_formatItalic'),
            $wikicode);

        /* Add simple links eq [[Page Title]] */
        $wikicode = preg_replace_callback("%\[\[((?!toc)[^|]*)\]\]%U",
            array('JOJO_Plugin_Jojo_wiki', '_formatSimpleLink'),
            $wikicode);

        /* Add named links eq [[Page Title|Link Label]] */
        $wikicode = preg_replace_callback("%\[\[([^|]*)\|(.*)\]\]%U",
            array('JOJO_Plugin_Jojo_wiki', '_formatNamedLink'),
            $wikicode);

        /* Add table of contents eq [[toc]] */
        $wikicode = preg_replace_callback("%\[\[toc\]\]%U",
            array('JOJO_Plugin_Jojo_wiki', '_insertTOC'),
            $wikicode);

        /* Add 'empty' class to dead wiki links */
        $wikicode = preg_replace_callback('%<a(.*?)href="'.JOJO_Plugin_Jojo_wiki::getPrefix().'/(.*?)/?"(.*?)>%',
            array('JOJO_Plugin_Jojo_wiki','_checkLinks'),
            $wikicode);

        /* Pull out code examples, lines starting with two whitespace lines */
        $wikicode = preg_replace_callback('%\n  (.*)\n<br \/>\n(  (.*)\n<br \/>\n)*%',
            array('JOJO_Plugin_Jojo_wiki','_codeExample'),
            $wikicode);

        return $wikicode;
    }

    function _codeExample($matches)
    {
        $clean = trim(str_replace(array("<br />\n", "\n  "), array('', "\n"), $matches[0]), "\n");
        $html = str_replace(' ', '&nbsp;', $clean);
        return "\n<div class='codeblock'>" . nl2br($html) . "</div>\n";
    }

    function _checkLinks($matches)
    {
        $data = Jojo::selectQuery("SELECT * FROM {wiki} WHERE wk_url=?", $matches[2]);
        if (!count($data)) {
            return sprintf('<a%shref="%s/%s/"%s class="wikilink empty" rel="nofollow">',
                                $matches[1],
                                JOJO_Plugin_Jojo_wiki::getPrefix(),
                                $matches[2],
                                $matches[3]);
        }
        return sprintf('<a%shref="%s/%s/"%s class="wikilink">',
                            $matches[1],
                            JOJO_Plugin_Jojo_wiki::getPrefix(),
                            $matches[2],
                            $matches[3]);
    }

    function _formatHeadings($matches)
    {
        $depth = strlen($matches[2]);

        $this->_headings[] = array(
                                'depth' => $depth,
                                'title' => $matches[3]
                                );
        return sprintf('<a name="wikiheading_%d"></a><h%d>%s</h%d>',
                count($this->_headings) - 1,
                $depth,
                $matches[3],
                $depth
            );
    }

    function _formatItalic($matches)
    {
        return sprintf('<em>%s</em>',
                $matches[1]
            );
    }

    function _formatBold($matches)
    {
        return sprintf('<strong>%s</strong>',
                $matches[1]
            );
    }

    function _formatSimpleLink($matches)
    {
        return sprintf('<a href="%s/%s/">%s</a>',
                            JOJO_Plugin_Jojo_wiki::getPrefix(),
                            strtolower(str_replace(' ', '_', $matches[1])),
                            $matches[1]);
    }

    function _formatNamedLink($matches)
    {
        return sprintf('<a href="%s/%s/">%s</a>',
                            JOJO_Plugin_Jojo_wiki::getPrefix(),
                            strtolower(str_replace(' ', '_', $matches[1])),
                            $matches[2]);
    }

    function _insertTOC($matches)
    {
        $minlevel = 6;
        foreach ($this->_headings as $heading) {
            $minlevel = min($minlevel, $heading['depth']);
        }

        $html = "<div class='wikitoc'>\n<ul>\n";
        $level = $minlevel;
        foreach ($this->_headings as $id => $heading) {
            while ($level < $heading['depth']) {
                $html .= '<ul>';
                $level++;
            }

            while ($level > $heading['depth']) {
                $html .= '</ul>';
                $level--;
            }

            $html .= sprintf("<li><a href='#wikiheading_%s'>%s</a></li>\n",
                            $id,
                            $heading['title']
                            );
        }
        $html .= "</ul>\n</div>";

        return $html;
    }
}
