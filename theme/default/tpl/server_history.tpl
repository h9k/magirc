{* $Id$ *}

<h1>Server history</h1>
<script type="text/javascript" src="js/highstock.js"></script>
<div id="container" style="height: 350px; min-width: 700px"></div>

<script type="text/javascript">
<!--
$(document).ready(function() {
    $.getJSON('rest/denora.php/servers/hourlystats', function(data) {
        window.chart = new Highcharts.StockChart({
            chart: {
                renderTo: 'container',
				backgroundColor: 'transparent'
            },
			credits: {
				enabled: false
			},
			xAxis: {
				ordinal: false // Firefox hang workaround
			},
			yAxis: {
				min: 0
			},
            rangeSelector: {
                selected: 1
            },
            series: [{
                name: 'Servers online',
                data: data,
                step: true,
                tooltip: {
                    valueDecimals: 0
                }
            }]
        });
    });
});
-->
</script>
