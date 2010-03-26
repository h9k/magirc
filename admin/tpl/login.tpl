{* $Id$ *}
{include file="_header.tpl"}

<h2>Login to MagIRC</h2>

{if $message}<div class="warning">{$message}</div>{/if}

<form name="login" id="login" method="post" action="?page=login">

  <table width="350" border="0" cellpadding="2" cellspacing="0">
    <tr>
      <td width="100"><label>Username</label></td>
      <td width="150"><input type="text" name="username" id="username" tabindex="1" /></td>
    </tr>
    <tr>
      <td width="100"><label>Password</label></td>
      <td width="150"><input type="password" name="password" id="password" tabindex="2" /></td>
    </tr>
  </table>

<div id="toolbar">
    <input type="hidden" name="form" value="login" />
    <ul>
        <li><a href="#" onclick="javascript:document.login.submit();return false"><img src="img/login.png" alt="" /> Login</a></li>
    </ul>
</div>

</form>

{include file="_footer.tpl"}