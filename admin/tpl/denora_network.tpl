<h2>Network Settings</h2>
<form id="network" method="post" action="">

<div id="toolbar">
    <input type="hidden" name="form" value="network" />
    <ul>
        <li><a href="#" onclick="javascript:document.forms['network'].submit();return false"><img src="img/save.png" alt="" /> Save</a></li>
    </ul>
</div>

  <table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr>
      <td align="right"><strong>IRCd Server Type</strong></td>
      <td align="left"><select name="ircd_type" id="ircd_type" tabindex="1">
      {foreach from=$ircds item=item}
      	<option value="{$item}"{if $config.ircd_type} selected="selected"{/if}>{$item}</option>
      {/foreach}
      </select></td>
    </tr>
    <tr>
      <td align="right"><strong>Hide Ulined Servers</strong></td>
      <td align="left"><input name="hide_ulined" type="checkbox" id="hide_ulined" tabindex="2" {if $config.hide_ulined}checked="checked" {/if}/></td>
    </tr>
    <tr>
      <td align="right">Servers you don't want MagIRC to show. Separate with commas without spaces, example: &quot;hub.mynet.tld,hub2.mynet.tld&quot;</td>
      <td align="left"><input name="hide_servers" type="text" id="hide_servers" value="{$config.hide_servers}" size="32" maxlength="1024" tabindex="3" /></td>
    </tr>
    <tr>
      <td align="right">Channels you don't want MagIRC to show. Separate with commas without spaces</td>
      <td align="left"><input name="hide_chans" type="text" id="hide_chans" value="{$config.hide_chans}" size="32" maxlength="1024" tabindex="5" /></td>
    </tr>
  </table>
</form>