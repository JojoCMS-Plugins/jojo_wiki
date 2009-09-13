{*
{if $wikis}
{$pg_body}
<ul>
{section name=w loop=$wikis}
<li><a href="{$wikiprefix}/{$wikis[w].wk_url}/">{$wikis[w].wk_title}</a></li>
{/section}
</ul>
{/if}
*}

{if $wiki}
{if $wiki_can_edit}
<button onclick="$('#wiki-edit').hide();$('#wiki-view').show('fast');">View</button>
<button onclick="$('#wiki-view').hide();$('#wiki-edit').show('fast');">Edit</button>
{/if}
<div id="wiki-view">
{$wiki.body}
</div>
{if $wiki_can_edit}
<div id="wiki-edit" style="display: none">
  <form method="post" action="actions/wiki_save.php?arg1={$wiki.wikiid}" target="frajax-iframe">
    <input type="hidden" name="title" value="{$wiki.wk_title}" />
    <input type="hidden" name="url" value="{$wiki.wk_url}" />
    <textarea name="body" id="body_code" class="jTagEditor jTagBB" rows="30" cols="70">{$wiki.wk_bodycode|escape:'html':'utf-8'}</textarea><br />
    <input type="submit" name="save" value="Save" /><br />
  </form>
</div>

{/if}

{else}
{if $wiki_can_edit}
{include file="jojo_wiki_new.tpl"}
{elseif $wikis}

{else}
This article has not yet been created.
{/if}

{/if}