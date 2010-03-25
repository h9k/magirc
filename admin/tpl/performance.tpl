{* $Id$ *}
{include file="_header.tpl"}

<h2>Performance Settings</h2>
<form id="performance" name="performance" method="post" action="">

<div id="toolbar">
    <input type="hidden" name="form" value="performance" />
    <ul>
        <li><a href="#" onclick="javascript:document.performance.submit();return false"><img src="img/save.png" alt="" /> Save</a></li>
    </ul>
</div>

  <table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr>
      <td align="right">Enable the <strong>caching of graph images</strong>. This speeds up things a bit. </td>
      <td align="left"><input name="graph_cache" type="checkbox" id="graph_cache" tabindex="1" {if $config.graph_cache}checked="checked" {/if}/></td>
    </tr>
    <tr>
      <td align="right">Specify the directory used for caching. </td>
      <td align="left"><input name="graph_cache_path" type="text" id="graph_cache_path" value="{$config.graph_cache_path}" size="32" maxlength="1024" tabindex="2" /></td>
    </tr>
    <tr>
      <td colspan="2" align="left"><em>The directory MUST be writeable by the web aserver AND contain a trailing /slash!</em></td>
    </tr>
    <tr>
      <td align="right">For how long should network graphs be cached?</td>
      <td align="left"><input name="net_cache_time" type="text" id="net_cache_time" value="{$config.net_cache_time}" size="5" maxlength="4" tabindex="3" /> 
        minute(s)</td>
    </tr>
    <tr>
      <td align="right">For how long should pie graphs be cached?</td>
      <td align="left"><input name="pie_cache_time" type="text" id="pie_cache_time" value="{$config.pie_cache_time}" size="5" maxlength="4" tabindex="4" />
        minute(s)</td>
    </tr>
    <tr>
      <td align="right">For how long should network graphs be cached?</td>
      <td align="left"><input name="bar_cache_time" type="text" id="bar_cache_time" value="{$config.bar_cache_time}" size="5" maxlength="4" tabindex="5" />
        minute(s)</td>
    </tr>
    <tr>
      <td colspan="2" align="center"><hr /></td>
    </tr>
    <tr>
      <td align="right">Enable <strong>gzip page compression</strong></td>
      <td align="left"><input name="gzip" type="checkbox" id="gzip" tabindex="6" {if $config.gzip}checked="checked" {/if}/></td>
    </tr>
  </table>
</form>

{include file="_footer.tpl"}