{* $Id$ *}

<h1>Live Network Status</h1>

<table class="details" style="width:100%;">
	<tr>
		<th colspan="2"><h3>Users</h3></th>
		<th colspan="2"><h3>Channels</h3></th>
		<th colspan="2"><h3>Operators</h3></th>
		<th colspan="2"><h3>Servers</h3></th>
	</tr>
	<tr>
		<th>Current:</th><td><span id="net_users" class="val"></span></td>
		<th>Current:</th><td><span id="net_chans" class="val"></span></td>
		<th>Current:</th><td><span id="net_opers" class="val"></span></td>
		<th>Current:</th><td><span id="net_servers" class="val"></span></td>
	</tr>
	<tr>
		<th>Peak:</th><td><span id="net_users_max" class="val"></span> on <span id="net_users_max_time"></span></td>
		<th>Peak:</th><td><span id="net_chans_max" class="val"></span> on <span id="net_chans_max_time"></span></td>
		<th>Peak:</th><td><span id="net_opers_max" class="val"></span> on <span id="net_opers_max_time"></span></td>
		<th>Peak:</th><td><span id="net_servers_max" class="val"></span> on <span id="net_servers_max_time"></span></td>
	</tr>
	<tr>
		<th>Today:</th><td><span id="net_users_today" class="val"></span> on <span id="net_users_today_time"></span></td>
		<td colspan="2" rowspan="3">&nbsp;</td>
	</tr>
</table>

<table>
	<tr>
		<td><div id="chart_users" style="height: 175px; width: 280px;"></div></td>
		<td><div id="chart_chans" style="height: 175px; width: 280px;"></div></td>
		<td><div id="chart_servers" style="height: 175px; width: 280px;"></div></td>
	</tr>
</table>

<table class="details" style="width:100%;">
	<tr>
		<th style="width:33%;"><h3>Current 10 Biggest Chans</h3></th>
		<th style="width:33%;"><h3>Top 10 Channels Today</h3></th>
		<th style="width:33%;"><h3>Top 10 Users Today</h3></th>
	</tr>
	<tr>
		<td valign="top">
			<table id="tbl_biggestchans" class="display">
				<thead>
					<tr>
						<th>Channel</th>
						<th>Users</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2">Loading...</td>
					</tr>
				</tbody>
			</table>
		</td>
		<td valign="top">
			<table id="tbl_top10chans" class="display">
				<thead>
					<tr>
						<th>Channel</th>
						<th>Lines</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2">Loading...</td>
					</tr>
				</tbody>
			</table>
		</td>
		<td valign="top">
			<table id="tbl_top10users" class="display">
				<thead>
					<tr>
						<th>User</th>
						<th>Lines</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2">Loading...</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
</table>

