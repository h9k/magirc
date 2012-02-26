{* $Id$ *}
{extends file="components/main.tpl"}
{block name="content"}
<div id="content">

<script type="text/javascript" src="js/highstock.js"></script>
<div id="container" style="height: 350px; min-width: 700px"></div>

<table class="list">
	<tr>
		<th>Channel</th>
		<th>Users</th>
		<th>Max</th>
		<th style="width:100%">Modes</th>
	<tr>
{foreach from=$chanlist item=item}
	{if $item.topic}<tr class="{cycle values="bg1, bg2" advance=false}">
	{else}<tr class="{cycle values="bg1, bg2" advance=true}">{/if}
		<td style="white-space:nowrap;"><a href="?section=channel&amp;channel={$item.name|escape:'url'}">{$item.name}</a></td>
		<td>{$item.users}</td>
		<td>{$item.users_max}</td>
		<td>{if $item.modes}+{$item.modes}{else}&nbsp;{/if}</td>
	</tr>
	{if $item.topic}
	<tr class="{cycle advance=true}">
		<td colspan="4"><div>{$item.topic|irc2html}</div></td>
	</tr>
	{/if}
{foreachelse}
	<tr>
		<td colspan="4">No channels to list</td>
	</tr>
{/foreach}
</table>

</div>

<script type="text/javascript">
<!--
$(document).ready(function() {
    $.getJSON('rest/denora.php/channels/hourlystats', function(data) {
        window.chart = new Highcharts.StockChart({
            chart: {
                renderTo: 'container'
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
            title: {
                text: 'Channels History'
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
{/block}