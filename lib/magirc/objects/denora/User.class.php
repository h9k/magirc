<?php

class User extends UserBase {
	private $mode_la, $mode_lb, $mode_lc, $mode_ld, $mode_le, $mode_lf, $mode_lg, $mode_lh, $mode_li, $mode_lj, $mode_lk, $mode_ll, $mode_lm, $mode_ln, $mode_lo, $mode_lp, $mode_lq, $mode_lr, $mode_ls, $mode_lt, $mode_lu, $mode_lv, $mode_lw, $mode_lx, $mode_ly, $mode_lz;
	private $mode_ua, $mode_ub, $mode_uc, $mode_ud, $mode_ue, $mode_uf, $mode_ug, $mode_uh, $mode_ui, $mode_uj, $mode_uk, $mode_ul, $mode_um, $mode_un, $mode_uo, $mode_up, $mode_uq, $mode_ur, $mode_us, $mode_ut, $mode_uu, $mode_uv, $mode_uw, $mode_ux, $mode_uy, $mode_uz;
	private $cmode_lq, $cmode_la, $cmode_lo, $cmode_lh, $cmode_lv;

	function __construct() {
		parent::__construct();
		
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
		$this->cmodes = $cmodes;
		
		// Oper mode
		if (!Protocol::oper_hidden_mode || !$this->hasMode(Protocol::oper_hidden_mode)) {
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
	}
}
