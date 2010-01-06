<?php
// $Id: advanced.php 315 2007-08-18 11:41:28Z Hal9000 $

if (isset($_POST['button'])) {
	if (isset($_POST['debug_mode'])) { $admin->saveConfig('debug_mode',$_POST['debug_mode']); }
	if (isset($_POST['show_exec_time'])) { $admin->saveConfig('show_exec_time',1); }
	else { $admin->saveConfig('show_exec_time',0); }
	if (isset($_POST['show_validators'])) { $admin->saveConfig('show_validators',1); }
	else { $admin->saveConfig('show_validators',0); }
}

$admin->tpl->assign('config', $admin->cfg->config);
$admin->tpl->display('advanced.tpl');
?>