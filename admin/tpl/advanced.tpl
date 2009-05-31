{* $Id$ *}
{include file="_header.tpl"}

<div class="page_title">Advanced Settings</div>
<form id="advanced" name="advanced" method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr>
      <td align="right">Debug mode</td>
      <td align="left"><select name="debug_mode" id="debug_mode" tabindex="1" >
        <option value="0"{if $config.debug_mode eq '0'} selected="selected"{/if}>Off</option>
        <option value="1"{if $config.debug_mode eq '1'} selected="selected"{/if}>PHP Warnings/SQL Errors</option>
        <option value="2"{if $config.debug_mode eq '2'} selected="selected"{/if}>Verbose debugging</option>
      </select></td>
    </tr>
    <tr>
      <td align="right">Show script execution time and number of performed SQL queries</td>
      <td align="left"><input name="show_exec_time" type="checkbox" id="show_exec_time" tabindex="2"{if $config.show_exec_time} checked="checked" {/if}/></td>
    </tr>
    <tr>
      <td align="right">Show links to XHTML and CSS validators</td>
      <td align="left"><input name="show_validators" type="checkbox" id="show_validators" tabindex="3"{if $config.show_validators} checked="checked" {/if}/></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><hr /></td>
    </tr>
    <tr>
      <td colspan="2" align="left"><p><em>You should leave the following settings as they are, unless you specified different tables in your Denora configuration<br />
      <strong>Don't change these values unless you know what you are doing!</strong></em></p>
      </td>
    </tr>
    <tr>
      <td align="right">Name of the Users Table</td>
      <td align="left"><input name="table_user" type="text" id="table_user" value="{$config.table_user}" size="32" maxlength="1024" tabindex="4" /></td>
    </tr>
    <tr>
      <td align="right">Name of the Channel Table</td>
      <td align="left"><input name="table_chan" type="text" id="table_chan" value="{$config.table_chan}" size="32" maxlength="1024" tabindex="5" /></td>
    </tr>
    <tr>
      <td align="right">Name of the Bans Table</td>
      <td align="left"><input name="table_chanbans" type="text" id="table_chanbans" value="{$config.table_chanbans}" size="32" maxlength="1024" tabindex="6" /></td>
    </tr>
    <tr>
      <td align="right">Name of the Ban Exceptions Table</td>
      <td align="left"><input name="table_chanexcepts" type="text" id="table_chanexcepts" value="{$config.table_chanexcepts}" size="32" maxlength="1024" tabindex="7" /></td>
    </tr>
    <tr>
      <td align="right">Name of the Invite Exceptions Table</td>
      <td align="left"><input name="table_chaninvites" type="text" id="table_chaninvites" value="{$config.table_chaninvites}" size="32" maxlength="1024" tabindex="8" /></td>
    </tr>
    <tr>
      <td align="right">Name of the Glines Table</td>
      <td align="left"><input name="table_glines" type="text" id="table_glines" value="{$config.table_glines}" size="32" maxlength="1024" tabindex="9" /></td>
    </tr>
    <tr>
      <td align="right">Name of the SQlines Table</td>
      <td align="left"><input name="table_sqlines" type="text" id="table_sqlines" value="{$config.table_sqlines}" size="32" maxlength="1024" tabindex="10" /></td>
    </tr>
    <tr>
      <td align="right"><p>Name of the Max Values Table</p>      </td>
      <td align="left"><input name="table_maxvalues" type="text" id="table_maxvalues" value="{$config.table_maxvalues}" size="32" maxlength="1024" tabindex="11" /></td>
    </tr>
    <tr>
      <td align="right">Name of the Servers Table</td>
      <td align="left"><input name="table_server" type="text" id="table_server" value="{$config.table_server}" size="32" maxlength="1024" tabindex="12" /></td>
    </tr>
    <tr>
      <td align="right">Name of the 'Is On' Table</td>
      <td align="left"><input name="table_ison" type="text" id="table_ison" value="{$config.table_ison}" size="32" maxlength="1024" tabindex="13" /></td>
    </tr>
    <tr>
      <td align="right">Name of the Country Table</td>
      <td align="left"><input name="table_tld" type="text" id="table_tld" value="{$config.table_tld}" size="32" maxlength="1024" tabindex="14" /></td>
    </tr>
    <tr>
      <td align="right">Name of the Chan Stats Table</td>
      <td align="left"><input name="table_cstats" type="text" id="table_cstats" value="{$config.table_cstats}" size="32" maxlength="1024" tabindex="15" /></td>
    </tr>
    <tr>
      <td align="right">Name of the User Stats Table</td>
      <td align="left"><input name="table_ustats" type="text" id="table_ustats" value="{$config.table_ustats}" size="32" maxlength="1024" tabindex="16" /></td>
    </tr>
    <tr>
      <td align="right">Name of the Current Values Table</td>
      <td align="left"><input name="table_current" type="text" id="table_current" value="{$config.table_current}" size="32" maxlength="1024" tabindex="17" /></td>
    </tr>
    <tr>
      <td align="right">Name of the Hourly Server Stats Table</td>
      <td align="left"><input name="table_serverstats" type="text" id="table_serverstats" value="{$config.table_serverstats}" size="32" maxlength="1024" tabindex="18" /></td>
    </tr>
    <tr>
      <td align="right">Name of the Hourly Channel Stats Table</td>
      <td align="left"><input name="table_channelstats" type="text" id="table_channelstats" value="{$config.table_channelstats}" size="32" maxlength="1024" tabindex="19" /></td>
    </tr>
    <tr>
      <td align="right">Name of the Hourly User StatsTable</td>
      <td align="left"><input name="table_userstats" type="text" id="table_userstats" value="{$config.table_userstats}" size="32" maxlength="1024" tabindex="20" /></td>
    </tr>
    <tr>
      <td align="right">Name of the Aliases Table</td>
      <td align="left"><input name="table_aliases" type="text" id="table_aliases" value="{$config.table_aliases}" size="32" maxlength="1024" tabindex="21" /></td>
    </tr>
  </table>
  <p align="right">
    <input type="submit" name="button" id="button" value="Save" tabindex="22" />
    </p>
</form>

{include file="_footer.tpl"}