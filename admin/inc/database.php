<?php
// $Id$

$service = isset($_GET['service']) ? basename($_GET['service']) : 'denora';
$db_config_file = "../conf/{$service}.cfg.php";
$db = array();
if (file_exists($db_config_file)) {
    include($db_config_file);
} else {
    @touch($db_config_file);
}
if (!$db) {
    $db = array('username' => 'magirc', 'password' => 'magirc', 'database' => 'magirc', 'hostname' => 'localhost');
}

if (isset($_POST['form'])) {
    $db['username'] = (isset($_POST['username'])) ? $_POST['username'] : $db['username'];
    $db['password'] = (isset($_POST['password'])) ? $_POST['password'] : $db['password'];
    $db['database'] = (isset($_POST['database'])) ? $_POST['database'] : $db['database'];
    $db['hostname'] = (isset($_POST['hostname'])) ? $_POST['hostname'] : $db['hostname'];
    $db['port'] = (isset($_POST['port'])) ? $_POST['port'] : $db['port'];
    $db_buffer = "<?php
\$db['username'] = \"{$db['username']}\";
\$db['password'] = \"{$db['password']}\";
\$db['database'] = \"{$db['database']}\";
\$db['hostname'] = \"{$db['hostname']}\";
\$db['port'] = \"{$db['port']}\";
?>";
    if (is_writable($db_config_file)) {
        $writefile = fopen($db_config_file,"w");
        fwrite($writefile,$db_buffer);
        fclose($writefile);
        $admin->tpl->assign('success', true);
    } else {
        $admin->tpl->assign('db_buffer', $db_buffer);
    }
}

$admin->tpl->assign('db_config_file', $db_config_file);
$admin->tpl->assign('writable', is_writable($db_config_file));
$admin->tpl->assign('db', $db);
$admin->tpl->display('database.tpl');
?>