{extends file="_main.tpl"}

{block name="title" append}Overview{/block}

{block name="content"}
<div id="content" class="div.ui-tabs-panel">

	<div class="halfleft">
		<form id="logout" method="post" action="index.php/logout">
			<h1>Welcome, {$smarty.session.username} <button type="submit" name="logout">Logout</button></h1>
		</form>
		<p>Here you can configure your installation and get access to support resources</p>

		<h3>Resources</h3>
		<ul>
			<li><a href="../">View Stats</a></li>
			<li><a href="index.php/support#registration">Register</a></li>
			<li><a href="http://www.magirc.org/">Homepage</a></li>
			<li><a href="index.php/support">Support</a></li>
		</ul>

		<h3>Software information</h3>
		<table>
			<tr><th style="text-align:right;">MagIRC</th><td style="text-align:left;">{$smarty.const.VERSION_FULL}</td></tr>
			<tr><th style="text-align:right;">IRCd</th><td style="text-align:left;">{$cfg->ircd_type}</td></tr>
			<tr><th style="text-align:right;">PHP</th><td style="text-align:left;">{$version.php}</td></tr>
			<tr><th style="text-align:right;">Slim Framework</th><td style="text-align:left;">{$version.slim}</td></tr>
			<tr><th style="text-align:right;">Smarty</th><td style="text-align:left;">{$smarty.version}</td></tr>
		</table>
		<br /><img src="img/gplv3.png" alt="GPLv3" />
	</div>

	<div class="halfright">
		<h2>Like MagIRC? Please consider a donation!</h2>
		Show some love to the developer so he can afford some cookies :)<br /><br />
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="width:100px;">
			<div>
				<input type="hidden" name="cmd" value="_s-xclick" />
				<input type="hidden" name="hosted_button_id" value="5270963" />
				<input type="image" src="https://www.paypal.com/en_GB/i/btn/btn_donate_SM.gif" name="submit" alt="PayPal - The safer, easier way to pay online." />
				<img alt="" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
			</div>
		</form>

		<h2>Bugs? Suggestions?</h2>
		Please let us know any wishes you have or any bugs you encounter by opening a ticket in our <a href="https://github.com/h9k/magirc/issues">Issue Tracker</a>

		<h2>Important notes</h2>
		{if $setup}
		<div class="warning">
			<p>As a safety measure, please<br /><strong>remove the <em>setup/</em> directory</strong>!</p>
		</div>
		{/if}
		<div class="warning">
			<p>You are using an alpha version of MagIRC.<br />
			This version most certainly does contain bugs<br />
			<strong>PRODUCTION USAGE IS DISCOURAGED</strong></p>
		</div>
	</div>

	<div class="clear"></div>

</div>
{/block}

{block name="js" append}
{jsmin}
<script type="text/javascript">{literal}
$(document).ready(function() {
	$("button").button();
});
{/literal}
</script>
{/jsmin}
{/block}