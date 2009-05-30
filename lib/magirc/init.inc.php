<?php
// $Id$

include('lib/magirc/version.inc.php');
require('lib/smarty/Smarty.class.php');
require('lib/pear/MDB2.php');
require('lib/magirc/DB.class.php');
require('lib/magirc/Magirc.class.php');

// database configuration
class Magirc_DB extends DB {
	function Magirc_DB() {
		global $db;
		$dsn = sprintf("mysqli://%s:%s@%s/%s", $db['magirc']['username'], $db['magirc']['password'], $db['magirc']['hostname'], $db['magirc']['database']);
		$this->connect($dsn) || die('Error opening Magirc database<br />'.$this->error);
	}
}

class Denora_DB extends DB {
	function Denora_DB() {
		global $db;
		$dsn = sprintf("mysqli://%s:%s@%s/%s", $db['denora']['username'], $db['denora']['password'], $db['denora']['hostname'], $db['denora']['database']);
		$this->connect($dsn) || die('Error opening Denora database<br />'.$this->error);
	}
}

// smarty configuration
class Magirc_Smarty extends Smarty {
	function Magirc_Smarty() {
		$this->template_dir = 'theme/'.THEME.'/tpl';
		$this->config_dir = 'theme/'.THEME.'/cfg';
		$this->compile_dir = 'tmp/compiled';
		$this->cache_dir = 'tmp/cache';
	}
}

?>