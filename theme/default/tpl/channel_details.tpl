{* $Id$ *}
{include file="_header.tpl"}
{include file="_menu_channel.tpl"}

<div id="content">

<div class="box">
<div class="boxtitle">Channel Details</div>

<table border="0" cellspacing="0" cellpadding="0" class="status">
	<tr>
		<th><img src="theme/default/img/icons/channel.png" alt="" title="" /> Modes</th>
		<td colspan="3">{if $channel->modes}+{$channel->modes}{else}none{/if}</td>
		<th rowspan="4" valign="top" style="white-space:normal">
			Topic <div class="topic">{$channel->topic|irc2html}</div>
			Set by {$channel->topic_author} on {$channel->topic_time|date_format:"%Y-%m-%d %H:%M"}<br />
		</th>
	</tr>
	<tr>
		<th><img src="theme/default/img/icons/user.png" alt="" title="" /> Current users</th>
		<td>{$channel->users}</td>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<th><img src="theme/default/img/icons/max.png" alt="" title="" /> User peak</th>
		<td>{$channel->users_max}</td>
		<th><img src="theme/default/img/icons/calendar.png" alt="on" title="" /></th>
		<td>{$channel->users_max_time|date_format:"%Y-%m-%d %H:%M"}</td>
	</tr>
	<tr>
		<th><img src="theme/default/img/icons/user.png" alt="" title="" /> Kicks</th>
		<td>{$channel->kicks}</td>
		<td colspan="2">&nbsp;</td>
	</tr>
</table>

</div>

</div>

{include file="_footer.tpl"}