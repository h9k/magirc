{* $Id$ *}
{include file="_header.tpl"}

<div id="content">

<div class="box">
<div class="boxtitle">Users - <strong>today</strong> | last week | last month | last year</div>
<img src="graph/line/?mode=users&ey={$smarty.now|date_format:"%Y"}&em={$smarty.now|date_format:"%m"}&ed={$smarty.now|date_format:"%d"}" alt="" /><br />
</div>

<pre>Under construction...</pre>

</div>

{include file="_footer.tpl"}