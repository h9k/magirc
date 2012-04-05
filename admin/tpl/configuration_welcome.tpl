<form id="welcome-form">
	<h1>Welcome message</h1>
	<p>You can welcome your users, describe your network, and put whatever information you want in here.
	<br />The message will be displayed on the MagIRC front page.</p>
	{$editor}
	<br /><button id="welcome-submit" type="button">Save</button>
</form>

{jsmin}
<script type="text/javascript"><!--{literal}
$(function() {
	$("#welcome-submit").button().click(function() {
		$("#welcome-form").ajaxSubmit({
			url: 'index.php/configuration',
			type: 'post',
			beforeSerialize:function(){
				for (instance in CKEDITOR.instances ) {
					CKEDITOR.instances[instance].updateElement();
				}
			},
			success: function(data) {
				if (data) $("#success").show().delay(1500).fadeOut(500);
				else $("#failure").show().delay(1500).fadeOut(500);
			}
		});
	});
});
{/literal}
--></script>
{/jsmin}