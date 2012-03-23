<?php
class Channels extends Magirc {
	function __construct() {
        parent::__construct('denora');
    }
    function index() {
		return $this->denora->getChannelList(@$_GET['format'] == 'datatables');
    }
	function hourlystats() {
		return $this->denora->getHourlyStats('channelstats');
    }
	function biggest($limit = 10) {
		if (@$_GET['format'] == 'datatables') {
			return array('aaData' => $this->denora->getChannelBiggest((int) $limit));
		}
		return $this->denora->getChannelBiggest((int) $limit);
	}
	function top($limit = 10) {
		if (@$_GET['format'] == 'datatables') {
			return array('aaData' => $this->denora->getChannelTop((int) $limit));
		}
		return $this->denora->getChannelTop((int) $limit);
	}
}