{* $Id$ *}
{include file="_header.tpl"}

{if $smarty.post.button}
<div class="configsave">Configuration saved</div>
{/if}
<div class="page_title">General Settings</div>
<form id="general" name="general" method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr>
      <td align="right">The <strong>name of your Network</strong></td>
      <td align="left"><input name="net_name" type="text" id="net_name" value="{$config.net_name}" size="32" maxlength="1024" tabindex="1" /></td>
    </tr>
    <tr>
      <td align="right">The URL of the <strong>Homepage of your Network</strong></td>
      <td align="left"><input name="net_url" type="text" id="net_url" value="{$config.net_url}" size="32" maxlength="1024" tabindex="2" /></td>
    </tr>
    <tr>
      <td align="right"><strong>Default Theme</strong></td>
      <td align="left"><em>Default</em></td>
    </tr>
    <tr>
      <td align="right"><strong>Default Language</strong><br />(will not override automatic detection by browser)</td>
      <td align="left"><em>English</em></td>
    </tr>
  </table>
  <p><strong>Welcome Message</strong><br />You can welcome your users, describe your network, and put whatever information you want in there.<br />
    <textarea name="msg_welcome" cols="64" rows="16" id="msg_welcome" tabindex="5">{$config.msg_welcome}</textarea>
  </p>
  <p align="right">
    <input type="submit" name="button" id="button" value="Save" tabindex="6" />
  </p>
</form>

{include file="_footer.tpl"}