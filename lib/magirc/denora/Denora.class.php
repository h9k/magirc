<?php
// $Id$

// Load the correct protocol file

class Denora {
	
	var $db = null;
	var $ircd = null;

	function Denora() {
		$this->db = new Denora_DB;
	}
	
	function loadProtocol($ircd) {
		require("lib/magirc/denora/protocol/$ircd.inc.php");
		$this->ircd = new Protocol;
	}
	
	// login function
	function login($username, $password) {
		if (!isset($username) || !isset($password))
			return false;
		
		return $this->db->select('admin', array('uname'), array('uname' => $username, 'passwd' => md5(trim($password)), 'level' => 1));
	}
	
	// Returns the Denora version
	function getVersion($what) {
		global $config;
		$table = isset($config['table_server']) ? $config['table_server'] : 'server';
		$query = sprintf("SELECT `version` FROM `%s` WHERE `version` LIKE 'Denora%%'", $table);
		$this->db->query($query, SQL_INIT, SQL_ASSOC);
		$result = $this->db->record;
		if (isset($result['version'])) {
			switch ($what) {
				case 'full':
					$version = explode("-", $result['version']);
					return @$version[1];
					break;
				case 'num':
					$pattern = '/([0-9.]+)/';
					preg_match($pattern, $result['version'], $version);
					return @$version[1];
					break;
				case 'rev':
					$pattern = '/([0-9.]+)/';
					preg_match($pattern, $result['version'], $num);
					if (!$num) {
						$version = explode("(",substr($result['version'], 0, -1));
					} else {
						$pattern = '/(\.[0-9]+)\s/';
						preg_match($pattern, $result['version'], $version);
						$version[1] = substr($version[1], 1);
					}
					return @$version[1];
					break;
				default:
					return NULL;
		 	}
		} else {
			return NULL;
		}
	}
	
}

?>