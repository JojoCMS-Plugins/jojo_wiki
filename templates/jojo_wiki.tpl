{if $wiki_can_edit}
    <div class="{if $wiki.wikiid}edit{else}create{/if}-wiki">
        <div id="wiki-edit-bar" class="edit">
            <button onclick="$('#wiki-edit').slideUp('slow');$('#wiki-view').slideDown('slow');">View</button>
            <button onclick="$('#wiki-view').slideUp('slow');$('#wiki-edit').slideDown('slow');">Edit</button>
            <span id="wiki-edit-status"></span>
        </div>
        <div class="create">Create a new wiki entry</div>

        <div id="wiki-edit"{if $wiki.wikiid} style="display: none"{/if}>
            <form method="post" action="actions/wiki_save.php?arg1={$wiki.wikiid}" target="frajax-iframe">
                Title: <input type="text" name="title" id="title" value="{$wiki.wk_title}" /><br />
                <div{if $wiki} class="creating-wiki"{/if}>{if !$wiki.wikiid}URL: {/if}<input type="{if !$wiki.wikiid}text{else}hidden{/if}" name="url" value="{$wiki.wk_url}" /></div>
                <input type="hidden" name="oldurl" value="{$wiki.wk_url}" />
                <textarea name="body" id="body_code" class="jTagEditor jTagBB" rows="30" cols="70">{$wiki.wk_bodycode|escape:'html':'utf-8'}</textarea><br />
                <input type="submit" name="save" value="Save" /><br />
            </form>
        </div>
        <div id="wiki-view">{if $wiki.wikiid}{$wiki.body}{/if}</div>
    </div>
    <iframe src="javascript:false;" name="frajax-iframe" id="frajax-iframe" style="display:none; height: 0; width: 0; border: 0;"></iframe>
{else}
    {if $wiki.wikiid}
        <div id="wiki-view">
            {$wiki.body}
        </div>
    {else}
        <p>This article has not yet been created.</p>
    {/if}
{/if}
