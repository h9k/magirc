{* $Id$ *}
{include file="_header.tpl"}

<div class="page_title">Network Settings</div>
<form id="network" name="network" method="post" action="">
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
      <td align="right">Servers you don't want phpDenora to show. Separate with commas without spaces, example: &quot;hub.mynet.tld,hub2.mynet.tld&quot;</td>
      <td align="left"><input name="hide_servers" type="text" id="hide_servers" value="{$config.hide_servers}" size="32" maxlength="1024" tabindex="3" /></td>
    </tr>
    <tr>
      <td align="right">Channels you don't want phpDenora to show. Separate with commas without spaces</td>
      <td align="left"><input name="hide_chans" type="text" id="hide_chans" value="{$config.hide_chans}" size="32" maxlength="1024" tabindex="5" /></td>
    </tr>
  </table>
  <p align="right">
    <input type="submit" name="button" id="button" value="Save" tabindex="6" />
    </p>
</form>

{include file="_footer.tpl"}