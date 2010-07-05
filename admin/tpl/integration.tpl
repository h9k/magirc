{* $Id$ *}
{include file="_header.tpl"}

<h2>Integration Settings</h2>
<form id="integration" method="post" action="">

<div id="toolbar">
    <input type="hidden" name="form" value="integration" />
    <ul>
        <li><a href="#" onclick="javascript:document.forms['integration'].submit();return false"><img src="img/save.png" alt="" /> Save</a></li>
    </ul>
</div>

  <table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr>
      <td align="right">The <strong>URL parameter for the Netsplit.de features</strong>, usually your network name. </td>
      <td align="left"><input name="netsplit_id" type="text" id="netsplit_id" value="{$config.netsplit_id}" size="32" maxlength="1024" tabindex="1" /></td>
    </tr>
    <tr>
      <td colspan="2" align="left"><em>For more information about being ranked on Netsplit.de visit <a href="http://irc.netsplit.de/">http://irc.netsplit.de/</a></em></td>
    </tr>
    <tr>
      <td align="right">Enable <strong>Netsplit.de Graphs</strong> integration</td>
      <td align="left"><input name="netsplit_graphs" type="checkbox" id="netsplit_graphs" tabindex="2" {if $config.netsplit_graphs}checked="checked" {/if}/></td>
    </tr>
    <tr>
      <td align="right">Enable <strong>Netsplit.de &quot;Last 2 Years&quot;</strong> graphs. If your network is too young this will not work</td>
      <td align="left"><input name="netsplit_years" type="checkbox" id="netsplit_years" tabindex="3" checked="checked" disabled="disabled" /></td>
    </tr>
    <tr>
      <td align="right">Enable <strong>Netsplit.de &quot;Complete History&quot;</strong> graphs. If your network is too young this will not work</td>
      <td align="left"><input name="netsplit_history" type="checkbox" id="netsplit_history" tabindex="4" {if $config.netsplit_history}checked="checked" {/if}/></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><hr /></td>
    </tr>
    <tr>
      <td align="right">Set yout <strong>network ID for SearchIRC</strong> features. </td>
      <td align="left"><input name="searchirc_id" type="text" id="searchirc_id" value="{$config.searchirc_id}" size="7" maxlength="6" tabindex="5" /></td>
    </tr>
    <tr>
      <td colspan="2" align="left"><em>For more information about being ranked on SearchIRC visit <a href="http://searchirc.com/">http://searchirc.com/</a>. To find out your ID go to your network information page (usually http://searchirc.com/network/YourNetwork) then right-click on the Users graph on the right to get its path and get the number from the 'n=' parameter.</em></td>
    </tr>
    <tr>
      <td align="right">Enable <strong>SearchIRC Ranking icon</strong> on the front page</td>
      <td align="left"><input name="searchirc_ranking" type="checkbox" id="searchirc_ranking" tabindex="6" {if $config.searchirc_ranking}checked="checked" {/if}/></td>
    </tr>
    <tr>
      <td align="right">Enable <strong>SearchIRC Graphs</strong> Integration</td>
      <td align="left"><input name="searchirc_graphs" type="checkbox" id="searchirc_graphs" tabindex="7" {if $config.searchirc_graphs}checked="checked" {/if}/></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><hr /></td>
    </tr>
    <tr>
      <td align="right">Enable <strong>Google AdSense</strong> advertisements</td>
      <td align="left"><input name="adsense" type="checkbox" id="adsense" tabindex="8" {if $config.adsense}checked="checked" {/if}/></td>
    </tr>
    <tr>
      <td align="right">Set your <strong>Google AdSense Client ID</strong>. </td>
      <td align="left"><input name="adsense_id" type="text" id="adsense_id" value="{$config.adsense_id}" size="32" maxlength="1024" tabindex="9" />      </td>
    </tr>
    <tr>
      <td colspan="2" align="left"><em>If you would like to support MagIRC you can use &quot;pub-2514457845805307&quot; ;)</em></td>
    </tr>
    <tr>
      <td align="right">Set the <strong>Ad Channel</strong> (optional)</td>
      <td align="left"><input name="adsense_channel" type="text" id="adsense_channel" value="{$config.adsense_channel}" size="32" maxlength="1024" tabindex="10" /></td>
    </tr>
  </table>
</form>

{include file="_footer.tpl"}