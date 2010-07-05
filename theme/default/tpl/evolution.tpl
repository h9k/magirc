{* $Id$ *}
{include file="_header.tpl"}

<div id="content">

<pre>To be implemented...</pre>

<div class="box">
<div class="boxtitle">Users - Last Month</div>
<img src="?section=graph&amp;graph=line&amp;mode=users&amp;ey={$smarty.now|date_format:"%Y"}&amp;em={$smarty.now|date_format:"%m"}&amp;ed={$smarty.now|date_format:"%d"}" alt="" /><br />
</div>

<div class="box">
<div class="boxtitle">Channels - Last Month</div>
<img src="?section=graph&amp;graph=line&amp;mode=channels&amp;ey={$smarty.now|date_format:"%Y"}&amp;em={$smarty.now|date_format:"%m"}&amp;ed={$smarty.now|date_format:"%d"}" alt="" /><br />
</div>

<div class="box">
<div class="boxtitle">Servers - Last Month</div>
<img src="?section=graph&amp;graph=line&amp;mode=servers&amp;ey={$smarty.now|date_format:"%Y"}&amp;em={$smarty.now|date_format:"%m"}&amp;ed={$smarty.now|date_format:"%d"}" alt="" /><br />
</div>

</div>

{include file="_footer.tpl"}