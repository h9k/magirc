{* $Id$ *}
{include file="_header.tpl"}

<div id="content">

<div class="box">
<div class="boxtitle">Channels - <strong>today</strong> | last week | last month | last year</div>
<img src="?section=graph&amp;graph=line&amp;mode=channels&ey={$smarty.now|date_format:"%Y"}&em={$smarty.now|date_format:"%m"}&ed={$smarty.now|date_format:"%d"}" alt="" /><br />
</div>

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

{include file="_footer.tpl"}