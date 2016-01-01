<?php
$status = $setup->requirementsCheck();
if ($status['error']) die('Failure. <a href="?step=1">back</a>');

$success = true;
if (isset($_POST['username']) && isset($_POST['password'])) {
	$ps = $setup->db->prepare("INSERT INTO `magirc_admin` SET `username` = :username, `password` = MD5(:password)");
	$ps->bindParam(':username', $_POST['username'], PDO::PARAM_STR);
	$ps->bindParam(':password', $_POST['password'], PDO::PARAM_STR);
	$success = $ps->execute();
}

$template = $setup->tpl->loadTemplate('step3.twig');
echo $template->render(array(
	'step' => 3,
	'admins' => $setup->checkAdmins(),
	'error' => !$success
));
