<?php
// $Id$

class Server extends Denora {

    function __construct($name) {
        #parent::__construct();
        $this->db = new Denora_DB();
        $server = $this->getServer($name);
        if ($server) {
            $this->id = $server['servid'];
            $this->name = $server['server'];
            $this->hops = $server['hops'];
            $this->description = $server['comment'];
            $this->linked_to_id = $server['linkedto'];
            $this->connecttime = strtotime($server['connecttime']);
            $this->online = $server['online'] == 'Y' ? true : false;
            $this->lastsplit = strtotime($server['lastsplit']);
            $this->version = $server['version'];
            $this->uptime = $server['uptime'];
            $this->motd = $server['motd'];
            $this->currentusers = $server['currentusers'];
            $this->maxusers = $server['maxusers'];
            $this->maxusertime = $server['maxusertime'];
            $this->ping = $server['ping'];
            $this->pingtime = $server['lastpingtime'];
            $this->maxping = $server['highestping'];
            $this->maxpingtime = $server['maxpingtime'];
            $this->uline = $server['uline'] ? true : false;
            $this->operkills = $server['ircopskills'];
            $this->serverkills = $server['serverkills'];
            $this->opers = $server['opers'];
            $this->maxopers = $server['maxopers'];
            $this->maxopertime = $server['maxopertime'];
        }
    }

    private function getServer($name) {
        return $this->db->selectOne('server', array('server' => $name));
    }
    
    private function getStatsDay($date) {
    	$ts = strtotime($date);
    	$query = "SELECT time_0,time_1,time_2,time_3,time_4,time_5,
    		time_6,time_7,time_8,time_9,time_10,time_11,
    		time_12,time_13,time_14,time_15,time_16,time_17,
    		time_18,time_19,time_20,time_21,time_22,time_23
    		FROM serverstats WHERE year = :year AND month = :month AND day = :day";
    	$stmt = $this->db->prepare($query);
    	$stmt->bindParam('year', date('Y', $ts), PDO::PARAM_INT);
    	$stmt->bindParam('month', date('m', $ts), PDO::PARAM_INT);
    	$stmt->bindParam('day', date('d', $ts), PDO::PARAM_INT);
    	$stmt->execute();
    	return array_map('intval', $stmt->fetch(PDO::FETCH_NUM));
    }

    /*function getServers() {
            return $this->db->selectAll('server', NULL, 'server', 'ASC');
    }*/
    
	function jsonList() {
    	$query = "SELECT online, server, comment, currentusers, opers FROM server";
    	$stmt = $this->db->prepare($query);
    	$stmt->execute();
    	return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    function jsonStats() {
    	$data = $this->getStatsDay('2010-05-29');
    	
    	require_once('lib/ofc/OFC_Chart.php');
    	
		$title = new OFC_Elements_Title( 'Servers today (hourly)' );
		$y = new OFC_Elements_Axis_Y();
		
		$line_dot = new OFC_Charts_Line();
		$line_dot->set_values( $data );
		$line_dot->set_colour('#d3d6ff');
		
		$chart = new OFC_Chart();
		$chart->set_title( $title );
		$chart->add_element( $line_dot );
		$chart->set_bg_colour('#FFFFFF');
		
		return $chart->toPrettyString();
    }

}

?>