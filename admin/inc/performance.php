<?php


if (isset($_POST['form'])) {
	if (isset($_POST['graph_cache'])) $admin->saveConfig('graph_cache',1);
	else $admin->saveConfig('graph_cache',0);
	if (isset($_POST['graph_cache_path'])) $admin->saveConfig('graph_cache_path',$_POST['graph_cache_path']);
	else $admin->saveConfig('graph_cache_path','');
	if (isset($_POST['net_cache_time'])) $admin->saveConfig('net_cache_time',$_POST['net_cache_time']);
	else $admin->saveConfig('net_cache_time','');
	if (isset($_POST['pie_cache_time'])) $admin->saveConfig('pie_cache_time',$_POST['pie_cache_time']);
	else $admin->saveConfig('pie_cache_time','');
	if (isset($_POST['bar_cache_time'])) $admin->saveConfig('bar_cache_time',$_POST['bar_cache_time']);
	else $admin->saveConfig('bar_cache_time','');
	if (isset($_POST['gzip'])) $admin->saveConfig('gzip',1);
	else $admin->saveConfig('gzip',0);
	$admin->tpl->assign('success', true);
}

$admin->tpl->display('performance.tpl');
?>