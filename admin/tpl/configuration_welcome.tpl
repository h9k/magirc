<h1>Welcome message</h1>
<p>You can welcome your users, describe your network, and put whatever information you want in here.</p>
<form id="welcome-form">
	<input type="radio" name="welcome_mode" value="statuspage"{if $cfg->welcome_mode eq 'statuspage'} checked="checked"{/if} /> Show the welcome message on the live network status page<br />
	<input type="radio" name="welcome_mode" value="ownpage"{if $cfg->welcome_mode eq 'ownpage'} checked="checked"{/if} /> Show the welcome message as a separate tab in the network section<br />
	<input type="radio" name="welcome_mode" value="disabled"{if $cfg->welcome_mode eq 'disabled'} checked="checked"{/if} /> Disable the welcome message<br />
</form>
<br />
<form id="content-form">{$editor}</form>
<br /><button id="welcome-submit" type="button">Save</button>

{jsmin}
<script type="text/javascript">{literal}
$(document).ready(function() {
	$("#welcome-submit").button().click(function() {
		var success = true;
		$("#welcome-form").ajaxSubmit({
			url: 'index.php/configuration',
			type: 'post',
			success: function(data) {
				if (!data) success = false; 
			}
		});
		$("#content-form").ajaxSubmit({
			url: 'index.php/content',
			type: 'post',
			beforeSerialize:function(){
				for (instance in CKEDITOR.instances ) {
					CKEDITOR.instances[instance].updateElement();
				}
			},
			success: function(data) {
				if (!data) success = false;
			}
		});
		if (success) $("#success").show().delay(1500).fadeOut(500);
		else $("#failure").show().delay(1500).fadeOut(500);			
	});
});
{/literal}
</script>
{/jsmin}