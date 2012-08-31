<!DOCTYPE html>
<html>
<head>
<title>{block name="title"}MagIRC - {/block}</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
<meta name="Keywords" content="MagIRC IRC Chat Statistics Denora stats phpDenora" />
<meta name="Description" content="IRC Statistics powered by MagIRC" />
<base href="{$smarty.const.BASE_URL}" />
<link rel="icon" href="../favicon.ico" type="image/x-icon">
{block name="css"}
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="css/datatables.css" rel="stylesheet" type="text/css" />
{if $cfg->cdn_enable}
<link href='http://fonts.googleapis.com/css?family=Share' rel='stylesheet' type='text/css'>
{else}
<link href="css/font.css" rel="stylesheet" type="text/css" />
{/if}
{/block}
</head>
<body>
{block name="body"}
<div id="header">
	<a href="./"><img src="img/magirc.png" alt="MagIRC" title="" id="logo" /></a>
	<div id="menu">
		<ul>
			<li><a href="index.php/overview"{if $section eq 'overview'} class="active"{/if}><span>&nbsp;Overview</span></a></li>
			<li><a href="index.php/configuration"{if $section eq 'configuration'} class="active"{/if}><span>&nbsp;Configuration</span></a></li>
			<li><a href="index.php/support"{if $section eq 'support'} class="active"{/if}><span>&nbsp;Support</span></a></li>
		</ul>
	</div>
	<div id="loading"><img src="img/loading.gif" alt="loading..." /></div>
</div>
<div id="main">{block name="content"}[content placeholder]{/block}
</div>
<div id="footer">powered by <a href="http://www.magirc.org/">MagIRC</a>{if $cfg->version_show} v{$smarty.const.VERSION_FULL}{/if}</div>
{/block}
{block name="js"}
{if $cfg->cdn_enable}
<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.ui/1.8.23/jquery-ui.min.js"></script>
<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.3/jquery.dataTables.js"></script>
{else}
<script type="text/javascript" src="../js/jquery.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../js/jquery.datatables.min.js"></script>
{/if}
<script type="text/javascript" src="../js/jquery.form.js"></script>
{jsmin}
<script type="text/javascript">
{literal}
var url_base = '{$smarty.const.BASE_URL}';
$(document).ready(function() {
	$("#loading").ajaxStart(function(){
		$(this).show();
	}).ajaxStop(function(){
		$(this).hide();
	});
});
{/literal}
</script>
{/jsmin}
{/block}
</body>
</html>
