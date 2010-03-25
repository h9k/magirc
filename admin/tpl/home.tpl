{* $Id$ *}
{include file="_header.tpl"}

<h2>Welcome, {$smarty.session.username}</h2>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top"><img src="img/screenshot.png" alt="" width="175" height="287" /></td>
    <td valign="top"><p>You can now administer the MagIRC configuration.<br />
        Please select a settings category from the left menu.</p>
    {if $setup}
      <div class="warning">
        <p>In order to make MagIRC available to the public, you must <strong>remove the <em>../setup/</em> directory</strong>!</p>
      </div>
    {/if}
      <div class="warning">
        <p>You are using an experimental version of MagIRC.<br />
          <strong>DO NOT USE IN PRODUCTION!<br />
          </strong>Please report any bugs you may encounter<br />
          to the <a href="http://dev.denorastats.org/">Bug Tracker</a>. Thank you!</p>
      </div>
        <p>MagIRC version: <em>{$smarty.const.VERSION_FULL}</em><br />
        IRCd protocol: <em>{$config.ircd_type}</em><br />
        Denora version: <em>{$version.denora}</em><br />
        PHP version: <em>{$version.php}</em><br />
        Smarty version: <em>{$smarty.version}</em><br />
        MySQL client version: <em>{$version.sql_client}</em><br />
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
    <td width="25%" align="center" valign="bottom" bgcolor="#F0F0F0"><a href="http://www.magirc.org/"><img src="img/homepage.png" alt="Project Homepage" width="32" height="32" /><br />
      Homepage</a></td>
    <td width="25%" align="center" valign="bottom" bgcolor="#F0F0F0"><a href="http://www.denorastats.org/support/"><img src="img/support.png" alt="Product Support" width="32" height="32" /><br />
      Support</a></td>
    <td width="25%" align="center" valign="bottom" bgcolor="#F0F0F0"><form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="width:100px;"> 
<div> 
<input type="hidden" name="cmd" value="_s-xclick" /> 
<input type="hidden" name="hosted_button_id" value="5270963" /> 
<input type="image" src="https://www.paypal.com/en_GB/i/btn/btn_donate_SM.gif" name="submit" alt="PayPal - The safer, easier way to pay online." /> 
<img alt="" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" /> 
</div> 
</form></td>
  </tr>
</table>
<p></p>
</td>
</tr>
</table>
<p>&nbsp;</p>
<p>&nbsp;</p>
{include file="_footer.tpl"}