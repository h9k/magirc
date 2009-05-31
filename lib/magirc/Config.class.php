<?php
// $Id$

class Config {
	
	var $config = null;
	
	function Config() {
		$this->config = $this->loadConfig();
	}
	
	// Load the configuraiton
	function loadConfig() {
		$db = new Magirc_DB;
		$config = array();
		$data = $db->select('magirc_config', array('parameter', 'value'));
		foreach ($data as $item) {
			$config[$item['parameter']] = $item['value'];
		}
		return $config;
	}
	
	// Reload the configuration
	function reloadConfig() {
		$this->config = $this->loadConfig();
	}
	
	// Return requested configuration parameter
	function getParam($param) {
		return @$this->config[$param];
	}
}

?>