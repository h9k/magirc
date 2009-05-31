<?php
// $Id: advanced.php 315 2007-08-18 11:41:28Z Hal9000 $

/** ensure this file is being included by a parent file */
defined('_VALID_PARENT') or header("Location: ../");

if (isset($_POST['button'])) {
	if (isset($_POST['debug_mode'])) { save_config('debug_mode',$_POST['debug_mode']); }
	if (isset($_POST['show_exec_time'])) { save_config('show_exec_time',1); }
	else { save_config('show_exec_time',0); }
	if (isset($_POST['show_validators'])) { save_config('show_validators',1); }
	else { save_config('show_validators',0); }
	if (isset($_POST['table_user'])) { save_config('table_user',$_POST['table_user']); }
	if (isset($_POST['table_chan'])) { save_config('table_chan',$_POST['table_chan']); }
	if (isset($_POST['table_chanbans'])) { save_config('table_chanbans',$_POST['table_chanbans']); }
	if (isset($_POST['table_chanexcepts'])) { save_config('table_chanexcepts',$_POST['table_chanexcepts']); }
	if (isset($_POST['table_chaninvites'])) { save_config('table_chaninvites',$_POST['table_chaninvites']); }
	if (isset($_POST['table_glines'])) { save_config('table_glines',$_POST['table_glines']); }
	if (isset($_POST['table_sqlines'])) { save_config('table_sqlines',$_POST['table_sqlines']); }
	if (isset($_POST['table_maxvalues'])) { save_config('table_maxvalues',$_POST['table_maxvalues']); }
	if (isset($_POST['table_server'])) { save_config('table_server',$_POST['table_server']); }
	if (isset($_POST['table_ison'])) { save_config('table_ison',$_POST['table_ison']); }
	if (isset($_POST['table_tld'])) { save_config('table_tld',$_POST['table_tld']); }
	if (isset($_POST['table_cstats'])) { save_config('table_cstats',$_POST['table_cstats']); }
	if (isset($_POST['table_ustats'])) { save_config('table_ustats',$_POST['table_ustats']); }
	if (isset($_POST['table_current'])) { save_config('table_current',$_POST['table_current']); }
	if (isset($_POST['table_serverstats'])) { save_config('table_serverstats',$_POST['table_serverstats']); }
	if (isset($_POST['table_channelstats'])) { save_config('table_channelstats',$_POST['table_channelstats']); }
	if (isset($_POST['table_userstats'])) { save_config('table_userstats',$_POST['table_userstats']); }
	if (isset($_POST['table_aliases'])) { save_config('table_aliases',$_POST['table_aliases']); }
	$config = get_config();
	echo "<div class=\"configsave\">Configuration saved</div>";
}

