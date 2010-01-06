{* $Id$ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>MagIRC PROTOTYPE TESTING</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="ROBOTS" content="INDEX, FOLLOW" />
<meta name="Keywords" content="MagIRC IRC Chat Statistics Denora stats phpDenora" />
<meta name="Description" content="IRC Statistics powered by MagIRC" />
<base href="{$smarty.const.BASE_URL}" />
<link href="theme/default/css/yui/reset-fonts.css" rel="stylesheet" type="text/css" />
<link href="theme/default/css/yui/base-min.css" rel="stylesheet" type="text/css" />
<link href="theme/default/css/default.css" rel="stylesheet" type="text/css" />
<link href="theme/default/css/menu.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="header">
<h1><strong>MagIRC</strong> <span style="color:#C0C0C0;">Prototype testing...</span></h1>
<div id="tabsH">
  <ul>
	<li><a href="home/" title="Home"{if $smarty.get.section eq 'home'} class="active"{/if}><img src="theme/default/img/menu/home.png" alt="" height="16" width="16" /><span>&nbsp;Home</span></a></li>
	<li><a href="status/" title="Status"{if $smarty.get.section eq 'status'} class="active"{/if}><img src="theme/default/img/menu/status.png" alt="" height="16" width="16" /><span>&nbsp;Status</span></a></li>
	<li><a href="evolution/" title="Evolution"{if $smarty.get.section eq 'evolution'} class="active"{/if}><img src="theme/default/img/menu/evolution.png" alt="" height="16" width="16" /><span>&nbsp;Evolution</span></a></li>
    <li><a href="server/" title="Servers"{if $smarty.get.section eq 'server'} class="active"{/if}><img src="theme/default/img/menu/servers.png" alt="" height="16" width="16" /><span>&nbsp;Servers</span></a></li>
    <li><a href="country/" title="Countries"{if $smarty.get.section eq 'country'} class="active"{/if}><img src="theme/default/img/menu/countries.png" alt="" height="16" width="16" /><span>&nbsp;Countries</span></a></li>
    <li><a href="client/" title="Clients"{if $smarty.get.section eq 'client'} class="active"{/if}><img src="theme/default/img/menu/clients.png" alt="" height="16" width="16" /><span>&nbsp;Clients</span></a></li>
    <li><a href="channel/" title="Channels"{if $smarty.get.section eq 'channel'} class="active"{/if}><img src="theme/default/img/menu/channels.png" alt="" height="16" width="16" /><span>&nbsp;Channels</span></a></li>
    <li><a href="user/" title="Users"{if $smarty.get.section eq 'user'} class="active"{/if}><img src="theme/default/img/menu/users.png" alt="" height="16" width="16" /><span>&nbsp;Users</span></a></li>
	<li><a href="operator/" title="Operators"{if $smarty.get.section eq 'oper'} class="active"{/if}><img src="theme/default/img/menu/operators.png" alt="" height="16" width="16" /><span>&nbsp;Operators</span></a></li>
	<li><a href="search/" title="Search"{if $smarty.get.section eq 'search'} class="active"{/if}><img src="theme/default/img/menu/search.png" alt="" height="16" width="16" /><span>&nbsp;Search</span></a></li>
  </ul>
</div>
</div>
<div id="main">
<div id="content">