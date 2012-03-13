<?php
// $Id$

// Root path
define('PATH_ROOT', __DIR__ . '/../../');

// database configuration
class Magirc_DB extends DB {
	function __construct() {
		parent::__construct();
		$error = false;
		if (file_exists(PATH_ROOT.'conf/magirc.cfg.php')) {
			include(PATH_ROOT.'conf/magirc.cfg.php');
		} else {
			$error = true;
		}
		if (!isset($db)) {
			$error = true;
		}
		if ($error) {
			die ('<strong>MagIRC</strong> is not configured<br />Please run <a href="setup/">Setup</a>');
		}
		$dsn = "mysql:dbname={$db['database']};host={$db['hostname']}";
		$this->connect($dsn, $db['username'], $db['password']) || die('Error opening Magirc database<br />'.$this->error);
	}
}

class Magirc {
	public $db;
	public $cfg;
	public $tpl;
	#public $anope;
	public $denora;

	function __construct($api_mode = "web") {
		if ($api_mode == "web") {
			// Setup the template engine
			$this->tpl = new Smarty;
			$this->tpl->template_dir = 'theme/default/tpl';
			$this->tpl->config_dir = 'theme/default/cfg';
			$this->tpl->compile_dir = 'tmp/compiled';
			$this->tpl->cache_dir = 'tmp/cache';
			$this->tpl->addPluginsDir('lib/smarty-plugins/');
		}

		// Setup the database
		$this->db = new Magirc_DB;
		$query = "SHOW TABLES LIKE 'magirc_config'";
		$this->db->query($query, SQL_INIT);
		if (!$this->db->record) {
			$this->displayError('Database table missing. Please run setup.', $api_mode);
		}

		// Get the configuration
		$this->cfg = new Config();

		// Initialize modules
		define('IRCD', $this->cfg->getParam('ircd_type'));
		/*if ($api_mode == "web" || $api_mode == "anope") {
			$this->anope = new Anope();
		}*/
		if ($api_mode == "web" || $api_mode == "denora") {
			$this->denora = new Denora();
		}

		if ($api_mode == "web") {
			// Set the locale
			$locale = $this->cfg->getParam('locale');
			$domain = "messages";
			putenv("LC_ALL={$locale}.utf8");
			setlocale(LC_ALL, $locale);
			bindtextdomain($domain, './locale/');
			bind_textdomain_codeset($domain, "UTF-8");
			textdomain($domain);
			#define('LANG', substr($locale, 0, 2));
		}
	}

	// Returns session status
	function sessionStatus() {
		if (!isset($_SESSION["loginUsername"])) {
			$_SESSION["message"] = "Access denied";
			return false;
		}
		if (!isset($_SESSION["loginIP"]) || ($_SESSION["loginIP"] != $_SERVER["REMOTE_ADDR"])) {
			$_SESSION["message"] = "Access denied";
			return false;
		}
		return true;
	}

	// Gets and returns the given url parameter depending on what it is
	function getUrlParameter($param) {
		switch ($param) {
			case 'section':
				$param = isset($_GET['section']) ? $_GET['section'] : 'home';
				break;
			case 'action':
				$param = isset($_GET['action']) ? $_GET['action'] : 'main';
				break;
			default:
				$param = isset($_GET[$param]) ? $_GET[$param] : '';
		}
		return stripslashes(htmlspecialchars(basename($param)));
	}

	// Load the appropriate code based on the section parameter
	function display() {
		$section = $this->getUrlParameter('section');
		$inc_file = 'inc/' . $section . '.inc.php';

		if (file_exists($inc_file)) {
			require_once($inc_file);
		} else {
			$content = $this->getPage($section);
			if ($content) {
				$this->tpl->assign('content', $content);
				$this->tpl->display('generic.tpl');
			} else {
				$this->displayError("The requested page '$section' does not exist");
				exit;
			}
		}
	}

	// Displays an error page with the given message
	function displayError($err_msg, $api_mode = "web") {
		if ($api_mode == "web") {
			$this->tpl->assign('err_msg', $err_msg);
			$this->tpl->assign('server', $_SERVER);
			$this->tpl->display('error.tpl');
		} else {
			die($err_msg);
		}
	}

	//TODO: implement :)
	function getPage($page) {
		return NULL;
	}
}

?>