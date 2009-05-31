<?php
// $Id$

class Setup {
	var $db = null;
	var $tpl = null;
	var $denora = null;

	function Setup() {
		$this->tpl = new Setup_Smarty;
		if (@$_GET['step'] > 1) {
			$this->db = new Magirc_DB;
			$this->denora = new Denora;
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
	function dbCheck($db, $table = NULL) {	
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
		
		if ($table) {
			mysql_query(sprintf("DESCRIBE `%s`", $table));
			if (mysql_errno() != 0) {
				return sprintf("ERROR: Table '%s' does not exist<br />Please check that the Denora database is set up. Run Denora's ./mydbgen utility.", $table);
			}
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