<?php
// $Id: pie.php 376 2009-09-11 20:56:44Z hal9000 $

// Load the JPGraph libraries
require_once("lib/jpgraph/jpgraph.php");
require_once("lib/jpgraph/jpgraph_pie.php");
require_once("lib/jpgraph/jpgraph_pie3d.php");

$denora_format_short = "%m/%d/%y %I:%M:%S %p";

// Load the appropriate theme file
$themefile = "theme/".$this->cfg->getParam('theme')."/cfg/graphs.php";
if (file_exists($themefile)) {
	require ($themefile);
} else {
	require ( 'theme/default/cfg/graphs.php' );
}

// Get the needed variables from URL
$mode = isset($_GET['mode']) ? htmlspecialchars($_GET['mode']) : NULL;
$chan = isset($_GET['chan']) ? html_entity_decode(stripslashes($_GET['chan'])) : "global";
if ($chan != "global" && $chan{0} != "#") { $chan = "#" . $chan; }

// Set the image filename
$filename = sprintf("pie_%s-%s",$mode,$chan);

// HTTP Header definitions
if ($this->cfg->getParam('debug_mode') < 2) {
	header("Content-Type: image/png");
	header("Content-Disposition: attachment; filename=$filename");
}

setlocale(LC_ALL, NULL); # Work around jpgraph bug

// Initialize the graph
$graph = new PieGraph(560,200,$filename . ".png",$this->cfg->getParam('pie_cache_time'));

// Some variables initialization
$data = array(); $labels = array();
$np_db_maxd = 10; $i=0; $buf=0; $sum=0;

// Do the appropriate query
if ($mode == "version" && $chan == "global") {
	$a1 = ""; $a2 = "";
	if ($this->denora->ircd->services_protection_mode) {
		$a1 = "AND ".$this->denora->ircd->services_protection_mode."=\"N\"";
		$a2 = "AND `user`.".$this->denora->ircd->services_protection_mode."=\"N\" ";
	}
	$r = $this->denora->db->query("SELECT COUNT(nickid) FROM `user` WHERE online=\"Y\" ".$a1.";", SQL_INIT);
	$sum = $r[0];
	$this->denora->db->query("SELECT `user`.ctcpversion, COUNT(*) AS version_count ".
		"FROM `user` ".
		"WHERE `user`.online=\"Y\" ".
		$a2.
		"GROUP by `user`.ctcpversion ".
		"ORDER BY version_count DESC;");
} elseif ($mode == "version" && $chan != "global") {
	$a1 = NULL;
	$this->denora->db->query("SELECT `user`.nickid FROM `user`, `chan`, `ison` ".
		"WHERE `chan`.chanid = `ison`.chanid ".
		"AND `user`.nickid = `ison`.nickid ".
		"AND `user`.online=\"Y\" ".
		"AND LOWER(channel)=LOWER(\"".sql_escape_string($chan)."\");");
	$sum = $this->denora->db->numRows();
	$this->denora->db->query("SELECT `user`.ctcpversion, COUNT(*) AS version_count ".
		"FROM `user`, `chan`, `ison` ".
		"WHERE `user`.nickid=`ison`.nickid ".
		"AND `ison`.chanid=`chan`.chanid ".
		"AND LOWER(`chan`.channel)=LOWER(\"".sql_escape_string($chan)."\") ".
		"AND `user`.online=\"Y\" ".
		$a1.
		"GROUP by `user`.ctcpversion ".
		"ORDER BY version_count DESC;");
} elseif ($mode == "country" && $chan == "global") {
	$r = $this->denora->db->query("SELECT SUM(`tld`.count) FROM `tld` WHERE `tld`.count != 0;", SQL_INIT);
	$sum = $r[0];
	$this->denora->db->query("SELECT `tld`.country, `tld`.count ".
		"FROM `tld` WHERE `tld`.count != 0 ".
		"ORDER BY `tld`.count DESC;");
} elseif ($mode == "country" && $chan != "global") {
	$this->denora->db->query("SELECT `user`.nickid FROM `chan`, `ison`, `user` ".
		"WHERE `chan`.chanid = `ison`.chanid ".
		"AND `ison`.nickid = `user`.nickid ".
		"AND LOWER(channel)=LOWER(".$this->denora->db->escape($chan).") ".
		"AND `user`.online=\"Y\";");
	$sum = $this->denora->db->numRows();
	$this->denora->db->query("SELECT `user`.country, COUNT(*) AS country_count ".
		"FROM `user`, `chan`, `ison` ".
		"WHERE `user`.nickid=`ison`.nickid ".
		"AND `ison`.chanid=`chan`.chanid ".
		"AND LOWER(`chan`.channel)=LOWER(".$this->denora->db->escape($chan).") ".
		"AND `user`.online=\"Y\" ".
		"GROUP by `user`.country ".
		"ORDER BY country_count DESC;");
} else {
	$this->displayError("Invalid mode parameter");
}

// Parse the collected data
while ($r = $this->denora->db->next()) {
	if ($i == ($np_db_maxd - 1)) {
		$labels[$i] = "Other";
		$data[$i] = $sum - $buf;
		$i++;
		break;
	}
	if ($r[0]) {
		if (strlen($r[0]) > 33) {
			$labels[$i] = substr($r[0], 0, 31) . "...";
		} else {
			$labels[$i] = $r[0];
		}
	} else {
		$labels[$i] = "Unknown";
	}
	$data[$i] = $r[1];
	$buf = $buf + $data[$i];
	$i++;
}

if ($data == NULL) { $data[0] = 1; }
if ($labels == NULL) {
	$labels[0] = "No data";
} else {
	// Add percentages to legend
	for ($i=0; $i < sizeof($data); $i++) {
		$labels[$i] .= " (" . round(($data[$i] * 100) / $sum, 2) . "%%)";
	}
}

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
