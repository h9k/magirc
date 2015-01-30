<div id="welcome"><h1>{if $cfg->live_interval}{t}Live Network Status{/t}{else}{t}Network Status{/t}{/if}</h1></div>

<table class="details" style="width:100%;">
	<tr>
		<th colspan="2"><h3>{t}Servers{/t}</h3></th>
		<th colspan="2"><h3>{t}Channels{/t}</h3></th>
		<th colspan="2"><h3>{t}Users{/t}</h3></th>
		<th colspan="2"><h3>{t}Operators{/t}</h3></th>
	</tr>
	<tr>
		<th>{t}Current{/t}:</th><td><span id="net_servers" class="val"></span></td>
		<th>{t}Current{/t}:</th><td><span id="net_chans" class="val"></span></td>
		<th>{t}Current{/t}:</th><td><span id="net_users" class="val"></span></td>
		<th>{t}Current{/t}:</th><td><span id="net_opers" class="val"></span></td>
	</tr>
	<tr>
		<th>{t}Peak{/t}:</th><td><span id="net_servers_max" class="val"></span> {t}on{/t} <span id="net_servers_max_time"></span></td>
		<th>{t}Peak{/t}:</th><td><span id="net_chans_max" class="val"></span> {t}on{/t} <span id="net_chans_max_time"></span></td>
		<th>{t}Peak{/t}:</th><td><span id="net_users_max" class="val"></span> {t}on{/t} <span id="net_users_max_time"></span></td>
		<th>{t}Peak{/t}:</th><td><span id="net_opers_max" class="val"></span> {t}on{/t} <span id="net_opers_max_time"></span></td>
	</tr>
</table>

<table>
	<tr>
		<td><div id="chart_line" style="height: 175px; width: {if $cfg->service_searchirc}446{else}560{/if}px;"></div></td>
		<td><div id="chart_status" style="height: 175px; width: 280px;"></div></td>
		{if $cfg->service_searchirc}<td style="width: 114px; margin: auto; vertical-align: top; text-align: center;">
			<img height="40" width="114" border="0" alt="Overall_Ranking" src="http://searchirc.com/img/ranked_logo.gif">
			<br /><a target="_blank" href="http://searchirc.com/rank/{$cfg->service_searchirc}">{$cfg->net_name}</a>
			<br /><span id="searchirc_ranking"></span>
		</td>{/if}
	</tr>
</table>

<table class="details" style="width:100%;">
	<tr>
		<th style="width:33%;"><h3>{t}Current 10 Biggest Chans{/t}</h3></th>
		<th style="width:33%;"><h3>{t}Top 10 Channels Today{/t}</h3></th>
		<th style="width:33%;"><h3>{t}Top 10 Users Today{/t}</h3></th>
	</tr>
	<tr>
		<td valign="top">
			<table id="tbl_biggestchans" class="display clickable">
				<thead>
					<tr>
						<th>{t}Channel{/t}</th>
						<th>{t}Users{/t}</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2">{t}Loading{/t}...</td>
					</tr>
				</tbody>
			</table>
		</td>
		<td valign="top">
			<table id="tbl_top10chans" class="display clickable">
				<thead>
					<tr>
						<th>{t}Channel{/t}</th>
						<th>{t}Lines{/t}</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2">{t}{t}Loading{/t}...{/t}</td>
					</tr>
				</tbody>
			</table>
		</td>
		<td valign="top">
			<table id="tbl_top10users" class="display clickable">
				<thead>
					<tr>
						<th>{t}User{/t}</th>
						<th>{t}Lines{/t}</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2">{t}{t}Loading{/t}...{/t}</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
