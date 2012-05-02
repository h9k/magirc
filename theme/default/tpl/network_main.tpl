{extends file="_main.tpl"}

{block name="title" append}{t}Network{/t}{/block}

{block name="content"}
<div id="tabs">
	<ul>
		{if $cfg->welcome_mode eq 'ownpage'}<li><a href="index.php/content/welcome" title="welcome">{t}Welcome{/t}</a></li>{/if}
		<li><a href="index.php/network/status" title="status">{t}Status{/t}</a></li>
		<li><a href="index.php/network/countries" title="countries">{t}Countries{/t}</a></li>
		<li><a href="index.php/network/clients" title="clients">{t}Clients{/t}</a></li>
		<li><a href="index.php/network/operators" title="operators">{t}Operators{/t}</a></li>
		<li><a href="index.php/network/history" title="history">{t}History{/t}</a></li>
		{if $cfg->service_netsplit}<li><a href="index.php/network/netsplit" title="netsplit">{t}Netsplit Graphs{/t}</a></li>{/if}
		{if $cfg->service_searchirc}<li><a href="index.php/network/searchirc" title="searchirc">{t}Searchirc Graphs{/t}</a></li>{/if}
	</ul>
</div>
{if $cfg->service_searchirc}<div id="searchirc_html" style="display:none;"><script type="text/javascript" src="http://searchirc.com/official_rank.php?ID={$cfg->service_searchirc}&amp;outof=1"></script></div>{/if}
{/block}

{block name="js" append}
{jsmin}
<script type="text/javascript">
var netsplit = '{$cfg->service_netsplit}';
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
	// Temporary fix to make sure netsplit generates the graphs we need
	if (netsplit) {
		$.get('http://irc.netsplit.de/networks/details.php.en?net='+netsplit+'&submenu=weeks');
		$.get('http://irc.netsplit.de/networks/details.php.en?net='+netsplit+'&submenu=months');
		$.get('http://irc.netsplit.de/networks/details.php.en?net='+netsplit+'&submenu=years');
	}
});
{/literal}
</script>
{/jsmin}
{/block}