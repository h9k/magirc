<?php


$magirc_url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$magirc_url = explode("admin/",$magirc_url);

$admin->tpl->assign('magirc_url', $magirc_url[0]);
$admin->tpl->display('registration.tpl');
?>