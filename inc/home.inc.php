<?php
// $Id$

$this->tpl->assign('welcome', $this->cfg->getParam('msg_welcome'));
$this->tpl->display('home.tpl');

?>