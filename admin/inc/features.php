<?php
// $Id: features.php 315 2007-08-18 11:41:28Z Hal9000 $


if (isset($_POST['button'])) {
	if (isset($_POST['status_lookup'])) { save_config('status_lookup',1); }
	else { save_config('status_lookup',0); }
	if (isset($_POST['tld_stats'])) { save_config('tld_stats',1); }
	else { save_config('tld_stats',0); }
	if (isset($_POST['client_stats'])) { save_config('client_stats',1); }
	else { save_config('client_stats',0); }
	if (isset($_POST['net_graphs'])) { save_config('net_graphs',1); }
	else { save_config('net_graphs',0); }
	if (isset($_POST['mirc'])) { save_config('mirc',1); }
	else { save_config('mirc',0); }
	if (isset($_POST['mirc_url'])) { save_config('mirc_url',$_POST['mirc_url']); }
	else { save_config('mirc_url',''); }
	if (isset($_POST['webchat'])) { save_config('webchat',1); }
	else { save_config('webchat',0); }
	if (isset($_POST['webchat_url'])) { save_config('webchat_url',$_POST['webchat_url']); }
	else { save_config('webchat_url',''); }
	if (isset($_POST['remote'])) { save_config('remote',1); }
	else { save_config('remote',0); }
	$config = get_config();
	echo "<div class=\"configsave\">Configuration saved</div>";
}

?>
<div class="page_title">Feature Settings</div>
<form id="features" name="features" method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr>
      <td align="right">Enables <strong>online status and country lookups</strong> in user listings. This will require an additional query for each user, set this to false if you want to keep sql load low</td>
      <td align="left"><input name="status_lookup" type="checkbox" id="status_lookup" tabindex="1" <?php if ($config['status_lookup'] == true) { echo "checked=\"checked\" "; } ?> /></td>
    </tr>
    <tr>
      <td align="right">Enables <strong>country statistics</strong>. Set to false if your ircds don't resolve hostnames</td>
      <td align="left"><input name="tld_stats" type="checkbox" id="tld_stats" tabindex="2" <?php if ($config['tld_stats'] == true) { echo "checked=\"checked\" "; } ?> /></td>
    </tr>
    <tr>
      <td align="right">Enables <strong>client version statistics</strong>. Set to false if your Denora does not version your clients</td>
      <td align="left"><input name="client_stats" type="checkbox" id="client_stats" tabindex="3" <?php if ($config['client_stats'] == true) { echo "checked=\"checked\" "; } ?> /></td>
    </tr>
    <tr>
      <td align="right">Enables network statistics <strong>graphs</strong>.</td>
      <td align="left"><input name="net_graphs" type="checkbox" id="net_graphs" tabindex="7" <?php if ($config['net_graphs'] == true) { echo "checked=\"checked\" "; } ?> /></td>
    </tr>
    <tr>
      <td align="right">Enable the <strong>mIRC icon</strong> in the channel list</td>
      <td align="left"><input name="mirc" type="checkbox" id="mirc" tabindex="8" <?php if ($config['mirc'] == true) { echo "checked=\"checked\" "; } ?> /></td>
    </tr>
    <tr>
      <td align="right">The URL for the mIRC icon, including trailing slash<strong></strong></td>
      <td align="left"><input name="mirc_url" type="text" id="mirc_url" value="<?php echo $config['mirc_url']; ?>" size="32" maxlength="1024" tabindex="9" />      </td>
    </tr>
    <tr>
      <td align="right">Enable the <strong>web chat icon</strong> in the channel list</td>
      <td align="left"><input name="webchat" type="checkbox" id="webchat" tabindex="10" <?php if ($config['webchat'] == true) { echo "checked=\"checked\" "; } ?> /></td>
    </tr>
    <tr>
      <td align="right">The URL for the web chat icon</td>
      <td align="left"><input name="webchat_url" type="text" id="webchat_url" value="<?php echo $config['webchat_url']; ?>" size="32" maxlength="1024" tabindex="11" /></td>
    </tr>
    <tr>
      <td align="right">Enable the <strong>remote api interface</strong>, which enables to easilly embed data in web sites</td>
      <td align="left"><input name="remote" type="checkbox" id="remote" tabindex="12" <?php if ($config['remote'] == true) { echo "checked=\"checked\" "; } ?> /></td>
    </tr>
  </table>
  <p align="right">
    <input type="submit" name="button" id="button" value="Save" tabindex="13" />
    </p>
</form>
