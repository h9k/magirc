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
		"bServerSide": true,
		"iDisplayLength": 25,
		"aaSorting": [[ 1, "desc" ]],
		"sAjaxSource": "rest/denora.php/channels?format=datatables",
		"aoColumns": [
			{ "mDataProp": "channel", "fnRender": function (oObj) {
				return getChannelLinks(oObj.aData['channel']) + ' ' + oObj.aData['channel'];
			} },
			{ "mDataProp": "users" },
			{ "mDataProp": "users_max" },
			{ "mDataProp": "modes", "bSortable": false, "fnRender": function (oObj) {
				return (oObj.aData['modes']) ? '+'+oObj.aData['modes'] : '';
			} }
		]
	});
	$("#tbl_channels tbody tr").live("click", function(event) {
		if (this.id) window.location = url_base + 'channel/' + encodeURIComponent(this.id) + '/profile';
	});
	$("#tbl_channels tbody tr a").live("click", function(e) { e.stopPropagation(); });
});
{/literal}
</script>
{/jsmin}
