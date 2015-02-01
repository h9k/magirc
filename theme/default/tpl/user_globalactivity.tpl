<h1>{t}Global user activity{/t}</h1>

<form>
	<div id="radio" class="choser">
		<input type="radio" id="radio0" name="radio" value="total" /><label for="radio0">{t}Total{/t}</label>
		<input type="radio" id="radio1" name="radio" value="daily" /><label for="radio1">{t}Today{/t}</label>
		<input type="radio" id="radio2" name="radio" value="weekly" /><label for="radio2">{t}This Week{/t}</label>
		<input type="radio" id="radio3" name="radio" value="monthly" checked="checked" /><label for="radio3">{t}This Month{/t}</label>
	</div>
</form>

<table id="tbl_activity" class="display clickable">
	<thead>
		<tr><th>{t}Nickname{/t}</th><th>{t}Letters{/t}</th><th>{t}Words{/t}</th><th>{t}Lines{/t}</th><th>{t}Actions{/t}</th><th>{t}Smileys{/t}</th><th>{t}Kicks{/t}</th><th>{t}Modes{/t}</th><th>{t}Topics{/t}</th></tr>
	</thead>
	<tbody>
		<tr><td colspan="9">{t}Loading{/t}...</td></tr>
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
        table_activity.ajax.reload(null, false);
    }
	var type = 'monthly';
	var table_activity = $('#tbl_activity').DataTable({
		"serverSide": true,
		"pageLength": 25,
		"order": [[ 3, "desc" ]],
		"ajax": "rest/service.php/users/activity/"+type+"?format=datatables",
		"columns": [
			{ "data": "uname", "render": function(data, type, row, meta) {
				return getUserStatus(row) + ' ' + getCountryFlag(row) + ' ' + escapeTags(data) + getUserExtra(row);
			} },
			{ "data": "letters" },
			{ "data": "words" },
			{ "data": "lines" },
			{ "data": "actions" },
			{ "data": "smileys" },
			{ "data": "kicks" },
			{ "data": "modes" },
			{ "data": "topics" }
		]
	});
	$("#tbl_activity tbody").on("click", "tr", function() {
		if (this.id) window.location = url_base + 'user/stats:' + encodeURIComponent(this.id) + '/profile#activity';
	});
	$("#radio").buttonset();
	$("#radio").change(function(event) {
		type = $('input[name=radio]:checked').val();
        table_activity.ajax.url("rest/service.php/users/activity/"+type+"?format=datatables").load();
	});
});
{/literal}
</script>
{/jsmin}
