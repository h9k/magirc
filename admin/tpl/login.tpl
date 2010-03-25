{* $Id$ *}
{include file="_header.tpl"}

{if $message}<pre>{$message}</pre>{/if}
<h2>Login to MagIRC</h2>
<form id="login" method="post" action="?page=login">
  <table width="350" border="0" cellpadding="2" cellspacing="0">
    <tr>
      <td width="100"><label>User</label></td>
      <td width="150"><input type="text" name="username" id="username" tabindex="1" /></td>
      <td width="50" rowspan="2" align="center" valign="middle"><input type="submit" name="login" id="login" value="Login" tabindex="3" /></td>
    </tr>
    <tr>
      <td width="100"><label>Password</label></td>
      <td width="150"><input type="password" name="password" id="password" tabindex="2" /></td>
    </tr>
  </table>
</form>

{include file="_footer.tpl"}