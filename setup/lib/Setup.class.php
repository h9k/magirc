<?php
// $Id$

class Setup {
	var $db = null;
	var $tpl = null;

	function Setup() {
		$this->tpl = new Setup_Smarty;
		if (@$_GET['step'] > 1) {
			#$this->db = new Magirc_DB;
		}
	}
	
	/* Makes preliminary requirements checks */
	function requirementsCheck() {
		global $magirc_conf;
		
		$status = array('error' => false);
		
		if (version_compare("5.2.0", phpversion(), "<") == 1) {
			$status['php'] = true;
		} else {
			$status['php'] = false;
			$status['error'] = true;
		}
		
		if (extension_loaded('mysqli') == 1) {
			$status['mysqli'] = true;
		} else {
			$status['mysqli'] = false;
			$status['error'] = true;
		}
		
		if (extension_loaded('gd') == 1) {
			$status['gd'] = true;
		} else {
			$status['gd'] = false;
			$status['error'] = true;
		}
		
		if (file_exists($magirc_conf)) {
			if (is_writable($magirc_conf)) {
				$status['writable'] = true;
			} else {
				$status['writable'] = false;
			}
		} else {
			$new = true;
			if (copy('../conf/magirc.cfg.dist.php', $magirc_conf)) {
				$status['writable'] = true;
			} else {
				$status['writable'] = false;
			}
		}
		
		if (is_writable('../tmp/compiled')) {
			$status['compiled'] = true;
		} else {
			$status['compiled'] = false;
			$status['error'] = true;
		}
		
		if (is_writable('../tmp/cache')) {
			$status['cache'] = true;
		} else {
			$status['cache'] = false;
			$status['error'] = true;
		}
		
		$status['magic_quotes'] = get_magic_quotes_gpc();
		
		return $status;
	}
	
	/* Save MagIRC SQL configuration file */
	function saveConfig() {
		global $magirc_conf;
		
		if (isset($_POST['savedb'])) {
			$db_buffer =
"<?php
	\$db['username'] = \"".$_POST['username']."\";
	\$db['password'] = \"".$_POST['password']."\";
	\$db['database'] = \"".$_POST['database']."\";
	\$db['hostname'] = \"".$_POST['hostname']."\";
	\$db['port'] = \"".$_POST['port']."\";
?>
		";
			$this->tpl->assign('db_buffer', $db_buffer);
			if (is_writable($magirc_conf)) {
				$writefile = fopen($magirc_conf,"w");
				fwrite($writefile, $db_buffer);
				fclose($writefile);
			}
		}
	}
	
	/* Checks if the configuration table is up to date
	 * (not really implemented since we only have one table version till now...)
	 */
	function configCheck(){
		$query = "SHOW TABLES LIKE 'magirc_config'";
		$this->db->query($query, SQL_INIT);
		return $this->db->record;
	}
	
	// Gets the Database schema version
	function getDbVersion(){
		$result = $this->db->select('magirc_config', array('value'), array('parameter' => 'db_version'));
		return $result[0]['value'];
	}
	
	// Tests the Database connection
	function dbCheck($db) {	
		$host = $db['port'] ? $db['hostname'] . ":" . $db['port'] : $db['hostname'];
		$link_id = mysql_connect($host, $db['username'], $db['password']);
		if (!$link_id) {
		    return 'ERROR: Failed to connect to the database : ' . mysql_error();
		}
		mysql_query("set names 'utf8'");
		$db_selected = mysql_select_db($db['database'], $link_id);
		if (!$db_selected) {
		    return 'ERROR: Failed to select the '.$db['database'].' database : ' . mysql_error();
		}
		return NULL;
	}

	/* Loads the configuration table schema to the Denora database */
	function configDump(){
		$file_content = file('sql/schema.sql');
		$query = ""; $error = 0;
		foreach($file_content as $sql_line) {
			$tsl = trim($sql_line);
			if (($sql_line != "") && (substr($tsl, 0, 2) != "--") && (substr($tsl, 0, 1) != "#")) {
				$query .= $sql_line;
				if(preg_match("/;\s*$/", $sql_line)) {
					$query = str_replace(";", "", "$query");
					$result = $this->db->query($query);
					if (!$result) { $error = 1; }
					$query = "";
				}
			}
		}
		return $error;
	}
}

?>