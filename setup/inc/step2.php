<?php
$status = $setup->requirementsCheck();
if ($status['error']) die('Failure. <a href="?step=1">back</a>');

// Handle saving of the database configuration
$db_config = $setup->saveConfig();

// Check Magirc Database connection
if (file_exists(MAGIRC_CFG_FILE)) {
    include(MAGIRC_CFG_FILE);
    if (isset($db) && is_array($db)) {
        $setup->db = Magirc_DB::getInstance();
        $status['error'] = $setup->db->error;
    } else {
        $status['error'] = "Invalid configuration file";
    }
} else {
    $status['error'] = 'new';
}

// Handle database initialization/upgrade
$dump = $check = $updated = false;
if (!$status['error']) {
    $check = $setup->configCheck();
    if (!$check) { // Dump sql file to db
        $dump = $setup->configDump();
        $base_url = $setup->generateBaseUrl();
        $setup->db->update('magirc_config', array('value' => $base_url), array('parameter' => 'base_url'));
    } else { // Upgrade db
        $updated = $setup->configUpgrade();
    }
}

$template = $setup->tpl->loadTemplate('step2.twig');
echo $template->render(array(
    'step' => 2,
    'magirc_conf' => MAGIRC_CFG_FILE,
    'status' => $status,
    'dump' => $dump,
    'updated' => $updated,
    'version' => DB_VERSION,
    'check' => $check,
    'db_magirc' => file_exists(MAGIRC_CFG_FILE) ? @$db : array('username' => '', 'password' => '', 'database' => '', 'hostname' => 'localhost', 'port' => 3306, 'ssl' => false, 'ssl_key' => null, 'ssl_cert' => null, 'ssl_ca' => null),
    'db_config' => $db_config,
    'savedb' => isset($_POST['savedb'])
));
