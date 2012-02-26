<?php
class Channels extends Magirc {
	function __construct() {
        parent::__construct('denora');
    }
    function index() {
		if (@$_GET['format'] == 'datatables') {
			return array('aaData' => $this->denora->getChannelList());
		}
		return $this->denora->getChannelList();
    }
	function hourlystats() {
		return $this->denora->getHourlyStats('channelstats');
    }
}