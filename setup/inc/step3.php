<?php
// $Id$

$config['debug_mode'] = 0;
$config['show_exec_time'] = 0;

$error = 0;
if (isset($_POST['username'])) { $username = htmlspecialchars($_POST['username']); }
if (isset($_POST['password'])) { $password = htmlspecialchars($_POST['password']); }
//sql_db_connect();
echo "<pre>Logging in... ";
if (isset($username) && isset($password)) {
	$result = $this->denora->login($username, $password);
	if ($result == 1) {
		echo "<span style=\"color:green;\">Done</span></pre>";
		
		echo "<pre>Checking configuration table... ";
		$check = $this->configCheck();
		if (!$check) { // Dump file to db
			echo " Creating... ";
			$result = $this->configDump();
			if ($result == 0) {
				echo "<span style=\"color:green;\">Done</span></pre>";
			} else {
				echo "<span style=\"color:red;\">Failed</span></pre>";
				$error = 1;
			}
		} else {
			echo "<span style=\"color:green;\">OK</span> (version ".$check.")</pre>";
		}
	} else {
		echo "<span style=\"color:red;\">Failed</span></pre>";
		$error = 1;
		echo "Please use a valid admin username and password, as specified in the denora server configuration file.";
	}
} else {
	$error = 1;
	echo "<span style=\"color:red;\">Failed</span></pre>";
}
if (!$error) {
	echo "<p>Setup finished! You <strong>MUST</strong> now logon into the <a href=\"../admin/\"><strong>Admin Interface</strong></a> to configure Magirc</p>";
}