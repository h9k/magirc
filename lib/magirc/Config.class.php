<?php
// $Id$

class Config {
	
	private $config = null;
	
	function Config($data) {
		$config = array();
		foreach ($data as $item) {
			$config[$item['parameter']] = $item['value'];
		}
		$this->config = $config;
	}
	
	// Return requested configuration parameter
	function getParam($param) {
		return @$this->config[$param];
	}
}

?>