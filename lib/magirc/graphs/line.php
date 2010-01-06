<?php
// $Id: line.php 376 2009-09-11 20:56:44Z hal9000 $

// Load the JPGraph libraries
require_once("lib/jpgraph/jpgraph.php");
require_once("lib/jpgraph/jpgraph_line.php");

$denora_format_short = "%m/%d/%y %I:%M:%S %p";

// Load the appropriate theme file
$themefile = "theme/".$this->cfg->getParam('theme')."/cfg/graphs.php";
if (file_exists($themefile)) {
	require ($themefile);
} else {
	require ( 'theme/default/cfg/graphs.php' );
}

// Get the needed variables from URL
$size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : "normal";
$mode = isset($_GET['mode']) ? htmlspecialchars($_GET['mode']) : NULL;
if ($mode == "servers") { $table = "serverstats"; }
elseif ($mode == "channels") { $table = "channelstats"; }
elseif ($mode == "users") { $table = "stats"; }
else { $this->displayError('Invalid mode parameter.'); }

// Get start date
$start['year'] = isset($_GET['sy']) ? htmlspecialchars($_GET['sy']) : date('Y');
$start['month'] = isset($_GET['sm']) ? htmlspecialchars($_GET['sm']) : date('m');
$start['day'] = isset($_GET['sd']) ? htmlspecialchars($_GET['sd']) : date('d');
// Get end date
$end['year'] = isset($_GET['ey']) ? htmlspecialchars($_GET['ey']) : date('Y');
$end['month'] = isset($_GET['em']) ? htmlspecialchars($_GET['em']) : date('m');
$end['day'] = isset($_GET['ed']) ? htmlspecialchars($_GET['ed']) : date('d');
// Be sure not to have a negative range
if ((mktime(0, 0, 0, $end['month'], $end['day'], $end['year']) - mktime(0, 0, 0, $start['month'], $start['day'], $start['year'])) < 0) {
	$start_ = $start;
	$start = $end;
	$end = $start_;
}

// Some useful functions
function array_avg($input) {
	$j = 0;
	for($i = 1; $i < (count($input)); $i++) {
		if ($input[$i] == 0) { $j++; }
	}
	if ((count($input) - $j) == 0) {
		return 0;
	} else {
		return round(array_sum($input) / (count($input) - $j));
	}
}

function GetTicks($aData,$aType) {
	$n = count($aData);
	$startmonth = date('n',$aData[0]);
	$startday = date('j',$aData[0]);
	$startyear = date('Y',$aData[0]);
	$endmonth = date('n',$aData[$n-1]);
	$endyear = date('Y',$aData[$n-1]);
	$endday = date('j',$aData[$n-1]);
	$tickPositions = array(); $minTickPositions = array(); $i=0; $j=0;
	$d = $startday;
	$m = $startmonth;
	$y = $startyear;
	if ($aType == 1) { # show months
		// Skip the first month label if it is before the startdate
		if( $startday == 1 ) {
			$tickPositions[$i++] = mktime(0,0,0,$m,1,$y);
		}
		if( $startday < 15 ) {
			$minTickPositions[$j++] = mktime(0,0,0,$m,15,$y);
		}
		$m++;
	}

	// Loop through all the years included in the scale
	for($y = $startyear; $y <= $endyear; $y++) {
		$stopmonth = ($y == $endyear) ? $endmonth : 12;
		while( $m <= $stopmonth ) {
			$days_in_month = $m == 2 ? ($y % 4 ? 28 : ($y % 100 ? 29 : ($y % 400 ? 28 : 29))) : (($m - 1) % 7 % 2 ? 30 : 31);
			$stopday = $m == $endmonth ? $endday : $days_in_month;
			switch( $aType ) {
				case 1: # show months
					if( $m <= $stopmonth ) {
						if( !($y==$endyear && $m==$stopmonth && $endday < 15) )
						$minTickPositions[$j++] = mktime(0,0,0,$m,15,$y);
					}
					$tickPositions[$i++] = mktime(0,0,0,$m,1,$y);
					break;
				case 2: # show days
					while ($d <= $stopday) {
						$tickPositions[$i++] = mktime(0,0,0,$m,$d,$y);
						$minTickPositions[$j++] = mktime(12,0,0,$m,$d,$y);
						$d++;
					}
					break;
				case 3: # show half the days
					while ($d <= $stopday) {
						$tickPositions[$i++] = mktime(0,0,0,$m,$d,$y);
						$minTickPositions[$j++] = mktime(12,0,0,$m,$d,$y);
						$d = $d + 2;
					}
					break;
				case 4: # show hours
					for ($x = 0; $x < 24; $x++) {
						$tickPositions[$i++] = mktime($x,0,0,$m,$d,$y);
						$minTickPositions[$j++] = mktime($x,30,0,$m,$d,$y);
					}
					break;
				case 5: # show hours (for small graphs)
					for ($x = 0; $x < 24; $x++) {
						if ($x == 0 || $x == 6 || $x == 12 || $x == 18 ||$x == 23) {
							$tickPositions[$i++] = mktime($x,0,0,$m,$d,$y);
						}
						else {
							$minTickPositions[$j++] = mktime($x,0,0,$m,$d,$y);
						}
					}
					break;
			}
			$d = 1;
			$m++;
		}
		$m=1;
	}
	return array($tickPositions,$minTickPositions);
}

