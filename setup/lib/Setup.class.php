<?php
// $Id$

class Setup {
	public $db;
	public $tpl;

	function __construct() {
		$this->tpl = new Smarty;
		$this->tpl->template_dir = 'tpl';
		$this->tpl->compile_dir = 'tmp';
		$this->db = new DB;
		// We skip db connection in the first step for check purposes
		if (@$_GET['step'] > 1) {
			if (file_exists('../conf/magirc.cfg.php')) {
				include('../conf/magirc.cfg.php');
			} else {
				die ('magirc.cfg.php configuration file missing');
			}
			$dsn = "mysql:dbname={$db['database']};host={$db['hostname']}";
			$this->db->connect($dsn, $db['username'], $db['password']) or die('Error opening MagIRC database<br />'.$this->db->error);
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

		if (in_array('mysql', PDO::getAvailableDrivers())) {
			$status['pdo'] = true;
		} else {
			$status['pdo'] = false;
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
?>";
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
	function configCheck() {
		$query = "SHOW TABLES LIKE 'magirc_config'";
		$this->db->query($query, SQL_INIT);
		return $this->db->record;
	}

	// Gets the Database schema version
	function getDbVersion() {
		$result = $this->db->selectOne('magirc_config', array('parameter' => 'db_version'));
		return $result['value'];
	}

	/* Loads the configuration table schema to the Denora database */
	function configDump() {
		$file_content = file('sql/schema.sql');
		$query = "";
		$error = 0;
		foreach($file_content as $sql_line) {
			$tsl = trim($sql_line);
			if (($sql_line != "") && (substr($tsl, 0, 2) != "--") && (substr($tsl, 0, 1) != "#")) {
				$query .= $sql_line;
				if(preg_match("/;\s*$/", $sql_line)) {
					$query = str_replace(";", "", "$query");
					$result = $this->db->query($query);
					if (!$result) {
						$error = 1;
					}
					$query = "";
				}
			}
		}
		return $error;
	}
}

?>