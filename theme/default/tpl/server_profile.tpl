{extends file="_main.tpl"}

{block name="title" append}{t}Server{/t}: {$target}{/block}

{block name="content"}
<div id="tabs">
	<ul>
		<li><a href="index.php/server/{$target|escape:'url'}/info" title="info">{t}Info{/t}</a></li>
		<li><a href="index.php/server/{$target|escape:'url'}/countries" title="countries">{t}Countries{/t}</a></li>
		<li><a href="index.php/server/{$target|escape:'url'}/clients" title="clients">{t}Clients{/t}</a></li>
	</ul>
</div>
{/block}

{block name="js" append}
{jsmin}
<script type="text/javascript">
var target = '{$target|escape:'url'}';
{literal}
$(document).ready(function() {
	var tabs = $("#tabs").tabs({
		select: function(event, ui) { window.location.hash = ui.tab.hash; },
		cache: true,
		spinner: '{t}Loading{/t}...',
		ajaxOptions: {
			error: function( xhr, status, index, anchor ) {
				$( anchor.hash ).html(mLang.LoadError);
			}
		}
	});
});
{/literal}
</script>
{/jsmin}
{/block}