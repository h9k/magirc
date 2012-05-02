<h1>{t 1=$target}Channel info for %1{/t}</h1>

<div class="halfleft">

<div id="chan_topic_container">
	<div id="chan_topic" class="topic"></div>
	<div style="text-align:right;">{t}Topic set by{/t} <span id="chan_topic_author" class="val"></span> {t}on{/t} <span id="chan_topic_time" class="val"></span></div>
</div>
<br />
<table class="details">
	<tr><th>{t}Current users{/t}:</th><td><span id="chan_users" class="val"></span></td></tr>
	<tr><th>{t}User peak{/t}:</th><td><span id="chan_users_max" class="val"></span> {t}on{/t} <span id="chan_users_max_time"></span></td></tr>
	<tr><th>{t}Modes{/t}:</th><td><span id="chan_modes" class="val"></span></td></tr>
	<tr><th>{t}Kicks{/t}:</th><td><span id="chan_kicks" class="val"></span></td></tr>
</table>
{if $cfg->net_roundrobin || $cfg->service_webchat}
<h2>{t}Join this channel{/t}</h2>
	{if $cfg->net_roundrobin}
		<img src="theme/{$cfg->theme}/img/icons/link.png" alt="" title="{t}Standard connection{/t}" style="vertical-align:middle;" />
		<a href="irc://{$cfg->net_roundrobin}:{$cfg->net_port|default:"6667"}/{$target|escape:"url"}">{t}IRC standard connection{/t}</a><br />
	{/if}
	{if $cfg->net_roundrobin && $cfg->net_port_ssl}
		<img src="theme/{$cfg->theme}/img/icons/ssl.png" alt="" title="{t}Secure connection{/t}" style="vertical-align:middle;" />
		<a href="irc://{$cfg->net_roundrobin}:+{$cfg->net_port_ssl}/{$target|escape:"url"}">{t}IRC secure connection{/t}</a><br />
	{/if}
	{if $cfg->service_webchat}
		<img src="theme/{$cfg->theme}/img/icons/webchat.png" alt="" title="{t}Webchat{/t}" style="vertical-align:middle;" />
		<a href="{$cfg->service_webchat}{$target|escape:"url"}">{t}Webchat{/t}</a><br />
	{/if}
	{if $cfg->net_roundrobin && $cfg->service_mibbit}
		<img src="theme/{$cfg->theme}/img/icons/mibbit.png" alt="" title="Mibbit" style="vertical-align:middle;" />
		<a href="http://widget.mibbit.com/?settings={$cfg->service_mibbit}&amp;server={$cfg->net_roundrobin}&amp;channel={$target|escape:"url"}&amp;promptPass=true">Mibbit</a><br />
	{/if}
{/if}
</div>

<div class="halfright">
	<h2>{t}Users currently in channel{/t}</h2>
	<table id="tbl_users" class="display clickable">
		<thead>
			<tr><th>{t}Nickname{/t}</th><th>{t}Modes{/t}</th></tr>
		</thead>
		<tbody>
			<tr><td colspan="2">{t}Loading{/t}...</td></tr>
		</tbody>
	</table>
</div>

<div class="clear"></div>

{jsmin}
<script type="text/javascript">
{literal}
$(document).ready(function() {
	$.getJSON('rest/denora.php/channels/'+target, function(result) {
		if (result.topic_html) {
			$("#chan_topic").html(result.topic_html);
			$("#chan_topic_author").html(result.topic_author);
			$("#chan_topic_time").html($.format.date(result.topic_time, format_datetime));
		} else {
			$("#chan_topic_container").hide();
		}
		$("#chan_users").html(result.users);
		$("#chan_users_max").html(result.users_max);
		$("#chan_users_max_time").html($.format.date(result.users_max_time, format_datetime));
		$("#chan_modes").html(result.modes ? "+"+result.modes : mLang.None);
		$("#chan_kicks").html(result.kicks);
	});
	$('#tbl_users').dataTable({
		"iDisplayLength": 10,
		"sPaginationType": "two_button",
		"aaSorting": [[ 0, "asc" ]],
		"sAjaxSource": 'rest/denora.php/channels/'+target+'/users?format=datatables',
		"aoColumns": [
			{ "mDataProp": "nickname", "fnRender": function(oObj) {
				return getUserStatus(oObj.aData) + ' ' + getCountryFlag(oObj.aData) + ' ' + oObj.aData['nickname'] + getUserExtra(oObj.aData);
			} },
			{ "mDataProp": "cmodes" }
		]
	});
	$("#tbl_users tbody tr").live("click", function(event) {
		if (this.id) window.location = url_base + 'user/nick:' + encodeURIComponent(this.id) + '/profile';
	});
});
{/literal}
</script>
{/jsmin}
