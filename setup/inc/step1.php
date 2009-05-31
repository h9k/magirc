<?php
$error = 0;
$status = array();

$setup->tpl->assign('phpversion', phpversion());

if (version_compare("5.2.0", phpversion(), "<") == 1) {
	$status['php'] = true;
} else {
	$status['php'] = false;
	$error = 1;
}

if (extension_loaded('mysqli') == 1) {
	$status['mysqli'] = true;
} else {
	$status['mysqli'] = false;
	$error = 1;
}

if (extension_loaded('gd') == 1) {
	$status['gd'] = true;
} else {
	$status['gd'] = false;
	$error = 1;
}

if (is_writable($magirc_conf) && is_writable($denora_conf)) {
	$status['writable'] = true;
} else {
	$status['writable'] = false;
}

if (is_writable('../tmp/compiled')) {
	$status['compiled'] = true;
} else {
	$status['compiled'] = false;
	$error = 1;
}

if (is_writable('../tmp/cache')) {
	$status['cache'] = true;
} else {
	$status['cache'] = false;
	$error = 1;
}

$config['table_server'] = (isset($_REQUEST['table_server'])) ? $_REQUEST['table_server'] : 'server';

// If the DB Test failed, user could fill a form to change config. Here we handle the new input.
if (isset($_POST['button'])) {
	$db['username'] = (isset($_POST['username'])) ? $_POST['username'] : $db['username'];
	$db['password'] = (isset($_POST['password'])) ? $_POST['password'] : $db['password'];
	$db['database'] = (isset($_POST['database'])) ? $_POST['database'] : $db['database'];
	$db['hostname'] = (isset($_POST['hostname'])) ? $_POST['hostname'] : $db['hostname'];
	$db['port'] = (isset($_POST['port'])) ? $_POST['port'] : $db['port'];
}

$status['error'] = $error;

if (!$error) {
	// Check Magirc Database connection
	include($magirc_conf);
	if (!($magirc_db = $setup->dbCheck($db))) {
		// DB Test was successful, so we can now save the new info
		if (isset($_POST['button']) && $_GET['db'] == "magirc") {
			$db_buffer = "<?php
\$db['username'] = \"".$db['username']."\";
\$db['password'] = \"".$db['password']."\";
\$db['database'] = \"".$db['database']."\";
\$db['hostname'] = \"".$db['hostname']."\";
\$db['port'] = \"".$db['port']."\";
?>";
			$setup->tpl->assign('db_buffer', $db_buffer);
			if (is_writable($magirc_conf)) {
				$writefile = fopen($magirc_conf,"w");
				fwrite($writefile,$db_buffer);
				fclose($writefile);
			}
		}
	}
	$status['magirc_db'] = $magirc_db;
	$setup->tpl->assign('db_magirc', $db);
	unset($db);
	
	// Check Denora Database connection
	include($denora_conf);
	if (!($denora_db = $setup->dbCheck($db, $config['table_server']))) { //TODO: Test Denora DB connection
			// DB Test was successful, so we can now save the new info
		if (isset($_POST['button']) && $_GET['db'] == "denora") {
			$db_buffer = "<?php
\$db['username'] = \"".$db['username']."\";
\$db['password'] = \"".$db['password']."\";
\$db['database'] = \"".$db['database']."\";
\$db['hostname'] = \"".$db['hostname']."\";
\$db['port'] = \"".$db['port']."\";
?>";
			$setup->tpl->assign('db_buffer', $db_buffer);
			if (is_writable($denora_conf)) {
				$writefile = fopen($denora_conf,"w");
				fwrite($writefile,$db_buffer);
				fclose($writefile);
			}
		}
	}
	$status['denora_db'] = $denora_db;
	$setup->tpl->assign('db_denora', $db);
	unset($db);
}

$setup->tpl->assign('status', $status);
$setup->tpl->assign('config', $config);
$setup->tpl->display('step1.tpl');
?>