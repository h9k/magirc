<?php

Class Server {

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

	function __construct() {
		$this->online = $this->online == 'Y';
		$this->motd_html = $this->motd ? Magirc::irc2html($this->motd) : null;
	}

}

Class User {

	private $nickid;
	public $nick;
	public $hopcount;
	public $realname;
	public $hostname;
	public $hiddenhostname;
	public $nickip;
	public $username;
	public $swhois;
	public $account;
	public $connecttime;
	private $servid;
	public $server;
	public $away;
	public $awaymsg;
	public $ctcpversion;
	public $ctcpversion_html;
	public $online;
	public $lastquit;
	public $lastquitmsg;
	public $countrycode;
	public $country;
	private $modes;
	private $modes_data;
	public $uline;
	public $operator;
	public $operator_level;
	public $helper;
	public $bot;
	private $mode_la, $mode_lb, $mode_lc, $mode_ld, $mode_le, $mode_lf, $mode_lg, $mode_lh, $mode_li, $mode_lj, $mode_lk, $mode_ll, $mode_lm, $mode_ln, $mode_lo, $mode_lp, $mode_lq, $mode_lr, $mode_ls, $mode_lt, $mode_lu, $mode_lv, $mode_lw, $mode_lx, $mode_ly, $mode_lz;
	private $mode_ua, $mode_ub, $mode_uc, $mode_ud, $mode_ue, $mode_uf, $mode_ug, $mode_uh, $mode_ui, $mode_uj, $mode_uk, $mode_ul, $mode_um, $mode_un, $mode_uo, $mode_up, $mode_uq, $mode_ur, $mode_us, $mode_ut, $mode_uu, $mode_uv, $mode_uw, $mode_ux, $mode_uy, $mode_uz;

	function __construct() {
		$this->online = $this->online == 'Y';
		$this->away = $this->away == 'Y';
		$this->uline = $this->uline == 'Y';
		$this->ctcpversion_html = Magirc::irc2html($this->ctcpversion);
		$this->country_code = $this->countrycode; # TODO: fix this query-side
		// User modes
		for ($j = 97; $j <= 122; $j++) {
			$mode_l = 'mode_l'.chr($j);
			$mode_u = 'mode_u'.chr($j);
			$this->$mode_l = isset($this->$mode_l) ? $this->$mode_l == "Y" : false;
			$this->$mode_u = isset($this->$mode_u) ? $this->$mode_u == "Y" : false;
			if ($this->$mode_l) $this->modes .= chr($j);
			if ($this->$mode_u) $this->modes .= chr($j - 32);
		}
		// Channel mode data
		/*if ($this->mode_lf_data) $this->modes_data .= " " . $this->mode_lf_data;
		if ($this->mode_lj_data) $this->modes_data .= " " . $this->mode_lj_data;
		if ($this->mode_ll_data) $this->modes_data .= " " . $this->mode_ll_data;
		if ($this->mode_uf_data) $this->modes_data .= " " . $this->mode_uf_data;
		if ($this->mode_uj_data) $this->modes_data .= " " . $this->mode_uj_data;
		if ($this->mode_ul_data) $this->modes_data .= " " . $this->mode_ul_data;*/
		// Futher info
		$this->bot = $this->hasMode(Protocol::bot_mode);
		$this->helper = $this->hasMode(Protocol::helper_mode);
		if (Protocol::ircd == "unreal32") {
			if ($this->mode_un) $this->operator_level = "Network Admin";
			elseif ($this->mode_ua) $this->operator_level = "Server Admin";
			elseif ($this->mode_la) $this->operator_level = "Services Admin";
			elseif ($this->mode_uc) $this->operator_level = "Co-Admin";
			elseif ($this->mode_lo) $this->operator_level = "Global Operator";
		} else {
			if ($this->mode_lo) $this->operator_level = "Operator";
		}
		if ($this->operator_level) $this->operator = true;
	}

	private function hasMode($mode) {
		return $mode ? strstr($this->modes, $mode) !== false : false;
	}
}

?>
