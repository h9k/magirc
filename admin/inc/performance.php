<?php
// $Id: performance.php 311 2007-07-30 20:17:06Z Hal9000 $

if (isset($_POST['button'])) {
	if (isset($_POST['graph_cache'])) { $admin->saveConfig('graph_cache',1); }
	else { $admin->saveConfig('graph_cache',0); }
	if (isset($_POST['graph_cache_path'])) { $admin->saveConfig('graph_cache_path',$_POST['graph_cache_path']); }
	else { $admin->saveConfig('graph_cache_path',''); }
	if (isset($_POST['net_cache_time'])) { $admin->saveConfig('net_cache_time',$_POST['net_cache_time']); }
	else { $admin->saveConfig('net_cache_time',''); }
	if (isset($_POST['pie_cache_time'])) { $admin->saveConfig('pie_cache_time',$_POST['pie_cache_time']); }
	else { $admin->saveConfig('pie_cache_time',''); }
	if (isset($_POST['bar_cache_time'])) { $admin->saveConfig('bar_cache_time',$_POST['bar_cache_time']); }
	else { $admin->saveConfig('bar_cache_time',''); }
	if (isset($_POST['gzip'])) { $admin->saveConfig('gzip',1); }
	else { $admin->saveConfig('gzip',0); }
}

$admin->tpl->assign('config', $admin->cfg->config);
$admin->tpl->display('performance.tpl');
?>