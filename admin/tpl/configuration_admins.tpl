<h1>MagIRC Administrators</h1>

<table id="tbl_admins" class="display clickable">
	<thead>
		<tr><th>Username</th><th>Real name</th><th>Email</th></tr>
	</thead>
	<tbody>
		<tr><td colspan="3">Loading...</td></tr>
	</tbody>
</table>

{jsmin}
<script type="text/javascript">{literal}
$(document).ready(function() {
	$("#tbl_admins").dataTable({
		"bProcessing": true,
		"bServerSide": false,
		"bJQueryUI": true,
		"bAutoWidth": false,
		"sPaginationType": "full_numbers",
		"iDisplayLength": 25,
		"aaSorting": [[ 1, "asc" ]],
		"sAjaxSource": 'index.php/admin/list',
		"aoColumns": [
			{ "mDataProp": "username" },
			{ "mDataProp": "realname" },
			{ "mDataProp": "email" }
		]
	});
});
{/literal}
</script>
{/jsmin}