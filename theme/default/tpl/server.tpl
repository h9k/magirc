{* $Id *}
{include file="_header.tpl"}

<h2>Server list</h2>
<ul>
{foreach from=$serverlist item=item}
<li><a href="server/{$item.server}/">{$item.server}</a>{if $item.uline} <span style="color:blue;">Ulined</span>{/if}</li>
{foreachelse}
<li>no servers to display</li>
{/foreach}
</ul>

<h2>Servers <strong>today</strong> | last week | last month | last year</h2>

[graph]

{include file="_footer.tpl"}