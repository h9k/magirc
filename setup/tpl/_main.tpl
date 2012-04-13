<!DOCTYPE html>
<html>
<head>
<title>MagIRC :: Setup</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<link href="../admin/css/styles.css" rel="stylesheet" type="text/css" />
<link href="../admin/css/font.css" rel="stylesheet" type="text/css" />
<link href="../admin/css/jquery-ui.css" rel="stylesheet" type="text/css" />
<style type="text/css">
div#header { width:580px; }
div#main { width:600px; min-height:400px; }
div#footer { width:590px; }
pre { white-space:normal; }
</style>
</head>
<body>
{block name="body"}
<div id="header">
	<a href="./"><img src="../admin/img/magirc.png" alt="MagIRC" title="" id="logo" /></a>
	<div id="loading"><img src="../admin/img/loading.gif" alt="loading..." /></div>
</div>
<div id="main">
	<div id="content">
		<h1>Welcome to the MagIRC Setup</h1>
		<p>Please follow the on-screen instructions to install MagIRC</p>
		{block name="content"}[content placeholder]{/block}
	</div>
</div>
<div id="footer">powered by <a href="http://www.magirc.org/">MagIRC</a></div>
{/block}
{block name="js"}
<script type="text/javascript" src="../js/jquery.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../js/jquery.form.js"></script>
<script type="text/javascript"><!--
$(document).ready(function() {
	$("#loading").ajaxStart(function(){
		$(this).show();
	}).ajaxStop(function(){
		$(this).hide();
	});
	$("button").button();
	$(".next").click(function() { window.location = '?step=' + {$smarty.get.step + 1}; });
});
--></script>
{/block}
</body>
</html>