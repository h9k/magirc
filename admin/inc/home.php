<?php
// $Id: home.php 310 2007-07-28 15:07:16Z Hal9000 $

/** ensure this file is being included by a parent file */
defined('_VALID_PARENT') or header("Location: ../");
$denora_version = phpdenora_versioncheck('num');
if (!$denora_version) { $denora_version = "unknown"; }
?>

<div class="page_title">Welcome, <?php echo $_SESSION["loginUsername"]; ?></div>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top"><img src="img/screenshot.png" alt="" width="175" height="287" /></td>
    <td valign="top"><p>You can now administer the phpDenora configuration.<br />
        Please select a settings category from the left menu.</p>
    <?php if (file_exists('../install/')) { ?>
      <div class="warning">
        <p>In order to make phpDenora available to the public, you must <strong>remove the <em>../install/</em> directory</strong>!</p>
      </div>
    <?php } ?>
      <div class="warning">
        <p>You are using an experimental version of phpDenora.<br />
          <strong>DO NOT USE IN PRODUCTION!<br />
          </strong>Please report any bugs you may encounter<br />
          to the <a href="http://denorastats.org/mantis/">Bug Tracker</a>. Thank you!</p>
      </div>
      <p><?php echo "phpDenora version: <em>" . VERSION_FULL . "</em>"; ?><br />
        <?php echo "IRCd protocol: <em>" . $config['ircd_type'] . "</em>"; ?><br />
        <?php echo "Denora version: <em>" . $denora_version . "</em>"; ?><br />
        <?php echo "PHP version: <em>" . phpversion() . "</em>"; ?><br />
        <?php echo "MySQL client version: <em>" . mysql_get_client_info() . "</em>"; ?><br />
        <?php
        $link = mysql_connect($sql['hostname'], $sql['username'], $sql['password']);
		if ($link)
			printf("MySQL server version: <em>%s</em>", mysql_get_server_info());
		?>
      </p>
      <p>
        <?php
       	if ($denora_version == 'unknown')
	  		echo "Denora seems to be <span style=\"color:red;\">down</span>. Please start your Denora server!";
		else
			echo "Denora seems to be <span style=\"color:green;\">up</span>.";
		?>
      </p>
      <table width="100%" cellpadding="5" cellspacing="2">
  <tr>
    <td width="25%" align="center" valign="bottom" bgcolor="#F0F0F0"><a href="?page=registration"><img src="img/register.png" alt="Product Registration" width="32" height="32" /><br />
      Register</a></td>
    <td width="25%" align="center" valign="bottom" bgcolor="#F0F0F0"><a href="http://denorastats.org/"><img src="img/homepage.png" alt="Project Homepage" width="32" height="32" /><br />
      Homepage</a></td>
    <td width="25%" align="center" valign="bottom" bgcolor="#F0F0F0"><a href="http://denorastats.org/?p=support"><img src="img/support.png" alt="Product Support" width="32" height="32" /><br />
      Support</a></td>
    <td width="25%" align="center" valign="bottom" bgcolor="#F0F0F0">Donate</td>
  </tr>
</table>
<p></p>
</td>
</tr>
</table>
<p>&nbsp;</p>
<p>&nbsp;</p>
