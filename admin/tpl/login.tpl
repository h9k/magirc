{extends "_main.tpl"}

{block name="title" append}Login{/block}

{block name="body"}

<div id="dialog-login" title="Welcome to MagIRC">
	<h1 id="title">Login</h1>
	<span id="message">Please insert your credentials</span><br /><br />
	<noscript>WARNING: Your web browser does not support JavaScript. However this is needed in order to use this application!</noscript>
	<form id="magirc-login" method="post" action="index.php/login">
		<table class="form">
			<tr>
				<th><label for="username">Username</label></th>
				<td><input type="text" name="username" id="username" value="{$smarty.post.username}" size="24" /></td>
			</tr>
			<tr>
				<th><label for="password">Password</label></th>
				<td><input type="password" name="password" id="password" size="24" /></td>
			</tr>
		</table>
	</form>
</div>

{/block}

{block name="js" append}
{jsmin}
<script type="text/javascript">
{literal}
$("#dialog-login").dialog({
	autoOpen: true,
	closeOnEscape: false,
	height: 260,
	width: 350,
	resizable: false,
	modal: true,
	buttons: {
		"Login": function() {
			login();
		}
	}
});
$(document).keyup(function(e){
    if(e.keyCode === 13) login();
});
function login() {
	$("#magirc-login").ajaxSubmit({ url: 'index.php/ajaxlogin', type: 'post', success: function(data) {
		if (data) {
			window.location = 'index.php';
		} else {
			$("#title").html('Login failed');
			$("#message").html('Please check your credentials and try again');
		}
	} });
}

{/literal}
</script>
{/jsmin}
{/block}