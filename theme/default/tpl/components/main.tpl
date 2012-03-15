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
<link href="theme/default/css/reset.css" rel="stylesheet" type="text/css" />
<link href="theme/default/css/styles.css" rel="stylesheet" type="text/css" />
<link href="theme/default/css/menu.css" rel="stylesheet" type="text/css" />
<link href="theme/default/css/submenu.css" rel="stylesheet" type="text/css" />
<link href="theme/default/css/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="theme/default/css/datatables.css" rel="stylesheet" type="text/css" />
{/block}
{block name="js"}
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery.datatables.min.js"></script>
<script type="text/javascript" src="js/datatables.plugins.js"></script>
<script type="text/javascript" src="js/highcharts.js"></script>
{/block}
</head>
<body>
{block name="body"}
<div id="header">
<a href="#"><img src="theme/default/img/logo.png" alt="" id="logo" /></a>
<div id="menu">
  <ul>
	<li><a href="?section=home" title="Home"{if $smarty.get.section eq 'home'} class="active"{/if}><span>&nbsp;Home</span></a></li>
	<li><a href="?section=network" title="Network"{if $smarty.get.section eq 'network'} class="active"{/if}><span>&nbsp;Network</span></a></li>
    <li><a href="?section=server" title="Servers"{if $smarty.get.section eq 'server'} class="active"{/if}><span>&nbsp;Servers</span></a></li>
    <li><a href="?section=channel" title="Channels"{if $smarty.get.section eq 'channel'} class="active"{/if}><span>&nbsp;Channels</span></a></li>
    <li><a href="?section=user" title="Users"{if $smarty.get.section eq 'user'} class="active"{/if}><span>&nbsp;Users</span></a></li>
  </ul>
</div>
</div>
<div id="main">
{block name="content"}
[content placeholder]
{/block}
</div>
<div id="footer">
powered by <span style="font-size:12px;"><strong>MagIRC</strong></span> v{$smarty.const.VERSION_FULL}</div>
{/block}
</body>
</html>