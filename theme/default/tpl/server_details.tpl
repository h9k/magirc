{* $Id *}
{include file="_header.tpl"}

<h2>Details for <em>{$server->name}</em></h2>

{$server->description}<br />
Connected since: {$server->connecttime|date_format:"%Y-%m-%d %H:%M"}<br />
Current status: {if $server->online}<span style="color:green;">Online</span>{else}<span style="color:red;">Offline</span>{/if}<br />
Last split: {$server->lastsplit|date_format:"%Y-%m-%d %H:%M"}<br />
Version: {$server->version}<br />
Uptime: {$server-uptime|date_format:"%d days, %H hours and %M minutes"}<br />
MOTD:<pre>{$server->motd|default:"<em>No MOTD</em>"}</pre><br />
Current users: {$server->currentusers}<br />
Max users: {$server->maxusers} on {$server->maxusertime|date_format:"%Y-%m-%d %H:%M"}<br />
Last Ping: {if $server-ping}{$server->ping} on {$server->pingtime|date_format:"%Y-%m-%d %H:%M"}{else}<em>no ping yet</em>{/if}<br />
Max Ping: {$server->maxping}{if $server->maxpingtime} on {$server-maxpingtime|date_format:"%Y-%m-%d %H:%M"}{/if}<br />
Uline: {if $server->uline}Yes{else}No{/if}<br />
Opers: {$server->opers} (max {$server->maxopers} on {$server->maxopertime|date_format:"%Y-%m-%d %H:%M"})<br />
Ircop kills: {$server->operkills}<br />
Server kills: {$server->serverkill}<br />

{include file="_footer.tpl"}