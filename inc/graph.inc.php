<?php
// $Id$

switch (@$_GET['graph']) {
	case 'bar':
		include('lib/magirc/graphs/bar.php');
		break;
	case 'line':
		include('lib/magirc/graphs/line.php');
		break;
	case 'pie':
		include('lib/magirc/graphs/pie.php');
		break;
	default:
		$this->displayError("Missing required parameter");
}

?>