</table>
{jsmin}
<script type="text/javascript">
var welcome_msg = '{$cfg->welcome_mode}';
var searchirc = '{$cfg->service_searchirc}';
{literal}
$(document).ready(function() {
	if (welcome_msg == 'statuspage') {
		$.get('index.php/content/welcome', function(result) {
			$("#welcome").html(result);
		});
	}
	var count = 0;

	var chart_line = new Highcharts.Chart({
		chart: { type: 'line', renderTo: 'chart_line', events: { load: startCron() } },
		yAxis: { title: { text: null } },
		series: [{ name: mLang.Servers, data: initData(), visible:false }, { name: mLang.Channels, data: initData(), visible:false }, { name: mLang.Users, data: initData() }, { name: mLang.Operators, data: initData(), visible:false }],
		legend: { enabled: true }
	});
	var chart_status = new Highcharts.Chart({
		chart: { renderTo: 'chart_status', type: 'column', events: { load: startCron() } },
		xAxis: { type: 'linear', categories: [ mLang.Servers, mLang.Channels, mLang.Users, mLang.Operators,  ], labels: { rotation: -45, align: 'right' } },
		yAxis: { min: 0, title: { text: null } },
		tooltip: { formatter: function() { return '<b>'+ this.x +'</b>: '+ Highcharts.numberFormat(this.y, 0); } },
		series: [{ name: mLang.Status, data: [0, 0, 0] }]
	});

	function startCron() {
		count++;
		if (count >= 2) {
			updateStatus();
			updateMax();
			if (refresh_interval > 0) {
				setInterval(updateStatus, refresh_interval);
				setInterval(updateTables, refresh_interval);
			}
		}
	}
	function updateStatus() {
		$.getJSON('rest/service.php/network/status', function(result) {
			var x = (new Date()).getTime();
			chart_line.series[0].addPoint([x, result.servers.val], true, true);
			chart_line.series[1].addPoint([x, result.chans.val], true, true);
			chart_line.series[2].addPoint([x, result.users.val], true, true);
			chart_line.series[3].addPoint([x, result.opers.val], true, true);
			chart_status.series[0].setData([result.servers.val, result.chans.val, result.users.val, result.opers.val ]);
			$("#net_users").text(result.users.val);
			if ($("#net_users").text() > $("#net_users_max")) {
				$("#net_users_max").text(result.users.val);
				$("#net_users_max_time").text($.format.date(result.users.time, format_datetime));
			}
			$("#net_chans").text(result.chans.val);
			if ($("#net_chans").text() > $("#net_chans_max")) {
				$("#net_chans_max").text(result.chans.val);
				$("#net_chans_max_time").text($.format.date(result.chans.time, format_datetime));
			}
			$("#net_servers").text(result.servers.val);
			if ($("#net_servers").text() > $("#net_servers_max")) {
				$("#net_servers_max").text(result.servers.val);
				$("#net_servers_max_time").text($.format.date(result.servers.time, format_datetime));
			}
			$("#net_opers").text(result.opers.val);
			if ($("#net_opers").text() > $("#net_opers_max")) {
				$("#net_opers_max").text(result.opers.val);
				$("#net_opers_max_time").text($.format.date(result.opers.time, format_datetime));
			}
		});
	}
	function updateMax() {
		$.getJSON('rest/service.php/network/max', function(result) {
			$("#net_users_max").text(result.users.val);
			$("#net_chans_max").text(result.channels.val);
			$("#net_servers_max").text(result.servers.val);
			$("#net_opers_max").text(result.opers.val);
			$("#net_users_max_time").text($.format.date(result.users.time, format_datetime));
			$("#net_chans_max_time").text($.format.date(result.channels.time, format_datetime));
			$("#net_servers_max_time").text($.format.date(result.servers.time, format_datetime));
			$("#net_opers_max_time").text($.format.date(result.opers.time, format_datetime));
		});
	}
	function updateTables() {
		table1.ajax.reload();
		table2.ajax.reload();
		table3.ajax.reload();
	}
	function initData() {
		var data = [], time = (new Date()).getTime();
		for (i = -19; i <= 0; i++) {
			data.push({ x: time + i * refresh_interval * 1000, y: null });
		}
		return data;
	}
	var table1 = $("#tbl_biggestchans").DataTable({
		"processing": false,
		"searching": false,
		"info": false,
		"lengthChange": false,
		"paging": false,
		"ordering": false,
		"ajax": "rest/service.php/channels/biggest/10?format=datatables",
		"columns": [
			{ "data": "channel", "render": function (data) {
				return getChannelLinks(data) + ' ' + escapeTags(data);
			} },
			{ "data": "users" }
		]
	});
	$("#tbl_biggestchans tbody").on("click", "tr", function(event) {
		if (this.id) window.location = url_base + 'channel/' + encodeURIComponent(this.id) + '/profile';
	});
	$("#tbl_biggestchans tbody").on("click", "tr button", function(event) {
		event.stopPropagation();
		openChanMenu(this);
	});
	var table2 = $("#tbl_top10chans").DataTable({
		"processing": false,
		"searching": false,
		"info": false,
		"lengthChange": false,
		"paging": false,
		"ordering": false,
		"ajax": "rest/service.php/channels/top/10?format=datatables",
		"columns": [
			{ "data": "channel", "render": function (data) {
				return getChannelLinks(data) + ' ' + escapeTags(data);
			} },
			{ "data": "lines" }
		]
	});
	$("#tbl_top10chans tbody").on("click", "tr", function(event) {
		if (this.id) window.location = url_base + 'channel/' + encodeURIComponent(this.id) + '/profile#activity';
	});
	$("#tbl_top10chans tbody").on("click", "tr button", function(event) {
		event.stopPropagation();
		openChanMenu(this);
	});
	var table3 = $("#tbl_top10users").DataTable({
		"processing": false,
		"searching": false,
		"info": false,
		"lengthChange": false,
		"paging": false,
		"ordering": false,
		"ajax": "rest/service.php/users/top/10?format=datatables",
		"columns": [
			{ "data": "uname", "render": function(data, type, row) {
				return getUserStatus(row) + ' ' + getCountryFlag(row) + ' ' + escapeTags(data) + getUserExtra(row);
			} },
			{ "data": "lines" }
		]
	});
	$("#tbl_top10users tbody").on("click", "tr", function(event) {
		if (this.id) window.location = url_base + 'user/stats:' + encodeURIComponent(this.id) + '/profile';
	});
	if (searchirc) {
		$("#searchirc_ranking").html($(".searchirc6").html());
	}
});
{/literal}
</script>
{/jsmin}