<script type="text/javascript">
<!--
$(function() {
	//TODO: make refresh configurable and do not run if the tab is not active
	var status_refresh = 10; // seconds
	var max_refresh = 30; // seconds
	var tbl_refresh = 15; // seconds
	var chart_users, chart_chans, chart_servers;
	var count = 0;
	function startCron() {
		count++;
		if (count >= 3) {
			updateStatus();
			updateMax();
			setInterval(updateStatus, status_refresh * 1000);
			setInterval(updateMax, max_refresh * 1000); //TODO: replace with internal logic based on status
			setInterval(updateTables, tbl_refresh * 1000);
		}
	}
	function updateStatus() {
		$.getJSON('rest/denora.php/network/status', function(result) {
			var x = (new Date()).getTime();
			chart_users.series[0].addPoint([x, result.users.val], true, true);
			chart_chans.series[0].addPoint([x, result.chans.val], true, true);
			chart_servers.series[0].addPoint([x, result.servers.val], true, true);
			$("#net_users").html(result.users.val);
			$("#net_users_today").html(result.daily_users.val);
			$("#net_users_today_time").html(result.daily_users.time);
			$("#net_users").html(result.users.val);
			$("#net_chans").html(result.chans.val);
			$("#net_servers").html(result.servers.val);
			$("#net_opers").html(result.opers.val);
		});
	}
	function updateMax() {
		$.getJSON('rest/denora.php/network/max', function(result) {
			$("#net_users_max").html(result.users.val);
			$("#net_chans_max").html(result.channels.val);
			$("#net_servers_max").html(result.servers.val);
			$("#net_opers_max").html(result.opers.val);
			$("#net_users_max_time").html(result.users.time);
			$("#net_chans_max_time").html(result.channels.time);
			$("#net_servers_max_time").html(result.servers.time);
			$("#net_opers_max_time").html(result.opers.time);
		});
	}
	function updateTables() {
		oTable1.fnReloadAjax();
		oTable2.fnReloadAjax();
		oTable3.fnReloadAjax();
	}
	Highcharts.setOptions({
		global: {
			useUTC: false
		}
	});
	$.getJSON('rest/denora.php/network/status', function(result) {
		chart_users = new Highcharts.Chart({
			colors: ['#89A54E'],
			chart: {
				renderTo: 'chart_users',
				backgroundColor: 'transparent',
				type: 'spline',
				marginRight: 10,
				style: { fontFamily: 'Share, cursive' },
				events: { load: startCron() }
			},
			credits: { enabled: false },
			title: { text: '' },
			xAxis: {
				type: 'datetime',
				tickPixelInterval: 150
			},
			yAxis: {
				title: { text: 'Users' },
				allowDecimals: false,
				plotLines: [{
					value: 0,
					width: 1,
					color: '#808080'
				}]
			},
			tooltip: {
				formatter: function() {
						return '<b>'+ this.series.name +'</b><br/>'+
						Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', this.x) +'<br/>'+
						Highcharts.numberFormat(this.y, 0);
				}
			},
			legend: { enabled: false },
			exporting: { enabled: false },
			plotOptions: {
                spline: {
                    lineWidth: 4,
                    states: {
                        hover: {
                            lineWidth: 5
                        }
                    },
                    marker: {
                        enabled: false,
                        states: {
                            hover: {
                                enabled: true,
                                symbol: 'circle',
                                radius: 5,
                                lineWidth: 1
                            }
                        }
                    }
                }
            },
			// TODO: cleanup
			series: [{
				name: 'Users',
				data: (function() {
					var data = [],
                        time = (new Date()).getTime(),
                        i;
                    for (i = -19; i <= 0; i++) {
                        data.push({
                            x: time + i * status_refresh * 1000,
                            y: result.users.val
                        });
                    }
                    return data;
                })()
			}]
		});
		chart_chans = new Highcharts.Chart({
			colors: ['#AA4643'],
			chart: {
				renderTo: 'chart_chans',
				backgroundColor: 'transparent',
				type: 'spline',
				marginRight: 10,
				events: { load: startCron() }
			},
			credits: { enabled: false },
			title: { text: '' },
			xAxis: {
				type: 'datetime',
				tickPixelInterval: 150
			},
			yAxis: {
				title: { text: 'Channels' },
				allowDecimals: false,
				plotLines: [{
					value: 0,
					width: 1,
					color: '#808080'
				}]
			},
			tooltip: {
				formatter: function() {
						return '<b>'+ this.series.name +'</b><br/>'+
						Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', this.x) +'<br/>'+
						Highcharts.numberFormat(this.y, 0);
				}
			},
			legend: { enabled: false },
			exporting: { enabled: false },
			plotOptions: {
                spline: {
                    lineWidth: 4,
                    states: {
                        hover: {
                            lineWidth: 5
                        }
                    },
                    marker: {
                        enabled: false,
                        states: {
                            hover: {
                                enabled: true,
                                symbol: 'circle',
                                radius: 5,
                                lineWidth: 1
                            }
                        }
                    }
                }
            },
			// TODO: cleanup
			series: [{
				name: 'Channels',
				data: (function() {
					var data = [],
                        time = (new Date()).getTime(),
                        i;
                    for (i = -19; i <= 0; i++) {
                        data.push({
                            x: time + i * status_refresh * 1000,
                            y: result.chans.val
                        });
                    }
                    return data;
                })()
			}]
		});
		chart_servers = new Highcharts.Chart({
			colors: ['#4572A7'],
			chart: {
				renderTo: 'chart_servers',
				backgroundColor: 'transparent',
				type: 'spline',
				marginRight: 10,
				events: { load: startCron() }
			},
			credits: { enabled: false },
			title: { text: '' },
			xAxis: {
				type: 'datetime',
				tickPixelInterval: 150
			},
			yAxis: {
				title: { text: 'Servers' },
				allowDecimals: false,
				plotLines: [{
					value: 0,
					width: 1,
					color: '#808080'
				}]
			},
			tooltip: {
				formatter: function() {
						return '<b>'+ this.series.name +'</b><br/>'+
						Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', this.x) +'<br/>'+
						Highcharts.numberFormat(this.y, 0);
				}
			},
			legend: { enabled: false },
			exporting: { enabled: false },
			plotOptions: {
                spline: {
                    lineWidth: 4,
                    states: {
                        hover: {
                            lineWidth: 5
                        }
                    },
                    marker: {
                        enabled: false,
                        states: {
                            hover: {
                                enabled: true,
                                symbol: 'circle',
                                radius: 5,
                                lineWidth: 1
                            }
                        }
                    }
                }
            },
			// TODO: cleanup
			series: [{
				name: 'Servers',
				data: (function() {
					var data = [],
                        time = (new Date()).getTime(),
                        i;
                    for (i = -19; i <= 0; i++) {
                        data.push({
                            x: time + i * status_refresh * 1000,
                            y: result.servers.val
                        });
                    }
                    return data;
                })()
			}]
		});
	});
	oTable1 = $("#tbl_biggestchans").dataTable({
		"bProcessing": false,
		"bServerSide": false,
		"bJQueryUI": true,
		"bAutoWidth": false,
		"bFilter": false,
		"bInfo": false,
		"bLengthChange": false,
		"bPaginate": false,
		"bSort": false,
		"bEscapeRegex": false,
		"sAjaxSource": "rest/denora.php/channels/biggest/10?format=datatables",
		"aoColumns": [
			{ "mDataProp": "channel" },
			{ "mDataProp": "currentusers" }
		]
	});
	$("#tbl_biggestchans tbody tr").live("click", function(event) {
		var chan = $(event.target.parentNode)[0].cells[0].innerHTML;
		window.location = url_base + '?section=channel&action=profile&chan=' + escape(chan);
	});
	oTable2 = $("#tbl_top10chans").dataTable({
		"bProcessing": false,
		"bServerSide": false,
		"bJQueryUI": true,
		"bAutoWidth": false,
		"bFilter": false,
		"bInfo": false,
		"bLengthChange": false,
		"bPaginate": false,
		"bSort": false,
		"bEscapeRegex": false,
		"sAjaxSource": "rest/denora.php/channels/top/10?format=datatables",
		"aoColumns": [
			{ "mDataProp": "chan" },
			{ "mDataProp": "line" }
		]
	});
	$("#tbl_top10chans tbody tr").live("click", function(event) {
		var chan = $(event.target.parentNode)[0].cells[0].innerHTML;
		window.location = url_base + '?section=channel&action=stats&chan=' + escape(chan);
	});
	oTable3 = $("#tbl_top10users").dataTable({
		"bProcessing": false,
		"bServerSide": false,
		"bJQueryUI": true,
		"bAutoWidth": false,
		"bFilter": false,
		"bInfo": false,
		"bLengthChange": false,
		"bPaginate": false,
		"bSort": false,
		"bEscapeRegex": false,
		"sAjaxSource": "rest/denora.php/users/top/10?format=datatables",
		"aoColumns": [
			{ "mDataProp": "uname" },
			{ "mDataProp": "line" }
		]
	});
	$("#tbl_top10users tbody tr").live("click", function(event) {
		var user = $(event.target.parentNode)[0].cells[0].innerHTML;
		window.location = url_base + '?section=user&action=stats&user=' + escape(user);
	});
});
-->
</script>