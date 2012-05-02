<h1>{t 1=$target}Server info for %1{/t}</h1>

<div class="halfleft">
	<table class="details">
		<tr><th>{t}Description{/t}:</th><td><span id="srv_description" class="val"></span></td></tr>
		{if $cfg->denora_version > '1.4'}<tr><th>{t}Country{/t}:</th><td><span id="srv_country" class="val"></span></td></tr>{/if}
		<tr><th>{t}Online{/t}:</th><td><span id="srv_online" class="val"></span></td></tr>
		<tr><th>{t}Version{/t}:</th><td><span id="srv_version" class="val"></span></td></tr>
		<tr><th>{t}Uptime{/t}:</th><td><span id="srv_uptime" class="val"></span></td></tr>
		<tr><th>{t}Last split{/t}:</th><td><span id="srv_lastsplit" class="val"></span></td></tr>
	</table>
</div>
<div class="halfright">
	<table class="details">
		<tr><th>{t}Last ping{/t}:</th><td><span id="srv_ping" class="val"></span></td></tr>
		<tr><th>{t}Highest ping{/t}:</th><td><span id="srv_maxping" class="val"></span> {t}on{/t} <span id="srv_maxpingtime"></td></tr>
		<tr><th>{t}Current users{/t}:</th><td><span id="srv_users" class="val"></span></td></tr>
		<tr><th>{t}Max users{/t}:</th><td><span id="srv_maxusers" class="val"></span> {t}on{/t} <span id="srv_maxusertime"></td></tr>
		<tr><th>{t}Current opers{/t}:</th><td><span id="srv_opers" class="val"></span></td></tr>
		<tr><th>{t}Max opers{/t}:</th><td><span id="srv_maxopers" class="val"></span> {t}on{/t} <span id="srv_maxopertime"></td></tr>
	</table>
</div>

<div class="clear">&nbsp;</div>

<h2>{t}Message of the day{/t}</h2>
<div id="srv_motd" class="motd clear" title="MOTD"><pre id="srv_motd_txt"></pre></div>

{jsmin}
<script type="text/javascript">
{literal}
$(document).ready(function() {
	$.getJSON("rest/denora.php/servers/"+target, function(data){
		if (data) {
			$("#dialog-server").dialog("option", "title", data.server);
			$("#srv_description").html(data.description);
			if (denora_version > '1.4') $("#srv_country").html(getCountryFlag(data)+' '+data.country);
			$("#srv_online").html(data.online ? mLang.Yes : mLang.No);
			$("#srv_version").html(data.version);
			$("#srv_uptime").html(getTimeElapsed(data.uptime));
			if((data.split_time).indexOf("1970") >= 0) { $("#srv_lastsplit").html(mLang.Never);	}
			else { $("#srv_lastsplit").html($.format.date(data.split_time, format_datetime)); }
			$("#srv_ping").html(data.ping);
			$("#srv_maxping").html(data.ping_max);
			$("#srv_maxpingtime").html($.format.date(data.ping_max_time, format_datetime));
			$("#srv_users").html(data.users);
			$("#srv_maxusers").html(data.users_max);
			$("#srv_maxusertime").html($.format.date(data.users_max_time, format_datetime));
			$("#srv_opers").html(data.opers);
			$("#srv_maxopers").html(data.opers_max);
			if((data.opers_max_time).indexOf("1970") < 0) { $("#srv_maxopertime").html(mLang.On+" " + $.format.date(data.opers_max_time, format_datetime)); }
			$("#srv_motd_txt").html(data.motd ? data.motd_html : mLang.NoMotd);
			$("#dialog-server").dialog("open");
			$("#srv_motd").scrollTop(0);
		} else {
			alert(mLang.Failed);
		}
	}, "json");
});
{/literal}
</script>
{/jsmin}
