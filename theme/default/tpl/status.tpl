{* $Id$ *}
{include file="_header.tpl"}

{*
    [ircd] => 
    [current_users] => 183
    [current_users_time] => 1249223088
    [current_chans] => 94
    [current_chans_time] => 1249222687
    [current_daily_users] => 184
    [current_daily_users_time] => 1249223080
    [current_servers] => 11
    [current_servers_time] => 1249201942
    [current_opers] => 43
    [current_opers_time] => 1249201942
    [max_users] => 234
    [max_users_time] => 1247747168
    [max_channels] => 105
    [max_channels_time] => 1247001473
    [max_servers] => 16
    [max_servers_time] => 1245118524
    [max_opers] => 204
    [max_opers_time] => 1243323703
*}

<h2>Current Status</h2>
Users: {$status->current_users} (today's peak: {$status->current_daily_users} at {$status->current_daily_users_time|date_format:"%H:%M"})<br />
Channels: {$status->current_chans}<br />
Servers: {$status->current_servers}<br />
Opers: {$status->current_opers}<br />

<h2>Peak Values</h2>
Users: {$status->max_users} on {$status->max_users_time|date_format:"%Y-%m-%d %H:%M"}<br />
Channels: {$status->max_channels} on {$status->max_channels_time|date_format:"%Y-%m-%d %H:%M"}<br />
Servers: {$status->max_servers} on {$status->max_servers_time|date_format:"%Y-%m-%d %H:%M"}<br />
Opers: {$status->max_opers} on {$status->max_opers_time|date_format:"%Y-%m-%d %H:%M"}<br />

{include file="_footer.tpl"}