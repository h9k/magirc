{* $Id$ *}

<div id="container" style="height: 350px; min-width: 700px"></div>

<script type="text/javascript">
<!--
$(function() {
	//TODO: make refresh configurable and do not run if the tab is not active
	var refresh = 5; // seconds
	Highcharts.setOptions({
		global: {
			useUTC: false
		}
	});
	$.getJSON('rest/denora.php/network/status', function(result) {
		new Highcharts.Chart({
			chart: {
				renderTo: 'container',
				backgroundColor: 'transparent',
				type: 'spline',
				marginRight: 10,
				events: {
					load: function() {
						var series = this.series;
						setInterval(function() {
							$.getJSON('rest/denora.php/network/status', function(result) {
								var x = (new Date()).getTime();
								series[0].addPoint([x, result.users], true, true);
								series[1].addPoint([x, result.chans], true, true);
								series[2].addPoint([x, result.servers], true, true);
							});
						}, refresh * 1000);
					}
				}
			},
			title: {
				text: 'Current Network Status'
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
			series: [{
				name: 'Users',
				data: (function() {
					var data = [],
                        time = (new Date()).getTime(),
                        i;
                    for (i = -19; i <= 0; i++) {
                        data.push({
                            x: time + i * refresh * 1000,
                            y: result.users
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
                            x: time + i * refresh * 1000,
                            y: result.chans
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
                            x: time + i * refresh * 1000,
                            y: result.servers
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