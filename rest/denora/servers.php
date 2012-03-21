<?php
class Servers extends Magirc {
	function __construct() {
        parent::__construct('denora');
    }
    function index() {
		if (@$_GET['format'] == 'datatables') {
			$servers = $this->denora->getServerList();
			foreach ($servers as $key => $val) {
				$servers[$key]["DT_RowId"] = $val["server"];
			}
			return array('aaData' => $servers);
		}
		return $this->denora->getServerList();
    }
	function server($server = null) {
		return $this->denora->getServer($server);
	}
	function hourlystats() {
		return $this->denora->getHourlyStats('serverstats');
    }
}