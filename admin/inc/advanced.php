<?php
// $Id$

if (isset($_POST['form'])) {
    if (isset($_POST['debug_mode'])) $admin->saveConfig('debug_mode',$_POST['debug_mode']);
    if (isset($_POST['show_exec_time'])) $admin->saveConfig('show_exec_time',1);
    else $admin->saveConfig('show_exec_time',0);
    if (isset($_POST['show_validators'])) $admin->saveConfig('show_validators',1);
    else $admin->saveConfig('show_validators',0);
    $admin->tpl->assign('success', true);
}

$admin->tpl->display('advanced.tpl');
?>