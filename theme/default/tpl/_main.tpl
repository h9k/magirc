<!DOCTYPE html>
<html>
<head>
<title>{block name="title"}{$cfg->net_name} - {/block}</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="ROBOTS" content="INDEX, FOLLOW" />
<meta name="Keywords" content="{$cfg->net_name} MagIRC IRC Chat Statistics Denora stats phpDenora" />
<meta name="Description" content="{$cfg->net_name} Network Statistics powered by MagIRC" />
<meta property='og:title' content='{$cfg->net_name}'/><meta property='og:url' content='{$smarty.const.BASE_URL}'/><meta property='og:image' content='{$smarty.const.BASE_URL}theme/{$cfg->theme}/img/logofb.png'/><meta property='og:site_name' content='{$cfg->net_name}'/><meta property='og:description' content='{$cfg->net_name} Network Statistics'/>
<base href="{$smarty.const.BASE_URL}" />
<link rel="icon" href="theme/{$cfg->theme}/img/favicon.ico" type="image/x-icon">
{block name="css"}
<link href="theme/{$cfg->theme}/css/styles.css" rel="stylesheet" type="text/css" />
<link href="theme/{$cfg->theme}/css/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="theme/{$cfg->theme}/css/datatables.css" rel="stylesheet" type="text/css" />
{if $cfg->cdn_enable}
<link href='http://fonts.googleapis.com/css?family=Share' rel='stylesheet' type='text/css'>
{else}
<link href="theme/{$cfg->theme}/css/font.css" rel="stylesheet" type="text/css" />
{/if}
{/block}
</head>
<body>
{block name="body"}
<div id="header">
	<a href="./"><img src="theme/{$cfg->theme}/img/magirc.png" alt="MagIRC" title="" id="logo" /></a>
	<div id="menu">
		<ul>
			<li><a href="{if !$cfg->rewrite_enable}index.php/{/if}network"{if $section eq 'network'} class="active"{/if}><span>&nbsp;{t}Network{/t}</span></a></li>
			<li><a href="{if !$cfg->rewrite_enable}index.php/{/if}server"{if $section eq 'server'} class="active"{/if}><span>&nbsp;{t}Servers{/t}</span></a></li>
			<li><a href="{if !$cfg->rewrite_enable}index.php/{/if}channel"{if $section eq 'channel'} class="active"{/if}><span>&nbsp;{t}Channels{/t}</span></a></li>
			<li><a href="{if !$cfg->rewrite_enable}index.php/{/if}user"{if $section eq 'user'} class="active"{/if}><span>&nbsp;{t}Users{/t}</span></a></li>
		</ul>
	</div>
	<div id="loading"><img src="theme/{$cfg->theme}/img/loading.gif" alt="{t}Loading{/t}..." /></div>
</div>
<div id="main">
	{block name="content"}[content placeholder]{/block}
	{if $cfg->service_adsense_id}
		<script type="text/javascript">
		google_ad_client = "{$cfg->service_adsense_id}";
		google_ad_width = 728;
		google_ad_height = 90;
		google_ad_format = "728x90_as";
		google_ad_type = "text_image";
		google_ad_channel ="{$cfg->service_adsense_channel}";
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
	<table>
		<tr>
			<td style="text-align:left;">
				<div id="selectlocale">
					<form method="get" action="./">
					{t}Language{/t}: <select name="locale" id="locale">
					{foreach from=$locales item=item}
						<option value="{$item}"{if $smarty.const.LOCALE eq $item} selected="selected"{/if}>{$item}</option>
					{/foreach}
					</select>
					</form>
				</div>
			</td>
			<td style="text-align:center;">
				{if $cfg->service_addthis}
				<div class="addthis_toolbox addthis_default_style">
				<a class="addthis_button_preferred_1"></a>
				<a class="addthis_button_preferred_2"></a>
				<a class="addthis_button_preferred_3"></a>
				<a class="addthis_button_preferred_4"></a>
				<a class="addthis_button_compact"></a>
				<a class="addthis_counter addthis_bubble_style"></a>
				</div>
				<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid="></script>
				{/if}
			</td>
			<td style="text-align:right;">powered by <a href="http://www.magirc.org/">MagIRC</a>{if $cfg->version_show} v{$smarty.const.VERSION_FULL}{/if}</td>
		</tr>
	</table>
</div>
<ul id="chanmenu" style="display:none;">
	{if $cfg->net_roundrobin}<li data-action="irc"><a href="#"><img src="theme/{$cfg->theme}/img/icons/link.png" alt="" title="{t}Standard connection{/t}" style="vertical-align:middle;" /> {t}IRC standard connection{/t}</a></li>{/if}
	{if $cfg->net_roundrobin && $cfg->net_port_ssl}<li data-action="ircs"><a href="#"><img src="theme/{$cfg->theme}/img/icons/ssl.png" alt="" title="{t}Secure connection{/t}" style="vertical-align:middle;" /> {t}IRC secure connection{/t}</a></li>{/if}
	{if $cfg->service_webchat}<li data-action="webchat"><a href="#"><img src="theme/{$cfg->theme}/img/icons/webchat.png" alt="" title="{t}Webchat{/t}" style="vertical-align:middle;" /> {t}Webchat{/t}</a></li>{/if}
	{if $cfg->net_roundrobin && $cfg->service_mibbit}<li data-action="mibbit"><a href="#"><img src="theme/{$cfg->theme}/img/icons/mibbit.png" alt="" title="Mibbit" style="vertical-align:middle;" /> Mibbit</a></li>{/if}
