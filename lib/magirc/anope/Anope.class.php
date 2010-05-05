<?php
// $Id$

class Anope {

    public $db;
    #public $ircd;
    
    function __construct($ircd) {
        $this->db = new Anope_DB();
        #require("lib/magirc/anope/protocol/{$ircd}.inc.php");
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
