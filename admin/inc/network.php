<?php
// $Id: network.php 311 2007-07-30 20:17:06Z Hal9000 $


if (isset($_POST['button'])) {
	if (isset($_POST['ircd_type'])) { save_config('ircd_type',$_POST['ircd_type']); }
	if (isset($_POST['hide_ulined'])) { save_config('hide_ulined',1); }
	else { save_config('hide_ulined',0); }
	if (isset($_POST['hide_servers'])) { save_config('hide_servers',$_POST['hide_servers']); }
	else { save_config('hide_servers',''); }
	if (isset($_POST['hide_chans'])) { save_config('hide_chans',$_POST['hide_chans']); }
	else { save_config('hide_chans',''); }
	$config = get_config();
	echo "<div class=\"configsave\">Configuration saved</div>";
}

$ircds = NULL;
foreach (glob("../libs/phpdenora/ircds/*") as $filename) {
	if ($filename != "../libs/phpdenora/ircds/index.php") {
		$ircdlist = explode("/", $filename);
		$ircdlist = explode(".", $ircdlist[4]);
		if ($config['ircd_type'] == $ircdlist[0]) {
			$ircds .= "<option value=\"".$ircdlist[0]."\" selected=\"selected\">".$ircdlist[0]."</option>";
		} else {
			$ircds .= "<option value=\"".$ircdlist[0]."\">".$ircdlist[0]."</option>";
		}
	}
}

?>
<div class="page_title">Network Settings</div>
<form id="network" name="network" method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr>
      <td align="right"><strong>IRCd Server Type</strong></td>
      <td align="left"><select name="ircd_type" id="ircd_type" tabindex="1" >
        <?php echo $ircds; ?>
      </select></td>
    </tr>
    <tr>
      <td align="right"><strong>Hide Ulined Servers</strong></td>
      <td align="left"><input name="hide_ulined" type="checkbox" id="hide_ulined" tabindex="2" <?php if ($config['hide_ulined'] == true) { echo "checked=\"checked\" "; } ?> /></td>
    </tr>
    <tr>
      <td align="right">Servers you don't want phpDenora to show. Separate with commas without spaces, example: &quot;hub.mynet.tld,hub2.mynet.tld&quot;</td>
      <td align="left"><input name="hide_servers" type="text" id="hide_servers" value="<?php echo $config['hide_servers']; ?>" size="32" maxlength="1024" tabindex="3" />      </td>
    </tr>
    <tr>
      <td align="right">Channels you don't want phpDenora to show. Separate with commas without spaces</td>
      <td align="left"><input name="hide_chans" type="text" id="hide_chans" value="<?php echo $config['hide_chans']; ?>" size="32" maxlength="1024" tabindex="5" /></td>
    </tr>
  </table>
  <p align="right">
    <input type="submit" name="button" id="button" value="Save" tabindex="6" />
    </p>
</form>
