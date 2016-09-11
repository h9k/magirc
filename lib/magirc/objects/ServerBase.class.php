<?php

abstract class ServerBase {
    public $server;
    public $online;
    public $description;
    public $connect_time;
    public $split_time;
    public $version;
    public $uptime;
    public $motd;
    public $motd_html;
    public $users;
    public $users_max;
    public $users_max_time;
    public $ping;
    public $ping_max;
    public $ping_max_time;
    public $opers;
    public $opers_max;
    public $opers_max_time;
    public $country;
    public $country_code;

    function __construct() {
        $this->online = ($this->online == 'Y');
        $this->motd_html = $this->motd ? Magirc::irc2html($this->motd) : null;
        $this->motd = htmlentities($this->motd, ENT_COMPAT, "UTF-8");
    }
}
