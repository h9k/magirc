<?php

$template = $setup->tpl->loadTemplate('step1.twig');
echo $template->render(array(
    'step' => 1,
    'phpversion' => phpversion(),
    'status' => $setup->requirementsCheck()
));
