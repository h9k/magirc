{* $Id$ *}
{extends file="components/main.tpl"}
{include file="components/_menu_channel.tpl"}
{block name="content"}
<div id="content">

<div class="box">
<div class="boxtitle">Client Statistics</div>
<img src="?section=graph&amp;graph=pie&amp;mode=version&amp;chan={$smarty.get.channel|escape:"url"}" alt="" />
</div>

<pre>Under construction...</pre>

</div>
{/block}