<?php

class Setup {
	public $db;
	public $tpl;

	function __construct() {
		$this->tpl = new Smarty;
		$this->tpl->template_dir = 'tpl';
		$this->tpl->compile_dir = '../tmp';
		$this->tpl->cache_dir = 'tmp';
		$this->tpl->autoload_filters = array('pre' => array('jsmin'));
		$this->tpl->addPluginsDir('../lib/smarty-plugins/');
		$this->db = new DB;
		// We skip db connection in the first steps for check purposes
		if (@$_GET['step'] > 2) {
			if (file_exists('../conf/magirc.cfg.php')) {
				include('../conf/magirc.cfg.php');
			} else {
				die ('magirc.cfg.php configuration file missing');
			}
			$dsn = "mysql:dbname={$db['database']};host={$db['hostname']}";
			$this->db->connect($dsn, $db['username'], $db['password']) or die('Error opening MagIRC database<br />'.$this->db->error);
		}
	}

	/**
	 * Makes preliminary requirements checks
	 * @return array
	 */
	function requirementsCheck() {
		$status = array('error' => false);

		if (version_compare("5.3.0", phpversion(), "<") == 1) {
			$status['php'] = true;
		} else {
			$status['php'] = false;
			$status['error'] = true;
		}

		if (extension_loaded('pdo') == 1 && in_array('mysql', PDO::getAvailableDrivers())) {
			$status['pdo'] = true;
		} else {
			$status['pdo'] = false;
			$status['error'] = true;
		}

		if (extension_loaded('gettext') == 1) {
			$status['gettext'] = true;
		} else {
			$status['gettext'] = false;
			$status['error'] = true;
		}

		if (extension_loaded('mcrypt') == 1) {
			$status['mcrypt'] = true;
		} else {
			$status['mcrypt'] = false;
			$status['error'] = true;
		}

		if (file_exists(MAGIRC_CFG_FILE)) {
			if (is_writable(MAGIRC_CFG_FILE)) {
				$status['writable'] = true;
			} else {
				$status['writable'] = false;
			}
		} else {
			$new = true;
			if (copy('../conf/magirc.cfg.dist.php', MAGIRC_CFG_FILE)) {
				$status['writable'] = true;
			} else {
				$status['writable'] = false;
			}
		}

		if (is_writable('../tmp')) {
			$status['tmp'] = true;
		} else {
			$status['tmp'] = false;
			$status['error'] = true;
		}

		if (get_magic_quotes_gpc()) {
			$status['magic_quotes'] = true;
			$status['error'] = true;
		} else {
			$status['magic_quotes'] = false;
		}

		return $status;
	}

	/**
	 *  Saves the MagIRC SQL configuration file
	 */
	function saveConfig() {
		if (isset($_POST['savedb'])) {
			$db_buffer =
                    "<?php
	\$db['username'] = '{$_POST['username']}';
	\$db['password'] = '{$_POST['password']}';
	\$db['database'] = '{$_POST['database']}';
	\$db['hostname'] = '{$_POST['hostname']}';
	\$db['port'] = '{$_POST['port']}';
?>";
			$this->tpl->assign('db_buffer', $db_buffer);
			if (is_writable(MAGIRC_CFG_FILE)) {
				$writefile = fopen(MAGIRC_CFG_FILE,"w");
				fwrite($writefile, $db_buffer);
				fclose($writefile);
			}
		}
	}

	/**
	 * Checks if the configuration table is there
	 * @return type
	 */
	function configCheck() {
		$query = "SHOW TABLES LIKE 'magirc_config'";
		$this->db->query($query, SQL_INIT);
		return $this->db->record;
	}

	/**
	 * Gets the Database schema version
	 * @return int Version
	 */
	private function getDbVersion() {
		$result = $this->db->selectOne('magirc_config', array('parameter' => 'db_version'));
		return $result['value'];
	}

	/**
	 * Loads the configuration table schema to the Denora database, for fresh installs
	 * @return boolean
	 */
	function configDump() {
		$file_content = file('sql/schema.sql');
		$query = "";
		foreach($file_content as $sql_line) {
			$tsl = trim($sql_line);
			if (($sql_line != "") && (substr($tsl, 0, 2) != "--") && (substr($tsl, 0, 1) != "#")) {
				$query .= $sql_line;
				if(preg_match("/;\s*$/", $sql_line)) {
					$query = str_replace(";", "", "$query");
					$result = $this->db->query($query);
					if (!$result) {
						return false;
					}
					$query = "";
				}
			}
		}
		return true;
	}

