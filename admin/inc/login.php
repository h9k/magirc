<?php
// $Id: login.php 310 2007-07-28 15:07:16Z Hal9000 $

/** ensure this file is being included by a parent file */
defined('_VALID_PARENT') or header("Location: ../");

?>
<div class="page_title">Login to phpDenora</div>
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
