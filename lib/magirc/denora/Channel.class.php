<?php
// $Id$

class Channel extends Denora {

    function __construct() {
        #parent::__construct();
        $this->db = new Denora_DB();
    }

    // return an array of all channels
    function getChannels() {
        $data = array();
        $i = 0;
        $chans = $this->db->selectAll('chan', NULL, 'channel', 'ASC');
        foreach ($chans as $chan) {
            $data[$i]['id'] = $chan['chanid'];
            $data[$i]['name'] = $chan['channel'];
            $data[$i]['users'] = $chan['currentusers'];
            $data[$i]['users_max'] = $chan['maxusers'];
            $data[$i]['users_max_time'] = $chan['maxusertime'];
            $data[$i]['topic'] = $chan['topic'];
            $data[$i]['topic_author'] = $chan['topicauthor'];
            $data[$i]['topic_time'] = strtotime($chan['topictime']);
            $data[$i]['kicks'] = $chan['kickcount'];
            $data[$i]['modes'] = $this->getModes($chan);
            $i++;
        }
        return $data;
    }

    function getChannel($name) {
        $chan = $this->db->selectOne('chan', array('channel' => $name));
        if ($chan) {
            $this->id = $chan['chanid'];
            $this->name = $chan['channel'];
            $this->users = $chan['currentusers'];
            $this->users_max = $chan['maxusers'];
            $this->users_max_time = $chan['maxusertime'];
            $this->topic = $chan['topic'];
            $this->topic_author = $chan['topicauthor'];
            $this->topic_time = strtotime($chan['topictime']);
            $this->kicks = $chan['kickcount'];
            $this->modes = $this->getModes($chan);
        }
        return $this;
    }

    private function getModes($chan) {
        $modes = "";
        $j = 97;
        while ($j <= 122) {
            if (@$chan['mode_l'.chr($j)] == "Y") {
                $modes .= chr($j);
            }
            if (@$chan['mode_u'.chr($j)] == "Y") {
                $modes .= chr($j-32);
            }
            $j++;
        }
        if (@$chan['mode_lf_data'] != NULL) {
            $modes .= " ".$chan['mode_lf_data'];
        }
        if (@$chan['mode_lj_data'] != NULL) {
            $modes .= " ".$chan['mode_lj_data'];
        }
        if (@$chan['mode_ll_data'] > 0) {
            $modes .= " ".$chan['mode_ll_data'];
        }
        if (@$chan['mode_uf_data'] != NULL) {
            $modes .= " ".$chan['mode_uf_data'];
        }
        if (@$chan['mode_uj_data'] > 0) {
            $modes .= " ".$chan['mode_uj_data'];
        }
        if (@$chan['mode_ul_data'] != NULL) {
            $modes .= " ".$chan['mode_ul_data'];
        }
        return $modes;
    }

    /* Checks if given channel can be displayed
	 * 0 = not existing, 1 = denied, 2 = ok */
    function checkChannel($chan) {
        $noshow = array();
        $no = explode(",", Config::getParam('hide_chans'));
        for ($i = 0; $i < count($no); $i++) {
            $noshow[$i] = strtolower($no[$i]);
        }
        if (in_array(strtolower($chan),$noshow))
            return 1;

        $stmt = $this->db->prepare("SELECT * FROM `chan` WHERE BINARY LOWER(`channel`) = LOWER(:channel)");
        $stmt->bindParam(':channel', $chan, SQL_STR);
        $stmt->execute();
        $data = $stmt->fetch();

        if (!$data) {
            return 0;
        } else {
            if (@$data['mode_li'] == "Y" || @$data['mode_lk'] == "Y" || @$data['mode_uo'] == "Y") {
                return 1;
            } else {
                return 2;
            }
        }
    }

    function getUsers($chan) {
        if ($this->checkChannel($chan) < 2) {
            return 0;
        }

        $array = array();
        $i = 0;
        $query = "SELECT ";
        /*if ($this->ircd->helper_mode) {
            $query .= sprintf("`user`.`%s` AS 'helper', ", $this->ircd->helper_mode);
        }*/
        $query .= "`user`.*, `ison`.*,`server`.`uline`
		FROM `ison`,`chan`,`user`,`server`
		WHERE LOWER(`chan`.`channel`) = LOWER(:channel)
			AND `ison`.`chanid` =`chan`.`chanid`
			AND `ison`.`nickid` =`user`.`nickid`
			AND `user`.`server` = `server`.`server`
		ORDER BY `user`.`nick` ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':channel', $chan, SQL_STR);
        $stmt->execute();

        while ($data = $stmt->fetch()) {
            if (isset($data['nick'])) {
                $mode = NULL;
                if (@$data['mode_lq'] == 'Y') {
                    $mode .= "q";
                }
                if (@$data['mode_la'] == 'Y') {
                    $mode .= "a";
                }
                if ($data['mode_lo'] == 'Y') {
                    $mode .= "o";
                }
                if (@$data['mode_lh'] == 'Y') {
                    $mode .= "h";
                }
                if ($data['mode_lv'] == 'Y') {
                    $mode .= "v";
                }
                $array[$i]['nick'] = $data['nick'];
                $array[$i]['modes'] = ($mode ? "+".$mode : "");
                $array[$i]['host'] = ((!empty($data['hiddenhostname']) && $data['hiddenhostname'] != "(null)") ? $data['hiddenhostname'] : $data['hostname']);
                $array[$i]['username'] = $data['username'];
                $array[$i]['countrycode'] = $data['countrycode'];
                $array[$i]['country'] = $data['country'];
                $array[$i]['bot'] = /*$this->ircd->bot_mode ? $data[$this->ircd->bot_mode] :*/ 'N';
                $array[$i]['away'] = $data['away'];
                $array[$i]['online'] = $data['online'];
                $array[$i]['uline'] = $data['uline'];
                $array[$i]['helper'] = /*$this->ircd->helper_mode ? $this->ircd->helper_mode :*/ 'N';
                $i++;
            }
        }

        return $array;
    }

}

?>