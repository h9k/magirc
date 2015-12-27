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
</table>
{if $cfg->net_roundrobin || $cfg->service_webchat}
<h2>{t}Join this channel{/t}</h2>
	{if $cfg->net_roundrobin}
		<img src="theme/{$cfg->theme}/img/icons/link.png" alt="" title="{t}Standard connection{/t}" style="vertical-align:middle;" />
		<a href="irc://{$cfg->net_roundrobin}:{$cfg->net_port|default:"6667"}/{$target|escape:"url"}">{t}IRC standard connection{/t}</a><br />
	{/if}
	{if $cfg->net_roundrobin && $cfg->net_port_ssl}
		<img src="theme/{$cfg->theme}/img/icons/ssl.png" alt="" title="{t}Secure connection{/t}" style="vertical-align:middle;" />
		<a href="ircs://{$cfg->net_roundrobin}:{$cfg->net_port_ssl}/{$target|escape:"url"}">{t}IRC secure connection{/t}</a><br />
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
    if (refresh_interval > 0) {
        setInterval(updateContent, refresh_interval);
    }
    function updateContent() {
        loadContent();
        table_users.ajax.reload(null, false);
    }
    function loadContent() {
        $.getJSON('rest/service.php/channels/'+target, function(result) {
            if (result.topic_html) {
                $("#chan_topic").html(result.topic_html);
                $("#chan_topic_author").text(result.topic_author);
                $("#chan_topic_time").text($.format.date(result.topic_time, format_datetime));
            } else {
                $("#chan_topic_container").hide();
            }
            $("#chan_users").text(result.users);
            $("#chan_users_max").text(result.users_max);
            $("#chan_users_max_time").text($.format.date(result.users_max_time, format_datetime));
            $("#chan_modes").text(result.modes ? "+"+result.modes : mLang.None);
        });
    }
	var table_users = $('#tbl_users').DataTable({
		"pageLength": 10,
		"pagingType": "simple",
		"order": [[ 0, "asc" ]],
		"ajax": 'rest/service.php/channels/'+target+'/users?format=datatables',
		"columns": [
			{ "data": "nickname", "render": function(data, type, row) {
				return getUserStatus(row) + ' ' + getCountryFlag(row) + ' ' + escapeTags(data) + getUserExtra(row);
			} },
			{ "data": "cmodes", "render": function(data) {
				return data ? '+' + data : null;
			} }
		]
	});
	$("#tbl_users tbody").on("click", "tr", function(event) {
		if (this.id) window.location = url_base + 'user/nick:' + encodeURIComponent(this.id) + '/profile';
	});
    loadContent();
});
{/literal}
</script>
{/jsmin}
