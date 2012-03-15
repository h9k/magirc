{* $Id$ *}

<script type="text/javascript" src="http://code.highcharts.com/highcharts.js"></script>
<div id="chart-clients" style="min-width: 700px; height: 400px; margin: 0 auto"></div>

<script type="text/javascript">
<!--
$(document).ready(function() {
    $.getJSON('rest/denora.php/clientstats', function(data) {
        new Highcharts.Chart({
			chart: {
				renderTo: 'chart-clients',
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false
			},
			title: {
				text: 'Current Client Statistics'
			},
			tooltip: {
				formatter: function() {
					return '<b>'+ this.point.name +'<\/b>: '+ Math.round(this.percentage * 100) / 100 +' %';
				}
			},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					cursor: 'pointer',
					dataLabels: {
						enabled: true,
						color: '#000000',
						connectorColor: '#000000',
						formatter: function() {
							return '<b>'+ this.point.name +'<\/b>: '+ Math.round(this.percentage * 100) / 100 +' %';
						}
					}
				}
			},
			series: [{
				type: 'pie',
				name: 'Client Statistics',
				data: data
			}]
		});
	});
});
-->
</script>
