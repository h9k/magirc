<?php
// $Id$

$config['debug_mode'] = 0;
$config['show_exec_time'] = 0;
$error = 0;
if (isset($_POST['username'])) { $username = htmlspecialchars($_POST['username']); }
if (isset($_POST['password'])) { $password = htmlspecialchars($_POST['password']); }

$result = $setup->denora->login(@$username, @$password);
$setup->tpl->assign('login', @$result[0]['uname'] == $username);
if (@$result[0]['uname'] == $username) {
	$check = $setup->configCheck();
	$setup->tpl->assign('check', $check);
	if (!$check) { // Dump file to db
		$result = $setup->configDump();
		$base_url = explode('setup/', $_SERVER['HTTP_REFERER']);
		$query = sprintf("UPDATE `magirc_config` SET `value` = '%s' WHERE `parameter` = 'base_url'", $base_url[0]);
		$setup->db->query($query, SQL_NONE);
		$setup->tpl->assign('result', $result);
		if ($result != 0) {
			$error = 1;
		}
	} else {
		$setup->tpl->assign('version', $setup->getDbVersion());
	}
} else {
	$error = 1;
}

$setup->tpl->assign('error', $error);
$setup->tpl->display('step3.tpl');

?>