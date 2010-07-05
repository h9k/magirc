{* $Id$ *}
{include file="_header.tpl"}

<h2>Login to MagIRC</h2>

{if $message}<div class="warning">{$message}</div>{/if}

<form id="login" method="post" action="?page=login">

  <table width="350" border="0" cellpadding="2" cellspacing="0">
    <tr>
      <td style="width:100px;"><label>Username</label></td>
      <td style="width:150px;"><input type="text" name="username" id="username" tabindex="1" /></td>
    </tr>
    <tr>
      <td style="width:100px;"><label>Password</label></td>
      <td style="width:150px;"><input type="password" name="password" id="password" tabindex="2" /></td>
    </tr>
  </table>

<div id="toolbar">
    <input type="hidden" name="form" value="login" />
    <ul>
        <li><a href="#" onclick="javascript:document.forms['login'].submit();return false"><img src="img/login.png" alt="" /> Login</a></li>
    </ul>
</div>

</form>

{include file="_footer.tpl"}