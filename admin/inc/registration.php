<?php
// $Id: registration.php 310 2007-07-28 15:07:16Z Hal9000 $

/** ensure this file is being included by a parent file */
defined('_VALID_PARENT') or header("Location: ../");

?>
<div class="page_title">Product Registration</div>
<form id="registration" method="post" action="http://denorastats.org/register.php">
      <table border="0" cellspacing="2" cellpadding="2">
        <tr>
          <td align="right"><strong>Network Name:</strong></td>
          <td><?php echo $config['net_name']; ?></td>
        </tr>
        <tr>
          <td align="right"><strong>Network Homepage URL: </strong></td>
          <td><?php echo $config['net_url']; ?></td>
        </tr>
        <tr>
          <td align="right"><strong>phpDenora URL: </strong></td>
          <td>http://<?php echo $phpdenora_url; ?></td>
        </tr>
        <tr>
          <td align="right"><strong>phpDenora Version: </strong></td>
          <td><?php echo  VERSION_FULL; ?></td>
        </tr>
        <tr>
          <td align="right"><strong>Email address:</strong></td>
          <td><input name="email" type="text" size="32" /></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>
		  <input type="hidden" name="netname" value="<?php echo $config['net_name']; ?>" />
		  <input type="hidden" name="netpage" value="<?php echo $config['net_url']; ?>" />
		  <input type="hidden" name="homepage" value="<?php echo "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" />
		  <input type="hidden" name="version" value="<?php echo VERSION_FULL; ?>" />
		  <input type="hidden" name="ircd" value="<?php echo $config['ircd_type']; ?>" />
            <input type="submit" name="Submit" value="Submit" /></td>
        </tr>
      </table>
    </form>