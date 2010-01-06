<?php
// $Id: pie.php 376 2009-09-11 20:56:44Z hal9000 $

define( '_VALID_PARENT', 1 );

ini_set('display_errors','on');
error_reporting(E_ALL);

require ("../../../phpdenora.cfg.php");         # Load phpDenora configuration file
require("../ircds/$denora_server_type.php"); # Load ircd definition file

if ($pd_debug < 2)
{
	ini_set('display_errors','off');
	error_reporting(E_ERROR);
}

date_default_timezone_set($pd_timezone) or die("Configuration error");

require_once("../sql.php");			# Load SQL library

// Load the JPGraph libraries
require_once("../../jpgraph/jpgraph.php");
require_once("../../jpgraph/jpgraph_pie.php");
require_once("../../jpgraph/jpgraph_pie3d.php");

// Compensate missing configuration parameters
if (!isset($np_db_host)) { $np_db_host = "localhost"; }
if (!isset($np_db_port)) { $np_db_port = "3306"; }
if (!isset($pd_style)) { $pd_style = "modern"; }
if (!isset($pd_lang)) { $pd_lang = "en"; }
if (!isset($denora_user_db)) { $denora_user_db = "user"; }
if (!isset($denora_chan_db)) { $denora_chan_db = "chan"; }
if (!isset($denora_ison_db)) { $denora_ison_db = "ison"; }
if (!isset($denora_tld_db)) { $denora_tld_db = "tld"; }
if (!isset($pd_cache_pie)) { $pd_cache_pie = 0; }

$link = sql_db_connect();

// Load the appropriate theme file
$theme = isset($_GET['theme']) ? htmlspecialchars($_GET['theme']) : $pd_style;
$themefile = "../../../themes/".$theme."/theme.php";
if (file_exists($themefile))
{
	require ($themefile);
}
else
{
	$theme = "futura";
	require ( '../../../themes/futura/theme.php' );
}

// Load appropriate language file
$lang = isset($_GET['lang']) ? htmlspecialchars($_GET['lang']) : $pd_lang;
$langfile = "../../../lang/".$lang."/lang.php";
if (file_exists($langfile))
{
	include ($langfile);
}
else {
	$lang = "en";
	include ( '../../../lang/en/lang.php' );
}

// Set the language encoding
if (!isset($charset)) { $charset = "utf-8"; }
ini_set('default_charset',$charset);

// Get the needed variables from URL
$mode = isset($_GET['mode']) ? htmlspecialchars($_GET['mode']) : NULL;
$chan = isset($_GET['chan']) ? html_entity_decode(stripslashes($_GET['chan'])) : "global";
if ($chan != "global" && $chan{0} != "#") { $chan = "#" . $chan; }

// Set the image filename
$filename = sprintf("pie_%s-%s_%s_%s",$mode,$chan,$theme,$lang);

// HTTP Header definitions
if ($pd_debug < 2) {
	header("Content-Type: image/png");
	header("Content-Disposition: attachment; filename=$filename");
}

setlocale(LC_ALL, NULL); # Work around jpgraph bug

// Initialize the graph
$graph = new PieGraph(560,200,$filename . ".png",$pd_cache_pie);

// Some variables initialization
$data = array(); $labels = array();
$np_db_maxd = 10; $i=0; $buf=0; $sum=0;

