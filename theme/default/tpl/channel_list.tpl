<h1>{t}Channel list{/t}</h1>
<table id="tbl_channels" class="display clickable">
	<thead>
		<tr>
			<th>{t}Channel{/t}</th>
			<th>{t}Current users{/t}</th>
			<th>{t}Max users{/t}</th>
			<th>{t}Modes{/t}</th>
		</tr>
	</thead>
	<tbody>
		<tr><td colspan="4">{t}Loading{/t}...</td></tr>
	</tbody>
</table>

{jsmin}
<script type="text/javascript">
{literal}
$(document).ready(function() {
	$('#tbl_channels').dataTable({
		"serverSide": true,
		"pageLength": 25,
		"order": [[ 1, "desc" ]],
		"ajax": "rest/service.php/channels?format=datatables",
		"columns": [
			{ "data": "channel", "render": function (data) {
				return getChannelLinks(data) + ' ' + data;
			} },
			{ "data": "users" },
			{ "data": "users_max" },
			{ "data": "modes", "orderable": false, "render": function (data) {
				return data ? '+'+data : '';
			} }
		]
	});
	$("#tbl_channels tbody").on("click", "tr", function(event) {
		if (this.id) window.location = url_base + 'channel/' + encodeURIComponent(this.id) + '/profile';
	});
	$("#tbl_channels tbody").on("click", "tr button", function(event) {
		event.stopPropagation();
		openChanMenu(this);
	});
});
{/literal}
</script>
{/jsmin}