function TimeCallback($aVal) {
	return Date('m/d/y',$aVal);
}
function HourCallback($aVal) {
	return Date('G',$aVal);
}
function DateCallback($aVal) {
	return strftime('%d %b %y',$aVal);
}
function WeekCallback($aVal) {
	return strftime('%a %d %b',$aVal);
}
function MonthCallback($aVal) {
	return strftime('%b %y',$aVal);
}

// Set the image filename
$filename = sprintf("graph_%s_%s%s%s-%s%s%s",$mode,$start['year'],$start['month'],$start['day'],$end['year'],$end['month'],$end['day']);
if ($size == "small") { $filename .= "_small"; }

// HTTP Header definitions
if ($this->cfg->getParam('debug_mode') < 2) {
	header("Content-Type: image/png");
	header("Content-Disposition: attachment; filename=$filename");
}

// Initialize the graph
if ($size == "small") {
	$graph = new Graph(190, 125, "{$filename}.png", $this->cfg->getParam('net_cache_time'));
	$graph->SetMargin(40,10,25,35);
}
else {
	$graph = new Graph(560, 230, "{$filename}.png", $this->cfg->getParam('net_cache_time'));
	$graph->SetMargin(40,40,10,80);
}

// Fetch data from database
try {
	$this->denora->db->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$stmt = $this->denora->db->pdo->prepare("SELECT `id` FROM `{$table}` WHERE `year` = :year AND month = :month AND day = :day");
	$stmt->bindParam(':year', $start['year'], PDO::PARAM_INT);
	$stmt->bindParam(':month', $start['month'], PDO::PARAM_INT);
	$stmt->bindParam(':day', $start['day'], PDO::PARAM_INT);
	$stmt->execute();
	$sid = $stmt->fetchColumn();

	$stmt->bindParam(':year', $end['year'], PDO::PARAM_INT);
	$stmt->bindParam(':month', $end['month'], PDO::PARAM_INT);
	$stmt->bindParam(':day', $end['day'], PDO::PARAM_INT);
	$stmt->execute();
	$eid = $stmt->fetchColumn();
	#echo "<pre>Start $sid End $eid"; exit;
} catch(PDOException $e) {
	$this->displayError($e->getMessage());
}

if ($sid == NULL && $eid == NULL && $start['year'] == date('Y') && $start['month'] == date('n') && $start['day'] == date('j')) {
	$query = "SELECT * FROM `{$table}` ORDER BY `id` DESC LIMIT 1";
	$this->denora->db->query($query);
} else {
	if ($sid == NULL) {
		$sid = 1;
	}
	if ($eid == NULL) {
		$this->denora->db->query("SELECT * FROM `{$table}` WHERE `id` >= {$sid}");
	} else {
		$this->denora->db->query("SELECT * FROM `{$table}` WHERE `id` >= {$sid} AND id <= {$eid}");
	}
}

// Calculate some needed stuff
$rsize = $this->denora->db->numRows(); # Forsee the number of days
if ($rsize > 120) { $rlimit = 24; }
elseif ($rsize > 80) { $rlimit = 6; }
elseif ($rsize > 40) { $rlimit = 4; }
elseif ($rsize > 20) { $rlimit = 2; }
else { $rlimit = 1; }

