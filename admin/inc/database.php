<?php
// $Id: advanced.php 308 2007-07-24 17:09:00Z Hal9000 $


$sql_config_file = '../sql.cfg.php';

if (isset($_POST['button'])) {
	$sql_username = (isset($_POST['username'])) ? $_POST['username'] : $sql['username'];
	$sql_password = (isset($_POST['password'])) ? $_POST['password'] : $sql['password'];
	$sql_db_name = (isset($_POST['db_name'])) ? $_POST['db_name'] : $sql['db_name'];
	$sql_hostname = (isset($_POST['hostname'])) ? $_POST['hostname'] : $sql['hostname'];
	$sql_port = (isset($_POST['port'])) ? $_POST['port'] : $sql['port'];
	$sql_buffer = "<?php
defined('_VALID_PARENT') or header(\"Location: ../\");
\$sql['username'] = \"$sql_username\";
\$sql['password'] = \"$sql_password\";
\$sql['db_name'] = \"$sql_db_name\";
\$sql['hostname'] = \"$sql_hostname\";
\$sql['port'] = \"$sql_port\";
?>";
	if (is_writable($sql_config_file)) {
		$writefile = fopen($sql_config_file,"w");
		fwrite($writefile,$sql_buffer);
		fclose($writefile);
		echo "<div class=\"configsave\">Configuration saved</div>";
	} else {
		echo "<pre><strong>Please copy the following text and paste it into the $sql_config_file file</strong></pre>";
		echo "<textarea name=\"sql_buffer\" cols=\"64\" rows=\"10\" readonly=\"readonly\">$sql_buffer</textarea>";
	}
	require($sql_config_file);
}

?>
<div class="page_title">Database Settings</div>
<form id="sql" name="sql" method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr>
      <td align="right">Username</td>
      <td align="left"><input name="username" type="text" id="username" tabindex="1" value="<?php echo $sql['username']; ?>" size="32" maxlength="1024" /></td>
    </tr>
    <tr>
      <td align="right">Password</td>
      <td align="left"><input type="password" name="password" id="password" tabindex="2" value="<?php echo $sql['password']; ?>" size="32" maxlength="1024" /></td>
    </tr>
    <tr>
      <td align="right">Database Name</td>
      <td align="left"><input type="text" name="db_name" id="db_name" tabindex="3" value="<?php echo $sql['db_name']; ?>" size="32" maxlength="1024" /></td>
    </tr>
    <tr>
      <td align="right">Hostname</td>
      <td align="left"><input type="text" name="hostname" id="hostname" tabindex="4" value="<?php echo $sql['hostname']; ?>" size="32" maxlength="1024" /></td>
    </tr>
    <tr>
      <td align="right">TCP Port</td>
      <td align="left"><input type="text" name="port" id="port" tabindex="5" value="<?php echo $sql['port']; ?>" size="32" maxlength="1024" /></td>
    </tr>
  </table>
  <?php
  	if (is_writable($sql_config_file)) {
		echo "<pre>The configuration file $sql_config_file is <span style=\"color:green;\">writable</span></pre>";
	} else {
		echo "<pre>The configuration file is <span style=\"color:red;\">not writable</span><br />Please ensure that the $sql_config_file file has enough write permissions. Try chmod 0666 or 0777.</pre>";
	}
  ?>
  <p align="right"><span style="color:red;">Warning:</span> you may break your phpDenora if you put wrong settings in here!<br />
    In that case you will need to edit the <?php echo $sql_config_file; ?> configuration file manually.<br />
    <input type="submit" name="button" id="button" value="Save" tabindex="7" />
    </p>
</form>
