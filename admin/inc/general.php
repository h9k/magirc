<?php
// $Id: general.php 315 2007-08-18 11:41:28Z Hal9000 $

/** ensure this file is being included by a parent file */
defined('_VALID_PARENT') or header("Location: ../");

if (isset($_POST['button'])) {
	if (isset($_POST['net_name'])) { save_config('net_name',$_POST['net_name']); }
	if (isset($_POST['net_url'])) { save_config('net_url',$_POST['net_url']); }
	if (isset($_POST['theme'])) { save_config('theme',$_POST['theme']); }
	if (isset($_POST['lang'])) { save_config('lang',$_POST['lang']); }
	if (isset($_POST['msg_welcome'])) { save_config('msg_welcome',$_POST['msg_welcome']); }
	else { save_config('msg_welcome',''); }
	$config = get_config();
	echo "<div class=\"configsave\">Configuration saved</div>";
}

$themes = NULL;
/*foreach (glob("../themes/*") as $filename) {
	if ($filename != "../themes/index.php" && file_exists($filename."/index.php")) {
		require($filename."/index.php");
		if ($theme_ver >= "1.9.0") {
			$themelist = explode("/", $filename);
			if ($config['theme'] == $themelist[2]) {
				$themes .= "<option value=\"".$themelist[2]."\" selected=\"selected\">$theme_name</option>";
			} else {
				$themes .= "<option value=\"".$themelist[2]."\">$theme_name</option>";
			}
		}
	}
}*/

$languages = NULL;
/*foreach (glob("../lang/*") as $filename) {
	if (substr($filename, -4, 4) != ".php" && file_exists($filename."/index.php")) {
		require($filename."/index.php");
		if ($lang_ver >= "1.9.0") {
			$langlist = explode("/", $filename);
			if ($config['lang'] == $langlist[2]) {
				$languages .= "<option value=\"".$langlist[2]."\" selected=\"selected\">$lang_name</option>";
			} else {
				$languages .= "<option value=\"".$langlist[2]."\">$lang_name</option>";
			}
		}
	}
}*/

?>
<div class="page_title">General Settings</div>
<form id="general" name="general" method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr>
      <td align="right">The <strong>name of your Network</strong></td>
      <td align="left"><input name="net_name" type="text" id="net_name" value="<?php echo $config['net_name']; ?>" size="32" maxlength="1024" tabindex="1" /></td>
    </tr>
    <tr>
      <td align="right">The URL of the <strong>Homepage of your Network</strong></td>
      <td align="left"><input name="net_url" type="text" id="net_url" value="<?php echo $config['net_url']; ?>" size="32" maxlength="1024" tabindex="2" /></td>
    </tr>
    <tr>
      <td align="right"><strong>Default Theme</strong></td>
      <td align="left"><select name="theme" id="theme" tabindex="3" disabled="disabled">
          <?php echo $themes; ?>
        </select>
      </td>
    </tr>
    <tr>
      <td align="right"><strong>Default Language</strong> (will not override automatic detection by browser)</td>
      <td align="left"><select name="lang" id="lang" tabindex="4" disabled="disabled">
          <?php echo $languages;  ?>
        </select>
      </td>
    </tr>
  </table>
  <p><strong>Welcome Message</strong>. You can welcome your users, describe your network, and put whatever information you want in there. Please use XHTML 1.0 Strict compilant markup language to avoid breaking validation.<br />
    <textarea name="msg_welcome" cols="64" rows="16" wrap="virtual" id="msg_welcome" tabindex="5"><?php echo $config['msg_welcome']; ?></textarea>
  </p>
  <p align="right">
    <input type="submit" name="button" id="button" value="Save" tabindex="6" />
    </p>
</form>
