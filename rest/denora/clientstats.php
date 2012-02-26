<?php
class ClientStats extends Magirc {
	function __construct() {
        parent::__construct('denora');
    }
    function index($chan='global') {
		return $this->denora->getClientStats($chan);
    }
}