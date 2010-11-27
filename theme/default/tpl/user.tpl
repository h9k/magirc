{* $Id$ *}
{extends file="components/main.tpl"}
{block name="content"}
<div id="content">

<div class="box">
<div class="boxtitle">Users - <strong>today</strong> | last week | last month | last year</div>
<img src="?section=graph&amp;graph=line&amp;mode=users&amp;ey={$smarty.now|date_format:"%Y"}&amp;em={$smarty.now|date_format:"%m"}&amp;ed={$smarty.now|date_format:"%d"}" alt="" /><br />
</div>

<pre>Under construction...</pre>

</div>
{/block}