<?php
// $Id$

define( '_VALID_PARENT', 1 );
ini_set('display_errors','on');
error_reporting(E_ALL);
ini_set('default_charset','UTF-8');

$debug_buf = NULL;

require_once("../conf/sql.cfg.php");			# Load Denora SQL configuration file
require_once("../lib/phpdenora/version.php");
require_once("../lib/phpdenora/sql.php");		# Load SQL library
require_once("../lib/phpdenora/auth.php");		# Load Authentication library
require_once("../lib/phpdenora/config.php");	# Load Configuration library
require_once("../lib/phpdenora/admin/functions.php");	# Load Admin library

// Connecting to SQL
@$db = new mysqli($sql['hostname'], $sql['username'], $sql['password'], $sql['db_name'], $sql['port']);
if (mysqli_connect_errno()) {
	printf("Error while connecting to the MySQL server: %s\n", mysqli_connect_error());
	exit();
}

$config = get_config();
if (!$config)
	die('<html><head></head><body><div style="border: 3px solid #FF3300; background-color:#FFCC99; text-align:center; padding: 2px; margin: 10px;"><strong>ERROR:</strong> SQL Config Table is missing!<br />Please run the <em>phpDenora Installer</em>.</div></body></html>');

if ($config['debug_mode'] < 1)
{
	ini_set('display_errors','off');
	error_reporting(E_ERROR);
}

// Set the language encoding
ini_set('default_charset','UTF-8');

$phpdenora_url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$phpdenora_url = explode("admin/",$phpdenora_url);
$phpdenora_url = $phpdenora_url[0];

session_start();
if (isset($_SESSION['loginUsername']))
	$page = (isset($_GET['page'])) ? $_GET['page'] : 'home';
else
	$page = (isset($_GET['page'])) ? $_GET['page'] : 'login';

if ($page == 'login' && isset($_POST['login'])) {
	$username = $_POST['username'];
	$password = $_POST['password'];
	if (denora_login($username, $password)) {
		$_SESSION['loginUsername'] = $username;
		$_SESSION['loginIP'] = $_SERVER['REMOTE_ADDR'];
		$page = 'home';
	} else {
		$_SESSION['message'] = "Could not connect to the admin panel as '{$username}'";
		$page = 'logout';
	}
}
if ($page == 'logout') {
	$message = "";
	
	if (isset($_SESSION["loginUsername"])) {
		$message .= "{$_SESSION["loginUsername"]}, thanks for using phpDenora.";
		unset($_SESSION["loginUsername"]);
	}
	if (isset($_SESSION["message"])) {
		$message .= $_SESSION["message"];
		unset($_SESSION["message"]);
	}
	session_destroy();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $config['net_name']; ?>:: Magirc ADMIN</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
<link href="css/default.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="header">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td align="left" valign="bottom"><img src="img/logo.png" alt="" width="138" height="50" longdesc="http://denorastats.org/" /> <strong>Administration Panel</strong></td>
      <td align="right" valign="bottom"><?php
if (isset($_SESSION["loginUsername"]))
	echo sprintf("Logged in as: <strong>%s</strong> [<a href=\"?page=logout\">logout</a>]" , $_SESSION["loginUsername"]);
else
	echo "<em>Not logged in</em> ";
?>
      </td>
    </tr>
  </table>
</div>
<div id="content">
  <table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr>
      <td align="left" valign="top" class="menu"><?php if (denora_session() == true) { include('includes/menu.php'); } ?></td>
      <td align="left" valign="top">
<?php
	if ($page == "login") {
		include('includes/login.php');
	} elseif ($page == "logout") {
		include('includes/logout.php');
	} elseif (denora_session() == true) {
		switch ($page) {
			case 'home':
				include('includes/home.php');
				break;
			case 'general':
				include('includes/general.php');
				break;
			case 'network':
				include('includes/network.php');
				break;
			case 'behavior':
				include('includes/behavior.php');
				break;
			case 'features':
				include('includes/features.php');
				break;
			case 'integration':
				include('includes/integration.php');
				break;
			case 'performance':
				include('includes/performance.php');
				break;
			case 'database':
				include('includes/database.php');
				break;
			case 'advanced':
				include('includes/advanced.php');
				break;
			case 'registration':
				include('includes/registration.php');
				break;
			case 'docs':
				include('includes/docs.php');
				break;
			default:
				echo "<pre>Error: unable to load page $page</pre>";
				break;
		}
	} else {
		echo "<pre>Access denied.</pre>";
		include('includes/login.php');
	}
?>
      </td>
    </tr>
  </table>
</div>
<div id="footer">Powered by <a href="http://denorastats.org/">phpDenora</a> v<?php echo VERSION_FULL; ?></div>
<?php
if (sql_errno() != 0 || $debug_buf) {
	echo "<div style=\"border: 2px solid #FF0033; background-color: #FFFFCC;\">";
	if ($debug_buf) { echo $debug_buf; }
	if (sql_errno() != 0) { echo "<pre class=\"left\" style=\"white-space:normal;\">[SQL Error] " . sql_errno() . ": " . sql_error(). "</pre>"; }
	echo "</div>";
}

$db->close();
?>
</body>
</html>
