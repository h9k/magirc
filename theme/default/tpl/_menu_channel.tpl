{* $Id$ *}
<div id="title">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td valign="bottom"><h1>{$smarty.get.channel}</h1></td>
			<td valign="bottom" style="text-align:right;">
				<div id="submenu">
				  <ul>
					<li><a href="?section=channel&amp;channel={$smarty.get.channel|escape:'url'}" title="Details"{if $smarty.get.action eq 'details'} class="active"{/if}><img src="theme/default/img/menu/channels.png" alt="" height="16" width="16" /><span>&nbsp;Details</span></a></li>
					<li><a href="?section=channel&amp;channel={$smarty.get.channel|escape:'url'}&amp;action=users" title="Users"{if $smarty.get.action eq 'users'} class="active"{/if}><img src="theme/default/img/menu/users.png" alt="" height="16" width="16" /><span>&nbsp;Users</span></a></li>
					<li><a href="?section=channel&amp;channel={$smarty.get.channel|escape:'url'}&amp;action=countries" title="Countries"{if $smarty.get.action eq 'countries'} class="active"{/if}><img src="theme/default/img/menu/countries.png" alt="" height="16" width="16" /><span>&nbsp;Countries</span></a></li>
				    <li><a href="?section=channel&amp;channel={$smarty.get.channel|escape:'url'}&amp;action=clients" title="Clients"{if $smarty.get.action eq 'clients'} class="active"{/if}><img src="theme/default/img/menu/clients.png" alt="" height="16" width="16" /><span>&nbsp;Clients</span></a></li>
				    <li><a href="?section=channel&amp;channel={$smarty.get.channel|escape:'url'}&amp;action=activity" title="Activity"{if $smarty.get.action eq 'activity'} class="active"{/if}><img src="theme/default/img/menu/evolution.png" alt="" height="16" width="16" /><span>&nbsp;Activity</span></a></li>
				    {if $mirc}<li><a href="{$mirc}{$smarty.get.channel|escape:'url'}" title="IRC"><img src="theme/default/img/menu/mirc.png" alt="" height="16" width="16" /><span>&nbsp;IRC</span></a></li>{/if}
				    {if $webchat}<li><a href="{$webchat}{$smarty.get.channel|escape:'url'}" title="Webchat"><img src="theme/default/img/menu/webchat.png" alt="" height="16" width="16" /><span>&nbsp;Webchat</span></a></li>{/if}
				  </ul>
				</div>
			</td>
		</tr>
	</table>
</div>