<?php
/**
 * MagIRC - Let the magirc begin!
 * Admin panel
 *
 * @author      Sebastian Vassiliou <hal9000@magirc.org>
 * @copyright   2012 - 2019 Sebastian Vassiliou
 * @link        http://www.magirc.org/
 * @license     GNU GPL Version 3, see http://www.gnu.org/licenses/gpl-3.0-standalone.html
 * @version     1.7.0
 */

ini_set('display_errors','on');
error_reporting(E_ALL);
ini_set('default_charset','UTF-8');
date_default_timezone_set('UTC');

if (version_compare(PHP_VERSION, '5.6.0', '<')
    || !extension_loaded('pdo')
    || !in_array('mysql', PDO::getAvailableDrivers())
    || !extension_loaded('gettext')
    || !extension_loaded('xml'))
    die('ERROR: System requirements not met. Please run Setup.');
if (!file_exists('../conf/magirc.cfg.php'))
    die('ERROR: MagIRC is not configured. Please run Setup.');
if (!is_writable('../tmp/'))
    die('ERROR: Unable to write temporary files. Please run Setup.');

session_start();

include_once(__DIR__.'/../lib/magirc/version.inc.php');
if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    require __DIR__.'/../vendor/autoload.php';
} else {
    die('Please run the `composer install` command to install library dependencies. See README for more information.');
}
if (!file_exists(__DIR__.'/../js/vendor/')) {
    die('Please run the `bower install` command to install script dependencies. See README for more information.');
}
require_once(__DIR__.'/../lib/magirc/DB.class.php');
require_once(__DIR__.'/../lib/magirc/Config.class.php');
require_once(__DIR__.'/lib/Admin.class.php');

$admin = new Admin();

date_default_timezone_set($admin->cfg->timezone);
define('DEBUG', $admin->cfg->debug_mode);
define('BASE_URL', $admin->cfg->base_url . '/' . basename(__DIR__) . '/');
if ($admin->cfg->db_version < DB_VERSION)
    die('SQL Config Table is missing or out of date!<br />Please run the <em>MagIRC Installer</em>');
if ($admin->cfg->debug_mode < 1) {
    ini_set('display_errors','off');
    error_reporting(E_ERROR);
}

// Handle POST login/logout
$admin->slim->post('/login', function($req, $res, $args) use ($admin) {
    if ($admin->login($_POST['username'], $_POST['password'])) {
        return $res->withStatus(301)->withHeader('Location', BASE_URL.'index.php/overview');
    } else {
        return $res->withStatus(301)->withHeader('Location', BASE_URL);
    }
});
$admin->slim->post('/ajaxlogin', function($req, $res, $args) use ($admin) {
    echo json_encode($admin->login($_POST['username'], $_POST['password']));
    return $res->withHeader('Content-Type', 'application/json');
});
$admin->slim->post('/logout', function($req, $res, $args) use ($admin) {
    // Unset session variables
    if (isset($_SESSION["username"]))
        unset($_SESSION["username"]);
    // Delete the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    // Destroy the session
    session_destroy();
    // Redirect to login screen
    return $res->withStatus(301)->withHeader('Location', BASE_URL);
});

