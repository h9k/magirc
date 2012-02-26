<?php
class Servers extends Magirc {
	function __construct() {
        parent::__construct('denora');
    }
    function index() {
		if (@$_GET['format'] == 'datatables') {
			return array('aaData' => $this->denora->getServerList());
		}
		return $this->denora->getServerList();
    }
	function hourlystats() {
		return $this->denora->getHourlyServers();
    }
}