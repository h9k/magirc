{extends file="_main.tpl"}

{block name="title" append}Network{/block}

{block name="content"}
<div id="tabs">
	<ul>
		<li><a href="index.php/network/status" title="status">Status</a></li>
		<li><a href="index.php/network/countries" title="countries">Countries</a></li>
		<li><a href="index.php/network/clients" title="clients">Clients</a></li>
		<li><a href="index.php/network/operators" title="operators">Operators</a></li>
		<li><a href="index.php/network/history" title="history">History</a></li>
	</ul>
</div>
{/block}

{block name="js" append}
{jsmin}
<script type="text/javascript"><!--{literal}
$(function() {
	$( "#tabs" ).tabs({
		select: function(event, ui) { window.location.hash = ui.tab.hash; },
		cache: true,
		spinner: 'Loading...',
		ajaxOptions: {
			error: function( xhr, status, index, anchor ) {
				$( anchor.hash ).html("Unable to load contents");
			}
		}
	});
});
{/literal}
--></script>
{/jsmin}
{/block}