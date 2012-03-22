{* $Id$ *}

<h1>Live Network Status</h1>

<table class="details">
	<tr>
		<th>Users:</th>
		<td><span id="net_users" class="val"></span></td>
		<th>Peak:</th>
		<td><span id="net_users_max" class="val"></span> on <span id="net_users_max_time"></span></td>
	</tr>
	<tr>
		<th colspan="2">&nbsp;</th>
		<th>Today:</th>
		<td><span id="net_users_today" class="val"></span> on <span id="net_users_today_time"></span></td>
	</tr>
	<tr>
		<th>Channels:</th>
		<td><span id="net_chans" class="val"></span></td>
		<th>Peak:</th>
		<td><span id="net_chans_max" class="val"></span> on <span id="net_chans_max_time"></span></td>
	</tr>
	<tr>
		<th>Opers:</th>
		<td><span id="net_opers" class="val"></span></td>
		<th>Peak:</th>
		<td><span id="net_opers_max" class="val"></span> on <span id="net_opers_max_time"></span></td>
	</tr>
	<tr>
		<th>Servers:</th>
		<td><span id="net_servers" class="val"></span></td>
		<th>Peak:</th>
		<td><span id="net_servers_max" class="val"></span> on <span id="net_servers_max_time"></span></td>
	</tr>
</table>

<h2>Live graph</h2>
<div id="container" style="height: 300px; min-width: 700px;"></div>

<script type="text/javascript">
<!--
$(function() {
	//TODO: make refresh configurable and do not run if the tab is not active
	var status_refresh = 5; // seconds
	var max_refresh = 30;
	var chart;
	function startCron() {
		updateStatus();
		updateMax();
		setInterval(updateStatus, status_refresh * 1000);
		setInterval(updateMax, max_refresh * 1000); //TODO: replace with internal logic based on status
	}
	function updateStatus() {
		$.getJSON('rest/denora.php/network/status', function(result) {
			var x = (new Date()).getTime();
			chart.series[0].addPoint([x, result.users.val], true, true);
			chart.series[1].addPoint([x, result.chans.val], true, true);
			chart.series[2].addPoint([x, result.servers.val], true, true);
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
	Highcharts.setOptions({
		global: {
			useUTC: false
		}
	});
	$.getJSON('rest/denora.php/network/status', function(result) {
		chart = new Highcharts.Chart({
			chart: {
				renderTo: 'container',
				backgroundColor: 'transparent',
				type: 'spline',
				marginRight: 10,
				events: {
					load: function() {
						startCron();
					}
				}
			},
			credits: {
				enabled: false
			},
			title: {
				text: ''
			},
			xAxis: {
				type: 'datetime',
				tickPixelInterval: 150
			},
			yAxis: {
				title: {
					text: 'Amount'
				},
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
			legend: {
				enabled: false
			},
			exporting: {
				enabled: false
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
			},{
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
			},{
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
});
-->
</script>