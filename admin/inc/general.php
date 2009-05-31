<?php
// $Id: general.php 315 2007-08-18 11:41:28Z Hal9000 $

if (isset($_POST['button'])) {
	if (isset($_POST['net_name'])) { $admin->saveConfig('net_name',$_POST['net_name']); }
	if (isset($_POST['net_url'])) { $admin->saveConfig('net_url',$_POST['net_url']); }
	if (isset($_POST['theme'])) { $admin->saveConfig('theme',$_POST['theme']); }
	if (isset($_POST['lang'])) { $admin->saveConfig('lang',$_POST['lang']); }
	if (isset($_POST['msg_welcome'])) { $admin->saveConfig('msg_welcome',$_POST['msg_welcome']); }
	else { $admin->saveConfig('msg_welcome',''); }
	$admin->cfg->reloadConfig();
}

$admin->tpl->assign('config', $admin->cfg->config);
$admin->tpl->display('general.tpl');
?>