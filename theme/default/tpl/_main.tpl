<!DOCTYPE html>
<html>
<head>
<title>{block name="title"}{$cfg.net_name} - {/block}</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="ROBOTS" content="INDEX, FOLLOW" />
<meta name="Keywords" content="{$cfg.net_name} MagIRC IRC Chat Statistics Denora stats phpDenora" />
<meta name="Description" content="{$cfg.net_name} Network Statistics powered by MagIRC" />
<meta property='og:title' content='{$cfg.net_name}'/><meta property='og:url' content='{$smarty.const.BASE_URL}'/><meta property='og:image' content='{$smarty.const.BASE_URL}theme/{$cfg.theme}/img/logofb.png'/><meta property='og:site_name' content='{$cfg.net_name}'/><meta property='og:description' content='{$cfg.net_name} Network Statistics'/>
<base href="{$smarty.const.BASE_URL}" />
<link rel="icon" href="theme/{$cfg.theme}/img/favicon.ico" type="image/x-icon">
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
			<li><a href="{if !$cfg.rewrite_enable}index.php/{/if}network"{if $section eq 'network'} class="active"{/if}><span>&nbsp;Network</span></a></li>
			<li><a href="{if !$cfg.rewrite_enable}index.php/{/if}server"{if $section eq 'server'} class="active"{/if}><span>&nbsp;Servers</span></a></li>
			<li><a href="{if !$cfg.rewrite_enable}index.php/{/if}channel"{if $section eq 'channel'} class="active"{/if}><span>&nbsp;Channels</span></a></li>
			<li><a href="{if !$cfg.rewrite_enable}index.php/{/if}user"{if $section eq 'user'} class="active"{/if}><span>&nbsp;Users</span></a></li>
		</ul>
	</div>
	<div id="loading"><img src="theme/{$cfg.theme}/img/loading.gif" alt="loading..." /></div>
</div>
<div id="main">
	{block name="content"}[content placeholder]{/block}
	{if $cfg.service_adsense_id}
		<script type="text/javascript">
		google_ad_client = "{$cfg.service_adsense_id}";
		google_ad_width = 728;
		google_ad_height = 90;
		google_ad_format = "728x90_as";
		google_ad_type = "text_image";
		google_ad_channel ="{$cfg.service_adsense_channel}";
		google_alternate_color = "f9f3df";
		google_color_border = "CCCCCC";
		google_color_bg = "FFFFFF";
		google_color_link = "333333";
		google_color_text = "666666";
		google_color_url = "0066CC";
		</script>
		<div style="width:728px; margin:auto;"><script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script></div>
	{/if}
</div>
<div id="footer">
	{if $cfg.service_addthis}
	<div class="addthis_toolbox addthis_default_style" style="float:left;">
	<a class="addthis_button_preferred_1"></a>
	<a class="addthis_button_preferred_2"></a>
	<a class="addthis_button_preferred_3"></a>
	<a class="addthis_button_preferred_4"></a>
	<a class="addthis_button_compact"></a>
	<a class="addthis_counter addthis_bubble_style"></a>
	</div>
	<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid="></script>
	{/if}
	powered by <a href="http://www.magirc.org/">MagIRC</a>{if $cfg.version_show} v{$smarty.const.VERSION_FULL}{/if}
</div>
{/block}
{block name="js"}
{if $cfg.cdn_enable}
<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.7.2.min.js"></script>
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
<script type="text/javascript" src="js/jquery.dateformat.js"></script>
{jsmin}
<script type="text/javascript">

var url_base = '{$smarty.const.BASE_URL}{if !$cfg.rewrite_enable}index.php/{/if}';
var theme = '{$cfg.theme}';
var net_roundrobin = '{$cfg.net_roundrobin}';
var net_port = '{$cfg.net_port|default:"6667"}';
var net_port_ssl = '{$cfg.net_port_ssl}';
var format_datetime = 'yyyy-MM-dd HH:mm:ss';
var format_datetime_charts = '%Y-%m-%d %H:%M:%S';
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
		tooltip: { valueDecimals: 0, xDateFormat: format_datetime_charts },
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

function getUserStatus(user) {
	if (user['away']) return '<img src="theme/'+theme+'/img/status/user-away.png" alt="away" title="Away as '+user['nickname']+'" \/>';
	else if (user['online']) return '<img src="theme/'+theme+'/img/status/user-online.png" alt="online" title="Online as '+user['nickname']+'" \/>';
	else return '<img src="theme/'+theme+'/img/status/user-offline.png" alt="offline" title="Offline" \/>';
}
function getUserExtra(user) {
	var out = '';
	if (user['bot']) out += ' <img src="theme/'+theme+'/img/status/bot.png" alt="bot" title="Bot" \/>';
	if (user['service']) out += ' <img src="theme/'+theme+'/img/status/service.png" alt="service" title="Service" \/>';
	if (user['operator']) out += ' <img src="theme/'+theme+'/img/status/operator.png" alt="oper" title="'+user['operator_level']+'" \/>';
	if (user['helper']) out += ' <img src="theme/'+theme+'/img/status/help.png" alt="help" title="Available for help" \/>';
	return out;
}
function getCountryFlag(user) {
	if (user['country_code'] != null && user['country_code'] != '' && user['country_code'] != '??' && user['country_code'] != 'local') {
		return '<img src="theme/'+theme+'/img/flags/'+user['country_code'].toLowerCase()+'.png" alt="'+user['country_code']+'" title="'+user['country']+'" />';
	} else {
		return '<img src="theme/'+theme+'/img/flags/unknown.png" alt="Unknown" title="Unknown" />';
	}
}
function getChannelLinks(chan) {
	var out = '';
	if (net_roundrobin) out += '<a href="irc://'+net_roundrobin+':'+net_port+'/'+encodeURIComponent(chan)+'"><img src="theme/'+theme+'/img/icons/link.png" alt="connect" title="Standard connection" /></a>';
	if (net_roundrobin && net_port_ssl) out += ' <a href="irc://'+net_roundrobin+':+'+net_port_ssl+'/'+encodeURIComponent(chan)+'"><img src="theme/'+theme+'/img/icons/ssl.png" alt="connect" title="Secure connection" /></a>';
	return out;
}
function getTimeElapsed(seconds) {
	var days = Math.floor(seconds / 86400);
	var hours = Math.floor((seconds - (days * 86400 ))/3600)
	var minutes = Math.floor((seconds - (days * 86400 ) - (hours *3600 ))/60)
	return days + " Days " + hours + " Hours " + minutes + " Minutes";
}
{/literal}
</script>
{/jsmin}
{/block}
</body>
</html>