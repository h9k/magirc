{extends file="_main.tpl"}

{block name="title" append}{t}Channels{/t}{/block}

{block name="content"}
<div id="tabs">
	<ul>
		<li><a href="index.php/channel/list" title="channels">{t}Channels{/t}</a></li>
		<li><a href="index.php/channel/globalactivity" title="activity">{t}Activity{/t}</a></li>
		<li><a href="index.php/channel/history" title="history">{t}History{/t}</a></li>
	</ul>
</div>
{/block}

{block name="js" append}
{jsmin}
<script type="text/javascript">
{literal}
$(document).ready(function() {
	$("#tabs").tabs({
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