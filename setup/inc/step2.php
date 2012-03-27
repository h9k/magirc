<?php


$config['debug_mode'] = 0;
$config['show_exec_time'] = 0;
$error = 0;

$check = $setup->configCheck();
$setup->tpl->assign('check', $check);
if (!$check) { // Dump file to db
	$result = $setup->configDump();
	/*$protocol = @$_SERVER['HTTPS'] ? 'https://' : 'http://';
	$base_url = $protocol.$_SERVER['SERVER_NAME'].str_replace('setup/index.php', '', $_SERVER['PHP_SELF']);
	$query = sprintf("UPDATE `magirc_config` SET `value` = '%s' WHERE `parameter` = 'base_url'", $base_url);
	$setup->db->query($query, SQL_NONE);*/
	$setup->tpl->assign('result', $result);
	if ($result != 0) {
		$error = 1;
	}
} else {
	$setup->tpl->assign('version', $setup->getDbVersion());
}


$setup->tpl->display('step2.tpl');
?>