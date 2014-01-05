<?php

class MagircDB extends DB {
	private static $instance = NULL;

	public static function getInstance() {
		if (is_null(self::$instance) === true) {
			$db = null;
			$error = false;
			if (file_exists(PATH_ROOT.'conf/magirc.cfg.php')) {
				include(PATH_ROOT.'conf/magirc.cfg.php');
			} else {
				$error = true;
			}
			if (!is_array($db)) {
				$error = true;
			}
			if ($error) {
				die ('<strong>MagIRC</strong> is not configured<br />Please run <a href="setup/">Setup</a>');
			}
			$dsn = "mysql:dbname={$db['database']};host={$db['hostname']}";
			$args = array();
			if (isset($db['ssl']) && $db['ssl_key']) $args[PDO::MYSQL_ATTR_SSL_KEY] = $db['ssl_key'];
			if (isset($db['ssl']) && $db['ssl_cert']) $args[PDO::MYSQL_ATTR_SSL_CERT] = $db['ssl_cert'];
			if (isset($db['ssl']) && $db['ssl_ca']) $args[PDO::MYSQL_ATTR_SSL_CA] = $db['ssl_ca'];
			self::$instance = new DB($dsn, $db['username'], $db['password'], $args);
			if (self::$instance->error) die('Error opening the MagIRC database<br />' . self::$instance->error);
		}
		return self::$instance;
	}
}
