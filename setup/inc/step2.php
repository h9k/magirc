<?php
// $Id$

$config['table_server'] = (isset($_GET['table_server'])) ? $_GET['table_server'] : 'server';
$config['debug_mode'] = 0;
$config['show_exec_time'] = 0;

$denoraver = $setup->denora->getVersion('full');
$denoranum = $setup->denora->getVersion('num');

echo "<pre>Checking Database consistency... ";
if (!$denoraver) {
	echo "<span style=\"color:red\">Failed</span><br />Please ensure that the Denora Stats Server is running and writing to the SQL database!</pre>";
} else {
	echo "<span style=\"color:green\">Passed</span></pre>";
	echo "<pre>Checking Denora version... ";
	if ($denoranum < '1.4') {
		echo "<span style=\"color:red\">Incompatible</span> (".$denoraver.")<br />You need Denora 1.4.3 or greater to use this version of Magirc!</pre>";
		$error = 1;
	} else {
		echo "<span style=\"color:green\">Supported</span> (".$denoraver.")</pre>";
?>
<p>You must now login to Magirc using one of the Admin users specified in your Denora server configuration file</p>
<form id="login" method="post" action="?step=3">
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
<?php } } ?>