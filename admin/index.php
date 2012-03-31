<?php
/**
 * MagIRC - Let the magirc begin!
 * Admin panel
 *
 * @author      Sebastian Vassiliou <hal9000@denorastats.org>
 * @copyright   2012 Sebastian Vassiliou
 * @link        http://www.magirc.org/
 * @license     GNU GPL Version 3, see http://www.gnu.org/licenses/gpl-3.0-standalone.html
 * @version     0.7.0
 */

ini_set('display_errors','on');
error_reporting(E_ALL);
ini_set('default_charset','UTF-8');
if (get_magic_quotes_gpc()) die('Disable magic_quotes_gpc in your php.ini');

session_start();

if (!file_exists('../conf/magirc.cfg.php')) die('Please configure conf/magirc.cfg.dist.php and rename it to conf/magirc.cfg.php');
if (!is_writable('tmp/')) die("The 'admin/tmp/' directory is not writable. Please chmod it to 0777.");

include_once('../lib/magirc/version.inc.php');
require_once('../lib/slim/Slim.php');
require_once('../lib/smarty/Smarty.class.php');
require_once('../lib/magirc/DB.class.php');
require_once('../lib/magirc/Config.class.php');
include_once('../lib/ckeditor/ckeditor.php');
require_once('lib/Admin.class.php');

define('BASE_URL', sprintf("%s://%s%s", @$_SERVER['HTTPS'] ? 'https' : 'http', $_SERVER['SERVER_NAME'], str_replace('index.php', '', $_SERVER['SCRIPT_NAME'])));
$admin = new Admin();

