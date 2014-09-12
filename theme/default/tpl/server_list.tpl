<h1>{t}Server list{/t}</h1>
{if $cfg->net_roundrobin}
	{t}Connect to our network round robin{/t} <strong>{$cfg->net_roundrobin}</strong>:
	<a href="irc://{$cfg->net_roundrobin}{if $cfg->net_port}:{$cfg->net_port}{/if}"><img src="theme/{$cfg->theme}/img/icons/server-link.png" alt="standard" title="{t}Standard connection{/t}" /></a>
	{if $cfg->net_port_ssl} <a href="irc://{$cfg->net_roundrobin}:+{$cfg->net_port_ssl}"><img src="theme/{$cfg->theme}/img/icons/ssl.png" alt="ssl" title="{t}Secure connection{/t}" /></a>{/if}
	<br /><br />
{/if}

<table id="tbl_servers" class="display clickable">
<thead>
	<tr>
		<th>{t}Status{/t}</th>
		<th>{t}Server{/t}</th>
		<th>{t}Description{/t}</th>
		<th>{t}Users{/t}</th>
		<th>{t}Operators{/t}</th>
	</tr>
</thead>
<tbody>
	<tr><td colspan="5">{t}Loading{/t}...</td></tr>
</tbody>
</table>
</div>

{jsmin}
<script type="text/javascript">
{literal}
$(document).ready(function() {
	$('#tbl_servers').DataTable({
		"pageLength": 25,
		"order": [[ 1, "asc" ]],
		"ajax": 'rest/service.php/servers?format=datatables',
		"columns": [
			{ "data": "online", "render": function (data, type, row, meta) { return data ? '<img src="theme/'+theme+'/img/status/online.png" alt="online" title="'+mLang.Online+'" \/>' : '<img src="theme/'+theme+'/img/status/offline.png" alt="offline" title="'+mLang.Offline+'" \/>'; } },
			{ "data": "server", "render": function (data, type, row, meta) { return (stats_server == 'denora') ? getCountryFlag(row) + ' ' + data : data;} },
			{ "data": "description" },
			{ "data": "users" },
			{ "data": "opers" }
		]
	});
	$("#tbl_servers tbody").on("click", "tr", function(event) {
		if (this.id) window.location = url_base + 'server/' + encodeURIComponent(this.id) + '/profile';
	});
});
{/literal}
</script>
{/jsmin}
