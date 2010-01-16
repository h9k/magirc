{* $Id$ *}
{include file="_header.tpl"}
{include file="_menu_channel.tpl"}

<div id="content">

<div class="box">
<div class="boxtitle">Users currently on the channel</div>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<th scope="col" align="center">n.</th>
		<th scope="col" colspan="3">Nick</th>
		<th scope="col">Modes</th>
		<th scope="col">Mask</th>
	</tr>
	{foreach from=$users item=user}
	<tr>
		<td valign="middle"><strong>%s</strong></td>
		<td valign="middle">%s</td>
		<td valign="middle">%s</td>
		<td><a href="#">%s</a></td>
		<td>%s</td>
		<td>%s</td>
	</tr>
	<tr>
		<td colspan="4"></td>
	</tr>
	{*
	$x + 1,
	$user_status,
	$countryflag,
	urlencode(html_entity_decode($chan)),
	$whoinlist[$x]['nick'],
	$whoinlist[$x]['nick'],
	$whoinlist[$x]['modes'],
	$whoinlist[$x]['username']."@".$whoinlist[$x]['host'])
	*}
	{/foreach}
</table>

</div>

</div>

{include file="_footer.tpl"}