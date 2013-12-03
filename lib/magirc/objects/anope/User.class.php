<?php

class User extends UserBase {

	function __construct() {
		parent::__construct();
		
		// Oper mode
		if (!Protocol::oper_hidden_mode || !$this->hasMode(Protocol::oper_hidden_mode)) {
			$levels = Protocol::$oper_levels;
			if (!empty($levels)) {
				foreach ($levels as $mode => $level) {
					if (strpos($this->umodes, $mode) !== false) {
						$this->operator_level = $level;
						break;
					}
				}
			} elseif (strpos($this->umodes, 'o') !== false) {
				$this->operator_level = "Operator";
			}
			if ($this->operator_level) $this->operator = true;
		}
	}
}
