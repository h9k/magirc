<?php
class Operators extends Magirc {
	function __construct() {
        parent::__construct('denora');
    }
    function index() {
		if (@$_GET['format'] == 'datatables') {
			$opers = $this->denora->getOperatorList();
			foreach ($opers as $key => $val) {
				$opers[$key]["DT_RowId"] = $val["nick"];
			}
			return array('aaData' => $opers);
		}
		return $this->denora->getOperatorList();
    }
}