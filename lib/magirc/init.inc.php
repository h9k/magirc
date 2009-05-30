<?php
// $Id$

include('lib/magirc/version.inc.php');
require('lib/magirc/DB.class.php');
require('lib/magirc/Magirc.class.php');
require('lib/smarty/Smarty.class.php');
require('lib/pear/MDB2.php');

// database configuration
class Magirc_DB extends DB {
	function Magirc_DB() {
		global $db;
		$dsn = "mysqli://$db[magirc][username]:$db[magirc][password]@$db[magirc][hostname]/$db[magirc][db_name]";
		$this->connect($dsn) || die($this->error);
	}
}

class Denora_DB extends DB {
	function Denora_DB() {
		global $db;
		$dsn = "mysqli://$db[denora][username]:$db[denora][password]@$db[denora][hostname]/$db[denora][db_name]";
		$this->connect($dsn) || die($this->error);
	}
}

// smarty configuration
class Magirc_Smarty extends Smarty {
	function Denora_Smarty() {
		$this->template_dir = 'themes/'.THEME.'/tpl';
		$this->config_dir = 'themes/'.THEME.'/cfg';
		$this->compile_dir = 'tmp/compiled';
		$this->cache_dir = 'tmp/cache';
	}
}

?>