try {
	if ($admin->cfg->getParam('db_version') < 1) $admin->slim->halt(500, 'SQL Config Table is missing or out of date!<br />Please run the <em>MagIRC Installer</em>');
	define('DEBUG', $admin->cfg->getParam('debug_mode'));
	$admin->tpl->assign('cfg', $admin->cfg->config);
	if ($admin->cfg->getParam('debug_mode') < 1) {
		ini_set('display_errors','off');
		error_reporting(E_ERROR);
	} else {
		$admin->tpl->force_compile = true;
		/*if ($admin->cfg->getParam('debug_mode') > 1) {
			$admin->tpl->debugging = true;
		}*/
	}

	$admin->slim->notFound(function () use ($admin) {
		$admin->tpl->assign('err_msg', 'HTTP 404 - Not Found');
		$admin->tpl->display('error.tpl');
	});

	// Handle POST login/logout
	$admin->slim->post('/login', function() use ($admin) {
		$admin->slim->contentType('application/json');
		echo json_encode($admin->login($_POST['username'], $_POST['password']));
	});
	$admin->slim->post('/logout', function() use ($admin) {
		// Unset session variables
		if (isset($_SESSION["username"])) unset($_SESSION["username"]);
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
		$admin->slim->redirect(BASE_URL);
	});

	$admin->slim->get('/(overview)', function() use ($admin) {
		if (!$admin->sessionStatus()) { $admin->tpl->display('login.tpl'); exit; }
		$admin->tpl->assign('section', 'overview');
		$admin->tpl->assign('setup', file_exists('../setup/'));
		$admin->tpl->assign('version', array('php' => phpversion(), 'sql_client' => @mysqli_get_client_info(), 'slim' => '1.5.0'));
		$admin->tpl->display('overview.tpl');
	});
	$admin->slim->get('/denora/settings', function() use ($admin) {
		if (!$admin->sessionStatus()) { $admin->slim->halt(403, "HTTP 403 Access Denied"); }
		$ircds = array();
		foreach (glob("../lib/magirc/denora/protocol/*") as $filename) {
			if ($filename != "../lib/magirc/denora/protocol/index.php") {
				$ircdlist = explode("/", $filename);
				$ircdlist = explode(".", $ircdlist[5]);
				$ircds[] = $ircdlist[0];
			}
		}
		$admin->tpl->assign('ircds', $ircds);
		$admin->tpl->display('denora_settings.tpl');
	});
	$admin->slim->post('/denora/settings', function() use ($admin) {
		if (!$admin->sessionStatus()) { $admin->slim->halt(403, "HTTP 403 Access Denied"); }
		$admin->slim->contentType('application/json');
		foreach ($_POST as $key => $val) {
			$admin->saveConfig($key, $val);
		}
		echo json_encode(true);
	});
	$admin->slim->get('/denora/welcome', function() use ($admin) {
		if (!$admin->sessionStatus()) { $admin->slim->halt(403, "HTTP 403 Access Denied"); }
		$admin->tpl->assign('editor', $admin->ckeditor->editor('msg_welcome', $admin->cfg->getParam('msg_welcome')));
		$admin->tpl->display('denora_welcome.tpl');
	});
	$admin->slim->get('/denora/database', function() use ($admin) {
		if (!$admin->sessionStatus()) { $admin->slim->halt(403, "HTTP 403 Access Denied"); }
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
		$admin->tpl->assign('db_config_file', $db_config_file);
		$admin->tpl->assign('writable', is_writable($db_config_file));
		$admin->tpl->assign('db', $db);
		$admin->tpl->display('denora_database.tpl');
	});
	$admin->slim->post('/:service/database', function($service) use ($admin) {
		if (!$admin->sessionStatus()) { $admin->slim->halt(403, "HTTP 403 Access Denied"); }
		$admin->slim->contentType('application/json');
		$db_config_file = "../conf/$service.cfg.php";
		$db = array();
		if (file_exists($db_config_file)) {
			include($db_config_file);
		} else {
			@touch($db_config_file);
		}
		if (!$db) {
			$db = array('username' => 'magirc', 'password' => 'magirc', 'database' => 'magirc', 'hostname' => 'localhost');
		}
		if (isset($_POST['database'])) {
			$db['username'] = (isset($_POST['username'])) ? $_POST['username'] : $db['username'];
			$db['password'] = (isset($_POST['password'])) ? $_POST['password'] : $db['password'];
			$db['database'] = (isset($_POST['database'])) ? $_POST['database'] : $db['database'];
			$db['hostname'] = (isset($_POST['hostname'])) ? $_POST['hostname'] : $db['hostname'];
			$db['port'] = (isset($_POST['port'])) ? $_POST['port'] : $db['port'];
			$db_buffer = "<?php\n".
				"\$db['username'] = \"{$db['username']}\";\n".
				"\$db['password'] = \"{$db['password']}\";\n".
				"\$db['database'] = \"{$db['database']}\";\n".
				"\$db['hostname'] = \"{$db['hostname']}\";\n".
				"\$db['port'] = \"{$db['port']}\";\n".
				"?>";
			if (is_writable($db_config_file)) {
				$writefile = fopen($db_config_file,"w");
				fwrite($writefile,$db_buffer);
				fclose($writefile);
				echo json_encode(true); exit;
			}
		}
		echo json_encode(false);
	});
	$admin->slim->get('/support/register', function() use ($admin) {
		if (!$admin->sessionStatus()) { $admin->slim->halt(403, "HTTP 403 Access Denied"); }
		$magirc_url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$magirc_url = explode("admin/",$magirc_url);
		$admin->tpl->assign('magirc_url', $magirc_url[0]);
		$admin->tpl->display('support_register.tpl');
	});
	$admin->slim->get('/support/doc/:file', function($file) use ($admin) {
		if (!$admin->sessionStatus()) { $admin->slim->halt(403, "HTTP 403 Access Denied"); }
		$path = ($file == 'readme') ? '../README.md' : '../doc/'.basename($file).'.md';
		if (is_file($path)) {
			$text = file_get_contents($path);
			$admin->tpl->assign('text', $text);
		} else {
			$admin->tpl->assign('text', "ERROR: Specified documentation file not found");
		}
		$admin->tpl->display('support_markdown.tpl');
	});
	$admin->slim->get('/:section(/:action)', function($section, $action = 'main') use ($admin) {
		if (!$admin->sessionStatus()) { $admin->tpl->display('login.tpl'); exit; }
		$tpl_file = basename($section) . '_' . basename($action) . '.tpl';
		$tpl_path = 'tpl/' . $tpl_file;
		if (file_exists($tpl_path)) {
			$admin->tpl->assign('section', $section);
			$admin->tpl->display($tpl_file);
		} else {
			$admin->slim->notFound();
		}
	});

	$admin->slim->run();

} catch (Exception $e) {
	$admin->tpl->assign('err_msg', $e->getMessage());
	$admin->tpl->assign('err_extra', $e->getTraceAsString());
	$admin->tpl->display('error.tpl');
}

?>