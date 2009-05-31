<?php
// $Id: docs.php 310 2007-07-28 15:07:16Z Hal9000 $

/** ensure this file is being included by a parent file */
defined('_VALID_PARENT') or header("Location: ../");
?>
<div class="page_title">Documentation</div>
<pre>
<?php
	$file = isset($_GET['f']) ? stripslashes($_GET['f']) : 'README';
	$path = ($file == 'README') ? '../README' : '../docs/' . basename($file);
	if (is_file($path)) {
		$text = file_get_contents($path);
		echo htmlspecialchars($text);
	} else {
		echo "<p>ERROR: Specified documentation file not found</p>";
	}
?>
</pre>