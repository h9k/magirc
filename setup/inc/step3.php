<?php
// $Id$

$config['debug_mode'] = 0;
$config['show_exec_time'] = 0;
$error = false;
$username = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : NULL;
$password = isset($_POST['password']) ? htmlspecialchars($_POST['password']) : NULL;

if ($username && $password) {
	$query = sprintf("INSERT INTO `magirc_admin` SET `username` = %s, `password` = MD5(%s)",
		$setup->db->escape($username), $setup->db->escape($password));
	$setup->db->query($query);
} else {
	$error = true;
}

$setup->tpl->assign('error', $error);
$setup->tpl->display('step3.tpl');

?>