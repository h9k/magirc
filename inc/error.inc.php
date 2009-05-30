<?php
// $Id$

$magirc->tpl->assign('err_msg', 'HTTP error '.$magirc->getUrlParameter('action'));
$magirc->tpl->assign('server', $_SERVER); // dunno why i need this, but $smarty.server seems not to work...
$magirc->tpl->display('error.tpl');

?>