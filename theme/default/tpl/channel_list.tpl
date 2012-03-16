{* $Id$ *}
{extends file="components/main.tpl"}
{block name="content"}

<h1>Channel history</h1>
<div id="container" style="height: 350px; min-width: 700px"></div>

<h1>Channel list</h1>
<table class="display">
	<thead>
		<tr>
			<th>Channel</th>
			<th>Current users</th>
			<th>Max users</th>
		</tr>
	</thead>
	<tbody>
		<tr><td colspan="3">Loading...</td></tr>
	</tbody>
</table>

</div>

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
            /*title: {
                text: 'Channels History'
            },*/
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

	$('.display').dataTable({
		"bJQueryUI": true,
		"bAutoWidth": false,
		"bProcessing": true,
		"bFilter": true,
		"bInfo": true,
		"bLengthChange": true,
		"bPaginate": true,
		"bSort": true,
		"bStateSave": false,
		"bServerSide": true,
		"iDisplayLength": 10,
		"sPaginationType": "full_numbers",
		"aaSorting": [[ 1, "asc" ]],
		"sAjaxSource": "rest/denora.php/channels?format=datatables",
		"aoColumns": [
			{ "mDataProp": "channel" },
			{ "mDataProp": "currentusers" },
			{ "mDataProp": "maxusers" }
		]
	});
});
-->
</script>
{/block}