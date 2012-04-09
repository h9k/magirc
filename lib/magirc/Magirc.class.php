<?php
// Root path
define('PATH_ROOT', __DIR__ . '/../../');

// Database configuration
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
	public $slim;
	public $denora;

	/**
	 * Magirc Class Constructor
	 * @param type $api_mode ('web': frontend, 'denora': Denora API)
	 */
	function __construct($api_mode = "web") {
		// Setup the Slim framework
		$this->slim = new Slim();
		if ($api_mode == "web") {
			// Setup the template engine
			$this->tpl = new Smarty;
			$this->tpl->template_dir = 'theme/default/tpl';
			$this->tpl->config_dir = 'theme/default/cfg';
			$this->tpl->compile_dir = 'tmp/compiled';
			$this->tpl->cache_dir = 'tmp/cache';
			$this->tpl->autoload_filters = array('pre' => array('jsmin'));
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
			/*if (!ini_get("safe_mode")) {
				putenv("LC_ALL={$locale}.utf8");
			}*/
			setlocale(LC_ALL, $locale);
			bindtextdomain($domain, './locale/');
			bind_textdomain_codeset($domain, "UTF-8");
			textdomain($domain);
			#define('LANG', substr($locale, 0, 2));
		}
	}
	
	/**
	 * Gets the page content for the specified name
	 * @param string $name Content identifier
	 * @return string HTML content 
	 */
	function getContent($name) {
		$ps = $this->db->prepare("SELECT text FROM magirc_content WHERE name = :name");
		$ps->bindParam(':name', $name, PDO::PARAM_STR);
		$ps->execute();
		return $ps->fetch(PDO::FETCH_COLUMN);
	}
	
	/**
	 * Checks thet permission for the given type and target.
	 * Terminates the program with an appropriate error message on failure.
	 * (Used by the RESTful API)
	 * @param string $type Choices: 'channel'
	 * @param string $target For example the channel name
	 */
	function checkPermission($type, $target) {
		$result = 200;
		switch($type) {
			case 'channel':
				$result = $this->denora->checkChannel($target);
				break;
		}
		// In case of error the application will terminate, otherwise it will continue normally
		switch ($result) {
			case 404: $this->slim->notFound();
			case 403: $this->slim->halt(403, $this->jsonOutput(array('error' => "HTTP 403 Access Denied")));
		}
	}
	
	/**
	 * Encodes the given data as a JSON object.
	 * (Used by the RESTful API)
	 * @param mixed $data Data
	 * @param boolean $datatables allow/forbid DataTables format
	 * @param string $idcolumn Column name to use as index for the DataTables automatic row id. If not specified, the first column will be used.
	 */
	function jsonOutput($data, $datatables = false, $idcolumn = null) {
		if ($datatables && @$_GET['format'] == "datatables") {
			if (!$idcolumn && count($data) > 0) $idcolumn = key($data[0]);
			foreach ($data as $key => $val) $data[$key]["DT_RowId"] = $val[$idcolumn];
			echo json_encode(array('aaData' => $data));
		} else {
			echo json_encode($data);
		}
	}

	/**
	 * Returns the session status
	 * @return boolean true: valid session, false: invalid or no session
	 */
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

}

?>