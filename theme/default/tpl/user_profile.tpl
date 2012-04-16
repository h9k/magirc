{extends file="_main.tpl"}

{block name="title" append}User: {$target}{/block}

{block name="content"}
<div id="tabs">
	<ul>
		<li><a href="index.php/user/{$mode}:{$target|escape:'url'}/info" title="info">Info</a></li>
		<li><a href="index.php/user/{$mode}:{$target|escape:'url'}/activity" title="activity">Activity</a></li>
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
	$("#tabs").tabs({
		select: function(event, ui) { window.location.hash = ui.tab.hash; },
		cache: true,
		spinner: 'Loading...',
		ajaxOptions: {
			error: function( xhr, status, index, anchor ) {
				$( anchor.hash ).html("Unable to load contents");
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