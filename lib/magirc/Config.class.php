<?php
// $Id$

class Config {
	var $config;
	
	function Config() {
		$this->config = $this->getConfig();	
	}
	
	// Gets Magirc configuration from DB
	function getConfig(){
		$config = array();
		$data = $this->db->select('magirc_config', array('parameter', 'value'));
		foreach ($data as $item) {
			$config[$item['parameter']] = $item['value'];
		}
		return $config;
	}
	
	// Return requested configuration parameter
	function getParam($param) {
		return @$this->config[$param];
	}
}

?>