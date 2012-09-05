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
	public $country;
	public $country_code;

	function __construct() {
		$this->online = $this->online == 'Y';
		$this->motd_html = $this->motd ? Magirc::irc2html($this->motd) : null;
		$this->motd = htmlentities($this->motd, ENT_COMPAT, "UTF-8");
	}

}

Class User {
	// From SQL
	public $nickname;
	public $realname;
	public $hostname;
	private $hostname_cloaked;
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
	public $service;
	// User modes, only used internally
	private $mode_la, $mode_lb, $mode_lc, $mode_ld, $mode_le, $mode_lf, $mode_lg, $mode_lh, $mode_li, $mode_lj, $mode_lk, $mode_ll, $mode_lm, $mode_ln, $mode_lo, $mode_lp, $mode_lq, $mode_lr, $mode_ls, $mode_lt, $mode_lu, $mode_lv, $mode_lw, $mode_lx, $mode_ly, $mode_lz;
	private $mode_ua, $mode_ub, $mode_uc, $mode_ud, $mode_ue, $mode_uf, $mode_ug, $mode_uh, $mode_ui, $mode_uj, $mode_uk, $mode_ul, $mode_um, $mode_un, $mode_uo, $mode_up, $mode_uq, $mode_ur, $mode_us, $mode_ut, $mode_uu, $mode_uv, $mode_uw, $mode_ux, $mode_uy, $mode_uz;
	private $cmode_lq, $cmode_la, $cmode_lo, $cmode_lh, $cmode_lv;
	// Filled by the constructor
	private $umodes;
	#public $cmodes;
	public $operator;
	public $operator_level;
	public $helper;
	public $bot;

	function __construct() {
		$this->online = $this->online == 'Y';
		$this->away = $this->away == 'Y';
		$this->realname = htmlentities($this->realname, ENT_COMPAT, "UTF-8");
		$this->swhois = htmlentities($this->swhois, ENT_COMPAT, "UTF-8");
		$this->away_msg = htmlentities($this->away_msg, ENT_COMPAT, "UTF-8");
		$this->client_html = $this->client ? Magirc::irc2html($this->client) : null;
		$this->client = htmlentities($this->client, ENT_COMPAT, "UTF-8");
		$this->quit_msg = htmlentities($this->quit_msg, ENT_COMPAT, "UTF-8");
		$this->service = $this->service == 'Y';
		if (Protocol::host_cloaking && !empty($this->hostname_cloaked)) $this->hostname = $this->hostname_cloaked;
		
		// User modes
		for ($j = 97; $j <= 122; $j++) {
			$mode_l = 'mode_l'.chr($j);
			$mode_u = 'mode_u'.chr($j);
			if (isset($this->$mode_l)) {
				if ($this->$mode_l == "Y") {
					$this->$mode_l = true;
					$this->umodes .= chr($j);
				} else {
					$this->$mode_l = false;
				}
			}
			if (isset($this->$mode_u)) {
				if ($this->$mode_u == "Y") {
					$this->$mode_u = true;
					$this->umodes .= chr($j - 32);
				} else {
					$this->$mode_u = false;
				}
			}
		}
		// Channel modes
		$cmodes = null;
		if ($this->cmode_lq == 'Y') $cmodes .= "q";
		if ($this->cmode_la == 'Y') $cmodes .= "a";
		if ($this->cmode_lo == 'Y') $cmodes .= "o";
		if ($this->cmode_lh == 'Y') $cmodes .= "h";
		if ($this->cmode_lv == 'Y') $cmodes .= "v";
		$this->cmodes = $cmodes ? "+".$cmodes : null;
		// Futher info
		$this->bot = $this->hasMode(Protocol::bot_mode);
		if (!Protocol::oper_hidden_mode || !$this->hasMode(Protocol::oper_hidden_mode)) {
			$this->helper = $this->hasMode(Protocol::helper_mode);
			$levels = Protocol::$oper_levels;
			if (!empty($levels)) {
				foreach ($levels as $mode => $level) {
					$mode = Denora::getSqlMode($mode);
					if ($this->$mode) {
						$this->operator_level = $level;
						break;
					}
				}
			} elseif ($this->mode_lo) {
				$this->operator_level = "Operator";
			}
			if ($this->operator_level) $this->operator = true;
		}
		// Get the server country if user country is local
		if ($this->country_code == 'local' && $this->server_country_code) {
			$this->country = $this->server_country;
			$this->country_code = $this->server_country_code;
		}
	}

	private function hasMode($mode) {
		return $mode ? strstr($this->umodes, $mode) !== false : false;
	}
}

class Channel {
	// From SQL
	public $channel;
	public $users;
	public $users_max;
	public $users_max_time;
	public $topic;
	public $topic_html;
	public $topic_author;
	public $topic_time;
	public $kicks;
	// User modes, only used internally
	private $mode_la, $mode_lb, $mode_lc, $mode_ld, $mode_le, $mode_lf, $mode_lg, $mode_lh, $mode_li, $mode_lj, $mode_lk, $mode_ll, $mode_lm, $mode_ln, $mode_lo, $mode_lp, $mode_lq, $mode_lr, $mode_ls, $mode_lt, $mode_lu, $mode_lv, $mode_lw, $mode_lx, $mode_ly, $mode_lz;
	private $mode_ua, $mode_ub, $mode_uc, $mode_ud, $mode_ue, $mode_uf, $mode_ug, $mode_uh, $mode_ui, $mode_uj, $mode_uk, $mode_ul, $mode_um, $mode_un, $mode_uo, $mode_up, $mode_uq, $mode_ur, $mode_us, $mode_ut, $mode_uu, $mode_uv, $mode_uw, $mode_ux, $mode_uy, $mode_uz;
	private $mode_lf_data, $mode_lj_data, $mode_lk_data, $mode_ll_data, $mode_ul_data, $mode_uf_data, $mode_uj_data;
	// Filled by the constructor
	public $modes;
	private $modes_data;
	public $DT_RowId;

	function __construct() {
		$this->DT_RowId = $this->channel;
		$this->topic_html = $this->topic ? Magirc::irc2html($this->topic) : null;
		$this->topic = htmlentities($this->topic, ENT_COMPAT, "UTF-8");
		$this->users_max_time = date('Y-m-d H:i:s', $this->users_max_time);
		// Channel modes
		for ($j = 97; $j <= 122; $j++) {
			$mode_l = 'mode_l'.chr($j);
			$mode_u = 'mode_u'.chr($j);
			if (isset($this->$mode_l)) {
				if ($this->$mode_l == "Y") {
					$this->$mode_l = true;
					$this->modes .= chr($j);
				} else {
					$this->$mode_l = false;
				}
			}
			if (isset($this->$mode_u)) {
				if ($this->$mode_u == "Y") {
					$this->$mode_u = true;
					$this->modes .= chr($j - 32);
				} else {
					$this->$mode_u = false;
				}
			}
		}
		// Channel mode data
		if ($this->mode_lf_data) $this->modes_data .= " " . $this->mode_lf_data;
		if ($this->mode_lj_data) $this->modes_data .= " " . $this->mode_lj_data;
		if ($this->mode_lk_data) $this->modes_data .= " " . $this->mode_lk_data;
		if ($this->mode_ll_data) $this->modes_data .= " " . $this->mode_ll_data;
		if ($this->mode_uf_data) $this->modes_data .= " " . $this->mode_uf_data;
		if ($this->mode_uj_data) $this->modes_data .= " " . $this->mode_uj_data;
		if ($this->mode_ul_data) $this->modes_data .= " " . $this->mode_ul_data;
	}

}

?>
