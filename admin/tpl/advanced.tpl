
{extends file="components/main.tpl"}
{block name="content"}

<h2>Advanced Settings</h2>
<form id="advanced" method="post" action="">

<div id="toolbar">
    <input type="hidden" name="form" value="advanced" />
    <ul>
        <li><a href="#" onclick="javascript:document.forms['advanced'].submit();return false"><img src="img/save.png" alt="" /> Save</a></li>
    </ul>
</div>

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
  </table>
</form>

{/block}