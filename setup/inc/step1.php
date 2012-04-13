<?php

$setup->tpl->assign('phpversion', phpversion());
$setup->tpl->assign('status', $setup->requirementsCheck());
$setup->tpl->display('step1.tpl');

?>