<?php
// $Id$

$this->tpl->assign('err_msg', 'HTTP error '.$this->getUrlParameter('action'));
$this->tpl->assign('server', $_SERVER); // dunno why i need this, but $smarty.server seems not to work...
$this->tpl->display('error.tpl');

?>