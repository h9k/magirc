<?php
class Users extends Magirc {
	function __construct() {
        parent::__construct('denora');
    }
    function index() {
		if (@$_GET['format'] == 'datatables') {
			return array('aaData' => $this->denora->getUserList());
		}
		return $this->denora->getUserList();
    }
	function hourlystats() {
		return $this->denora->getHourlyStats('stats');
    }
}