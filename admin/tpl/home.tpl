{* $Id$ *}
{include file="_header.tpl"}

<div class="page_title">Welcome, {$smarty.const.loginUsername}</div>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top"><img src="img/screenshot.png" alt="" width="175" height="287" /></td>
    <td valign="top"><p>You can now administer the Magirc configuration.<br />
        Please select a settings category from the left menu.</p>
    {if $setup}
      <div class="warning">
        <p>In order to make Magirc available to the public, you must <strong>remove the <em>../setup/</em> directory</strong>!</p>
      </div>
    {/if}
      <div class="warning">
        <p>You are using an experimental version of Magirc.<br />
          <strong>DO NOT USE IN PRODUCTION!<br />
          </strong>Please report any bugs you may encounter<br />
          to the <a href="http://dev.denorastats.org/projects/magirc/issues">Bug Tracker</a>. Thank you!</p>
      </div>
        <p>Magirc version: <em>{$smarty.const.VERSION_FULL}</em><br />
        IRCd protocol: <em>{$config.ircd_type}</em><br />
        Denora version: <em>{$version.denora}</em><br />
        PHP version: <em>{$version.php}</em><br />
        Smarty version: <em>{$smarty.version}</em><br />
        MySQL client version: <em>{$version.sql_client}</em><br />
        MySQL server version: <em>{$version.sql_server}</em><br />
        </p>
        <p>
        {if $version.denora eq 'unknown'}
	  		Denora seems to be <span style="color:red;">down</span>. Please start your Denora server!
		{else}
			Denora seems to be <span style="color:green;">up</span>.
		{/if}
        </p>
      <table width="100%" cellpadding="5" cellspacing="2">
  <tr>
    <td width="25%"
     align="center" valign="bottom" bgcolor="#F0F0F0"><a href="?page=registration"><img src="img/register.png" alt="Product Registration" width="32" height="32" /><br />
      Register</a></td>
    <td width="25%" align="center" valign="bottom" bgcolor="#F0F0F0"><a href="http://magirc.org/"><img src="img/homepage.png" alt="Project Homepage" width="32" height="32" /><br />
      Homepage</a></td>
    <td width="25%" align="center" valign="bottom" bgcolor="#F0F0F0"><a href="http://denorastats.org/support/"><img src="img/support.png" alt="Product Support" width="32" height="32" /><br />
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
{include file="_footer.tpl"}