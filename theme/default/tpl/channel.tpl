{* $Id$ *}
{include file="_header.tpl"}

<table>
	<tr>
		<th>Channel</th>
		<th>Users</th>
		<th>Modes</th>
	<tr>
{foreach from=$chanlist item=item}
	<tr>
		<td><a href="channel/?channel={$item.name|escape:'url'}">{$item.name}</a></td>
		<td>{$item.users}</td>
		<td>{if $item.modes}+{$item.modes}{else}&nbsp;{/if}</td>
	</tr>
	<tr>
		<td colspan="3">{$item.topic|irc2html}</td>
	</tr>
{foreachelse}
	<tr>
		<td colspan="3">No channels to list</td>
	</tr>
{/foreach}
</table>

{include file="_footer.tpl"}