	/**
	 * Upgrade the MagIRC database
	 * @return boolean true: updated, false: no update needed
	 */
	function configUpgrade() {
		$version = $this->getDbVersion();
		$updated = false;
		if ($version != DB_VERSION) {
			if ($version < 2) {
				$this->db->insert('magirc_config', array('parameter' => 'live_interval', 'value' => 15));
				$this->db->insert('magirc_config', array('parameter' => 'cdn_enable', 'value' => 1));
			}
			if ($version < 3) {
				$this->db->insert('magirc_config', array('parameter' => 'rewrite_enable', 'value' => 0));
			}
			if ($version < 4) {
				$this->db->insert('magirc_config', array('parameter' => 'timezone', 'value' => 'UTC'));
			}
			if ($version < 5) {
				$this->db->insert('magirc_config', array('parameter' => 'welcome_mode', 'value' => 'statuspage'));
				$this->db->query("CREATE TABLE IF NOT EXISTS `magirc_content` (
					`name` varchar(16) NOT NULL default '', `text` text NOT NULL default '',
					PRIMARY KEY (`name`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
				$welcome_msg = $this->db->selectOne('magirc_config', array('parameter' => 'msg_welcome'));
				$this->db->insert('magirc_content', array('name' => 'welcome', 'text' => $welcome_msg['value']));
				$this->db->delete('magirc_config', array('parameter' => 'msg_welcome'));
				$this->db->query("ALTER TABLE `magirc_config` CHANGE `value` `value` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''");
				$this->db->query("ALTER TABLE `magirc_config` ENGINE = InnoDB");
			}
			if ($version < 6) {
				$this->db->insert('magirc_config', array('parameter' => 'block_spchans', 'value' => 0));
				$this->db->insert('magirc_config', array('parameter' => 'net_roundrobin', 'value' => ''));
				$this->db->insert('magirc_config', array('parameter' => 'service_adsense_id', 'value' => ''));
				$this->db->insert('magirc_config', array('parameter' => 'service_adsense_channel', 'value' => ''));
				$this->db->insert('magirc_config', array('parameter' => 'service_searchirc', 'value' => ''));
				$this->db->insert('magirc_config', array('parameter' => 'service_netsplit', 'value' => ''));
			}
			if ($version < 7) {
				$this->db->insert('magirc_config', array('parameter' => 'version_show', 'value' => '1'));
			}
			if ($version < 8) {
				$this->db->insert('magirc_config', array('parameter' => 'net_port', 'value' => '6667'));
				$this->db->insert('magirc_config', array('parameter' => 'net_port_ssl', 'value' => ''));
				$roundrobin = $this->db->selectOne('magirc_config', array('parameter' => 'net_roundrobin'));
				if ($roundrobin['value']) {
					$array = explode(':', $roundrobin['value']);
					$this->db->update('magirc_config', array('value' => $array[0]), array('parameter' => 'net_roundrobin'));
					if (count($array) > 1) {
						$this->db->update('magirc_config', array('value' => $array[1]), array('parameter' => 'net_port'));
					}
				}
				$this->db->insert('magirc_config', array('parameter' => 'service_webchat', 'value' => ''));
				$this->db->insert('magirc_config', array('parameter' => 'service_mibbit', 'value' => ''));
				$this->db->insert('magirc_config', array('parameter' => 'service_addthis', 'value' => '0'));
			}
			if ($version < 9) {
				$this->db->insert('magirc_config', array('parameter' => 'denora_version', 'value' => '1.4'));
			}
			$this->db->update('magirc_config', array('value' => DB_VERSION), array('parameter' => 'db_version'));
			$updated = true;
		}
		return $updated;
	}

	/**
	 * Checks if there are any admins in the admin table
	 * @return boolean true: yes, false: no
	 */
	function checkAdmins() {
		$this->db->query("SELECT id FROM magirc_admin", SQL_INIT);
		return $this->db->record ? true : false;
	}
}

?>