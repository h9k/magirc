<?php


class Anope_DB extends DB {
	function __construct() {
		parent::__construct();
		$error = false;
		if (file_exists('conf/anope.cfg.php')) {
			include('conf/anope.cfg.php');
		} else {
			$error = true;
		}
		if (!isset($db)) {
			$error = true;
		}
		if ($error) {
			die ('<strong>MagIRC</strong> is not properly configured<br />Please configure the Anope database in the <a href="admin/">Admin Panel</a>');
		}
		$dsn = "mysql:dbname={$db['database']};host={$db['hostname']}";
		$this->connect($dsn, $db['username'], $db['password']) || die('Error opening Anope database<br />'.$this->error);
	}
}


class Anope {

    public $db;
    #public $ircd;
    
    function __construct() {
        $this->db = new Anope_DB();
        #require("lib/magirc/anope/protocol/".IRCD.".inc.php");
        #$this->ircd = new Protocol;
    }

    // login function
    function login($username, $password) {
        if (!isset($username) || !isset($password))
            return false;

        return $this->db->selectOne('anope_ns_core', array('display' => $username, 'pass' => md5(trim($password))));
    }

   

}

?>
