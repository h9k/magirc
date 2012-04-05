<!DOCTYPE html>
<html>
<head>
<title>{block name="title"}{$cfg.net_name} - {/block}</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="ROBOTS" content="INDEX, FOLLOW" />
<meta name="Keywords" content="MagIRC IRC Chat Statistics Denora stats phpDenora" />
<meta name="Description" content="IRC Statistics powered by MagIRC" />
<base href="{$smarty.const.BASE_URL}" />
<link rel="icon" href="theme/{$cfg.theme}/css/favicon.ico" type="image/x-icon">
{block name="css"}
<link href="theme/{$cfg.theme}/css/styles.css" rel="stylesheet" type="text/css" />
<link href="theme/{$cfg.theme}/css/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="theme/{$cfg.theme}/css/datatables.css" rel="stylesheet" type="text/css" />
{if $cfg.cdn_enable}
<link href='http://fonts.googleapis.com/css?family=Share' rel='stylesheet' type='text/css'>
{else}
<link href="theme/{$cfg.theme}/css/font.css" rel="stylesheet" type="text/css" />
{/if}
{/block}
</head>
<body>
{block name="body"}
<div id="header">
	<a href="./"><img src="theme/{$cfg.theme}/img/magirc.png" alt="MagIRC" title="" id="logo" /></a>
	<div id="menu">
		<ul>
			<li><a href="index.php/network"{if $section eq 'network'} class="active"{/if}><span>&nbsp;Network</span></a></li>
			<li><a href="index.php/server"{if $section eq 'server'} class="active"{/if}><span>&nbsp;Servers</span></a></li>
			<li><a href="index.php/channel"{if $section eq 'channel'} class="active"{/if}><span>&nbsp;Channels</span></a></li>
			<li><a href="index.php/user"{if $section eq 'user'} class="active"{/if}><span>&nbsp;Users</span></a></li>
		</ul>
	</div>
	<div id="loading"><img src="theme/{$cfg.theme}/img/loading.gif" alt="loading..." /></div>
</div>
<div id="main">{block name="content"}[content placeholder]{/block}</div>
<div id="footer">powered by <span style="font-size:12px;"><strong>MagIRC</strong></span> v{$smarty.const.VERSION_FULL}</div>
{/block}
{block name="js"}
{if $cfg.cdn_enable}
<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.5.2.min.js"></script>
<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.ui/1.8.18/jquery-ui.min.js"></script>
<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.0/jquery.dataTables.js"></script>
{else}
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery.datatables.min.js"></script>
{/if}
<script type="text/javascript" src="js/datatables.fnReloadAjax.js"></script>
<script type="text/javascript" src="js/highcharts.js"></script>
<script type="text/javascript" src="js/highstock.js"></script>
{jsmin}
<script type="text/javascript">
<!--
var url_base = '{$smarty.const.BASE_URL}';
var theme = '{$cfg.theme}';
{literal}
$(document).ready(function() {
	$("#loading").ajaxStart(function(){
		$(this).show();
	}).ajaxStop(function(){
		$(this).hide();
	});
	// Datatable default settings
	$.extend($.fn.dataTable.defaults, {
        "bProcessing": true,
		"bServerSide": false,
		"bJQueryUI": true,
		"bAutoWidth": false,
		"sPaginationType": "full_numbers"
    });
	// Highcharts default settings
	Highcharts.setOptions({
		global: { useUTC: false },
		chart: {
			backgroundColor: 'transparent',
			type: 'spline',
			marginRight: 10,
			style: { fontFamily: 'Share, cursive' },
			plotBackgroundColor: null,
			plotBorderWidth: null,
			plotShadow: false
		},
		title: { text: null },
		xAxis: {
			type: 'datetime',
			tickPixelInterval: 150,
			ordinal: true
		},
		yAxis: {
			title: { align: 'low' },
			allowDecimals: false,
			plotLines: [{
				value: 0,
				width: 1,
				color: '#808080'
			}]
		},
		rangeSelector: {
			buttons: [{
				type: 'day',
				count: 1,
				text: '1d'
			},{
				type: 'week',
				count: 1,
				text: '1w'
			},{
				type: 'month',
				count: 1,
				text: '1m'
			}, {
				type: 'month',
				count: 3,
				text: '3m'
			}, {
				type: 'month',
				count: 6,
				text: '6m'
			}, {
				type: 'ytd',
				text: 'YTD'
			}, {
				type: 'year',
				count: 1,
				text: '1y'
			}, {
				type: 'all',
				text: 'All'
			}],
			selected: 3
		},
		tooltip: { valueDecimals: 0 },
		legend: { enabled: false },
		exporting: { enabled: false },
		plotOptions: {
			spline: {
				lineWidth: 2,
				states: { hover: { lineWidth: 3 } },
				marker: {
					enabled: false,
					states: {
						hover: {
							enabled: true,
							symbol: 'circle',
							radius: 5,
							lineWidth: 1
						}
					}
				}
			},
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: true,
					color: '#000000',
					connectorColor: '#000000',
					formatter: function() {
						return '<b>'+ this.point.name +'<\/b>: '+ Math.round(this.percentage * 100) / 100 +' %';
					}
				}
			},
			column: {
				dataLabels: {
					enabled: true,
					rotation: -90,
					x: 3,
					y: -12
				}
			}
		},
		rangeSelector: { selected: 4 },
		credits: { enabled: false }
	});
});
{/literal}
--></script>
{/jsmin}
{/block}
</body>
</html>