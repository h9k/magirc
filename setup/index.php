<?php
// $Id$

ini_set('display_errors','on');
error_reporting(E_ALL);
ini_set('default_charset','UTF-8');

$step = (isset($_GET['step'])) ? htmlspecialchars($_GET['step']) : 1;
$magirc_conf = '../conf/magirc.cfg.php';
$denora_conf = '../conf/denora.cfg.php';
$config = array(); $sql = array();

if (!file_exists($magirc_conf)) {
	die('Please configure conf/magirc.cfg.dist.php and rename it to conf/magirc.cfg.php');
}
if (!file_exists($denora_conf)) {
	die('Please configure conf/denora.cfg.dist.php and rename it to conf/denora.cfg.php');
}

require_once($magirc_conf);
require_once($denora_conf);
require_once('../lib/magirc/version.inc.php');
require_once('../lib/magirc/DB.class.php');
require_once('../lib/magirc/Config.class.php');
require_once('../lib/magirc/denora/Denora.class.php');
require_once('lib/Setup.class.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Magirc :: Setup</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
<link href="css/default.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="header">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="left" valign="bottom"><a href="http://magirc.org/"><img src="img/logo.png" width="138" height="50" /></a> <strong>Installer</strong></td>
    <td align="right" valign="bottom"><strong>Step <?php echo $step; ?>/3</strong></td>
  </tr>
</table>
  </div>
<div id="content">
<?php
switch($step) {
	case 1: // System requirements checks, SQL config check
		include('inc/step1.php');
		break;
	case 2: // Check denora version, display login form
		include('inc/step2.php');
		break;
	case 3: // Log in, Perform DB checks, create/update phpdenora config table, display link to admin panel
		include('inc/step3.php');
		break;
	default:
		echo "<pre><span style=\"color:red;\">Error:</span> unknown step $step</pre>";
		break;
}
?>
  </div>
  <div id="footer">
    Powered by <a href="http://magirc.org/">Magirc</a> v<?php echo VERSION_FULL; ?>
  </div>
</body>
</html>
