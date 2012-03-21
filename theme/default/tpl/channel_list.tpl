{* $Id$ *}

<h1>Channel list</h1>
<table id="tbl_channels" class="display">
	<thead>
		<tr>
			<th>Channel</th>
			<th>Current users</th>
			<th>Max users</th>
		</tr>
	</thead>
	<tbody>
		<tr><td colspan="3">Loading...</td></tr>
	</tbody>
</table>

<script type="text/javascript">
<!--
$(document).ready(function() {
	$('#tbl_channels').dataTable({
		"bJQueryUI": true,
		"bAutoWidth": false,
		"bProcessing": true,
		"bFilter": true,
		"bInfo": true,
		"bLengthChange": true,
		"bPaginate": true,
		"bSort": true,
		"bStateSave": false,
		"bServerSide": true,
		"iDisplayLength": 25,
		"sPaginationType": "full_numbers",
		"aaSorting": [[ 1, "desc" ]],
		"sAjaxSource": "rest/denora.php/channels?format=datatables",
		"aoColumns": [
			{ "mDataProp": "channel" },
			{ "mDataProp": "currentusers" },
			{ "mDataProp": "maxusers" }
		]
	});
});
-->
</script>
