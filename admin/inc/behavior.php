<?php
// $Id: behavior.php 311 2007-07-30 20:17:06Z Hal9000 $

/** ensure this file is being included by a parent file */
defined('_VALID_PARENT') or header("Location: ../");

if (isset($_POST['button'])) {
	if (isset($_POST['chanstats_sort'])) { save_config('chanstats_sort',$_POST['chanstats_sort']); }
	if (isset($_POST['chanstats_type'])) { save_config('chanstats_type',$_POST['chanstats_type']); }
	if (isset($_POST['list_limit'])) { save_config('list_limit',$_POST['list_limit']); }
	if (isset($_POST['top_limit'])) { save_config('hide_secret',$_POST['top_limit']); }
	if (isset($_POST['search_min_chars'])) { save_config('search_min_chars',$_POST['search_min_chars']); }
	$config = get_config();
	echo "<div class=\"configsave\">Configuration saved</div>";
}

?>
<div class="page_title">Behavior Settings</div>
<form id="behavior" name="behavior" method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr>
      <td align="right">Set the <strong>default ordering column</strong> for user/channel stat tables</td>
      <td align="left"><select name="chanstats_sort" id="chanstats_sort" tabindex="1" >
        <option value="actions" <?php if ($config['chanstats_sort'] == 'actions') { echo "selected=\"selected\""; } ?>>Actions</option>
        <option value="kicks" <?php if ($config['chanstats_sort'] == 'kicks') { echo "selected=\"selected\""; } ?>>Kicks</option>
        <option value="letters" <?php if ($config['chanstats_sort'] == 'letters') { echo "selected=\"selected\""; } ?>>Letters</option>
        <option value="line" <?php if ($config['chanstats_sort'] == 'line') { echo "selected=\"selected\""; } ?>>Lines</option>
        <option value="modes" <?php if ($config['chanstats_sort'] == 'modes') { echo "selected=\"selected\""; } ?>>Modes</option>
        <option value="smileys" <?php if ($config['chanstats_sort'] == 'smileys') { echo "selected=\"selected\""; } ?>>Smileys</option>
        <option value="topics" <?php if ($config['chanstats_sort'] == 'topics') { echo "selected=\"selected\""; } ?>>Topics</option>
        <option value="words" <?php if ($config['chanstats_sort'] == 'words') { echo "selected=\"selected\""; } ?>>Words</option>
      </select></td>
    </tr>
    <tr>
      <td align="right">Set the <strong>default stats type</strong> for user/Channel stat tables</td>
      <td align="left"><select name="chanstats_type" id="chanstats_type" tabindex="2" >
        <option value="1" <?php if ($config['chanstats_type'] == '1') { echo "selected=\"selected\""; } ?>>Today</option>
        <option value="2" <?php if ($config['chanstats_type'] == '2') { echo "selected=\"selected\""; } ?>>This Week</option>
        <option value="3" <?php if ($config['chanstats_type'] == '3') { echo "selected=\"selected\""; } ?>>This Month</option>
        <option value="0" <?php if ($config['chanstats_type'] == '0') { echo "selected=\"selected\""; } ?>>Total</option>
      </select></td>
    </tr>
    <tr>
      <td align="right">Sets the <strong>limit for table listed outputs</strong></td>
      <td align="left"><input name="list_limit" type="text" id="list_limit" value="<?php echo $config['list_limit']; ?>" size="4" maxlength="3" tabindex="3" />      </td>
    </tr>
    <tr>
      <td align="right">Sets the <strong>limit for TOP channels/users</strong> outputs on the front page</td>
      <td align="left"><input name="top_limit" type="text" id="top_limit" value="<?php echo $config['top_limit']; ?>" size="4" maxlength="3" tabindex="4" /></td>
    </tr>
    <tr>
      <td align="right">Minimum amount of characters needed (excluding wildcards) to make a search query</td>
      <td align="left"><input name="search_min_chars" type="text" id="search_min_chars" value="<?php echo $config['search_min_chars']; ?>" size="2" maxlength="1" tabindex="5" /></td>
    </tr>
  </table>
  <p align="right">
    <input type="submit" name="button" id="button" value="Save" tabindex="6" />
    </p>
</form>