// Parse data
$i = 0; $ts = 0; $sumarray = array(); $maxdata = array(); $mindata = array(); $data = array(); $xdata = array();
while ($r = $this->denora->db->next()) {
	$ts_ = $ts;
	$ts = mktime(0, 0, 0, $r[2], $r[3], $r[1]);
	while ($i > 0 && ($ts - $ts_) > 60*60*26) { # if expected row is missing (2 hours extra in case of daylight savings)
		for ($j = 4; $j < 28; $j++) {
			$data[$i] = 0;
			$maxdata[$i] = 0;
			$mindata[$i] = 0;
			$xdata[$i] = ($j - 4)*60 + $ts_ + 60*60*24;
			$j = $j + ($rlimit - 1);
			$i++;
		}
		$ts_ = $ts_ + 60*60*24;
	}
	for ($j = 4; $j < 28; $j++) {
		$sumarray = array_slice($r, $j, ($rlimit) * 2);	# take the portion needed
		$data[$i] = array_avg($sumarray);
		$maxdata[$i] = max($sumarray);
		$mindata[$i] = min($sumarray);
		$xdata[$i] = mktime($j - 4, 0, 0, $r[2], $r[3], $r[1]);
		$j = $j + ($rlimit - 1);
		$i++;
	}
}
if ($xdata == NULL) { for ($i = 0; $i < 24; $i++) { $xdata[$i] = $i*60*60; } }
if ($data == NULL) { for ($i = 0; $i < 24; $i++) { $data[$i] = 0; } }
$ts_diff = $xdata[count($xdata)-1] - $xdata[0];

// Generate the graph
$graph->SetScale('intlin',0,0,$xdata[0],$xdata[count($xdata)-1]);
$graph->yaxis->scale->SetAutoMin(0);
$graph->yaxis->scale->SetGrace(20);
$graph->SetMarginColor($tpl_graph_bg); # graph frame bg
$graph->SetFrame(true,$tpl_graph_bg,0); # graph frame margin
$graph->xaxis->SetColor($tpl_graph_axis,$tpl_graph_labels); # x axis color
$graph->xaxis->title->SetColor($tpl_graph_labels); # title colors
$graph->yaxis->SetColor($tpl_graph_axis,$tpl_graph_labels); # y axis color
$graph->yaxis->title->SetColor($tpl_graph_labels); # title colors
$graph->xgrid->SetColor($tpl_graph_axis); # grid lines colors
$graph->xgrid->Show();
$graph->ygrid->SetColor($tpl_graph_axis); # grid lines colors
$graph->ygrid->SetFill(true,$tpl_grid_fill[0],$tpl_grid_fill[1]); # graph plot bg
if ($size == "small") {
	$graph->footer->center->Set("(" . strftime($denora_format_short) . ")"); # display time of generation
	$graph->footer->center->SetColor($tpl_graph_labels);
	$graph->footer->center->SetFont(FF_FONT0);
}
else {
	$graph->footer->right->Set("(" . strftime($denora_format_short) . ")"); # display time of generation
	$graph->footer->right->SetColor($tpl_graph_labels);
	$graph->footer->right->SetFont(FF_FONT0);
}

