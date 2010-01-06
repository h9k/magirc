<?php
// $Id$

class Admin {
	var $tpl = null;
	var $db = null;
	var $denora = null;
	var $cfg = null;
	
	function Admin() {
		$this->tpl = new Smarty();
		$this->tpl->template_dir = 'tpl';
		$this->tpl->compile_dir = 'tmp';
		$this->tpl->config_dir = '../conf';
		$this->tpl->cache_dir = 'tmp';
		$this->db = new Magirc_DB();
		$this->denora = new Denora();
		$this->cfg = new Config();
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

	/* Saves the given configuration parameter and value */
	function saveConfig($parameter, $value){
		$this->cfg->config[$parameter] = $value;
		return $this->db->update('magirc_config', array('value' => $value), array('parameter' => $parameter));
	}
}

?>