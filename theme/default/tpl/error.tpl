{* $Id$ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Magirc PROTOTYPE TESTING</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="ROBOTS" content="INDEX, FOLLOW" />
<meta name="Keywords" content="Magirc IRC Chat Statistics Denora stats phpDenora" />
<meta name="Description" content="IRC Statistics powered by Magirc" />
<base href="{$smarty.const.BASE_URL}" />
<link href="theme/default/css/bsod.css" rel="stylesheet" type="text/css" />
</head>
<body>
*** STOP: {$err_msg}
<br /><br />
{if $smarty.const.DEBUG}
{foreach from=$server item=item key=key}
{$key}: {$item}<br />
{/foreach}
{else}
This should not have happened. Please contact the Administrator.<br />
If you are the Administrator, please enable Debug mode to see more information<br />
and submit it to the Magirc developers. Thank you!
{/if}
</body>
</html>