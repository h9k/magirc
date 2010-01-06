<?php
// $Id: bar.php 392 2009-10-16 07:27:33Z hal9000 $

// Load the JPGraph libraries
require_once("lib/jpgraph/jpgraph.php");
require_once("lib/jpgraph/jpgraph_bar.php");

$denora_format_short = "%m/%d/%y %I:%M:%S %p";

// Load the appropriate theme file
$themefile = "theme/".$this->cfg->getParam('theme')."/cfg/graphs.php";
if (file_exists($themefile)) {
	require ($themefile);
} else {
	require ( 'theme/default/cfg/graphs.php' );
}

// Get the needed variables from URL
$mode = isset($_GET['mode']) ? $_GET['mode'] : NULL;
$type = isset($_GET['type']) ? $_GET['type'] : 0;
settype($type, 'integer');
if ($type == 4) { $type = 0; }
$user = isset($_GET['user']) ? stripslashes($_GET['user']) : NULL;
$chan = isset($_GET['chan']) ? stripslashes($_GET['chan']) : "global";
if ($chan != "global" && $chan{0} != "#") { $chan = "#" . $chan; }

// Set the image filename
$filename = null;
if ($mode == "chan") {
	$filename = sprintf("bar_%s-t%s",$chan,$type);
} elseif ($mode == "user") {
	$filename = sprintf("bar_%s-%s-t%s",$user,$chan,$type);
}

// HTTP Header definitions
if ($this->cfg->getParam('debug_mode') < 2) {
	header("Content-Type: image/png");
	header("Content-Disposition: attachment; filename=$filename");
}

// Initialize the graph
$graph = new Graph(560,200,$filename . ".png",$this->cfg->getParam('bar_cache_time'));

// Some variables initialization
$data = array(); $labels = array();

// Do the appropriate query
if ($mode == "chan") {
	$this->denora->db->query("SELECT time0,time1,time2,time3,time4,time5,time6,time7,time8,time9,time10,time11,time12,time13,time14,time15,time16,time17,time18,time19,time20,time21,time22,time23 FROM `cstats` WHERE chan=".$this->denora->db->escape($chan)." AND type=".$this->denora->db->escape($type).";");
}
elseif ($mode == "user") {
	$this->denora->db->query("SELECT time0,time1,time2,time3,time4,time5,time6,time7,time8,time9,time10,time11,time12,time13,time14,time15,time16,time17,time18,time19,time20,time21,time22,time23 FROM `ustats` WHERE uname=".$this->denora->db->escape($user)." AND chan=".$this->denora->db->escape($chan)." AND type=".$this->denora->db->escape($type).";");
}
else {
	$this->displayError("Invalid mode parameter");
}

// Parse the collected data
$r = $this->denora->db->next();
for ($i=0; $i < 24; $i++) {
	$data[$i] = $r[$i];
	$labels[$i] = $i;
}

// Generate the graph
$graph->SetScale("textlin");
$graph->yaxis->scale->SetGrace(20);

$graph->img->SetMargin(50,50,20,20);
$graph->xaxis->SetTickLabels($labels);
$graph->xaxis->SetTitle("Hour",'high'); 
$graph->yaxis->SetTitle("Lines",'low');
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
