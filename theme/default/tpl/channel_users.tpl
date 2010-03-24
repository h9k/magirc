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
	{foreach from=$users item=user name=users}
	<tr>
		<td valign="middle"><strong>{$smarty.foreach.users.iteration}.</strong></td>
		<td valign="middle"></td>
		<td valign="middle"></td>
		<td align="left"><a href="#">{$user.nick}</a></td>
		<td align="left">{$user.modes}</td>
		<td align="left">{$user.username}@{$user.host}</td>
	</tr>
	<tr>
		<td colspan="4"></td>
	</tr>
	{/foreach}
</table>

</div>

</div>

{include file="_footer.tpl"}