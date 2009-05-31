<?php
// $Id: advanced.php 315 2007-08-18 11:41:28Z Hal9000 $

if (isset($_POST['button'])) {
	if (isset($_POST['debug_mode'])) { $admin->saveConfig('debug_mode',$_POST['debug_mode']); }
	if (isset($_POST['show_exec_time'])) { $admin->saveConfig('show_exec_time',1); }
	else { $admin->saveConfig('show_exec_time',0); }
	if (isset($_POST['show_validators'])) { $admin->saveConfig('show_validators',1); }
	else { $admin->saveConfig('show_validators',0); }
	if (isset($_POST['table_user'])) { $admin->saveConfig('table_user',$_POST['table_user']); }
	if (isset($_POST['table_chan'])) { $admin->saveConfig('table_chan',$_POST['table_chan']); }
	if (isset($_POST['table_chanbans'])) { $admin->saveConfig('table_chanbans',$_POST['table_chanbans']); }
	if (isset($_POST['table_chanexcepts'])) { $admin->saveConfig('table_chanexcepts',$_POST['table_chanexcepts']); }
	if (isset($_POST['table_chaninvites'])) { $admin->saveConfig('table_chaninvites',$_POST['table_chaninvites']); }
	if (isset($_POST['table_glines'])) { $admin->saveConfig('table_glines',$_POST['table_glines']); }
	if (isset($_POST['table_sqlines'])) { $admin->saveConfig('table_sqlines',$_POST['table_sqlines']); }
	if (isset($_POST['table_maxvalues'])) { $admin->saveConfig('table_maxvalues',$_POST['table_maxvalues']); }
	if (isset($_POST['table_server'])) { $admin->saveConfig('table_server',$_POST['table_server']); }
	if (isset($_POST['table_ison'])) { $admin->saveConfig('table_ison',$_POST['table_ison']); }
	if (isset($_POST['table_tld'])) { $admin->saveConfig('table_tld',$_POST['table_tld']); }
	if (isset($_POST['table_cstats'])) { $admin->saveConfig('table_cstats',$_POST['table_cstats']); }
	if (isset($_POST['table_ustats'])) { $admin->saveConfig('table_ustats',$_POST['table_ustats']); }
	if (isset($_POST['table_current'])) { $admin->saveConfig('table_current',$_POST['table_current']); }
	if (isset($_POST['table_serverstats'])) { $admin->saveConfig('table_serverstats',$_POST['table_serverstats']); }
	if (isset($_POST['table_channelstats'])) { $admin->saveConfig('table_channelstats',$_POST['table_channelstats']); }
	if (isset($_POST['table_userstats'])) { $admin->saveConfig('table_userstats',$_POST['table_userstats']); }
	if (isset($_POST['table_aliases'])) { $admin->saveConfig('table_aliases',$_POST['table_aliases']); }
}

$admin->tpl->assign('config', $admin->cfg->config);
$admin->tpl->display('advanced.tpl');
?>