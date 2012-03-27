<?php


if (isset($_POST['form'])) {
	if (isset($_POST['status_lookup'])) $admin->saveConfig('status_lookup',1);
	else $admin->saveConfig('status_lookup',0);
	if (isset($_POST['tld_stats'])) $admin->saveConfig('tld_stats',1);
	else $admin->saveConfig('tld_stats',0);
	if (isset($_POST['client_stats'])) $admin->saveConfig('client_stats',1);
	else $admin->saveConfig('client_stats',0);
	if (isset($_POST['net_graphs'])) $admin->saveConfig('net_graphs',1);
	else $admin->saveConfig('net_graphs',0);
	if (isset($_POST['mirc']) && isset($_POST['mirc_url'])) {
		$admin->saveConfig('mirc',1);
		$admin->saveConfig('mirc_url',$_POST['mirc_url']);
	} else {
		$admin->saveConfig('mirc',0);
		$admin->saveConfig('mirc_url','');
	}
	if (isset($_POST['webchat']) && isset($_POST['webchat_url'])) {
		$admin->saveConfig('webchat',1);
		$admin->saveConfig('webchat_url',$_POST['webchat_url']);
	} else {
		$admin->saveConfig('webchat',0);
		$admin->saveConfig('webchat_url','');
	}
	if (isset($_POST['remote'])) $admin->saveConfig('remote',1);
	else $admin->saveConfig('remote',0);

	$admin->tpl->assign('success', true);
}

$admin->tpl->display('features.tpl');
?>