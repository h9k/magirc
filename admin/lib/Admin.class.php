<?php
// Root path
define('PATH_ROOT', __DIR__ . '/../../');

// Database configuration
class Magirc_DB extends DB {
	function Magirc_DB() {
		if (file_exists('../conf/magirc.cfg.php')) {
			include('../conf/magirc.cfg.php');
		} else {
			die ('magirc.cfg.php configuration file missing');
		}
		$dsn = "mysql:dbname={$db['database']};host={$db['hostname']}";
		$this->connect($dsn, $db['username'], $db['password']) || die('Error opening Magirc database<br />'.$this->error);
	}
}

class Admin {
	public $slim;
	public $tpl;
	public $db;
	public $cfg;

	function __construct() {
		$this->db = new Magirc_DB();
		$this->cfg = new Config();
		$this->slim = new Slim();
		$this->tpl = new Smarty();
		$this->tpl->template_dir = 'tpl';
		$this->tpl->compile_dir = '../tmp';
		$this->tpl->config_dir = '../conf';
		$this->tpl->cache_dir = '../tmp';
		$this->tpl->error_reporting = E_ALL & ~E_NOTICE;
		$this->tpl->autoload_filters = array('pre' => array('jsmin'));
		$this->tpl->addPluginsDir('../lib/smarty-plugins/');
		$this->ckeditor = new CKEditor();
		$this->ckeditor->basePath = BASE_URL.'../js/ckeditor/';
		$this->ckeditor->returnOutput = true;
		$this->ckeditor->config['height'] = 300;
		$this->ckeditor->config['width'] = 740;
		$this->ckeditor->config['baseHref'] = '../';
		$this->ckeditor->config['contentsCss'] = array('../theme/'.$this->cfg->theme.'/css/styles.css', '../theme/'.$this->cfg->theme.'/css/editor.css', 'http://fonts.googleapis.com/css?family=Share');
		$this->ckeditor->config['docType'] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
		$this->ckeditor->config['emailProtection'] = 'encode';
		$this->ckeditor->config['entities'] = true;
		$this->ckeditor->config['forcePasteAsPlainText'] = true;
		$this->ckeditor->config['language'] = 'en';
		$this->ckeditor->config['resizeEnabled'] = true;
		$this->ckeditor->config['toolbar'] = array(
			array('Maximize','ShowBlocks','Preview','Templates'),
			array('Cut','Copy','PasteText','-','Print','Scayt'),
			array('Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'),
			array('Source'),
			array('Link','Unlink','Anchor'),
			array('Image','Table','HorizontalRule','Smiley','SpecialChar'),
	            '/',
			array('Format','FontSize','TextColor','BGColor'),
			array('Bold','Italic','Underline','Strike','-','Subscript','Superscript'),
			array('NumberedList','BulletedList','-','Outdent','Indent','Blockquote'),
			array('JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock')
		);
	}

	/**
	 * Admin Login
	 * @param string $username
	 * @param string $password
	 * @return boolean true: successful, false: failed
	 */
	function login($username, $password) {
		if (!isset($username) || !isset($password)) {
			return false;
		}
		if ($this->db->selectOne('magirc_admin', array('username' => trim($username), 'password' => md5(trim($password))))) {
			$_SESSION['username'] = $_POST['username'];
			$_SESSION["ipaddr"] = $_SERVER["REMOTE_ADDR"];
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Returns session status
	 * @return boolean true: valid session, false: no valid session
	 */
	function sessionStatus() {
		if (!isset($_SESSION["username"])) {
			$_SESSION["message"] = "Access denied";
			return false;
		}
		if (!isset($_SESSION["ipaddr"]) || ($_SESSION["ipaddr"] != $_SERVER["REMOTE_ADDR"])) {
			$_SESSION["message"] = "Access denied";
			return false;
		}
		return true;
	}

	/**
	 * Saves the given configuration parameter and value
	 * @param string $parameter
	 * @param string $value
	 * @return boolean true: updated, false: not updated
	 */
	function saveConfig($parameter, $value) {
		$this->cfg->$parameter = $value;
		return $this->db->update('magirc_config', array('value' => $value), array('parameter' => $parameter));
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
	 * Saves the HTML content for the given page
	 * @param string $name Page name
	 * @param string $text HTML content
	 * @return boolean true: updated, false: not updated
	 */
	function saveContent($name, $text) {
		$name = str_replace('content_', '', $name);
		return $this->db->update('magirc_content', array('text' => $text), array('name' => $name));
	}
}

?>