<?php

// Database configuration
class Magirc_DB extends DB {
    private static $instance = NULL;

    public static function getInstance() {
        if (is_null(self::$instance) === true) {
            $db = array();
            if (file_exists(__DIR__.'/../../conf/magirc.cfg.php')) {
                include(__DIR__.'/../../conf/magirc.cfg.php');
            } else {
                die ('magirc.cfg.php configuration file missing');
            }
            $dsn = "mysql:dbname={$db['database']};host={$db['hostname']}";
            $args = array();
            if (isset($db['ssl']) && $db['ssl_key']) $args[PDO::MYSQL_ATTR_SSL_KEY] = $db['ssl_key'];
            if (isset($db['ssl']) && $db['ssl_cert']) $args[PDO::MYSQL_ATTR_SSL_CERT] = $db['ssl_cert'];
            if (isset($db['ssl']) && $db['ssl_ca']) $args[PDO::MYSQL_ATTR_SSL_CA] = $db['ssl_ca'];
            self::$instance = new DB($dsn, $db['username'], $db['password'], $args);
        }
        return self::$instance;
    }
}

class Setup {
    public $db;
    public $tpl;

    function __construct() {
        $loader = new Twig_Loader_Filesystem(__DIR__.'/../tpl');
        $this->tpl = new Twig_Environment($loader, array(
            'cache' => __DIR__ . '/../../tmp',
            'debug' => true
        ));

        // We skip db connection in the first steps for check purposes
        if (@$_GET['step'] > 2) {
            $this->db = Magirc_DB::getInstance();
        }
    }

    /**
     * Makes preliminary requirements checks
     * @return array
     */
    function requirementsCheck() {
        $status = array('error' => false);

        if (version_compare("5.5.0", phpversion(), "<") == 1) {
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

        if (extension_loaded('xml') == 1) {
            $status['xml'] = true;
        } else {
            $status['xml'] = false;
            $status['error'] = true;
        }

        if (file_exists(MAGIRC_CFG_FILE)) {
            if (is_writable(MAGIRC_CFG_FILE)) {
                $status['writable'] = true;
            } else {
                $status['writable'] = false;
            }
        } else {
            if (@copy('../conf/magirc.cfg.dist.php', MAGIRC_CFG_FILE)) {
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

        return $status;
    }

    /**
     *  Saves the MagIRC SQL configuration file
     */
    function saveConfig() {
        if (isset($_POST['savedb'])) {
            $ssl = isset($_POST['ssl']) ? 'true' : 'false';
            $db_buffer =
                    "<?php
    \$db['username'] = '{$_POST['username']}';
    \$db['password'] = '{$_POST['password']}';
    \$db['database'] = '{$_POST['database']}';
    \$db['hostname'] = '{$_POST['hostname']}';
    \$db['port'] = '{$_POST['port']}';
    \$db['ssl'] = $ssl;
    \$db['ssl_key'] = '{$_POST['ssl_key']}';
    \$db['ssl_cert'] = '{$_POST['ssl_cert']}';
    \$db['ssl_ca'] = '{$_POST['ssl_ca']}';
";
            if (is_writable(MAGIRC_CFG_FILE)) {
                $writefile = fopen(MAGIRC_CFG_FILE,"w");
                fwrite($writefile, $db_buffer);
                fclose($writefile);
            }
            return $db_buffer;
        }
        return null;
    }

    /**
     * Checks if the configuration table is there
     * @return PDOStatement Configuration
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
     * Generates the base url
     * @return string
     */
    function generateBaseUrl() {
        $base_url = @$_SERVER['HTTPS'] ? 'https://' : 'http://';
        $base_url .= $_SERVER['SERVER_NAME'];
        $base_url .= $_SERVER['SERVER_PORT'] == 80 ? '' : ':'.$_SERVER['SERVER_PORT'];
        $base_url .= str_replace('setup/index.php', '', $_SERVER['SCRIPT_NAME']);
        return (substr($base_url, -1) == "/") ? substr($base_url, 0, -1) : $base_url;
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
                $this->db->insert('magirc_config', array('parameter' => 'cdn_enable', 'value' => 0));
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
            if ($version < 10) {
                $this->db->query("ALTER TABLE magirc_config CHANGE value value VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''");
            }
            if ($version < 11) {
                $base_url = $this->generateBaseUrl();
                $this->db->insert('magirc_config', array('parameter' => 'base_url', 'value' => $base_url));
            }
            if ($version < 13) {
                $this->db->insert('magirc_config', array('parameter' => 'service_mibbitid', 'value' => ''));
            }
            if ($version < 14) {
                $this->db->delete('magirc_config', array('parameter' => 'denora_version'));
                $this->db->insert('magirc_config', array('parameter' => 'service', 'value' => 'denora'));
            }
            if ($version < 15) {
                $this->db->insert('magirc_config', array('parameter' => 'hide_nickaliases', 'value' => 0));
            }
            if ($version < 16) {
                $block_spchans = $this->db->selectOne('magirc_config', array('parameter' => 'block_spchans'));
                $this->db->insert('magirc_config', array('parameter' => 'block_schans', 'value' => $block_spchans['value']));
                $this->db->insert('magirc_config', array('parameter' => 'block_pchans', 'value' => $block_spchans['value']));
                $this->db->delete('magirc_config', array('parameter' => 'block_spchans'));
            }
            if ($version < 17) {
                $this->db->delete('magirc_config', array('parameter' => 'service_searchirc'));
            }
            if ($version < 18) {
                $base_url = $this->generateBaseUrl();
                $this->db->update('magirc_config', array('value' => $base_url), array('parameter' => 'base_url'));
            }
            if ($version < 19) {
                $this->db->insert('magirc_config', array('parameter' => 'service_webchat_urlencode', 'value' => 1));
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
