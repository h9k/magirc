{* $Id$ *}
{include file="_header.tpl"}

<div id="content">

<div class="box" style="width:170px; height:220px; float:left;">
<div class="boxtitle">Current Status</div>
<table border="0" cellspacing="0" cellpadding="0" class="status">
	<tr>
		<th><img src="theme/default/img/icons/user.png" alt="" title="" /> Users</th>
		<td>{$status->current_users}</td>
	</tr>
	<tr>
		<th><img src="theme/default/img/icons/channel.png" alt="" title="" /> Channels</th>
		<td>{$status->current_chans}</td>
	</tr>
	<tr>
		<th><img src="theme/default/img/icons/server.png" alt="" title="" /> Servers</th>
		<td>{$status->current_servers}</td>
	</tr>
	<tr>
		<th><img src="theme/default/img/icons/oper.png" alt="" title="" /> Opers</th>
		<td>{$status->current_opers}</td>
	</tr>
</table>
</div>

<div class="box" style="width:380px; height:220px; float:left;">
<div class="boxtitle">Peak Values</div>
<table border="0" cellspacing="0" cellpadding="0" class="status">
	<tr>
		<th><img src="theme/default/img/icons/user.png" alt="" /> Users</th>
		<td>{$status->max_users}</td>
		<th><img src="theme/default/img/icons/calendar.png" alt="on" title="" /></th>
		<td>{$status->max_users_time|date_format:"%Y-%m-%d %H:%M"}</td>
	</tr>
	<tr>
		<th><img src="theme/default/img/icons/user.png" alt="" /> Users today</th>
		<td>{$status->current_daily_users}</td>
		<th><img src="theme/default/img/icons/clock.png" alt="at" title="" /></th>
		<td>{$status->current_daily_users_time|date_format:"%H:%M"}</td>
	</tr>	
	<tr>
		<th><img src="theme/default/img/icons/channel.png" alt="" /> Channels</th>
		<td>{$status->max_channels}</td>
		<th><img src="theme/default/img/icons/calendar.png" alt="on" title="" /></th>
		<td>{$status->max_channels_time|date_format:"%Y-%m-%d %H:%M"}</td>
	</tr>
	<tr>
		<th><img src="theme/default/img/icons/server.png" alt="" title="" /> Servers</th>
		<td>{$status->max_servers}</td>
		<th><img src="theme/default/img/icons/calendar.png" alt="on" /></th>
		<td>{$status->max_servers_time|date_format:"%Y-%m-%d %H:%M"}</td>
	</tr>
	<tr>
		<th><img src="theme/default/img/icons/oper.png" alt="" title="" /> Opers</th>
		<td>{$status->max_opers}</td>
		<th><img src="theme/default/img/icons/calendar.png" alt="on" /></th>
		<td>{$status->max_opers_time|date_format:"%Y-%m-%d %H:%M"}</td>
	</tr>
</table>
</div>

<div class="box" style="float:right;">
<div class="boxtitle">Today</div>
<img src="graph/line/?mode=users&amp;size=small" alt="" /><br />
<img src="graph/line/?mode=channels&amp;size=small" alt="" /><br />
<img src="graph/line/?mode=servers&amp;size=small" alt="" />
</div>

<div class="box" style="float:left;">
<div class="boxtitle">Biggest Channels</div>
testing
</div>

<div class="box" style="float:left;">
<div class="boxtitle">Biggest Servers</div>
testing
</div>

<div class="box" style="float:left;">
<div class="boxtitle">Top 10 Channels Today</div>
testing
</div>

<div class="box" style="float:left;">
<div class="boxtitle">Top 10 Users Today</div>
testing
</div>

<div class="clear"></div>

</div>

{include file="_footer.tpl"}