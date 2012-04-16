{extends file="_main.tpl"}

{block name="title" append}Network{/block}

{block name="content"}
<div id="tabs">
	<ul>
		{if $cfg.welcome_mode eq 'ownpage'}<li><a href="index.php/content/welcome" title="welcome">Welcome</a></li>{/if}
		<li><a href="index.php/network/status" title="status">Status</a></li>
		<li><a href="index.php/network/countries" title="countries">Countries</a></li>
		<li><a href="index.php/network/clients" title="clients">Clients</a></li>
		<li><a href="index.php/network/operators" title="operators">Operators</a></li>
		<li><a href="index.php/network/history" title="history">History</a></li>
		{if $cfg.service_netsplit}<li><a href="index.php/network/netsplit" title="netsplit">Netsplit Graphs</a></li>{/if}
		{if $cfg.service_searchirc}<li><a href="index.php/network/searchirc" title="searchirc">Searchirc Graphs</a></li>{/if}
	</ul>
</div>
{if $cfg.service_searchirc}<div id="searchirc_html" style="display:none;"><script language="JavaScript" src="http://searchirc.com/official_rank.php?ID={$cfg.service_searchirc}&amp;outof=1"></script></div>{/if}
{/block}

{block name="js" append}
{jsmin}
<script type="text/javascript">
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
});
{/literal}
</script>
{/jsmin}
{/block}