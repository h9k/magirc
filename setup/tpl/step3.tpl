{extends file="_main.tpl"}

{block name="content"}
<h2>3. Admin check</h2>

<pre>Checking for MagIRC Admin users... {if $admins}<span style="color:green;">Found</span>{else}<span style="color:red">Not found</span>{/if}</pre>

{if !$admins}
	{if $error}<pre>Creating Admin user... <span style="color:red;">Failed</span></pre>{/if}
	<p>You must now create a MagIRC admin user</p>
	<form id="login" method="post" action="?step=3">
		<table class="form">
			<tr>
				<th><label for="username">User</label></th>
				<td><input type="text" name="username" id="username" /></td>
			</tr>
			<tr>
				<th><label for="password">Password</label></th>
				<td><input type="password" name="password" id="password" /></td>
			</tr>
		</table>
		<button type="submit" name="login" >Create</button>
	</form>
{else}
	<button class="next">Continue</button>
{/if}

{/block}