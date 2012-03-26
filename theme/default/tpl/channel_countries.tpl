{* $Id$ *}

<h1>Current Country Statistics for {$smarty.get.chan}</h1>
<div id="chart-countries" style="min-width: 700px; height: 400px; margin: 0 auto"></div>

<script type="text/javascript">
<!--
$(document).ready(function() {
    $.getJSON('rest/denora.php/countrystats/{$smarty.get.chan|escape:'url'}', function(data) {
        new Highcharts.Chart({
			chart: {
				renderTo: 'chart-countries',
				backgroundColor: 'transparent',
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false
			},
			credits: {
				enabled: false
			},
			title: {
				text: ''
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
				name: 'Country Statistics share',
				data: data
			}]
		});
	});
});
-->
</script>
