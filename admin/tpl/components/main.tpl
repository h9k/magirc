{* $Id: _header.tpl 79 2010-07-05 18:15:54Z hal9000 $ *}
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
<body>

<div id="header"{if $error} style="background-color:#FFA0A0;"{elseif $success} style="background-color:#CCEEDD;"{/if}>
	<div id="top">
		<a href="./"><img alt="" src="img/magirc.png" /></a>
                {if $error}
                <div class="error"><h1>Failed</h1>Something wicked happened</div>
                {elseif $success}
                <div class="success"><h1>Succeeded</h1>The settings have been saved</div>
                {/if}
		<div style="float:right; padding-top:25px;">
			<a href="{$config.net_url}">{$config.net_name}</a><br />
			{$smarty.now|date_format:"%d.%m.%Y %H:%M"} - IP: {$smarty.server.REMOTE_ADDR}<br />
			{if $smarty.session.username}Logged in as <strong>{$smarty.session.username}</strong> - <a href="?page=logout">Logout</a>{/if}
		</div>
	</div>
</div>

<div id="bottom"{if !$smarty.session.username} style="width:400px;"{/if}>
{if $smarty.session.username}
<table cellspacing="0" cellpadding="0" border="0" width="100%">
  <tr>
    <td style="width:185px;" valign="top">
	{include file="components/_menu.tpl"}
    </td>
    <td valign="top">
{/if}
	<div id="content"{if !$smarty.session.username} style="width:100%; min-height:200px;"{/if}>
	{block name="content"}[content placeholder]{/block}
	</div>
{if $smarty.session.username}
<div id="footer">Powered by <a href="http://magirc.org/">MagIRC</a> v{$smarty.const.VERSION_FULL}</div>
</td></tr></table>
{/if}
</div>
</body>
</html>