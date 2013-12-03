<?php

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
		
		if ($this->modes)
			return;
		
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