</ul>
{/block}
{block name="js"}
{if $cfg->cdn_enable}
<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.ui/1.8.19/jquery-ui.min.js"></script>
<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.0/jquery.dataTables.js"></script>
{else}
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery.datatables.min.js"></script>
{/if}
<script type="text/javascript" src="js/datatables.fnReloadAjax.js"></script>
<script type="text/javascript" src="js/highcharts.js"></script>
<script type="text/javascript" src="js/highstock.js"></script>
<script type="text/javascript" src="js/jquery.cookie.js"></script>
<script type="text/javascript" src="js/jquery.dateformat.js"></script>
{jsmin}
<script type="text/javascript">
var url_base = '{$smarty.const.BASE_URL}{if !$cfg->rewrite_enable}index.php/{/if}';
var theme = '{$cfg->theme}';
var net_roundrobin = '{$cfg->net_roundrobin}';
var net_port = '{$cfg->net_port|default:"6667"}';
var net_port_ssl = '{$cfg->net_port_ssl}';
var service_webchat = '{$cfg->service_webchat}';
var service_mibbit = '{$cfg->service_mibbit}';
var denora_version = '{$cfg->denora_version}';
var format_datetime = '{t}yyyy-MM-dd HH:mm:ss{/t}';
var format_datetime_charts = '{t}%Y-%m-%d %H:%M:%S{/t}';
{/jsmin}
var mLang = {
	"Unknown": "{t}Unknown{/t}",
	"AwayAs": "{t}Away as{/t}",
	"OnlineAs": "{t}Online as{/t}",
	"Online": "{t}Online{/t}",
	"Offline": "{t}Offline{/t}",
	"Bot": "{t}Bot{/t}",
	"Service": "{t}Service{/t}",
	"Helper": "{t}Available for help{/t}",
	"Join": "{t}join{/t}",
	"Days": "{t}days{/t}",
	"Hours": "{t}hours{/t}",
	"Minutes": "{t}minutes{/t}",
	"zoom_All": "{t}All{/t}",
	"zoom_1d": "{t}1d{/t}",
	"zoom_1w": "{t}1w{/t}",
	"zoom_1m": "{t}1m{/t}",
	"zoom_3m": "{t}3m{/t}",
	"zoom_6m": "{t}6m{/t}",
	"zoom_1y": "{t}1y{/t}",
	"LoadError": "{t}Unable to load contents{/t}",
	"Message": "{t}Message{/t}",
	"ConnectedSince": "{t}Connected since{/t}",
	"LastQuit": "{t}Last quit{/t}",
	"UsersOnline": "{t}Users online{/t}",
	"ServersOnline": "{t}Servers online{/t}",
	"Servers": "{t}Servers{/t}",
	"Channels": "{t}Channels{/t}",
	"Users": "{t}Users{/t}",
	"Operators": "{t}Operators{/t}",
	"Total": "{t}Total{/t}",
	"Today": "{t}Today{/t}",
	"ThisWeek": "{t}This Week{/t}",
	"ThisMonth": "{t}This Month{/t}",
	"Yes": "{t}Yes{/t}",
	"No": "{t}No{/t}",
	"Never": "{t}Never{/t}",
	"On": "{t}on{/t}",
	"NoMotd": "{t}MOTD not available for this server{/t}",
	"Failed": "{t}Failed{/t}",
	"Close": "{t}Close{/t}",
	"Status": "{t}Status{/t}",
	"CountryStatistics": "{t}Country Statistics{/t}",
	"ClientStatistics": "{t}Client Statistics{/t}",
	"None": "{t}none{/t}",
	"NetsplitRelWeeks": "{t}Relation of users and channels during the last 2 weeks{/t}",
	"NetsplitRelMonths": "{t}Relation of users and channels during the last 2 months{/t}",
	"NetsplitRelYears": "{t}Relation of users and channels during the last 2 years{/t}",
	"NetsplitChanWeeks": "{t}Channels during the last 2 weeks{/t}",
	"NetsplitChanMonths": "{t}Channels during the last 2 months{/t}",
	"NetsplitChanYears": "{t}Channels during the last 2 years{/t}",
	"NetsplitSrvWeeks": "{t}Servers during the last 2 weeks{/t}",
	"NetsplitSrvMonths": "{t}Servers during the last 2 months{/t}",
	"NetsplitSrvYears": "{t}Servers during the last 2 years{/t}",
	"DataTables": {
		"sProcessing": "{t}Processing...{/t}",
		"sLoadingRecords": "{t}Please wait - loading...{/t}",
		"sLengthMenu": "{t}Show _MENU_ entries{/t}",
		"sZeroRecords": "{t}No matching records found{/t}",
		"sEmptyTable": "{t}No data available in table{/t}",
		"sInfo": "{t}Showing _START_ to _END_ of _TOTAL_ entries{/t}",
		"sInfoEmpty": "{t}Showing 0 to 0 of 0 entries{/t}",
		"sInfoFiltered": "{t}(filtered from _MAX_ total entries){/t}",
		"sInfoPostFix": "",
		"sInfoThousands": "'",
		"sSearch": "{t}Search{/t}:",
		"sUrl": "",
		"oAria": {
			"sSortAscending": " - {t}click/return to sort ascending{/t}",
			"sSortDescending": " - {t}click/return to sort descending{/t}"
		},
		"oPaginate": {
			"sFirst": "{t}First{/t}",
			"sPrevious": "{t}Previous{/t}",
			"sNext": "{t}Next{/t}",
			"sLast": "{t}Last{/t}"
		}
	}
}
</script>
<script type="text/javascript" src="js/magirc.min.js"></script>
{/block}
</body>
</html>