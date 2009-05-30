<?php
// $Id$

$magirc->tpl->assign('err_msg', 'HTTP error '.$magirc->getUrlParameter('action'));
$magirc->tpl->display('error.tpl');

?>