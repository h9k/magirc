{* $Id$ *}
{extends file="components/main.tpl"}
{block name="content"}
<div id="content">

<h1>User history</h1>
<div id="container" style="height: 350px; min-width: 700px"></div>

</div>
<script type="text/javascript">
<!--
$(document).ready(function() {
    $.getJSON('rest/denora.php/users/hourlystats', function(data) {
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
            /*title: {
                text: 'Users History'
            },*/
            series: [{
                name: 'Users online',
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
{/block}