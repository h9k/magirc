<?php
$new = false;

$status = $setup->requirementsCheck();
$setup->saveConfig();

if (!$status['error']) {
	// Check Magirc Database connection
	if (!$new && file_exists($magirc_conf)) {
		include($magirc_conf);
		$setup->tpl->assign('db_magirc', $db);
		$status['magirc_db'] = $setup->dbCheck($db);
		unset($db);
	} else {
		$setup->tpl->assign('db_magirc', array('hostname' => 'localhost', 'port' => 3306));
		$status['magirc_db'] = 'new';
	}	
}

$setup->tpl->assign('phpversion', phpversion());
$setup->tpl->assign('status', $status);
$setup->tpl->assign('config', $config);

$setup->tpl->display('step1.tpl');
?>
