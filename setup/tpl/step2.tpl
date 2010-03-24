{* $Id$ *}
{include file="_header.tpl"}

<pre>Checking MagIRC database schema...
{if !$check}
	Creating...
	{if !$result}
		<span style="color:green;">Done</span></pre>
	{else}
		<span style="color:red;">Failed</span></pre>
	{/if}
{else}
	<span style="color:green;">OK</span> (version {$version})</pre>
{/if}
{if $check || !$result}
	<p>You must now create a MagIRC admin user</p>
	<form id="login" method="post" action="?step=3">
	  <table width="350" border="0" cellpadding="2" cellspacing="0">
	    <tr>
	      <td width="100"><label>User</label></td>
	      <td width="150"><input type="text" name="username" id="username" tabindex="1" /></td>
	      <td width="50" rowspan="2" align="center" valign="middle"><input type="submit" name="login" id="login" value="Create" tabindex="3" /></td>
	    </tr>
	    <tr>
	      <td width="100"><label>Password</label></td>
	      <td width="150"><input type="password" name="password" id="password" tabindex="2" /></td>
	    </tr>
	  </table>
	</form>	
{/if}

{include file="_footer.tpl"}