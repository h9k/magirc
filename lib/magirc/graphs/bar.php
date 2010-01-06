<?php
// $Id: bar.php 392 2009-10-16 07:27:33Z hal9000 $

define( '_VALID_PARENT', 1 );

ini_set('display_errors','on');
error_reporting(E_ALL);

require ("../../../phpdenora.cfg.php");	# Load phpDenora configuration file

if ($pd_debug < 2)
{
	ini_set('display_errors','off');
	error_reporting(E_ERROR);
}

date_default_timezone_set($pd_timezone) or die("Configuration error");

require_once("../sql.php");			# Load SQL library

// Load the JPGraph libraries
require_once("../../jpgraph/jpgraph.php");
require_once("../../jpgraph/jpgraph_bar.php");

// Compensate missing configuration parameters
if (!isset($np_db_host)) { $np_db_host = "localhost"; }
if (!isset($np_db_port)) { $np_db_port = "3306"; }
if (!isset($pd_style)) { $pd_style = "modern"; }
if (!isset($pd_lang)) { $pd_lang = "en"; }
if (!isset($denora_cstats_db)) { $denora_cstats_db = "cstats"; }
if (!isset($denora_ustats_db)) { $denora_ustats_db = "ustats"; }
if (!isset($pd_cache_bar)) { $pd_cache_bar = 0; }

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
$mode = isset($_GET['mode']) ? sql_escape_string($_GET['mode']) : NULL;
$type = isset($_GET['type']) ? $_GET['type'] : 0;
settype($type, 'integer');
if ($type == 4) { $type = 0; }
$user = isset($_GET['user']) ? sql_escape_string(stripslashes($_GET['user'])) : NULL;
$chan = isset($_GET['chan']) ? sql_escape_string(stripslashes($_GET['chan'])) : "global";
if ($chan != "global" && $chan{0} != "#") { $chan = "#" . $chan; }

// Set the image filename
if ($mode == "chan") {
	$filename = sprintf("bar_%s-t%s_%s_%s",$chan,$type,$theme,$lang);
}
elseif ($mode == "user") {
	$filename = sprintf("bar_%s-%s-t%s_%s_%s",$user,$chan,$type,$theme,$lang);
}

// HTTP Header definitions
if ($pd_debug < 2) {
	header("Content-Type: image/png");
	header("Content-Disposition: attachment; filename=$filename");
}

// Initialize the graph
$graph = new Graph(560,200,$filename . ".png",$pd_cache_bar);

// Some variables initialization
$data = array(); $labels = array();

// Do the appropriate query
if ($mode == "chan") {
	$q = sql_query("SELECT time0,time1,time2,time3,time4,time5,time6,time7,time8,time9,time10,time11,time12,time13,time14,time15,time16,time17,time18,time19,time20,time21,time22,time23 FROM ".$denora_cstats_db." WHERE chan=\"".$chan."\" AND type=\"".$type."\";");
}
elseif ($mode == "user") {
	$q = sql_query("SELECT time0,time1,time2,time3,time4,time5,time6,time7,time8,time9,time10,time11,time12,time13,time14,time15,time16,time17,time18,time19,time20,time21,time22,time23 FROM ".$denora_ustats_db." WHERE uname=\"".$user."\" AND chan=\"".$chan."\" AND type=\"".$type."\";");
}
else {
	die("Invalid mode parameter. Must die!");
}

// Parse the collected data
$r = sql_fetch_array($q);
for ($i=0; $i < 24; $i++) {
	$data[$i] = $r[$i];
	$labels[$i] = $i;
}

sql_db_close($link);

// Generate the graph
$graph->SetScale("textlin");
$graph->yaxis->scale->SetGrace(20);

$graph->img->SetMargin(50,50,20,20);
$graph->xaxis->SetTickLabels($labels);
$graph->xaxis->SetTitle(_GD_HOUR,'high'); 
$graph->yaxis->SetTitle(_GD_LINES,'low');
$graph->yaxis->SetTitlemargin(35);

$bplot = new BarPlot($data);
$bplot->value->Show();
$bplot->value->SetFormat('%d');
$bplot->value->SetAngle(90);

if (function_exists("imagettfbbox")) {
	$graph->xaxis->SetFont(FF_DV_SANSSERIFMONO,FS_NORMAL,7);
	$graph->yaxis->SetFont(FF_DV_SANSSERIFMONO,FS_NORMAL,7);
	$graph->xaxis->title->SetFont(FF_DV_SANSSERIFMONO,FS_NORMAL,8);
	$graph->yaxis->title->SetFont(FF_DV_SANSSERIFMONO,FS_NORMAL,8);
	$bplot->value->SetFont(FF_DV_SANSSERIFMONO,FS_NORMAL,7);
}

$graph->SetMarginColor($tpl_graph_bg); # graph frame bg
$graph->SetFrame(true,$tpl_graph_bg,0); # graph frame margin
$graph->SetColor($tpl_graph_bg); # graph plot bg
$graph->xaxis->SetColor($tpl_graph_axis,$tpl_graph_labels); # x axis color
$graph->yaxis->SetColor($tpl_graph_axis,$tpl_graph_labels); # y axis color
$graph->ygrid->SetColor($tpl_graph_axis); # grid lines colors
$graph->xaxis->title->SetColor($tpl_graph_labels); # title colors
$graph->yaxis->title->SetColor($tpl_graph_labels); # title colors
$bplot->SetColor($tpl_bar_border); # bar border
$bplot->SetFillgradient($tpl_bar_fill[0],$tpl_bar_fill[1],GRAD_HOR); # bar fill
$bplot->value->SetColor($tpl_graph_labels); # value numbers above bars
$graph->footer->left->Set("(" . strftime($denora_format_short) . ")"); # display time of generation
$graph->footer->left->SetColor($tpl_graph_labels);
$graph->footer->left->SetFont(FF_FONT0);

$graph->Add($bplot);
$graph->Stroke();

?>
