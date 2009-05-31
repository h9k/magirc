<?php
// $Id: advanced.php 308 2007-07-24 17:09:00Z Hal9000 $

$db_config_file = '../conf/denora.cfg.php';
include($db_config_file);

if (isset($_POST['button'])) {
	$db_username = (isset($_POST['username'])) ? $_POST['username'] : $db['username'];
	$db_password = (isset($_POST['password'])) ? $_POST['password'] : $db['password'];
	$db_db_name = (isset($_POST['database'])) ? $_POST['database'] : $db['database'];
	$db_hostname = (isset($_POST['hostname'])) ? $_POST['hostname'] : $db['hostname'];
	$db_port = (isset($_POST['port'])) ? $_POST['port'] : $db['port'];
	$db_buffer = "<?php
defined('_VALID_PARENT') or header(\"Location: ../\");
\$db['username'] = \"$db_username\";
\$db['password'] = \"$db_password\";
\$db['db_name'] = \"$db_db_name\";
\$db['hostname'] = \"$db_hostname\";
\$db['port'] = \"$db_port\";
?>";
	if (is_writable($db_config_file)) {
		$writefile = fopen($db_config_file,"w");
		fwrite($writefile,$db_buffer);
		fclose($writefile);
		echo "<div class=\"configsave\">Configuration saved</div>";
	} else {
		echo "<pre><strong>Please copy the following text and paste it into the $db_config_file file</strong></pre>";
		echo "<textarea name=\"sql_buffer\" cols=\"64\" rows=\"10\" readonly=\"readonly\">$db_buffer</textarea>";
	}
}

$admin->tpl->assign('db_config_file', $db_config_file);
$admin->tpl->assign('writable', is_writable($db_config_file));
$admin->tpl->assign('db', $db);
$admin->tpl->display('database.tpl');
?>