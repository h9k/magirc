<?php
// $Id: integration.php 315 2007-08-18 11:41:28Z Hal9000 $


if (isset($_POST['button'])) {
	if (isset($_POST['netsplit_id'])) { save_config('netsplit_id',$_POST['netsplit_id']); }
	else { save_config('netsplit_id',''); }
	if (isset($_POST['netsplit_graphs'])) { save_config('netsplit_graphs',1); }
	else { save_config('netsplit_graphs',0); }
	if (isset($_POST['netsplit_years'])) { save_config('netsplit_years',1); }
	else { save_config('netsplit_years',0); }
	if (isset($_POST['netsplit_history'])) { save_config('netsplit_history',1); }
	else { save_config('netsplit_history',0); }
	if (isset($_POST['searchirc_id'])) { save_config('searchirc_id',$_POST['searchirc_id']); }
	else { save_config('searchirc_id',''); }
	if (isset($_POST['searchirc_ranking'])) { save_config('searchirc_ranking',1); }
	else { save_config('searchirc_ranking',0); }
	if (isset($_POST['searchirc_graphs'])) { save_config('searchirc_graphs',1); }
	else { save_config('searchirc_graphs',0); }
	if (isset($_POST['adsense'])) { save_config('adsense',1); }
	else { save_config('adsense',0); }
	if (isset($_POST['adsense_id'])) { save_config('adsense_id',$_POST['adsense_id']); }
	else { save_config('adsense_id',''); }
	if (isset($_POST['adsense_channel'])) { save_config('adsense_channel',$_POST['adsense_channel']); }
	else { save_config('adsense_channel',''); }
	$config = get_config();
	echo "<div class=\"configsave\">Configuration saved</div>";
}

?>
<div class="page_title">Integration Settings</div>
<form id="integration" name="integration" method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr>
      <td align="right">The <strong>URL parameter for the Netsplit.de features</strong>, usually your network name. </td>
      <td align="left"><input name="netsplit_id" type="text" id="netsplit_id" value="<?php echo $config['netsplit_id']; ?>" size="32" maxlength="1024" tabindex="1" /></td>
    </tr>
    <tr>
      <td colspan="2" align="left"><em>For more information about being ranked on Netsplit.de visit <a href="http://irc.netsplit.de/">http://irc.netsplit.de/</a></em></td>
    </tr>
    <tr>
      <td align="right">Enable <strong>Netsplit.de Graphs</strong> integration</td>
      <td align="left"><input name="netsplit_graphs" type="checkbox" id="netsplit_graphs" tabindex="2" <?php if ($config['netsplit_graphs'] == true) { echo "checked=\"checked\" "; } ?> /></td>
    </tr>
    <tr>
      <td align="right">Enable <strong>Netsplit.de &quot;Last 2 Years&quot;</strong> graphs. If your network is too young this will not work</td>
      <td align="left"><input name="netsplit_years" type="checkbox" id="netsplit_years" tabindex="3" checked="checked" disabled="disabled" /></td>
    </tr>
    <tr>
      <td align="right">Enable <strong>Netsplit.de &quot;Complete History&quot;</strong> graphs. If your network is too young this will not work</td>
      <td align="left"><input name="netsplit_history" type="checkbox" id="netsplit_history" tabindex="4" <?php if ($config['netsplit_history'] == true) { echo "checked=\"checked\" "; } ?> /></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><hr /></td>
    </tr>
    <tr>
      <td align="right">Set yout <strong>network ID for SearchIRC</strong> features. </td>
      <td align="left"><input name="searchirc_id" type="text" id="searchirc_id" value="<?php echo $config['searchirc_id']; ?>" size="7" maxlength="6" tabindex="5" /></td>
    </tr>
    <tr>
      <td colspan="2" align="left"><em>For more information about being ranked on SearchIRC visit <a href="http://searchirc.com/">http://searchirc.com/</a>. To find out your ID go to your network information page (usually http://searchirc.com/network/YourNetwork) then right-click on the Users graph on the right to get its path and get the number from the 'n=' parameter.</em></td>
    </tr>
    <tr>
      <td align="right">Enable <strong>SearchIRC Ranking icon</strong> on the front page</td>
      <td align="left"><input name="searchirc_ranking" type="checkbox" id="searchirc_ranking" tabindex="6" <?php if ($config['searchirc_ranking'] == true) { echo "checked=\"checked\" "; } ?> /></td>
    </tr>
    <tr>
      <td align="right">Enable <strong>SearchIRC Graphs</strong> Integration</td>
      <td align="left"><input name="searchirc_graphs" type="checkbox" id="searchirc_graphs" tabindex="7" <?php if ($config['searchirc_graphs'] == true) { echo "checked=\"checked\" "; } ?> /></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><hr /></td>
    </tr>
    <tr>
      <td align="right">Enable <strong>Google AdSense</strong> advertisements</td>
      <td align="left"><input name="adsense" type="checkbox" id="adsense" tabindex="8" <?php if ($config['adsense'] == true) { echo "checked=\"checked\" "; } ?> /></td>
    </tr>
    <tr>
      <td align="right">Set your <strong>Google AdSense Client ID</strong>. </td>
      <td align="left"><input name="adsense_id" type="text" id="adsense_id" value="<?php echo $config['adsense_id']; ?>" size="32" maxlength="1024" tabindex="9" />      </td>
    </tr>
    <tr>
      <td colspan="2" align="left"><em>If you would like to support phpDenora you can use &quot;pub-2514457845805307&quot; ;)</em></td>
    </tr>
    <tr>
      <td align="right">Set the <strong>Ad Channel</strong> (optional)</td>
      <td align="left"><input name="adsense_channel" type="text" id="adsense_channel" value="<?php echo $config['adsense_channel']; ?>" size="32" maxlength="1024" tabindex="10" /></td>
    </tr>
  </table>
  <p align="right">
    <input type="submit" name="button" id="button" value="Save" tabindex="11" />
    </p>
</form>
