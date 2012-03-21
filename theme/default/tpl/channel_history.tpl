{* $Id$ *}

<h1>Channel history</h1>
<div id="container" style="height: 350px; min-width: 700px"></div>

<script type="text/javascript">
<!--
$(document).ready(function() {
    $.getJSON('rest/denora.php/channels/hourlystats', function(data) {
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
                name: 'Channels online',
                data: data,
                step: false,
                tooltip: {
                    valueDecimals: 0
                }
            }]
        });
    });
});
-->
</script>
