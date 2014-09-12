{extends file="_main.tpl"}

{block name="title" append}{t}Network{/t}{/block}

{block name="content"}
<div id="tabs">
	<ul>
		{if $cfg->welcome_mode eq 'ownpage'}<li title="welcome"><a href="index.php/content/welcome">{t}Welcome{/t}</a></li>{/if}
		<li title="status"><a href="index.php/network/status">{t}Status{/t}</a></li>
		<li title="countries"><a href="index.php/network/countries">{t}Countries{/t}</a></li>
		<li title="clients"><a href="index.php/network/clients">{t}Clients{/t}</a></li>
		<li title="operators"><a href="index.php/network/operators">{t}Operators{/t}</a></li>
        <li title="history"><a href="index.php/network/history">{t}History{/t}</a></li>
		{if $cfg->service_netsplit}<li title="netsplit"><a href="index.php/network/netsplit">{t}Netsplit Graphs{/t}</a></li>{/if}
		{if $cfg->service_searchirc}<li title="searchirc"><a href="index.php/network/searchirc">{t}Searchirc Graphs{/t}</a></li>{/if}
		{if $cfg->service_mibbitid}<li title="mibbit"><a href="index.php/network/mibbit">{t}Mibbit Graphs{/t}</a></li>{/if}
	</ul>
</div>
{if $cfg->service_searchirc}<div id="searchirc_html" style="display:none;"><script type="text/javascript" src="http://searchirc.com/official_rank.php?ID={$cfg->service_searchirc}&amp;outof=1"></script></div>{/if}
{/block}

{block name="js" append}
{jsmin}
<script type="text/javascript">
var netsplit = '{$cfg->service_netsplit}';
var mibbitid = '{$cfg->service_mibbitid}';
{literal}
$(document).ready(function() {
	$("#tabs").tabs({
		beforeActivate: function(event, ui) {
			window.location.hash = ui.newTab.attr('title');
		},
		beforeLoad: function(event, ui) {
			if (window.location.hash) {
				var title = window.location.hash.substring(1, window.location.hash.length);
				$("li[title='"+title+"'] a").trigger("click");
			}
			if (ui.tab.data("loaded")) {
				event.preventDefault();
				return;
			}
			ui.jqXHR.success(function() {
				ui.tab.data("loaded", true);
			});
			ui.jqXHR.error(function() {
				ui.panel.html(mLang.LoadError);
			});
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