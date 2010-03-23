<?php
// $Id$

if (get_magic_quotes_gpc()) {
    function stripslashes_deep($value) {
        $value = is_array($value) ?
                array_map('stripslashes_deep', $value) :
                stripslashes($value);

        return $value;
    }

    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
    $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
}

include('../lib/magirc/version.inc.php');
require('../lib/smarty/Smarty.class.php');
require('../lib/magirc/DB.class.php');
require('../lib/magirc/Config.class.php');
require('../lib/magirc/denora/Denora.class.php');

// database configuration
class Magirc_DB extends DB {
    function Magirc_DB() {
        if (file_exists('../conf/magirc.cfg.php')) {
            include('../conf/magirc.cfg.php');
        } else {
            die ('magirc.cfg.php configuration file missing');
        }
        $dsn = "mysql:dbname={$db['database']};host={$db['hostname']}";
        $this->connect($dsn, $db['username'], $db['password']) || die('Error opening Magirc database<br />'.$this->error);
    }
}

class Denora_DB extends DB {
    function Denora_DB() {
        if (file_exists('../conf/denora.cfg.php')) {
            include('../conf/denora.cfg.php');
        } else {
            die ('denora.cfg.php configuration file missing');
        }
        $dsn = "mysql:dbname={$db['database']};host={$db['hostname']}";
        $this->connect($dsn, $db['username'], $db['password']) || die('Error opening Denora database<br />'.$this->error);
    }
}

?>