<?php
// $Id$

class Setup {
	var $db = null;
	var $denora = null;

	function Setup() {
		$this->db = new Magirc_DB;
		$this->denora = new Denora;
	}

	/* Checks if the configuration table is up to date
	 * (not really implemented since we only have one table version till now...)
	 */
	function configCheck(){
		$tables = $this->db->getTables();
		return isset($tables['magirc_config']);
	}
	
	function getDbVersion(){
		$result = $this->db->select('magirc_config', array('value'), array('parameter' => 'db_version'));
		return $result['value'];
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