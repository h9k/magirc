<?php
// $Id: status.inc.php 34 2009-06-01 11:10:30Z hal9000 $

$server = new Server($_GET['server']);

$magirc->tpl->assign('serverlist', $magirc->denora->getServers());
$magirc->tpl->assign('server', $server);
$magirc->tpl->display('server.tpl');

?>