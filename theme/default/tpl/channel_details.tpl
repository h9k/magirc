{* $Id$ *}
{include file="_header.tpl"}

<div id="title">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td valign="bottom"><h1>{$channel->name}</h1></td>
			<td valign="bottom" style="text-align:right;">
				<div id="submenu">
				  <ul>
					<li><a href="channel/?channel={$smarty.get.channel|escape:'url'}" title="Details"{if $smarty.get.action eq 'details'} class="active"{/if}><img src="theme/default/img/menu/channels.png" alt="" height="16" width="16" /><span>&nbsp;Details</span></a></li>
					<li><a href="channel/?channel={$smarty.get.channel|escape:'url'}&amp;action=users" title="Users"{if $smarty.get.action eq 'users'} class="active"{/if}><img src="theme/default/img/menu/users.png" alt="" height="16" width="16" /><span>&nbsp;Users</span></a></li>
					<li><a href="channel/?channel={$smarty.get.channel|escape:'url'}&amp;action=countries" title="Countries"{if $smarty.get.action eq 'countries'} class="active"{/if}><img src="theme/default/img/menu/countries.png" alt="" height="16" width="16" /><span>&nbsp;Countries</span></a></li>
				    <li><a href="channel/?channel={$smarty.get.channel|escape:'url'}&amp;action=clients" title="Clients"{if $smarty.get.action eq 'clients'} class="active"{/if}><img src="theme/default/img/menu/clients.png" alt="" height="16" width="16" /><span>&nbsp;Clients</span></a></li>
				    <li><a href="channel/?channel={$smarty.get.channel|escape:'url'}&amp;action=activity" title="Activity"{if $smarty.get.action eq 'activity'} class="active"{/if}><img src="theme/default/img/menu/evolution.png" alt="" height="16" width="16" /><span>&nbsp;Activity</span></a></li>
				    <li><a href="{$mirc}{$smarty.get.channel|escape:'url'}" title="IRC"><img src="theme/default/img/menu/mirc.png" alt="" height="16" width="16" /><span>&nbsp;IRC</span></a></li>
				    <li><a href="{$webchat}{$smarty.get.channel|escape:'url'}" title="Webchat"><img src="theme/default/img/menu/webchat.png" alt="" height="16" width="16" /><span>&nbsp;Webchat</span></a></li>
				  </ul>
				</div>
			</td>
		</tr>
	</table>
</div>

<div id="content">

<div class="box">
<div class="boxtitle">Channel Details</div>
Modes: {if $channel->modes}+{$channel->modes}{else}none{/if}<br />
Current users: {$channel->users}<br />
User peak: {$channel->users_max} on {$channel->users_max_time|date_format:"%Y-%m-%d %H:%M"}<br />
Kicks: {$channel->kicks}<br />
Topic: {$channel->topic|irc2html} <br />
Set by: {$channel->topic_author} on {$channel->topic_time|date_format:"%Y-%m-%d %H:%M"}<br />
</div>

</div>

{include file="_footer.tpl"}