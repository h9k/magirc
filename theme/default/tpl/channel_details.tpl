{* $Id$ *}
{include file="_header.tpl"}
{include file="_menu_channel.tpl"}

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