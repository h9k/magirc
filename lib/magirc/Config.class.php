<?php


class Config {

	public $config;

	function __construct() {
		$this->config = $this->loadConfig();
	}

	// Load the configuraiton
	function loadConfig() {
		$db = new Magirc_DB;
		$config = array();
		$data = $db->selectAll('magirc_config');
		foreach ($data as $item) {
			$config[$item['parameter']] = $item['value'];
		}
		if (isset($config['timezone']) && !date_default_timezone_set($config['timezone'])) {
			die("ERROR: Invalid timezone setting.<br/>Please check your configuration.");
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