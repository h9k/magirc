<?php
class Network extends Magirc {
	function __construct() {
        parent::__construct('denora');
    }
	function status() {
		return $this->denora->getCurrentStatus();
    }
	function max() {
		return $this->denora->getMaxValues();
    }
}