$admin->slim->get('/[overview]', function($req, $res, $args) use ($admin) {
    if (!$admin->sessionStatus()) {
        $this->view->render($res, 'login.twig', []);
    } else {
        $this->view->render($res, 'overview.twig', [
            'cfg' => $admin->cfg->config,
            'section' => 'overview',
            'setup' => file_exists('../setup/'),
            'version' => array('php' => phpversion(), 'slim' => Slim\App::VERSION),
            'username' => $_SESSION['username']
        ]);
    }
});
$admin->slim->get('/configuration/welcome', function($req, $res, $args) use ($admin) {
    if (!$admin->sessionStatus()) {
        return $res->withStatus(403)->write('HTTP 403 Access Denied');
    }
    $this->view->render($res, 'configuration_welcome.twig', [
        'cfg' => $admin->cfg->config,
        'content' => $admin->getContent('welcome')
    ]);
});
$admin->slim->get('/configuration/interface', function($req, $res, $args) use ($admin) {
    if (!$admin->sessionStatus()) {
        return $res->withStatus(403)->write('HTTP 403 Access Denied');
    }
    $locales = array();
    foreach (glob("../locale/*") as $filename) {
        if (is_dir($filename)) $locales[] = basename($filename);
    }
    $themes = array();
    foreach (glob("../theme/*") as $filename) {
        $themes[] = basename($filename);
    }

    $this->view->render($res, 'configuration_interface.twig', [
        'cfg' => $admin->cfg->config,
        'locales' => $locales,
        'themes' => $themes,
        'timezones' => DateTimeZone::listIdentifiers(),
    ]);
});
$admin->slim->get('/configuration/network', function($req, $res, $args) use ($admin) {
    if (!$admin->sessionStatus()) {
        return $res->withStatus(403)->write('HTTP 403 Access Denied');
    }
    $ircds = array();
    foreach (glob("../lib/magirc/ircds/*") as $filename) {
        if ($filename != "../lib/magirc/ircds/index.php") {
            $ircdlist = explode("/", $filename);
            $ircdlist = explode(".", $ircdlist[4]);
            $ircds[] = $ircdlist[0];
        }
    }
    $this->view->render($res, 'configuration_network.twig', [
        'cfg' => $admin->cfg->config,
        'ircds' => $ircds
    ]);
});
$admin->slim->get('/configuration/service/{service}', function($req, $res, $args) use ($admin) {
    if (!$admin->sessionStatus()) {
        return $res->withStatus(403)->write('HTTP 403 Access Denied');
    }
    $db_config_file = __DIR__."/../conf/{$args['service']}.cfg.php";
    $db = array();
    if (file_exists($db_config_file)) {
        include($db_config_file);
    } else {
        @touch($db_config_file);
    }
    if (!$db) {
        $db = array(
            'username' => $args['service'],
            'password' => $args['service'],
            'database' => $args['service'],
            'prefix' => ($args['service'] == "anope") ? "anope_" : null,
            'hostname' => 'localhost'
        );
    }

    $this->view->render($res, 'configuration_service.twig', [
        'cfg' => $admin->cfg->config,
        'db_config_file' => $db_config_file,
        'writable' => is_writable($db_config_file),
        'db' => $db,
        'service' => $args['service']
    ]);
});
$admin->slim->post('/content', function($req, $res, $args) use ($admin) {
    if (!$admin->sessionStatus()) {
        return $res->withStatus(403)->write('HTTP 403 Access Denied');
    }
    foreach ($_POST as $key => $val) {
        $admin->saveContent($key, $val);
    }
    echo json_encode(true);
    return $res->withHeader('Content-Type', 'application/json');
});
$admin->slim->post('/configuration', function($req, $res, $args) use ($admin) {
    if (!$admin->sessionStatus()) {
        return $res->withStatus(403)->write('HTTP 403 Access Denied');
    }
    foreach ($_POST as $key => $val) {
        if ($key == 'base_url') {
            $val = (substr($val, -1) == "/") ? substr($val, 0, -1) : $val;
        }
        $admin->saveConfig($key, $val);
    }
    echo json_encode(true);
    return $res->withHeader('Content-Type', 'application/json');
});
$admin->slim->post('/configuration/{service}/database', function($req, $res, $args) use ($admin) {
    if (!$admin->sessionStatus()) {
        return $res->withStatus(403)->write('HTTP 403 Access Denied');
    }
    $db_config_file = __DIR__."/../conf/{$args['service']}.cfg.php";
    $db = array();
    if (file_exists($db_config_file)) {
        include($db_config_file);
    } else {
        @touch($db_config_file);
    }
    if (!$db) {
        $db = array(
            'username' => $args['service'],
            'password' => $args['service'],
            'database' => $args['service'],
            'prefix' => null,
            'hostname' => 'localhost',
            'port' => 3306,
            'ssl' => false,
            'ssl_key' => null,
            'ssl_cert' => null,
            'ssl_ca' => null
        );
        if ($args['service'] == "anope") {
            $db['prefix'] = "anope_";
        }
    }
    if (isset($_POST['database'])) {
        $fields = array('username', 'password', 'database', 'hostname', 'port', 'ssl', 'ssl_key', 'ssl_cert', 'ssl_ca');
        if ($args['service'] == "anope") {
            $fields[] = 'prefix';
        } elseif ($args['service'] == "denora") {
            $fields = array_merge($fields, array('current', 'maxvalues', 'user', 'server', 'stats', 'channelstats', 'serverstats', 'ustats', 'cstats', 'chan', 'ison', 'aliases'));
        }
        foreach ($fields as $field) {
            $db[$field] = (isset($_POST[$field])) ? addslashes(trim($_POST[$field])) : $db[$field];
        }
        $db['ssl'] = isset($_POST['ssl']) ? 'true' : 'false';

        $db_buffer = "<?php\n\$db = array(\n";
        foreach ($db as $key => $val) {
            $db_buffer .= "    '{$key}' => '{$val}',\n";
        }
        $db_buffer .= ");\n";

        if (is_writable($db_config_file)) {
            $writefile = fopen($db_config_file,"w");
            fwrite($writefile,$db_buffer);
            fclose($writefile);
            echo json_encode(true);
        }
    }
    echo json_encode(false);
    return $res->withHeader('Content-Type', 'application/json');
});
$admin->slim->get('/support/register', function($req, $res, $args) use ($admin) {
    if (!$admin->sessionStatus()) {
        return $res->withStatus(403)->write('HTTP 403 Access Denied');
    }
    $magirc_url = (@$_SERVER['HTTPS'] ? 'https' : 'http') .'://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $magirc_url = explode("admin/",$magirc_url);
    $this->view->render($res, 'support_register.twig', [
        'cfg' => $admin->cfg->config,
        'magirc_url' => $magirc_url[0]
    ]);
});
$admin->slim->get('/support/doc/{file}', function($req, $res, $args) use ($admin) {
    if (!$admin->sessionStatus()) {
        return $res->withStatus(403)->write('HTTP 403 Access Denied');
    }
    $path = ($args['file'] == 'readme') ? '../README.md' : '../doc/'.basename($args['file']).'.md';
    if (is_file($path)) {
        $text = file_get_contents($path);
    } else {
        $text = "ERROR: Specified documentation file not found";
    }
    $this->view->render($res, 'support_markdown.twig', [
        'cfg' => $admin->cfg->config,
        'text' => $text
    ]);
});
$admin->slim->get('/admin/list', function($req, $res, $args) use ($admin) {
    if (!$admin->sessionStatus()) {
        return $res->withStatus(403)->write('HTTP 403 Access Denied');
    }
    $admin->db->query("SELECT username, realname, email FROM magirc_admin", SQL_ALL, SQL_ASSOC);
    echo json_encode(array('aaData' => $admin->db->record));
    return $res->withHeader('Content-Type', 'application/json');
});
$admin->slim->get('/{section}[/{action}]', function($req, $res, $args) use ($admin) {
    $action = isset($args['action']) ? $args['action'] : "main";
    if (!$admin->sessionStatus()) {
        $this->view->render($res, 'login.twig', []);
    } else {
        $tpl_file = basename($args['section']) . '_' . basename($action) . '.twig';
        $tpl_path = 'tpl/' . $tpl_file;
        if (file_exists($tpl_path)) {
            $this->view->render($res, $tpl_file, [
                'cfg' => $admin->cfg->config,
                'section' => $args['section'],
            ]);
        } else {
            $this->view->render($res, "error.twig", [
                'cfg' => $admin->cfg->config,
                'err_code' => 404,
            ]);
            return $res->withStatus(404);
        }
    }
});

$admin->slim->run();
