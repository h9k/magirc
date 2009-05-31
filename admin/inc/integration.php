<?php
// $Id: integration.php 315 2007-08-18 11:41:28Z Hal9000 $

if (isset($_POST['button'])) {
	if (isset($_POST['netsplit_id'])) { $admin->saveConfig('netsplit_id',$_POST['netsplit_id']); }
	else { $admin->saveConfig('netsplit_id',''); }
	if (isset($_POST['netsplit_graphs'])) { $admin->saveConfig('netsplit_graphs',1); }
	else { $admin->saveConfig('netsplit_graphs',0); }
	if (isset($_POST['netsplit_years'])) { $admin->saveConfig('netsplit_years',1); }
	else { $admin->saveConfig('netsplit_years',0); }
	if (isset($_POST['netsplit_history'])) { $admin->saveConfig('netsplit_history',1); }
	else { $admin->saveConfig('netsplit_history',0); }
	if (isset($_POST['searchirc_id'])) { $admin->saveConfig('searchirc_id',$_POST['searchirc_id']); }
	else { $admin->saveConfig('searchirc_id',''); }
	if (isset($_POST['searchirc_ranking'])) { $admin->saveConfig('searchirc_ranking',1); }
	else { $admin->saveConfig('searchirc_ranking',0); }
	if (isset($_POST['searchirc_graphs'])) { $admin->saveConfig('searchirc_graphs',1); }
	else { $admin->saveConfig('searchirc_graphs',0); }
	if (isset($_POST['adsense'])) { $admin->saveConfig('adsense',1); }
	else { $admin->saveConfig('adsense',0); }
	if (isset($_POST['adsense_id'])) { $admin->saveConfig('adsense_id',$_POST['adsense_id']); }
	else { $admin->saveConfig('adsense_id',''); }
	if (isset($_POST['adsense_channel'])) { $admin->saveConfig('adsense_channel',$_POST['adsense_channel']); }
	else { $admin->saveConfig('adsense_channel',''); }
}

$admin->tpl->assign('config', $admin->cfg->config);
$admin->tpl->display('integration.tpl');
?>