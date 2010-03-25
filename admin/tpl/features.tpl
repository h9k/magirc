{* $Id$ *}
{include file="_header.tpl"}

<h2>Feature Settings</h2>
<form id="features" name="features" method="post" action="">

<div id="toolbar">
    <input type="hidden" name="form" value="features" />
    <ul>
        <li><a href="#" onclick="javascript:document.features.submit();return false"><img src="img/save.png" alt="" /> Save</a></li>
    </ul>
</div>

  <table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr>
      <td align="right">Enables <strong>online status and country lookups</strong> in user listings. This will require an additional query for each user, set this to false if you want to keep sql load low</td>
      <td align="left"><input name="status_lookup" type="checkbox" id="status_lookup" tabindex="1" {if $config.status_lookup}checked="checked" {/if}/></td>
    </tr>
    <tr>
      <td align="right">Enables <strong>country statistics</strong>. Set to false if your ircds don't resolve hostnames</td>
      <td align="left"><input name="tld_stats" type="checkbox" id="tld_stats" tabindex="2" {if $config.tld_stats}checked="checked" {/if}/></td>
    </tr>
    <tr>
      <td align="right">Enables <strong>client version statistics</strong>. Set to false if your Denora does not version your clients</td>
      <td align="left"><input name="client_stats" type="checkbox" id="client_stats" tabindex="3" {if $config.client_stats}checked="checked" {/if}/></td>
    </tr>
    <tr>
      <td align="right">Enables network statistics <strong>graphs</strong>.</td>
      <td align="left"><input name="net_graphs" type="checkbox" id="net_graphs" tabindex="7" {if $config.net_graphs}checked="checked" {/if}/></td>
    </tr>
    <tr>
      <td align="right">Enable the <strong>mIRC icon</strong> in the channel list</td>
      <td align="left"><input name="mirc" type="checkbox" id="mirc" tabindex="8" {if $config.mirc}checked="checked" {/if}/></td>
    </tr>
    <tr>
      <td align="right">The URL for the mIRC icon, including trailing slash<strong></strong></td>
      <td align="left"><input name="mirc_url" type="text" id="mirc_url" value="{$config.mirc_url}" size="32" maxlength="1024" tabindex="9" />      </td>
    </tr>
    <tr>
      <td align="right">Enable the <strong>web chat icon</strong> in the channel list</td>
      <td align="left"><input name="webchat" type="checkbox" id="webchat" tabindex="10" {if $config.webchat}checked="checked" {/if}/></td>
    </tr>
    <tr>
      <td align="right">The URL for the web chat icon</td>
      <td align="left"><input name="webchat_url" type="text" id="webchat_url" value="{$config.webchat_url}" size="32" maxlength="1024" tabindex="11" /></td>
    </tr>
    <tr>
      <td align="right">Enable the <strong>remote api interface</strong>, which enables to easilly embed data in web sites</td>
      <td align="left"><input name="remote" type="checkbox" id="remote" tabindex="12" {if $config.remote}checked="checked" {/if}/></td>
    </tr>
  </table>
</form>

{include file="_footer.tpl"}