{extends file="_main.tpl"}

{block name="title" append}{t}User{/t}: {$target}{/block}

{block name="content"}
<div id="tabs">
	<ul>
		<li><a href="index.php/user/{$mode}:{$target|escape:'url'}/info" title="info">{t}Info{/t}</a></li>
		<li><a href="index.php/user/{$mode}:{$target|escape:'url'}/activity" title="activity">{t}Activity{/t}</a></li>
	</ul>
</div>
{/block}

{block name="js" append}
{jsmin}
<script type="text/javascript">
var target = '{$target|escape:'url'}';
var mode = '{$mode}';
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
	$.getJSON('rest/denora.php/users/'+mode+'/'+target+'/checkstats', function(data) {
		if (!data) tabs.tabs("remove", 1);
	});
});
{/literal}
</script>
{/jsmin}
{/block}