if ($ts_diff <= 60*60*24) { # day
	$graph->xaxis->SetTitle('Hour','high');
	if (function_exists("imagettfbbox"))
	$graph->xaxis->title->SetFont(FF_DV_SANSSERIFMONO, FS_NORMAL, 8);
	$cbmode = 'hour';
	list($tickPositions,$minTickPositions) = ($size == "small") ? GetTicks($xdata,5) : GetTicks($xdata,4);
}
elseif ($ts_diff <= 60*60*24*8) { # week
	$graph->xaxis->SetLabelAngle(90);
	$cbmode = 'week';
	list($tickPositions,$minTickPositions) = GetTicks($xdata,2);
}
elseif ($ts_diff <= 60*60*24*32) { # month
	$graph->xaxis->SetLabelAngle(90);
	$cbmode = 'date';
	list($tickPositions,$minTickPositions) = GetTicks($xdata,2);
}
elseif ($ts_diff <= 60*60*24*62) { # 2 months
	$graph->xaxis->SetLabelAngle(90);
	$cbmode = 'date';
	list($tickPositions,$minTickPositions) = GetTicks($xdata,3);
}
else {
	$graph->xaxis->SetLabelAngle(90);
	$cbmode = 'month';
	list($tickPositions,$minTickPositions) = GetTicks($xdata,1);
}
$graph->xaxis->SetTickPositions($tickPositions,$minTickPositions);
switch ($cbmode) {
	case 'hour':
		$graph->xaxis->SetLabelFormatCallback('HourCallback');
		break;
	case 'date':
		$graph->xaxis->SetLabelFormatCallback('DateCallback');
		break;
	case 'week':
		$graph->xaxis->SetLabelFormatCallback('WeekCallback');
		break;
	case 'month':
		$graph->xaxis->SetLabelFormatCallback('MonthCallback');
		break;
	default:
		$graph->xaxis->SetLabelFormatCallback('TimeCallback');
}
if (function_exists("imagettfbbox")) {
	$graph->xaxis->SetFont(FF_DV_SANSSERIFMONO,FS_NORMAL,7);
	$graph->yaxis->SetFont(FF_DV_SANSSERIFMONO,FS_NORMAL,7);
}

$line = new LinePlot($data,$xdata);

if ($rlimit > 8) {
	$maxline = new LinePlot($maxdata,$xdata);
	$maxline ->SetColor($tpl_maxline_color);
	$maxline->SetLegend("Max");
	$minline = new LinePlot($mindata,$xdata);
	$minline ->SetColor($tpl_minline_color);
	$minline->SetLegend("Min");
	$line->SetLegend("Avg");
	$graph->legend->SetColor($tpl_graph_labels,$tpl_graph_axis); # legend text and border colors
	$graph->legend->SetFillColor($tpl_graph_bg); # legend fill color
	$graph->legend->SetShadow($tpl_graph_bg,0); # turn off legend shadow
	$graph->legend->SetLayout(LEGEND_HOR);
	$graph->legend->Pos(0.5, 0.99, "center", "bottom");
	if ($mode == "servers") {
		$maxline->SetStepStyle();
		$minline->SetStepStyle();
	}
}

$line->SetColor($tpl_line_color);
if ($mode == "servers") {
	$line->SetFillColor($tpl_line_servers);
	$line->SetStepStyle();
	if ($size == "small") {
		if (function_exists("imagettfbbox"))
		$graph->tabtitle->SetFont(FF_DV_SANSSERIFMONO, FS_NORMAL, 9);
		$graph->tabtitle->Set("Servers");
	}
	else {
		$graph->yaxis->SetTitle("Servers",'low');
		if (function_exists("imagettfbbox"))
		$graph->yaxis->title->SetFont(FF_DV_SANSSERIFMONO, FS_NORMAL, 8);
	}
}
elseif ($mode == "channels") {
	$line->SetFillColor($tpl_line_channels);
	if ($size == "small") {
		if (function_exists("imagettfbbox"))
		$graph->tabtitle->SetFont(FF_DV_SANSSERIFMONO, FS_NORMAL, 9);
		$graph->tabtitle->Set("Channels");
	}
	else {
		$graph->yaxis->SetTitle("Channels",'low');
		if (function_exists("imagettfbbox"))
		$graph->yaxis->title->SetFont(FF_DV_SANSSERIFMONO, FS_NORMAL, 8);
	}
}
elseif ($mode == "users") {
	$line->SetFillColor($tpl_line_users);
	if ($size == "small") {
		if (function_exists("imagettfbbox"))
		$graph->tabtitle->SetFont(FF_DV_SANSSERIFMONO, FS_NORMAL, 9);
		$graph->tabtitle->Set("Users");
	}
	else {
		$graph->yaxis->SetTitle("Users",'low');
		if (function_exists("imagettfbbox"))
		$graph->yaxis->title->SetFont(FF_DV_SANSSERIFMONO, FS_NORMAL, 8);
	}
}

if ($size == "small") {
	$graph->tabtitle->SetColor($tpl_graph_labels,$tpl_grid_fill[1],$tpl_graph_labels);
}
if ($rlimit > 8) {
	$graph->Add($minline);
	$graph->Add($maxline);
}
$graph->Add($line);
$graph->Stroke();

?>