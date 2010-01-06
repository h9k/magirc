{* $Id$ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{$config.net_name} :: MagIRC ADMIN</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
<link href="css/default.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="header">
<div style="width:800px; margin:0 auto;">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td align="left" valign="bottom"><img src="img/logo.png" alt="" width="138" height="50" longdesc="http://www.magirc.org/" /> <strong>Administration Panel</strong></td>
      <td align="right" valign="bottom">
{if $smarty.session.loginUsername}
	Logged in as: <strong>{$smarty.session.loginUsername}</strong> [<a href="?page=logout">logout</a>]
{else}
	<em>Not logged in</em>
{/if}
      </td>
    </tr>
  </table>
</div>
</div>
<div id="content">
  <table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr>
      <td align="left" valign="top" class="menu">{if $smarty.session.loginUsername}{include file="_menu.tpl"}{/if}</td>
      <td align="left" valign="top">
      {if $success}<div class="configsave">Configuration saved</div>{/if}