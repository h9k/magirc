{* $Id$ *}
{include file="_header.tpl"}

<script type="text/javascript" src="js/swfobject.js"></script>
<script type="text/javascript">
swfobject.embedSWF("swf/open-flash-chart.swf", "my_chart", "550", "200", "9.0.0", "expressInstall.swf", { "data-file":"?section=server%26action=json" });
</script>

<div id="content">

<h2>Test chart</h2>
<div id="my_chart"></div>

<h2>Server list</h2>
<table border="0" cellpadding="0" cellspacing="0" class="display">
<thead>
	<tr>
		<th>Status</th>
		<th>Server</th>
		<th>Description</th>
		<th>Users</th>
		<th>Operators</th>
	</tr>
</thead>
<tbody>
	{foreach from=$serverlist item=item}
	<tr class="{cycle values="bg1, bg2" advance="true"}">
		<td><img src="theme/default/img/status/{if $item.online}online{else}offline{/if}.png" alt="{if $item.online}online{else}offline{/if}" title="{if $item.online}online{else}offline{/if}" /></td>
		<td><a href="?section=server&amp;server={$item.server}">{$item.server}</a>{if $item.uline} (<span style="color:blue;">Ulined</span>){/if}</td>
		<td>{$item.comment}</td>
		<td>{$item.currentusers}</td>
		<td>{$item.opers}</td>
	</tr>
	{/foreach}
</tbody>
</table>

<div class="box">
<div class="boxtitle">Servers - <strong>today</strong> | last week | last month | last year</div>
<img src="?section=graph&amp;graph=line&amp;mode=servers&amp;ey={$smarty.now|date_format:"%Y"}&amp;em={$smarty.now|date_format:"%m"}&amp;ed={$smarty.now|date_format:"%d"}" alt="" /><br />
</div>

</div>

<script type="text/javascript">
$(document).ready(function() {
	$('.display').dataTable({
		"bJQueryUI": true,
		"bAutoWidth": true,
		"bProcessing": true,
		"sPaginationType": "full_numbers"
	});
} );
</script>

{include file="_footer.tpl"}