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
		"bServerSide": true,
		"iDisplayLength": 25,
		"aaSorting": [[ 1, "desc" ]],
		"sAjaxSource": "rest/denora.php/channels?format=datatables",
		"aoColumns": [
			{ "mDataProp": "name" },
			{ "mDataProp": "users" },
			{ "mDataProp": "users_max" }
		]
	});
	$("#tbl_channels tbody tr").live("click", function(event) {
		var chan = $(event.target.parentNode)[0].cells[0].innerHTML;
		window.location = url_base + '/channel/' + escape(chan) + '/profile';
	});
});
-->
</script>
