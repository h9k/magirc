{* $Id$ *}

<script type="text/javascript" src="js/highstock.js"></script>
<div id="container" style="height: 350px; min-width: 700px"></div>

<script type="text/javascript">
<!--
$(function() {
    var seriesOptions = [],
        yAxisOptions = [],
        seriesCounter = 0,
        names = ['Servers', 'Channels', 'Users'],
        colors = Highcharts.getOptions().colors;

    $.each(names, function(i, name) {
        $.getJSON('rest/denora.php/'+ name.toLowerCase() +'/hourlystats', function(data) {
            seriesOptions[i] = {
                name: name,
                data: data
            };
            seriesCounter++;
            if (seriesCounter == names.length) {
                createChart();
            }
        });
    });

    function createChart() {
        chart = new Highcharts.StockChart({
            chart: {
                renderTo: 'container'
            },
            rangeSelector: {
                selected: 4
            },
			xAxis: {
				ordinal: false // Firefox hang workaround
			},
            yAxis: {
				min: 0
            },
            tooltip: {
                pointFormat: '{literal}<span style="color:{series.color}">{series.name}<\/span>: <b>{point.y}<\/b><br\/>{/literal}',
                valueDecimals: 0
            },
            series: seriesOptions
        });
    }

});
-->
</script>
