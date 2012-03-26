{* $Id$ *}
<!DOCTYPE html>
<html>
<head>
<title>{block name="title"}MagIRC PROTOTYPE TESTING{/block}</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="ROBOTS" content="INDEX, FOLLOW" />
<meta name="Keywords" content="MagIRC IRC Chat Statistics Denora stats phpDenora" />
<meta name="Description" content="IRC Statistics powered by MagIRC" />
<base href="{$smarty.const.BASE_URL}" />
{block name="css"}
<link href="theme/default/css/styles.css" rel="stylesheet" type="text/css" />
<link href="theme/default/css/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="theme/default/css/datatables.css" rel="stylesheet" type="text/css" />
<link href='http://fonts.googleapis.com/css?family=Share' rel='stylesheet' type='text/css'>
{/block}
{block name="js"}
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery.datatables.min.js"></script>
<script type="text/javascript" src="js/datatables.plugins.js"></script>
<script type="text/javascript" src="js/datatables.fnReloadAjax.js"></script>
<script type="text/javascript" src="js/highcharts.js"></script>
<script type="text/javascript" src="js/highstock.js"></script>
<script type="text/javascript" src="js/date.js"></script>
<script type="text/javascript">
<!--
var url_base = '{$smarty.const.BASE_URL}';
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
		//"bFilter": true,
		//"bInfo": true,
		//"bLengthChange": true,
		//"bPaginate": true,
		//"bSort": true,
		//"bRegex": false,
		//"bSmart": false,
		//"bStateSave": false,
		//"iDisplayLength": 10,
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
		title: { text: '' },
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
			}
		},
		rangeSelector: { selected: 4 },
		credits: { enabled: false }
	});
});
-->
</script>
{/block}
</head>
<body>
{block name="body"}

<div id="header">
	<a href="#"><img src="theme/default/img/logo.png" alt="" id="logo" /></a>
	<div id="menu">
		<ul>
			<li><a href="?section=home"{if $smarty.get.section eq 'home'} class="active"{/if}><span>&nbsp;Home</span></a></li>
			<li><a href="?section=network"{if $smarty.get.section eq 'network'} class="active"{/if}><span>&nbsp;Network</span></a></li>
			<li><a href="?section=server"{if $smarty.get.section eq 'server'} class="active"{/if}><span>&nbsp;Servers</span></a></li>
			<li><a href="?section=channel"{if $smarty.get.section eq 'channel'} class="active"{/if}><span>&nbsp;Channels</span></a></li>
			<li><a href="?section=user"{if $smarty.get.section eq 'user'} class="active"{/if}><span>&nbsp;Users</span></a></li>
		</ul>
	</div>
	<div id="loading"><img src="theme/default/img/loading.gif" alt="loading..." /></div>
</div>

<div id="main">
{block name="content"}
[content placeholder]
{/block}
</div>

<div id="footer">
powered by <span style="font-size:12px;"><strong>MagIRC</strong></span> v{$smarty.const.VERSION_FULL}
</div>

{/block}

</body>
</html>