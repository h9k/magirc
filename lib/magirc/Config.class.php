<?php

class Config {

	private $config;

	function __construct() {
		$this->loadConfig();
	}

	/**
	 * Load the configuration and return it
	 */
	function loadConfig() {
		$db = Magirc_DB::getInstance();
		$config = array();
		$data = $db->selectAll('magirc_config');
		foreach ($data as $item) {
			$this->config[$item['parameter']] = $item['value'];
		}
		if (isset($config['timezone']) && !date_default_timezone_set($config['timezone'])) {
			die("ERROR: Invalid timezone setting.<br/>Please check your configuration.");
		}
	}

	/**
	 * Get the value of the requested parameter
	 * @param string $var Parameter
	 * @return string Value
	 */
	public function __get($var) {
		return isset($this->config[$var]) ? $this->config[$var] : null;
	}

	/**
	 * Set the value to the given parameter
	 * @param string $var Parameter
	 * @param string $val Value
	 */
	public function __set($var, $val) {
		$this->config[$var] = $val;
	}

}

?>