{* $Id$ *}
{include file="_header.tpl"}

<div id="content">

<pre>To be implemented...</pre>

<div class="box">
<div class="boxtitle">Users - Last Month</div>
<img src="graph/line/?mode=users&ey={$smarty.now|date_format:"%Y"}&em={$smarty.now|date_format:"%m"}&ed={$smarty.now|date_format:"%d"}" alt="" /><br />
</div>

<div class="box">
<div class="boxtitle">Channels - Last Month</div>
<img src="graph/line/?mode=channels&ey={$smarty.now|date_format:"%Y"}&em={$smarty.now|date_format:"%m"}&ed={$smarty.now|date_format:"%d"}" alt="" /><br />
</div>

<div class="box">
<div class="boxtitle">Servers - Last Month</div>
<img src="graph/line/?mode=servers&ey={$smarty.now|date_format:"%Y"}&em={$smarty.now|date_format:"%m"}&ed={$smarty.now|date_format:"%d"}" alt="" /><br />
</div>

</div>

{include file="_footer.tpl"}