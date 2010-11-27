{* $Id$ *}
{extends file="components/main.tpl"}
{block name="content"}
<div id="content">

<h2><a href="?section=server">Servers</a> &raquo; Server Details</h2>

<h3>{$server->name}</h3>
<p>{$server->description}</p>

<h3>Currently <strong>{if $server->online}<span style="color:green;">Online</span>{else}<span style="color:red;">Offline</span>{/if}</strong></h3>
{if $server->online}
	Running <em>{$server->version|default:"unknown"}</em>{if $server->uline} (ulined){/if} for <em>{$server->uptime|date_format:"%e days, %H hours and %M minutes"}</em><br />
	Connected on <em>{$server->connecttime|date_format:"%Y-%m-%d %H:%M"}</em>
	{if $server->lastsplit}Last split on <em>{$server->lastsplit|date_format:"%Y-%m-%d %H:%M"}</em>{/if}
{else}
	Running <em>{$server->version|default:"unknown"}</em> for <em>{$server->uptime|date_format:"%d days, %H hours and %M minutes"}</em><br />
	Connected on <em>{$server->connecttime|date_format:"%Y-%m-%d %H:%M"}</em>
	{if $server->lastsplit}and split on <em>{$server->lastsplit|date_format:"%Y-%m-%d %H:%M"}</em>{/if}
{/if}

<h3>Latency</h3>
Last measured latency: {if $server->pingtime}<em>{$server->ping}s</em> on <em>{$server->pingtime|date_format:"%Y-%m-%d %H:%M"}</em>{else}<em>no data</em>{/if}<br />
Maximum measured latency: {if $server->maxpingtime}<em>{$server->maxping}s</em> on <em>{$server->maxpingtime|date_format:"%Y-%m-%d %H:%M"}</em>{else}<em>none</em>{/if}<br />

<h3>Users</h3>
{if $server->online}Currently{else}Last measured{/if} <em>{$server->currentusers}</em> users (max <em>{$server->maxusers}</em> on <em>{$server->maxusertime|date_format:"%Y-%m-%d %H:%M"}</em>)<br />
of which <em>{$server->opers}</em> are opers (max <em>{$server->maxopers}</em> on <em>{$server->maxopertime|date_format:"%Y-%m-%d %H:%M"}</em>)<br />

<h3>Kills</h3>
Opers issued <em>{$server->operkills}</em> kills<br />
This server issued <em>{$server->serverkills}</em> kills<br />

<h3>Message of the day (MOTD)</h3>
<div style="background-color:#F0F0F0; border:1px solid #CCCCCC; padding: 5px; margin: 10px;">
{if $server->motd}<pre>{$server->motd|irc2html}</pre>{else}<em>MOTD not available for this server</em>{/if}
</div>

</div>
{/block}