// Do the appropriate query
if ($mode == "version" && $chan == "global") {
	$a1 = ""; $a2 = "";
	if ($ircd['services_protection'] == 1) {
		$a1 = "AND ".$ircd['services_protection_mode']."=\"N\"";
		$a2 = "AND ".$denora_user_db.".".$ircd['services_protection_mode']."=\"N\" ";
	}
	$q = sql_query("SELECT COUNT(nickid) FROM ".$denora_user_db." WHERE online=\"Y\" ".$a1.";");
	$r = sql_fetch_array($q);
	$sum = $r[0];
	$q = sql_query("SELECT ".$denora_user_db.".ctcpversion, COUNT(*) AS version_count ".
		"FROM ".$denora_user_db." ".
		"WHERE ".$denora_user_db.".online=\"Y\" ".
		$a2.
		"GROUP by ".$denora_user_db.".ctcpversion ".
		"ORDER BY version_count DESC;");
}
elseif ($mode == "version" && $chan != "global") {
	//$a1 = ($ircd['chanhide'] == 1) ? "AND ".$denora_user_db.".".$ircd['chanhide_mode']."=\"N\" " : NULL;
	$a1 = NULL;
	$sum = sql_query_num_rows("SELECT ".$denora_user_db.".nickid FROM ".$denora_user_db.", ".$denora_chan_db.", ".$denora_ison_db." ".
		"WHERE ".$denora_chan_db.".chanid = ".$denora_ison_db.".chanid ".
		"AND ".$denora_user_db.".nickid = ".$denora_ison_db.".nickid ".
		"AND ".$denora_user_db.".online=\"Y\" ".
		"AND LOWER(channel)=LOWER(\"".sql_escape_string($chan)."\");");
	$q = sql_query("SELECT ".$denora_user_db.".ctcpversion, COUNT(*) AS version_count ".
		"FROM ".$denora_user_db.", ".$denora_chan_db.", ".$denora_ison_db." ".
		"WHERE ".$denora_user_db.".nickid=".$denora_ison_db.".nickid ".
		"AND ".$denora_ison_db.".chanid=".$denora_chan_db.".chanid ".
		"AND LOWER(".$denora_chan_db.".channel)=LOWER(\"".sql_escape_string($chan)."\") ".
		"AND ".$denora_user_db.".online=\"Y\" ".
		$a1.
		"GROUP by ".$denora_user_db.".ctcpversion ".
		"ORDER BY version_count DESC;");
}
elseif ($mode == "country" && $chan == "global") {
	$q = sql_query("SELECT SUM(".$denora_tld_db.".count) FROM ".$denora_tld_db." WHERE ".$denora_tld_db.".count != 0;");
	$r = sql_fetch_array($q);
	$sum = $r[0];
	$q = sql_query("SELECT ".$denora_tld_db.".country, ".$denora_tld_db.".count ".
		"FROM ".$denora_tld_db." WHERE ".$denora_tld_db.".count != 0 ".
		"ORDER BY ".$denora_tld_db.".count DESC;");
}
elseif ($mode == "country" && $chan != "global") {
	//$a1 = ($ircd['chanhide'] == 1) ? "AND ".$denora_user_db.".".$ircd['chanhide_mode']."=\"N\" " : NULL;
	$a1 = NULL;
	$sum = sql_query_num_rows("SELECT ".$denora_user_db.".nickid FROM ".$denora_chan_db.", ".$denora_ison_db.", ".$denora_user_db." ".
		"WHERE ".$denora_chan_db.".chanid = ".$denora_ison_db.".chanid ".
		"AND ".$denora_ison_db.".nickid = ".$denora_user_db.".nickid ".
		"AND LOWER(channel)=LOWER(\"".sql_escape_string($chan)."\") ".
		"AND ".$denora_user_db.".online=\"Y\";");
	$q = sql_query("SELECT ".$denora_user_db.".country, COUNT(*) AS country_count ".
		"FROM ".$denora_user_db.", ".$denora_chan_db.", ".$denora_ison_db." ".
		"WHERE ".$denora_user_db.".nickid=".$denora_ison_db.".nickid ".
		"AND ".$denora_ison_db.".chanid=".$denora_chan_db.".chanid ".
		"AND LOWER(".$denora_chan_db.".channel)=LOWER(\"".sql_escape_string($chan)."\") ".
		"AND ".$denora_user_db.".online=\"Y\" ".
		$a1.
		"GROUP by ".$denora_user_db.".country ".
		"ORDER BY country_count DESC;");
}
else {
	die("Invalid mode parameter. Must die!");
}

// Parse the collected data
while ($r = sql_fetch_array($q)) {
	if ($i == ($np_db_maxd - 1)) {
		$labels[$i] = _GD_OTHER;
		$data[$i] = $sum - $buf;
		$i++;
		break;
	}
	if ($r[0]) {
		if (strlen($r[0]) > 33) {
			$labels[$i] = substr($r[0], 0, 31) . "...";
		}
		else {
			$labels[$i] = $r[0];
		}
	}
	else {
		$labels[$i] = _GD_UNKNOWN;
	}
	$data[$i] = $r[1];
	$buf = $buf + $data[$i];
	$i++;
}

if ($data == NULL) { $data[0] = 1; }
if ($labels == NULL) {
	$labels[0] = _ER_NODATA;
}
else {
	// Add percentages to legend
	for ($i=0; $i < sizeof($data); $i++) {
		$labels[$i] .= " (" . round(($data[$i] * 100) / $sum, 2) . "%%)";
	}
}

sql_db_close($link);

// Generate the graph
$graph->SetAntiAliasing();
$graph->legend->Pos(0.44, 0.05, "left", "top");
$pieplot = new PiePlot3D($data);
$pieplot->ExplodeSlice(0);
$pieplot->SetCenter(0.22);
$pieplot->SetLegends($labels);
$pieplot->value->Show(false);

$graph->SetMarginColor($tpl_graph_bg); # graph frame bg
$graph->SetFrame(true,$tpl_graph_bg,0); # graph frame margin
$graph->SetColor($tpl_graph_bg); # graph plot bg
$graph->legend->SetColor($tpl_graph_labels,$tpl_graph_axis); # legend text and border colors
$graph->legend->SetFillColor($tpl_graph_bg); # legend fill color
$graph->legend->SetShadow($tpl_graph_bg,0); # turn off legend shadow
if (function_exists("imagettfbbox"))
	$graph->legend->SetFont(FF_DV_SANSSERIFMONO,FS_NORMAL,8);
$pieplot->value->SetColor($tpl_graph_labels); # labels color
$pieplot->SetTheme($tpl_pie_theme); # pie color theme
$graph->footer->left->Set("(" . strftime($denora_format_short) . ")"); # display time of generation
$graph->footer->left->SetColor($tpl_graph_labels);
$graph->footer->left->SetFont(FF_FONT0);

$graph->Add($pieplot);
$graph->Stroke();

?>
