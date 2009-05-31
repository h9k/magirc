<?php
// $Id: behavior.php 311 2007-07-30 20:17:06Z Hal9000 $

if (isset($_POST['button'])) {
	if (isset($_POST['chanstats_sort'])) { $admin->saveConfig('chanstats_sort',$_POST['chanstats_sort']); }
	if (isset($_POST['chanstats_type'])) { $admin->saveConfig('chanstats_type',$_POST['chanstats_type']); }
	if (isset($_POST['list_limit'])) { $admin->saveConfig('list_limit',$_POST['list_limit']); }
	if (isset($_POST['top_limit'])) { $admin->saveConfig('hide_secret',$_POST['top_limit']); }
	if (isset($_POST['search_min_chars'])) { $admin->saveConfig('search_min_chars',$_POST['search_min_chars']); }
}

$admin->tpl->assign('config', $admin->cfg->config);
$admin->tpl->display('behavior.tpl');
?>