?>
<div class="page_title">Advanced Settings</div>
<form id="advanced" name="advanced" method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr>
      <td align="right">Debug mode</td>
      <td align="left"><select name="debug_mode" id="debug_mode" tabindex="1" >
        <option value="0" <?php if ($config['debug_mode'] == '0') { echo "selected=\"selected\""; } ?>>Off</option>
        <option value="1" <?php if ($config['debug_mode'] == '1') { echo "selected=\"selected\""; } ?>>PHP Warnings/SQL Errors</option>
        <option value="2" <?php if ($config['debug_mode'] == '2') { echo "selected=\"selected\""; } ?>>Verbose debugging</option>
      </select></td>
    </tr>
    <tr>
      <td align="right">Show script execution time and number of performed SQL queries</td>
      <td align="left"><input name="show_exec_time" type="checkbox" id="show_exec_time" tabindex="2" <?php if ($config['show_exec_time'] == true) { echo "checked=\"checked\" "; } ?> /></td>
    </tr>
    <tr>
      <td align="right">Show links to XHTML and CSS validators</td>
      <td align="left"><input name="show_validators" type="checkbox" id="show_validators" tabindex="3" <?php if ($config['show_validators'] == true) { echo "checked=\"checked\" "; } ?> /></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><hr /></td>
    </tr>
    <tr>
      <td colspan="2" align="left"><p><em>You should leave the following settings as they are, unless you specified different tables in your Denora configuration<br />
      <strong>Don't change these values unless you know what you are doing!</strong></em></p>
      </td>
    </tr>
    <tr>
      <td align="right">Name of the Users Table</td>
      <td align="left"><input name="table_user" type="text" id="table_user" value="<?php echo $config['table_user']; ?>" size="32" maxlength="1024" tabindex="4" /></td>
    </tr>
    <tr>
      <td align="right">Name of the Channel Table</td>
      <td align="left"><input name="table_chan" type="text" id="table_chan" value="<?php echo $config['table_chan']; ?>" size="32" maxlength="1024" tabindex="5" /></td>
    </tr>
    <tr>
      <td align="right">Name of the Bans Table</td>
      <td align="left"><input name="table_chanbans" type="text" id="table_chanbans" value="<?php echo $config['table_chanbans']; ?>" size="32" maxlength="1024" tabindex="6" /></td>
    </tr>
    <tr>
      <td align="right">Name of the Ban Exceptions Table</td>
      <td align="left"><input name="table_chanexcepts" type="text" id="table_chanexcepts" value="<?php echo $config['table_chanexcepts']; ?>" size="32" maxlength="1024" tabindex="7" /></td>
    </tr>
    <tr>
      <td align="right">Name of the Invite Exceptions Table</td>
      <td align="left"><input name="table_chaninvites" type="text" id="table_chaninvites" value="<?php echo $config['table_chaninvites']; ?>" size="32" maxlength="1024" tabindex="8" /></td>
    </tr>
    <tr>
      <td align="right">Name of the Glines Table</td>
      <td align="left"><input name="table_glines" type="text" id="table_glines" value="<?php echo $config['table_glines']; ?>" size="32" maxlength="1024" tabindex="9" /></td>
    </tr>
    <tr>
      <td align="right">Name of the SQlines Table</td>
      <td align="left"><input name="table_sqlines" type="text" id="table_sqlines" value="<?php echo $config['table_sqlines']; ?>" size="32" maxlength="1024" tabindex="10" /></td>
    </tr>
    <tr>
      <td align="right"><p>Name of the Max Values Table</p>      </td>
      <td align="left"><input name="table_maxvalues" type="text" id="table_maxvalues" value="<?php echo $config['table_maxvalues']; ?>" size="32" maxlength="1024" tabindex="11" /></td>
    </tr>
    <tr>
      <td align="right">Name of the Servers Table</td>
      <td align="left"><input name="table_server" type="text" id="table_server" value="<?php echo $config['table_server']; ?>" size="32" maxlength="1024" tabindex="12" /></td>
    </tr>
    <tr>
      <td align="right">Name of the 'Is On' Table</td>
      <td align="left"><input name="table_ison" type="text" id="table_ison" value="<?php echo $config['table_ison']; ?>" size="32" maxlength="1024" tabindex="13" /></td>
    </tr>
    <tr>
      <td align="right">Name of the Country Table</td>
      <td align="left"><input name="table_tld" type="text" id="table_tld" value="<?php echo $config['table_tld']; ?>" size="32" maxlength="1024" tabindex="14" /></td>
    </tr>
    <tr>
      <td align="right">Name of the Chan Stats Table</td>
      <td align="left"><input name="table_cstats" type="text" id="table_cstats" value="<?php echo $config['table_cstats']; ?>" size="32" maxlength="1024" tabindex="15" /></td>
    </tr>
    <tr>
      <td align="right">Name of the User Stats Table</td>
      <td align="left"><input name="table_ustats" type="text" id="table_ustats" value="<?php echo $config['table_ustats']; ?>" size="32" maxlength="1024" tabindex="16" /></td>
    </tr>
    <tr>
      <td align="right">Name of the Current Values Table</td>
      <td align="left"><input name="table_current" type="text" id="table_current" value="<?php echo $config['table_current']; ?>" size="32" maxlength="1024" tabindex="17" /></td>
    </tr>
    <tr>
      <td align="right">Name of the Hourly Server Stats Table</td>
      <td align="left"><input name="table_serverstats" type="text" id="table_serverstats" value="<?php echo $config['table_serverstats']; ?>" size="32" maxlength="1024" tabindex="18" /></td>
    </tr>
    <tr>
      <td align="right">Name of the Hourly Channel Stats Table</td>
      <td align="left"><input name="table_channelstats" type="text" id="table_channelstats" value="<?php echo $config['table_channelstats']; ?>" size="32" maxlength="1024" tabindex="19" /></td>
    </tr>
    <tr>
      <td align="right">Name of the Hourly User StatsTable</td>
      <td align="left"><input name="table_userstats" type="text" id="table_userstats" value="<?php echo $config['table_userstats']; ?>" size="32" maxlength="1024" tabindex="20" /></td>
    </tr>
    <tr>
      <td align="right">Name of the Aliases Table</td>
      <td align="left"><input name="table_aliases" type="text" id="table_aliases" value="<?php echo $config['table_aliases']; ?>" size="32" maxlength="1024" tabindex="21" /></td>
    </tr>
  </table>
  <p align="right">
    <input type="submit" name="button" id="button" value="Save" tabindex="22" />
    </p>
</form>
