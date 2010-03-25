{* $Id$ *}
{include file="_header.tpl"}

<h2>Behavior Settings</h2>
<form id="behavior" name="behavior" method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr>
      <td align="right">Set the <strong>default ordering column</strong> for user/channel stat tables</td>
      <td align="left"><select name="chanstats_sort" id="chanstats_sort" tabindex="1" >
        <option value="actions"{if $config.chanstats_sort == 'actions'} selected="selected"{/if}>Actions</option>
        <option value="kicks"{if $config.chanstats_sort == 'kicks'} selected="selected"{/if}>Kicks</option>
        <option value="letters"{if $config.chanstats_sort == 'letters'} selected="selected"{/if}>Letters</option>
        <option value="line"{if $config.chanstats_sort == 'line'} selected="selected"{/if}>Lines</option>
        <option value="modes"{if $config.chanstats_sort == 'modes'} selected="selected"{/if}>Modes</option>
        <option value="smileys"{if $config.chanstats_sort == 'smileys'} selected="selected"{/if}>Smileys</option>
        <option value="topics"{if $config.chanstats_sort == 'topics'} selected="selected"{/if}>Topics</option>
        <option value="words"{if $config.chanstats_sort == 'words'} selected="selected"{/if}>Words</option>
      </select></td>
    </tr>
    <tr>
      <td align="right">Set the <strong>default stats type</strong> for user/Channel stat tables</td>
      <td align="left"><select name="chanstats_type" id="chanstats_type" tabindex="2" >
        <option value="1"{if $config.chanstats_type == '1'} selected="selected"{/if}>Today</option>
        <option value="2"{if $config.chanstats_type == '2'} selected="selected"{/if}>This Week</option>
        <option value="3"{if $config.chanstats_type == '3'} selected="selected"{/if}>This Month</option>
        <option value="0"{if $config.chanstats_type == '0'} selected="selected"{/if}>Total</option>
      </select></td>
    </tr>
    <tr>
      <td align="right">Sets the <strong>limit for table listed outputs</strong></td>
      <td align="left"><input name="list_limit" type="text" id="list_limit" value="{$config.list_limit}" size="4" maxlength="3" tabindex="3" />      </td>
    </tr>
    <tr>
      <td align="right">Sets the <strong>limit for TOP channels/users</strong> outputs on the front page</td>
      <td align="left"><input name="top_limit" type="text" id="top_limit" value="{$config.top_limit}" size="4" maxlength="3" tabindex="4" /></td>
    </tr>
    <tr>
      <td align="right">Minimum amount of characters needed (excluding wildcards) to make a search query</td>
      <td align="left"><input name="search_min_chars" type="text" id="search_min_chars" value="{$config.search_min_chars}" size="2" maxlength="1" tabindex="5" /></td>
    </tr>
  </table>
  <p align="right">
    <input type="submit" name="button" id="button" value="Save" tabindex="6" />
    </p>
</form>

{include file="_footer.tpl"}