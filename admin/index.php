<?php
/**
 * MagIRC - Let the magirc begin!
 * Admin panel
 *
 * @author      Sebastian Vassiliou <hal9000@denorastats.org>
 * @copyright   2012 Sebastian Vassiliou
 * @link        http://www.magirc.org/
 * @license     GNU GPL Version 3, see doc/LICENSE
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

$admin = new Admin();

try {
	if ($admin->cfg->getParam('db_version') < 1) $admin->slim->halt(500, 'SQL Config Table is missing or out of date!<br />Please run the <em>MagIRC Installer</em>');
	define('DEBUG', $admin->cfg->getParam('debug_mode'));
	define('BASE_URL', sprintf("%s://%s%s", @$_SERVER['HTTPS'] ? 'https' : 'http', $_SERVER['SERVER_NAME'], $_SERVER['SCRIPT_NAME']));
	$admin->tpl->assign('cfg', $admin->cfg->config);
	if ($admin->cfg->getParam('debug_mode') < 1) {
		ini_set('display_errors','off');
		error_reporting(E_ERROR);
	} else {
		$admin->tpl->force_compile = true;
		if ($admin->cfg->getParam('debug_mode') > 1) {
			$admin->tpl->debugging = true;
		}
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
		#$denora_version = @$admin->denora->getVersion('num');
		if (!@$denora_version) $denora_version = "unknown";
		$version = array(
				'denora' => $denora_version,
				'php' => phpversion(),
				'sql_client' => @mysqli_get_client_info()
		);
		$admin->tpl->assign('setup', file_exists('../setup/'));
		$admin->tpl->assign('version', $version);
		$admin->tpl->display('overview_main.tpl');
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