<?php
// $Id: menu.php 310 2007-07-28 15:07:16Z Hal9000 $

/** ensure this file is being included by a parent file */
defined('_VALID_PARENT') or header("Location: ../");

?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom:15px;">
  <tr>
    <td width="50%" align="center"><a href="?page=home"><img src="img/home.png" alt="" width="22" height="22" /><br />
Home</a></td>
    <td width="50%" align="center"><a href="http://<?php echo $phpdenora_url; ?>"><img src="img/stats.png" alt="" width="22" height="22" /><br />
    View Stats</a></td>
  </tr>
</table>
<div class="menu_title"><img src="img/config.png" alt="" width="16" height="16" /> Configuration</div>
<ul>
  <li><a href="?page=general">General</a></li>
  <li><a href="?page=network">Network</a></li>
  <li><a href="?page=behavior">Behavior</a></li>
  <li><a href="?page=features">Features</a></li>
  <li><a href="?page=integration">Integration</a></li>
  <li><a href="?page=performance">Performance</a></li>
  <li><a href="?page=database">Database</a></li>
  <li><a href="?page=advanced">Advanced</a></li>
</ul>
<div class="menu_title"><img src="img/help.png" alt="" width="16" height="16" /> Support</div>
<ul>
  <li><a href="?page=registration">Registration</a></li>
  <li><a href="http://denorastats.org/">Homepage</a></li>
  <li><a href="http://denorastats.org/mantis/">Bug Tracker</a></li>
  <li><a href="http://denorastats.org/forum/">Support Forum</a></li>
  <li><a href="irc://irc.denorastats.org:6667/denora">IRC Channel</a></li>  
</ul>
<div class="menu_title"><img src="img/documentation.png" alt="" width="16" height="16" /> Documentation</div>
<ul>
  <li><a href="?page=docs&amp;f=README">Read Me</a></li>
  <li><a href="?page=docs&amp;f=FAQ">FAQ</a></li>
  <li><a href="?page=docs&amp;f=REMOTE">Remote API</a></li>
  <li><a href="?page=docs&amp;f=THEMES">Themes</a></li>
  <li><a href="?page=docs&amp;f=TRANSLATIONS">Languages</a></li>  
</ul>