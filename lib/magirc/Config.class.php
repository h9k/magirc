<?php
// $Id$

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