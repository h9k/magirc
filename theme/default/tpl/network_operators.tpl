<h1>{t}Operators currently online{/t}</h1>
<table id="tbl_operators" class="display clickable">
<thead>
	<tr>
		<th>{t}Nickname{/t}</th>
		<th>{t}Server{/t}</th>
		<th>{t}Online since{/t}</th>
	</tr>
</thead>
<tbody>
	<tr><td colspan="3">{t}Loading{/t}...</td></tr>
</tbody>
</table>

{jsmin}
<script type="text/javascript">
{literal}
$(document).ready(function() {
    if (refresh_interval > 0) {
        setInterval(updateContent, refresh_interval);
    }
    function updateContent() {
        table_operators.ajax.reload();
    }
	var table_operators = $('#tbl_operators').DataTable({
		"pageLength": 25,
		"order": [[ 0, "asc" ]],
		"ajax": 'rest/service.php/operators?format=datatables',
		"columns": [
			{ "data": "nickname", "render": function(data, type, row) {
				return getUserStatus(row) + ' ' + getCountryFlag(row) + ' ' + data + getUserExtra(row);
			} },
			{ "data": "server" },
			{ "data": "connect_time", "render": function(data) { return $.format.date(data, format_datetime); } }
		]
	});
	$("#tbl_operators tbody").on("click", "tr", function() {
		if (this.id) window.location = url_base + 'user/nick:' + encodeURIComponent(this.id) + '/profile';
	});
});
{/literal}
</script>
{/jsmin}
