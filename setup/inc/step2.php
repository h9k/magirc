<?php
// $Id$

$config['table_server'] = (isset($_GET['table_server'])) ? $_GET['table_server'] : 'server';
$config['debug_mode'] = 0;
$config['show_exec_time'] = 0;

$setup->tpl->assign('denoraver', $setup->denora->getVersion('full'));
$setup->tpl->assign('denoranum', $setup->denora->getVersion('num'));
$setup->tpl->display('step2.tpl');
?>