<?php

abstract Class UserBase {
	public $nickname;
	public $realname;
	public $hostname;
	public $hostname_cloaked;
	public $username;
	public $swhois;
	public $connect_time;
	public $server;
	public $server_country;
	public $server_country_code;
	public $away;
	public $away_msg;
	public $client;
	public $client_html;
	public $online;
	public $quit_time;
	public $quit_msg;
	public $country_code;
	public $country;
	public $city;
	public $region;
	public $service;
	public $umodes;
	public $cmodes;
	public $operator;
	public $operator_level;
	public $helper;
	public $bot;
	public $ipv6;

	function __construct() {
		$this->online = ($this->online == 'Y');
		$this->away = ($this->away == 'Y');
		$this->realname = htmlentities($this->realname, ENT_COMPAT, "UTF-8");
		$this->swhois = htmlentities($this->swhois, ENT_COMPAT, "UTF-8");
		$this->away_msg = htmlentities($this->away_msg, ENT_COMPAT, "UTF-8");
		$this->client_html = $this->client ? Magirc::irc2html($this->client) : null;
		$this->client = htmlentities($this->client, ENT_COMPAT, "UTF-8");
		$this->quit_msg = htmlentities($this->quit_msg, ENT_COMPAT, "UTF-8");
		$this->service = ($this->service == 'Y');
		$this->bot = $this->hasMode(Protocol::bot_mode);
		if (filter_var($this->hostname, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
			$this->ipv6 = true;
		}
		if (Protocol::host_cloaking && !empty($this->hostname_cloaked)) {
			$this->hostname = $this->hostname_cloaked;
		}
		if (!Protocol::oper_hidden_mode || !$this->hasMode(Protocol::oper_hidden_mode)) {
			$this->helper = $this->hasMode(Protocol::helper_mode);
		}
		// Get the server country if user country is local
		if ($this->country_code == 'local' && $this->server_country_code) {
			$this->country = $this->server_country;
			$this->country_code = $this->server_country_code;
		}
	}
	
	public function hasMode($mode) {
		return $mode ? strstr($this->umodes, $mode) !== false : false;
	}
}
