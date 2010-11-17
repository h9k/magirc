{* $Id$ *}
{include file="_header.tpl"}

<script type="text/javascript" src="js/swfobject.js"></script>
<script type="text/javascript">
swfobject.embedSWF("swf/open-flash-chart.swf", "my_chart", "800", "220", "9.0.0", "expressInstall.swf", { "data-file":"?section=server%26action=json" });
</script>

<div id="content">

<h2>Servers today</h2>
<div id="my_chart"></div>

<h2>Server list</h2>
<table border="0" cellpadding="0" cellspacing="0" class="display">
<thead>
	<tr>
		<th>Status</th>
		<th>Server</th>
		<th>Description</th>
		<th>Users</th>
		<th>Operators</th>
	</tr>
</thead>
<tbody>

</tbody>
</table>

</div>

<script type="text/javascript">
$(document).ready(function() {
	$('.display').dataTable({
		"bJQueryUI": true,
		"bAutoWidth": true,
		"bProcessing": true,
		"sAjaxSource": '?section=server&action=list',
		"aoData": [{ "sName": "Status" }, { "sName": "Server" }, { "sName": "Description" }, { "sName": "Users" }, { "sName": "Operators" }],
		"sPaginationType": "full_numbers"
	});
} );
</script>

{include file="_footer.tpl"}