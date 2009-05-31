<?php
// $Id: performance.php 311 2007-07-30 20:17:06Z Hal9000 $

/** ensure this file is being included by a parent file */
defined('_VALID_PARENT') or header("Location: ../");

if (isset($_POST['button'])) {
	if (isset($_POST['graph_cache'])) { save_config('graph_cache',1); }
	else { save_config('graph_cache',0); }
	if (isset($_POST['graph_cache_path'])) { save_config('graph_cache_path',$_POST['graph_cache_path']); }
	else { save_config('graph_cache_path',''); }
	if (isset($_POST['net_cache_time'])) { save_config('net_cache_time',$_POST['net_cache_time']); }
	else { save_config('net_cache_time',''); }
	if (isset($_POST['pie_cache_time'])) { save_config('pie_cache_time',$_POST['pie_cache_time']); }
	else { save_config('pie_cache_time',''); }
	if (isset($_POST['bar_cache_time'])) { save_config('bar_cache_time',$_POST['bar_cache_time']); }
	else { save_config('bar_cache_time',''); }
	if (isset($_POST['gzip'])) { save_config('gzip',1); }
	else { save_config('gzip',0); }
	$config = get_config();
	echo "<div class=\"configsave\">Configuration saved</div>";
}

?>
<div class="page_title">Performance Settings</div>
<form id="performance" name="performance" method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr>
      <td align="right">Enable the <strong>caching of graph images</strong>. This speeds up things a bit. </td>
      <td align="left"><input name="graph_cache" type="checkbox" id="graph_cache" tabindex="1" <?php if ($config['graph_cache'] == true) { echo "checked=\"checked\" "; } ?> /></td>
    </tr>
    <tr>
      <td align="right">Specify the directory used for caching. </td>
      <td align="left"><input name="graph_cache_path" type="text" id="graph_cache_path" value="<?php echo $config['graph_cache_path']; ?>" size="32" maxlength="1024" tabindex="2" /></td>
    </tr>
    <tr>
      <td colspan="2" align="left"><em>The directory MUST be writeable by the web aserver AND contain a trailing /slash!</em></td>
    </tr>
    <tr>
      <td align="right">For how long should network graphs be cached?</td>
      <td align="left"><input name="net_cache_time" type="text" id="net_cache_time" value="<?php echo $config['net_cache_time']; ?>" size="5" maxlength="4" tabindex="3" /> 
        minute(s)</td>
    </tr>
    <tr>
      <td align="right">For how long should pie graphs be cached?</td>
      <td align="left"><input name="pie_cache_time" type="text" id="pie_cache_time" value="<?php echo $config['pie_cache_time']; ?>" size="5" maxlength="4" tabindex="4" />
        minute(s)</td>
    </tr>
    <tr>
      <td align="right">For how long should network graphs be cached?</td>
      <td align="left"><input name="bar_cache_time" type="text" id="bar_cache_time" value="<?php echo $config['bar_cache_time']; ?>" size="5" maxlength="4" tabindex="5" />
        minute(s)</td>
    </tr>
    <tr>
      <td colspan="2" align="center"><hr /></td>
    </tr>
    <tr>
      <td align="right">Enable <strong>gzip page compression</strong></td>
      <td align="left"><input name="gzip" type="checkbox" id="gzip" tabindex="6" <?php if ($config['gzip'] == true) { echo "checked=\"checked\" "; } ?> /></td>
    </tr>
  </table>
  <p align="right">
    <input type="submit" name="button" id="button" value="Save" tabindex="7" />
    </p>
</form>
