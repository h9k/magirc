{* $Id$ *}
{include file="_header.tpl"}

<h2><a href="channel/">Channels</a> &raquo; Channel Details</h2>

<h3>{$channel->name}</h3>
Modes: {if $channel->modes}+{$channel->modes}{else}none{/if}<br />
Current users: {$channel->users}<br />
User peak: {$channel->users_max} on {$channel->users_max_time|date_format:"%Y-%m-%d %H:%M"}<br />
Kicks: {$channel->kicks}<br />
Topic: {$channel->topic|irc2html} <br />
Set by: {$channel->topic_author} on {$channel->topic_time|date_format:"%Y-%m-%d %H:%M"}<br />

{include file="_footer.tpl"}