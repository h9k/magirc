{* $Id$ *}
{extends file="components/main.tpl"}
{include file="components/_menu_channel.tpl"}
{block name="content"}
<div id="content">

<div class="box">
<div class="boxtitle">Activity Statistics - This Month</div>
<img src="?section=graph&amp;graph=bar&amp;mode=chan&amp;chan={$smarty.get.channel|escape:"url"}&amp;type=3" alt="" />
</div>

<pre>Under construction...</pre>

</div>
{/block}