<?php
// $Id$

// Little dirty hack
if (!isset($_GET['action'])) {
	if (!isset($_GET['channel'])) {
		$_GET['action'] = 'list';
	} else {
		$_GET['action'] = 'details';
	}
}

require_once('lib/magirc/denora/Channel.class.php');
$c = new Channel();

$this->tpl->assign('mirc', $this->cfg->getParam('mirc_url'));
$this->tpl->assign('webchat', $this->cfg->getParam('webchat_url'));

switch($_GET['action']) {
	case 'list':
		$this->tpl->assign('chanlist', $c->getChannels());
		$this->tpl->display('channel_list.tpl');
		break;
	case 'details':
		$this->tpl->assign('channel', $c->getChannel(urldecode($_GET['channel'])));
		$this->tpl->display('channel_details.tpl');
		break;
	case 'users':
		/*$whoinlist = denora_who_in_channel($chan);
		 $qsum = sizeof($whoinlist);
		 $qlimit = ( $subpage - 1 ) * $pd_rlimit;
		 $rchan2 = (($subpage * $pd_rlimit) < $qsum) ? ($subpage * $pd_rlimit) : $qsum;

		 for ($x = $qlimit; $x < $rchan2; $x++)  {
			if ($whoinlist[$x]['bot'] == "Y") {
			$user_status = "<img src=\"themes/$theme/img/".$tpl_status_bot[0]."\" alt=\""._OL_BOT."\" title=\""._OL_BOT."\" />";
			}
			elseif ($whoinlist[$x]['uline'] == "1") {
			$user_status = "<img src=\"themes/$theme/img/".$tpl_status_service[0]."\" alt=\""._OL_SERVICE."\" title=\""._OL_SERVICE."\" />";
			}
			elseif ($whoinlist[$x]['away'] == "Y") {
			$user_status = "<img src=\"themes/$theme/img/".$tpl_status_away[0]."\" alt=\""._OL_AWAY."\" title=\""._OL_AWAY."\" />";
			}
			elseif ($whoinlist[$x]['helper'] == "Y") {
			$user_status = "<img src=\"themes/$theme/img/".$tpl_status_helper[0]."\" alt=\""._OL_AVAILABLE."\" title=\""._OL_AVAILABLE."\" />";
			}
			elseif ($whoinlist[$x]['online'] == "Y") {
			$user_status = "<img src=\"themes/$theme/img/".$tpl_status_online[0]."\" alt=\"Online\" title=\"Online\" />";
			}
			if ($whoinlist[$x]['online'] == "N") {
			$user_status = "<img src=\"themes/$theme/img/".$tpl_status_offline[0]."\" alt=\"Offline\" title=\"Offline\" />";
			}
			$flagfile = "libs/phpdenora/flags/".strtolower($whoinlist[$x]['countrycode']).".png";
			$countryflag = file_exists($flagfile) ? "<img src=\"".$flagfile."\" alt=\"".$whoinlist[$x]['country']."\" title=\"".$whoinlist[$x]['country']."\" />" : "&nbsp;";
			echo sprintf("<tr class=\"bg\"><td class=\"b l t n\" align=\"center\" valign=\"middle\"><strong>%s</strong></td><td class=\"t b\" align=\"center\" valign=\"middle\">%s</td><td class=\"t b\" align=\"center\" valign=\"middle\">%s</td><td class=\"b t\"><a href=\"?m=c&amp;p=ustats&amp;type=0&amp;chan=%s&amp;nick=%s\">%s</a></td><td class=\"t b\">%s&nbsp;</td><td class=\"t r b\">%s</td></tr><tr><td colspan=\"4\"></td></tr>",$x + 1,$user_status,$countryflag,urlencode(html_entity_decode($chan)),$whoinlist[$x]['nick'],$whoinlist[$x]['nick'],$whoinlist[$x]['modes'],$whoinlist[$x]['username']."@".$whoinlist[$x]['host']);
			}*/
		$this->tpl->assign('users', $c->getUsers($_GET['channel']));
		$this->tpl->display('channel_users.tpl');
		break;
	case 'countries':
		$this->tpl->display('channel_countries.tpl');
		break;
	case 'clients':
		$this->tpl->display('channel_clients.tpl');
		break;
	case 'activity':
		$this->tpl->display('channel_activity.tpl');
		break;
	default:
		$this->displayError("Unknown action");
}



?>