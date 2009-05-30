<?php
// $Id$

?>
<div class="page_title">Welcome to the Magirc Setup!</div>
<p>Please follow the on-screen instructions to install Magirc</p>
<?php
$error = 0;
require_once($magirc_conf);

echo "<pre>Checking PHP version... ";
if (version_compare("5.2.0", phpversion(), "<") == 1) {
	echo "<span style=\"color:green\">Supported</span> (".phpversion().")</pre>";
} else {
	echo "<span style=\"color:red\">Not Supported</span> (".phpversion().") ><br />You need at least version 5.2.0</pre>";
	$error = 1;
}

echo "<pre>Checking PHP MySQLi extension... ";
if (extension_loaded('mysqli') == 1) {
	echo "<span style=\"color:green\">Present</span></pre>";
} else {
	echo "<span style=\"color:red\">Missing!</span><br />This component is required to run phpDenora. Please contact your Administrator.</pre>";
	$error = 1;
}

echo "<pre>Checking PHP GD extension... ";
if (extension_loaded('gd') == 1) {
	echo "<span style=\"color:green\">Present</span></pre>";
} else {
	echo "<span style=\"color:red\">Missing!</span><br />This component is required to run phpDenora. Please contact your Administrator.</pre>";
	$error = 1;
}

echo "<pre>Checking SQL configuration files... ";
if (is_writable($magirc_conf) && is_writable($denora_conf)) {
	echo "<span style=\"color:green;\">Writable</span></pre>";
} else {
	echo "<span style=\"color:orange;\">Not writable</span><br />Please ensure that the $magirc_conf and $denora_conf files have enough write permissions.<br />Try chmod 0666 or 0777. If it still doesn't work don't worry, you can continue anyway.</pre>";
}

// If the DB Test failed, user could fill a form to change config. Here we handle the new input.
if (isset($_POST['button'])) {
	$db['username'] = (isset($_POST['username'])) ? $_POST['username'] : $db['username'];
	$db['password'] = (isset($_POST['password'])) ? $_POST['password'] : $db['password'];
	$db['database'] = (isset($_POST['database'])) ? $_POST['database'] : $db['database'];
	$db['hostname'] = (isset($_POST['hostname'])) ? $_POST['hostname'] : $db['hostname'];
	$db['port'] = (isset($_POST['port'])) ? $_POST['port'] : $db['port'];
}
$config['table_server'] = (isset($_REQUEST['table_server'])) ? $_REQUEST['table_server'] : 'server';

if (!$error) {
	echo "<pre>Testing Magirc Database connection... ";
	$db_error = false;
	// Test DB connection
	if (!$db_error) {
		echo "<span style=\"color:green;\">Passed</span></pre>";
		// DB Test was successful, so we can now save the new info
		if (isset($_POST['button'])) {
			$db_buffer = "<?php
defined('_VALID_PARENT') or header(\"Location: ../\");
\$db['username'] = \"".$db['username']."\";
\$db['password'] = \"".$db['password']."\";
\$db['database'] = \"".$db['database']."\";
\$db['hostname'] = \"".$db['hostname']."\";
\$db['port'] = \"".$db['port']."\";
?>";
			if (is_writable($magirc_conf)) {
				$writefile = fopen($magirc_conf,"w");
				fwrite($writefile,$db_buffer);
				fclose($writefile);
				echo "<div class=\"configsave\">Configuration saved</div>";
				echo "<p>Continue to the <a href=\"?step=2&amp;table_server=".$config['table_server']."\">next step</a></p>";
			} else {
				echo "<p><strong>Please replace the contents of the $magirc_conf file with the text below:</strong></p>";
				echo "<textarea name=\"sql_buffer\" cols=\"64\" rows=\"10\" readonly=\"readonly\">$db_buffer</textarea>";
				echo "<p>When you are done please <a href=\"?step=1&amp;table_server=".$config['table_server']."\">repeat this step</a></p>";
			}
		} else {
			//TODO: check for Denora DB here
			echo "<pre>Testing Denora Database connection... ";
			$db_error = false;
			// Test DB connection
			if (!$db_error) {
				echo "<span style=\"color:green;\">Passed</span></pre>";
				// DB Test was successful, so we can now save the new info
				if (isset($_POST['button'])) {
					$db_buffer = "<?php
		defined('_VALID_PARENT') or header(\"Location: ../\");
		\$db['username'] = \"".$db['username']."\";
		\$db['password'] = \"".$db['password']."\";
		\$db['database'] = \"".$db['database']."\";
		\$db['hostname'] = \"".$db['hostname']."\";
		\$db['port'] = \"".$db['port']."\";
		?>";
					if (is_writable($denora_conf)) {
						$writefile = fopen($denora_conf,"w");
						fwrite($writefile,$db_buffer);
						fclose($writefile);
						echo "<div class=\"configsave\">Configuration saved</div>";
						echo "<p>Continue to the <a href=\"?step=2&amp;table_server=".$config['table_server']."\">next step</a></p>";
					} else {
						echo "<p><strong>Please replace the contents of the $denora_conf file with the text below:</strong></p>";
						echo "<textarea name=\"sql_buffer\" cols=\"64\" rows=\"10\" readonly=\"readonly\">$db_buffer</textarea>";
						echo "<p>When you are done please <a href=\"?step=1&amp;table_server=".$config['table_server']."\">repeat this step</a></p>";
					}
				} else {
					echo "<p>Continue to the <a href=\"?step=2&amp;table_server=".$config['table_server']."\">next step</a></p>";
				}
			}
		}
	} else {
		echo "<span style=\"color:red;\">$db_error</span></pre>";
		?>
<p>Please configure the access to the Magirc SQL database</p>
<form id="database" name="database" method="post" action="">
<table width="100%" border="0" cellspacing="0" cellpadding="5">
	<tr>
		<td align="right">Username</td>
		<td align="left"><input name="username" type="text" id="username"
			tabindex="1" value="<?php echo $db['username']; ?>" size="32"
			maxlength="1024" /></td>
	</tr>
	<tr>
		<td align="right">Password</td>
		<td align="left"><input type="password" name="password" id="password"
			tabindex="2" value="<?php echo $db['password']; ?>" size="32"
			maxlength="1024" /></td>
	</tr>
	<tr>
		<td align="right">Database Name</td>
		<td align="left"><input type="text" name="db_name" id="db_name"
			tabindex="3" value="<?php echo $db['db_name']; ?>" size="32"
			maxlength="1024" /></td>
	</tr>
	<tr>
		<td align="right">Hostname</td>
		<td align="left"><input type="text" name="hostname" id="hostname"
			tabindex="4" value="<?php echo $db['hostname']; ?>" size="32"
			maxlength="1024" /></td>
	</tr>
	<tr>
		<td align="right">TCP Port</td>
		<td align="left"><input type="text" name="port" id="port" tabindex="5"
			value="<?php echo $db['port']; ?>" size="32" maxlength="1024" /></td>
	</tr>
	<tr>
		<td align="right">Server table</td>
		<td align="left"><input type="text" name="table_server"
			id="table_server" tabindex="6"
			value="<?php echo $config['table_server']; ?>" size="32"
			maxlength="1024" /></td>
	</tr>
</table>
<p align="right"><input type="submit" name="button" id="button"
	value="Continue" tabindex="8" /></p>
</form>

		<?php } } ?>
