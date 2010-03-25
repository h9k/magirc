<?php
// $Id$

if (isset($_POST['form'])) {
    if (isset($_POST['net_name'])) $admin->saveConfig('net_name',$_POST['net_name']);
    if (isset($_POST['net_url'])) $admin->saveConfig('net_url',$_POST['net_url']);
    if (isset($_POST['theme'])) $admin->saveConfig('theme',$_POST['theme']);
    if (isset($_POST['lang'])) $admin->saveConfig('lang',$_POST['lang']);
    if (isset($_POST['msg_welcome'])) $admin->saveConfig('msg_welcome',$_POST['msg_welcome']);
    else $admin->saveConfig('msg_welcome','');
    $admin->tpl->assign('success', true);
}

$admin->tpl->assign('editor', $admin->ckeditor->editor('msg_welcome', $admin->cfg->getParam('msg_welcome')));
$admin->tpl->assign('config', $admin->cfg->config);
$admin->tpl->display('general.tpl');
?>