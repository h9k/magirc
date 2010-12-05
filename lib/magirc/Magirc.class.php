<?php
// $Id$

class Magirc {
	public $db;
	public $cfg;
	public $tpl;
	public $anope;
	public $denora;

	function __construct() {
		// Setup the template engine
		$this->tpl = new Smarty;
		$this->tpl->template_dir = 'theme/default/tpl';
		$this->tpl->config_dir = 'theme/default/cfg';
		$this->tpl->compile_dir = 'tmp/compiled';
		$this->tpl->cache_dir = 'tmp/cache';
		$this->tpl->plugins_dir[] = 'lib/smarty-plugins/';
		
		// Setup the database
		$this->db = new Magirc_DB;
		$query = "SHOW TABLES LIKE 'magirc_config'";
		$this->db->query($query, SQL_INIT);
		if (!$this->db->record) {
			$this->displayError('Database table missing. Please run setup.');
		}
		
		// Get the configuration
		$this->cfg = new Config();
		
		// Initialize modules
		$this->anope = new Anope($this->cfg->getParam('ircd_type'));
		$this->denora = new Denora($this->cfg->getParam('ircd_type'));
		
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
	function displayError($err_msg) {
		$this->tpl->assign('err_msg', $err_msg);
		$this->tpl->assign('server', $_SERVER);
		$this->tpl->display('error.tpl');
	}

	//TODO: implement :)
	function getPage($page) {
		return NULL;
	}
}

?>