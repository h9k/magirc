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
	<tr>
		<td colspan="4" rowspan="2">&nbsp;</td>
		<th>{t}Today{/t}:</th><td><span id="net_users_today" class="val"></span> {t}on{/t} <span id="net_users_today_time"></span></td>
		<td rowspan="2">&nbsp;</td>
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
var refresh_interval = {$cfg->live_interval};
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
				setInterval(updateStatus, refresh_interval * 1000);
				setInterval(updateTables, refresh_interval * 1000);
			}
		}
	}
	function updateStatus() {
		$.getJSON('rest/denora.php/network/status', function(result) {
			var x = (new Date()).getTime();
			chart_line.series[0].addPoint([x, result.servers.val], true, true);
			chart_line.series[1].addPoint([x, result.chans.val], true, true);
			chart_line.series[2].addPoint([x, result.users.val], true, true);
			chart_line.series[3].addPoint([x, result.opers.val], true, true);
			chart_status.series[0].setData([result.servers.val, result.chans.val, result.users.val, result.opers.val ]);
			$("#net_users").html(result.users.val);
			if ($("#net_users").html() > $("#net_users_max")) {
				$("#net_users_max").html(result.users.val);
				$("#net_users_max_time").html($.format.date(result.users.time, format_datetime));
			}
			$("#net_users_today").html(result.daily_users.val);
			$("#net_users_today_time").html($.format.date(result.daily_users.time, format_datetime));
			$("#net_chans").html(result.chans.val);
			if ($("#net_chans").html() > $("#net_chans_max")) {
				$("#net_chans_max").html(result.chans.val);
				$("#net_chans_max_time").html($.format.date(result.chans.time, format_datetime));
			}
			$("#net_servers").html(result.servers.val);
			if ($("#net_servers").html() > $("#net_servers_max")) {
				$("#net_servers_max").html(result.servers.val);
				$("#net_servers_max_time").html($.format.date(result.servers.time, format_datetime));
			}
			$("#net_opers").html(result.opers.val);
			if ($("#net_opers").html() > $("#net_opers_max")) {
				$("#net_opers_max").html(result.opers.val);
				$("#net_opers_max_time").html($.format.date(result.opers.time, format_datetime));
			}
		});
	}
	function updateMax() {
		$.getJSON('rest/denora.php/network/max', function(result) {
			$("#net_users_max").html(result.users.val);
			$("#net_chans_max").html(result.channels.val);
			$("#net_servers_max").html(result.servers.val);
			$("#net_opers_max").html(result.opers.val);
			$("#net_users_max_time").html($.format.date(result.users.time, format_datetime));
			$("#net_chans_max_time").html($.format.date(result.channels.time, format_datetime));
			$("#net_servers_max_time").html($.format.date(result.servers.time, format_datetime));
			$("#net_opers_max_time").html($.format.date(result.opers.time, format_datetime));
		});
	}
	function updateTables() {
		oTable1.fnReloadAjax();
		oTable2.fnReloadAjax();
		oTable3.fnReloadAjax();
	}
	function initData() {
		var data = [], time = (new Date()).getTime();
		for (i = -19; i <= 0; i++) {
			data.push({ x: time + i * refresh_interval * 1000, y: null });
		}
		return data;
	}
	oTable1 = $("#tbl_biggestchans").dataTable({
		"bProcessing": false,
		"bFilter": false,
		"bInfo": false,
		"bLengthChange": false,
		"bPaginate": false,
		"bSort": false,
		"bEscapeRegex": false,
		"sAjaxSource": "rest/denora.php/channels/biggest/10?format=datatables",
		"aoColumns": [
			{ "mDataProp": "channel", "fnRender": function (oObj) {
				return getChannelLinks(oObj.aData['channel']) + ' ' + oObj.aData['channel'];
			} },
			{ "mDataProp": "users" }
		]
	});
	$("#tbl_biggestchans tbody tr").live("click", function(event) {
		if (this.id) window.location = url_base + 'channel/' + encodeURIComponent(this.id) + '/profile';
	});
	$("#tbl_biggestchans tbody tr a").live("click", function(e) { e.stopPropagation(); });
	oTable2 = $("#tbl_top10chans").dataTable({
		"bProcessing": false,
		"bFilter": false,
		"bInfo": false,
		"bLengthChange": false,
		"bPaginate": false,
		"bSort": false,
		"bEscapeRegex": false,
		"sAjaxSource": "rest/denora.php/channels/top/10?format=datatables",
		"aoColumns": [
			{ "mDataProp": "channel", "fnRender": function (oObj) {
				return getChannelLinks(oObj.aData['channel']) + ' ' + oObj.aData['channel'];
			} },
			{ "mDataProp": "lines" }
		]
	});
	$("#tbl_top10chans tbody tr").live("click", function(event) {
		if (this.id) window.location = url_base + 'channel/' + encodeURIComponent(this.id) + '/profile#activity';
	});
	$("#tbl_top10chans tbody tr a").live("click", function(e) { e.stopPropagation(); });
	oTable3 = $("#tbl_top10users").dataTable({
		"bProcessing": false,
		"bFilter": false,
		"bInfo": false,
		"bLengthChange": false,
		"bPaginate": false,
		"bSort": false,
		"bEscapeRegex": false,
		"sAjaxSource": "rest/denora.php/users/top/10?format=datatables",
		"aoColumns": [
			{ "mDataProp": "uname", "fnRender": function(oObj) {
				return getUserStatus(oObj.aData) + ' ' + getCountryFlag(oObj.aData) + ' ' + oObj.aData['uname'] + getUserExtra(oObj.aData);
			} },
			{ "mDataProp": "lines" }
		]
	});
	$("#tbl_top10users tbody tr").live("click", function(event) {
		if (this.id) window.location = url_base + 'user/stats:' + encodeURIComponent(this.id) + '/profile';
	});
	if (searchirc) {
		$("#searchirc_ranking").html($(".searchirc6").html());
	}
});
{/literal}
</script>
{/jsmin}
