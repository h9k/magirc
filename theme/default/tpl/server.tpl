{* $Id$ *}
{include file="_header.tpl"}

<div id="content">

<h2>Server list</h2>
<table border="0" cellpadding="0" cellspacing="0" class="list">
	<tr>
		<th>&nbsp;</th>
		<th>Server</th>
		<th>Description</th>
		<th>Users</th>
		<th>Operators</th>
	</tr>
	{foreach from=$serverlist item=item}
	<tr class="{cycle values="bg1, bg2" advance="true"}">
		<td><img src="theme/default/img/status/{if $item.online}online{else}offline{/if}.png" alt="{if $item.online}online{else}offline{/if}" title="{if $item.online}online{else}offline{/if}" /></td>
		<td><a href="?section=server&amp;server={$item.server}">{$item.server}</a>{if $item.uline} (<span style="color:blue;">Ulined</span>){/if}</td>
		<td>{$item.comment}</td>
		<td>{$item.currentusers}</td>
		<td>{$item.opers}</td>
	</tr>
	{foreachelse}
	<tr>
		<td colspan="5"><em>no servers to display</em></td>
	{/foreach}
</table>

<div class="box">
<div class="boxtitle">Servers - <strong>today</strong> | last week | last month | last year</div>
<img src="?section=graph&amp;graph=line&amp;mode=servers&amp;ey={$smarty.now|date_format:"%Y"}&amp;em={$smarty.now|date_format:"%m"}&amp;ed={$smarty.now|date_format:"%d"}" alt="" /><br />
</div>

</div>

{include file="_footer.tpl"}