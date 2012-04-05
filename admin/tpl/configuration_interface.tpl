<form id="interface-form" method="post" action="index.php/configuration">
	<h1>Interface settings</h1>
	<table border="0" cellspacing="0" cellpadding="5">
		<tr>
			<td align="right"><strong>Default Theme</strong></td>
			<td align="left"><em>Default</em></td>
		</tr>
		<tr>
			<td align="right"><strong>Default Language</strong><br />(will not override automatic detection by browser)</td>
			<td align="left"><em>English</em></td>
		</tr>
		<tr>
			<td align="right">Debug mode</td>
			<td align="left">
				<select name="debug_mode" id="debug_mode">
					<option value="0"{if $cfg.debug_mode eq '0'} selected="selected"{/if}>Off</option>
					<option value="1"{if $cfg.debug_mode eq '1'} selected="selected"{/if}>PHP Warnings/SQL Errors</option>
					<option value="2"{if $cfg.debug_mode eq '2'} selected="selected"{/if}>Verbose debugging</option>
				</select>
			</td>
		</tr>
	</table>
	<br /><button id="interface-submit" type="button">Save</button>
</form>

{jsmin}
<script type="text/javascript"><!--{literal}
$(function() {
	$("#interface-submit").button().click(function() {
		$("#interface-form").ajaxSubmit({ url: 'index.php/configuration', type: 'post', success: function(data) {
			if (data) $("#success").show().delay(1500).fadeOut(500);
			else $("#failure").show().delay(1500).fadeOut(500);
		} });
	});
});
{/literal}
--></script>
{/jsmin}