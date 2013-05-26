<?php
$status = $setup->requirementsCheck();
if ($status['error']) die('Failure. <a href="?step=1">back</a>');

// Handle saving of the database configuration
$setup->saveConfig();

// Check Magirc Database connection
if (file_exists(MAGIRC_CFG_FILE)) {
	include(MAGIRC_CFG_FILE);
	if (isset($db) && is_array($db)) {
		$setup->db = Magirc_DB::getInstance();
		$status['error'] = $setup->db->error;
		$setup->tpl->assign('db_magirc', $db);
	} else {
		$status['error'] = "Invalid configuration file";
	}
} else {
	$setup->tpl->assign('db_magirc', array('username' => '', 'password' => '', 'database' => '', 'hostname' => 'localhost', 'port' => 3306, 'ssl' => false, 'ssl_key' => null, 'ssl_cert' => null, 'ssl_ca' => null));
	$status['error'] = 'new';
}

// Handle database initialization/upgrade
$dump = $check = $updated = false;
if (!$status['error']) {
	$check = $setup->configCheck();
	$setup->tpl->assign('check', $check);
	if (!$check) { // Dump sql file to db
		$dump = $setup->configDump();
		$base_url = "//";
		$base_url .= $_SERVER['SERVER_NAME'];
		$base_url .= $_SERVER['SERVER_PORT'] == 80 ? '' : ':'.$_SERVER['SERVER_PORT'];
		$base_url .= str_replace('setup/index.php', '', $_SERVER['SCRIPT_NAME']);
		$setup->db->update('magirc_config', array('value' => $base_url), array('parameter' => 'base_url'));
	} else { // Upgrade db
		$updated = $setup->configUpgrade();
	}
}

$setup->tpl->assign('magirc_conf', MAGIRC_CFG_FILE);
$setup->tpl->assign('status', $status);
$setup->tpl->assign('dump', $dump);
$setup->tpl->assign('updated', $updated);
$setup->tpl->assign('version', DB_VERSION);
$setup->tpl->display('step2.tpl');
?>