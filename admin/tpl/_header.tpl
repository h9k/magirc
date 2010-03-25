{* $Id$ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<title>{$config.net_name} :: MagIRC ADMIN</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
<meta name="generator" content="MagIRC" />

<link rel="stylesheet" type="text/css" href="../js/yui/reset/reset-min.css" />
<link rel="stylesheet" type="text/css" href="../js/yui/base/base-min.css" />
<link rel="stylesheet" type="text/css" href="../js/yui/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="css/yui_override.css" />
<link rel="stylesheet" type="text/css" href="css/default.css" />
<link rel="stylesheet" type="text/css" href="css/menu.css" />

</head>
<body class="yui-skin-sam">

<div id="header"{if $smarty.session.ERR} style="background-color:#FFA0A0;"{elseif $smarty.session.MSG} style="background-color:#CCEEDD;"{/if}>
	<div id="top">
		<a href="home/"><img alt="" src="img/magirc.png" /></a>
                {if $smarty.session.ERR}
                <div class="error"><h1>Failed</h1>{$smarty.session.ERR}</div>
                {elseif $smarty.session.MSG}
                <div class="success"><h1>Succeeded</h1>{$smarty.session.MSG}</div>
                {/if}
		<div style="float:right; padding-top:25px;">
			<a href="{$config.net_url}" target="_blank">{$config.net_name}</a><br />
			{$smarty.now|date_format:"%d.%m.%Y %H:%M"} - IP: {$smarty.server.REMOTE_ADDR}<br />
			{if $smarty.session.username}Logged in as <strong>{$smarty.session.username}</strong> - <a href="logout/">Logout</a>{/if}
		</div>
	</div>
</div>

<div id="bottom">
<table cellspacing="0" cellpadding="0" border="0" width="100%">
  <tr>
    <td width="185" valign="top">
	{include file="_menu.tpl"}
    </td>
    <td valign="top">
	<div id="content">
