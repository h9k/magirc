<h1>Welcome message</h1>
<p>You can welcome your users, describe your network, and put whatever information you want in there.</p>

<form id="welcome-form" method="post" action="index.php/denora/welcome">
{$editor}
<br /><button id="welcome-submit" type="button">Save</button>
</form>

{jsmin}
<script type="text/javascript"><!--{literal}
$(function() {
	$("#welcome-submit").button().click(function() {
		$("#welcome-form").ajaxSubmit({ url: 'index.php/denora/settings', type: 'post', success: function(data) {
			if (data) $("#success").show().delay(1500).fadeOut(500);
			else $("#failure").show().delay(1500).fadeOut(500);
		} });
	});
});
{/literal}
--></script>
{/jsmin}