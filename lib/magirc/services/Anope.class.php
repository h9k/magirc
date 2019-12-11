<?php

class AnopeDB extends DB {
    private static $instance = null;

    public static function getInstance() {
        if (is_null(self::$instance) === true) {
            $db = null;
            $error = false;
            $config_file = PATH_ROOT . 'conf/anope.cfg.php';
            if (file_exists($config_file)) {
                include($config_file);
            } else {
                $error = true;
            }
            if ($error || !is_array($db)) {
                die('<strong>MagIRC</strong> is not properly configured<br />Please configure the Anope database in the <a href="admin/">Admin Panel</a>');
            }
            $dsn = "mysql:dbname={$db['database']};host={$db['hostname']}";
            $args = array();
            if (isset($db['ssl']) && $db['ssl_key']) {
                $args[PDO::MYSQL_ATTR_SSL_KEY] = $db['ssl_key'];
            }
            if (isset($db['ssl']) && $db['ssl_cert']) {
                $args[PDO::MYSQL_ATTR_SSL_CERT] = $db['ssl_cert'];
            }
            if (isset($db['ssl']) && $db['ssl_ca']) {
                $args[PDO::MYSQL_ATTR_SSL_CA] = $db['ssl_ca'];
            }
            self::$instance = new DB($dsn, $db['username'], $db['password'], $args);
            $prefix = isset($db['prefix']) ? $db['prefix'] : null;
            self::setTableNames($prefix);
            if (self::$instance->error) {
                die('Error opening the Anope database<br />' . self::$instance->error);
            }
        }
        return self::$instance;
    }

    private static function setTableNames($prefix) {
        define('TBL_CHAN', $prefix.'chan');
        define('TBL_CHANSTATS', $prefix.'chanstats');
        define('TBL_ISON', $prefix.'ison');
        define('TBL_MAXUSERS', $prefix.'maxusers');
        define('TBL_SERVER', $prefix.'server');
        define('TBL_USER', $prefix.'user');
        define('TBL_CURRENTUSAGE', $prefix.'currentusage');
        define('TBL_MAXUSAGE', $prefix.'maxusage');
        define('TBL_HISTORY', $prefix.'history');
    }
}

class Anope implements Service {
    private $db;
    private $cfg;

    public function __construct() {
        $ircd_file = PATH_ROOT . "lib/magirc/ircds/" . IRCD . ".inc.php";
        if (file_exists($ircd_file)) {
            require_once($ircd_file);
        } else {
            die('<strong>MagIRC</strong> is not properly configured<br />Please configure the ircd in the <a href="admin/">Admin Panel</a>');
        }
        $this->db = AnopeDB::getInstance();
        $this->cfg = new Config();
        require_once(__DIR__.'/../objects/anope/Server.class.php');
        require_once(__DIR__.'/../objects/anope/Channel.class.php');
        require_once(__DIR__.'/../objects/anope/User.class.php');
    }

    /**
     * Returns the current status
     * @return array of arrays (int val, int time)
     */
    public function getCurrentStatus() {
        $query = sprintf("SELECT * FROM `%s`", TBL_CURRENTUSAGE);
        $this->db->query($query, SQL_INIT, SQL_ASSOC);
        $result = $this->db->record;

        $data = array(
            'users' => array('val' => (int) $result['users'], 'time' => $result['datetime']),
            'chans' => array('val' => (int) $result['channels'], 'time' => $result['datetime']),
            'servers' => array('val' => (int) $result['servers'], 'time' => $result['datetime']),
            'opers' => array('val' => (int) $result['operators'], 'time' => $result['datetime'])
        );
        return $data;
    }

    /**
     * Returns the max values
     * @return array of arrays (int val, int time)
     */
    public function getMaxValues() {
        $this->db->query(sprintf("SELECT * FROM `%s`", TBL_MAXUSAGE), SQL_ALL, SQL_ASSOC);
        $data = array();
        foreach ($this->db->record as $row) {
            if ($row['type'] == 'operators') {
                $row['type'] = 'opers';
            }
            $data[$row['type']] = array('val' => $row['count'], 'time' => $row['datetime']);
        }
        return $data;
    }

    /**
     * Get the global or channel-specific user count
     * @param string $mode Mode ('server', 'channel', null: global)
     * @param string $target Target (channel or server name, depends on $mode)
     * @return int User count
     */
    public function getUserCount($mode = null, $target = null) {
        $query = sprintf("SELECT COUNT(*) FROM `%s` AS u JOIN `%s` AS s ON s.id = u.servid", TBL_USER, TBL_SERVER);
        $where = null;
        if ($mode == 'channel' && $target) {
            $query .= sprintf(" JOIN `%s` AS i ON i.nickid = u.nickid
            JOIN `%s` AS c ON c.chanid = i.chanid", TBL_ISON, TBL_CHAN);
            $where .= "LOWER(c.channel) = LOWER(:chan)";
        } elseif ($mode == 'server' && $target) {
            $where .= "LOWER(s.name) = LOWER(:server)";
        }
        if ($this->cfg->hide_ulined) {
            $where .= ($where ? " AND " : "") . "s.ulined = 'N'";
        }
        if (Protocol::services_protection_mode) {
            $where .= sprintf(($where ? " AND " : "") . "u.modes NOT LIKE BINARY '%%%s%%'", Protocol::services_protection_mode);
        }
        if ($where){
            $query .= " WHERE {$where}";
        }
        $ps = $this->db->prepare($query);
        if ($mode == 'channel' && $target) {
            $ps->bindValue(':chan', $target, PDO::PARAM_STR);
        }
        if ($mode == 'server' && $target) {
            $ps->bindValue(':server', $target, PDO::PARAM_STR);
        }
        $ps->execute();
        return $ps->fetch(PDO::FETCH_COLUMN);
    }

    /**
     * Get CTCP client statistics
     * @param string $mode Mode ('server', 'channel', null: global)
     * @param string $target Target
     * @return array Data
     */
    public function getClientStats($mode = null, $target = null) {
        $query = sprintf("SELECT u.version AS client, COUNT(*) AS count
            FROM `%s` AS u
            JOIN `%s` AS s ON s.id = u.servid",
                TBL_USER, TBL_SERVER);
        if ($mode == 'channel' && $target) {
            $query .= sprintf(" JOIN `%s` AS i ON i.nickid = u.nickid
                JOIN `%s` AS c ON c.chanid = i.chanid
                WHERE LOWER(c.channel) = LOWER(:chan)",
                TBL_ISON, TBL_CHAN);
        } elseif ($mode == 'server' && $target) {
            $query .= " WHERE LOWER(u.server) = LOWER(:server)";
        } else {
            $query .= " WHERE 0 = 0";
        }
        if ($this->cfg->hide_ulined) {
            $query .= " AND s.ulined = 'N'";
        }
        if (Protocol::services_protection_mode) {
            $query .= sprintf(" AND u.modes NOT LIKE BINARY '%%%s%%'", Protocol::services_protection_mode);
        }
        $query .= " GROUP by u.version ORDER BY count DESC";
        $ps = $this->db->prepare($query);
        if ($mode == 'channel' && $target) $ps->bindValue(':chan', $target, PDO::PARAM_STR);
        if ($mode == 'server' && $target) $ps->bindValue(':server', $target, PDO::PARAM_STR);
        $ps->execute();
        return $ps->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get GeoIP country statistics
     * @param string $mode Mode ('server', 'channel', null: global)
     * @param string $target Target
     * @return array Data
     */
    public function getCountryStats($mode = null, $target = null) {
        $query = sprintf("SELECT u.geocountry AS country, u.geocode AS country_code, COUNT(*) AS count
            FROM `%s` AS u
            JOIN `%s` AS s ON s.id = u.servid",
                TBL_USER, TBL_SERVER);
        $where = null;
        if ($mode == 'channel' && $target) {
            $query .= sprintf(" JOIN `%s` AS i ON i.nickid = u.nickid
                JOIN `%s` AS c ON i.chanid = c.chanid",
                TBL_ISON, TBL_CHAN);
            $where .= " LOWER(c.channel) = LOWER(:chan)";
        } elseif ($mode == 'server' && $target) {
            $where .= " LOWER(u.server) = LOWER(:server)";
        }
        if ($this->cfg->hide_ulined) {
            $where .= ($where ? " AND " : "") . "s.ulined = 'N'";
        }
        if (Protocol::services_protection_mode) {
            $where .= sprintf(($where ? " AND " : "") . "u.modes NOT LIKE BINARY '%%%s%%'", Protocol::services_protection_mode);
        }
        if ($where){
            $query .= " WHERE {$where}";
        }
        $query .= " GROUP by u.geocode, u.geocountry ORDER BY count DESC";
        $ps = $this->db->prepare($query);
        if ($mode == 'channel' && $target) $ps->bindValue(':chan', $target, PDO::PARAM_STR);
        if ($mode == 'server' && $target) $ps->bindValue(':server', $target, PDO::PARAM_STR);
        $ps->execute();
        return $ps->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get GeoIP country statistics for Highmaps
     * @param string $mode Mode ('server', 'channel', null: global)
     * @param string $target Target
     * @return array Data
     * @todo finish implementation
     */
    public function getCountryMap($mode = null, $target = null){
        $result = array();
        $data = $this->getCountryStats($mode, $target);
        foreach ($data as $item){
            if ($item['country_code']){
                $result[] = array('code' => $item['country_code'], 'z' => (int) $item['count']);
            }
        }
        return $result;
    }

    /**
     * Prepare data for use by country pie charts
     * @param array $result Array of data
     * @param type $sum user count
     * @return array of arrays (string 'name', int 'count', double 'y')
     */
    public function makeCountryPieData($result, $sum) {
        $data = array();
        $unknown = 0;
        $other = 0;
        foreach ($result as $val) {
            $percent = round($val["count"] / $sum * 100, 2);
            if (in_array ($val['country'], array("Unknown", "localhost"))) {
                $unknown += $val["count"];
            } elseif (in_array ($val['country_code'], array(null, "", "??"))) {
                $unknown += $val["count"];
            } elseif ($percent < 2) {
                $other += $val["count"];
            } else {
                $data[] = array('name' => $val['country'] ? $val['country'] : $val['country_code'], 'count' => $val["count"], 'y' => $percent);
            }
        }
        if ($unknown > 0) {
            $data[] = array('name' => gettext('Unknown'), 'count' => $unknown, 'y' => round($unknown / $sum * 100, 2));
        }
        if ($other > 0) {
            $data[] = array('name' => gettext('Other'), 'count' => $other, 'y' => round($other / $sum * 100, 2));
        }
        return $data;
    }

    /**
     * Prepare data for use by client pie charts
     * @param array $result Array of data
     * @param type $sum user count
     * @return array (clients => (name, count, y), versions (name, version, cat, count, y))
     */
    public function makeClientPieData($result, $sum) {
        $clients = array();
        foreach ($result as $client) {
            // Determine client name and version
            $matches = array();
            preg_match('/^(.*?)\s*(\S*\d\S*)/', str_replace(array('(',')','[',']','{','}'), '', $client['client']), $matches);
            if (count($matches) == 3) {
                $name = $matches[1];
                $version = $matches[2][0] == 'v' ? substr($matches[2], 1) : $matches[2];
            } else {
                $name = $client['client'] ? $client['client'] : gettext('Unknown');
                $version = '';
            }
            $name = trim($name);
            $version = trim($version);
            // Categorize the versions
            if (!array_key_exists($name, $clients)) {
                $clients[$name] = array('count' => $client['count'], 'versions' => array());
            } else {
                $clients[$name]['count'] += $client['count'];
            }
            if (isset($clients[$name]['versions'][$version])) {
                $clients[$name]['versions'][$version] += $client['count'];
            } else {
                $clients[$name]['versions'][$version] = $client['count'];
            }
        }
        // Sort by count descending
        uasort($clients, function($a, $b) {
            return $a['count'] < $b['count'];
        });
        foreach ($clients as $key => $val) {
            arsort($clients[$key]['versions']);
            unset($val);
        }

        // Prepare data for output
        $min_count = ceil($sum / 300);
        $data = array('clients' => array(), 'versions' => array());
        $other = array('count' => 0, 'versions' => array());
        $other_various = 0;
        foreach ($clients as $name => $client) {
            $percent = round($client['count'] / $sum * 100, 2);
            if ($percent < 2 || $name == gettext('Unknown')) { // Too small or unknown
                $other['count'] += $client['count'];
                foreach ($client['versions'] as $version => $count) {
                    if ($count < $min_count) {
                        $other_various += $count;
                    } else {
                        $other['versions'][] = array('name' => $name, 'version' => $version, 'cat' => gettext('Other'), 'count' => (int) $count, 'y' => (double) round($count / $sum * 100, 2));
                    }
                }
            } else {
                $data_various = 0;
                $data['clients'][] = array('name' => $name, 'count' => (int) $client['count'], 'y' => (double) $percent);
                foreach ($client['versions'] as $version => $count) {
                    if ($count < $min_count) {
                        $data_various += $count;
                    } else {
                        $data['versions'][] = array('name' => $name, 'version' => $version, 'cat' => $name, 'count' => (int) $count, 'y' => (double) round($count / $sum * 100, 2));
                    }
                }
                if ($data_various) {
                    $data['versions'][] = array('name' => $name, 'version' => '('.gettext('various').')', 'cat' => $name, 'count' => (int) $data_various, 'y' => (double) round($data_various / $sum * 100, 2));
                }
            }
        }
        if ($other_various) {
            $other['versions'][] = array('name' => gettext('Various'), 'version' => '', 'cat' => gettext('Other'), 'count' => (int) $other_various, 'y' => (double) round($other_various / $sum * 100, 2));;
        }
        // Append other slices
        if ($other['count'] > 0) {
            $other['percent'] = round($other['count'] / $sum * 100, 2);
            $data['clients'][] = array('name' => gettext('Other'), 'count' => (int) $other['count'], 'y' => (double) $other['percent']);
            $data['versions'] = array_merge($data['versions'], $other['versions']);
        }
        return $data;
    }

    /**
     * Gets the user history data
     * @return array of arrays (int milliseconds, int value)
     */
    public function getUserHistory() {
        return $this->getHistory('users');
    }

    /**
     * Gets the channel history data
     * @return array of arrays (int milliseconds, int value)
     */
    public function getChannelHistory() {
        return $this->getHistory('channels');
    }

    /**
     * Gets the server history data
     * @return array of arrays (int milliseconds, int value)
     */
    public function getServerHistory() {
        return $this->getHistory('servers');
    }

    private function getHistory($value) {
        $query = sprintf("SELECT `datetime`, `%s` FROM `%s` ORDER BY `datetime` ASC", $value, TBL_HISTORY);
        $ps = $this->db->prepare($query);
        $ps->execute();
        $rows = $ps->fetchAll(PDO::FETCH_ASSOC);
        $data = array();
        foreach ($rows as $row) {
            $data[] = array(strtotime($row['datetime']) * 1000, (int) $row[$value]);
        }
        return $data;
    }

    /**
     * Gets a list of servers
     * @return array of Server
     */
    public function getServerList() {
        $where = null;
        $hide_servers = $this->cfg->hide_servers;
        if ($hide_servers) {
            $hide_servers = explode(",", $hide_servers);
            foreach ($hide_servers as $key => $server) {
                $hide_servers[$key] = $this->db->escape(trim($server));
            }
            $where .= sprintf(" name NOT IN(%s)", implode(",", $hide_servers));
        }
        if ($this->cfg->hide_ulined) {
            $where .= $where ? " AND ulined = 'N'" : " ulined = 'N'";
        }
        $query = sprintf("SELECT name AS server, online, comment AS description, currentusers AS users,
        (SELECT COUNT(*) FROM `%s` AS u WHERE u.oper = 'Y' AND u.servid = s.id) AS opers
        FROM `%s` AS s WHERE %s",
                TBL_USER, TBL_SERVER, $where);
        $ps = $this->db->prepare($query);
        $ps->execute();
        return $ps->fetchAll(PDO::FETCH_CLASS, 'Server');
    }

    /**
     * Gets a server
     * @param string $server Server name
     * @return Server
     */
    public function getServer($server) {
        $query = sprintf("SELECT s.name AS server, online, comment AS description, link_time AS connect_time,
            split_time, version, currentusers AS users, maxusers AS users_max, maxtime AS users_max_time,
            (SELECT COUNT(*) FROM `%s` AS u WHERE u.oper = 'Y' AND u.servid = s.id) AS opers
            FROM `%s` AS s
            LEFT JOIN `%s` AS m ON m.name = s.name
            WHERE s.name = :server", TBL_USER, TBL_SERVER, TBL_MAXUSERS);
        $ps = $this->db->prepare($query);
        $ps->bindValue(':server', $server, PDO::PARAM_STR);
        $ps->execute();
        return $ps->fetchObject('Server');
    }

    /**
     * Get the list of Operators currently online
     * @return array of User
     */
    public function getOperatorList() {
        $query = sprintf("SELECT u.nick AS nickname, u.realname, u.host AS hostname, u.chost AS hostname_cloaked, u.vhost AS vhost,
            u.ident AS username, u.signon AS connect_time, u.server, u.away, u.awaymsg AS away_msg, u.version AS client,
            u.geocode AS country_code, u.geocountry AS country, s.ulined AS service, u.modes AS umodes
            FROM `%s` AS u
            LEFT JOIN `%s` AS s ON s.id = u.servid
            WHERE",
                TBL_USER, TBL_SERVER);
        $levels = Protocol::$oper_levels;
        if (!empty($levels)) {
            $i = 1;
            $query .= " (";
            foreach ($levels as $mode => $level) {
                $query .= sprintf(" u.modes LIKE BINARY '%%%s%%'", $mode);
                if ($i < count($levels)) {
                    $query .= " OR ";
                }
                $i++;
            }
            $query .= ")";
        } else {
            $query .= " u.modes LIKE BINARY '%o%'";
        }
        if (Protocol::oper_hidden_mode) {
            $query .= sprintf(" AND u.modes NOT LIKE BINARY '%%%s%%'", Protocol::oper_hidden_mode);
        }
        if (Protocol::services_protection_mode) {
            $query .= sprintf(" AND u.modes NOT LIKE BINARY '%%%s%%'", Protocol::services_protection_mode);
        }
        $query .= " AND u.server = s.name";
        if ($this->cfg->hide_ulined) {
            $query .= " AND s.ulined = 'N'";
        }
        $query .= " ORDER BY u.nick ASC";
        $ps = $this->db->prepare($query);
        $ps->execute();
        return $ps->fetchAll(PDO::FETCH_CLASS, 'User');
    }

    /**
     * Gets the list of current channels
     * @param boolean $datatables Set true to enable server-side datatables functionality
     * @return array of Channel
     */
    public function getChannelList($datatables = false) {
        $secret_mode = Protocol::chan_secret_mode;
        $private_mode = Protocol::chan_private_mode;

        $where = "true"; // LOL
        if ($secret_mode) {
            $where .= sprintf(" AND modes NOT LIKE BINARY '%%%s%%'", $secret_mode);
        }
        if ($private_mode) {
            $where .= sprintf(" AND modes NOT LIKE BINARY '%%%s%%'", $private_mode);
        }
        $hide_channels = $this->cfg->hide_chans;
        if ($hide_channels) {
            $hide_channels = explode(",", $hide_channels);
            foreach ($hide_channels as $key => $channel) {
                $hide_channels[$key] = $this->db->escape(trim(strtolower($channel)));
            }
            $where .= sprintf("AND LOWER(channel) NOT IN(%s)", implode(",", $hide_channels));
        }

        $query = sprintf("SELECT SQL_CALC_FOUND_ROWS channel, (SELECT COUNT(*) FROM `%s` AS i WHERE c.chanid = i.chanid) AS users, topic, topicauthor AS topic_author,"
                . " topictime AS topic_time, modes, maxusers AS users_max, maxtime AS users_max_time"
                . " FROM `%s` AS c"
                . " LEFT JOIN `%s` AS m ON m.name = c.channel"
                . " WHERE %s",
                TBL_ISON, TBL_CHAN, TBL_MAXUSERS, $where);
        if ($datatables) {
            $total = $this->db->datatablesTotal($query);
            $filtering = $this->db->datatablesFiltering(array('channel', 'topic'));
            $ordering = $this->db->datatablesOrdering();
            $paging = $this->db->datatablesPaging();
            $query .= sprintf(" %s %s %s", $filtering ? "AND " . $filtering : "", $ordering, $paging);
        } else {
            $query .= " ORDER BY `channel` ASC";
        }
        $ps = $this->db->prepare($query);
        $ps->execute();
        $aaData = $ps->fetchAll(PDO::FETCH_CLASS, 'Channel');
        if ($datatables) {
            $filteredTotal = $this->db->foundRows();
            return $this->db->datatablesOutput($total, $filteredTotal, $aaData);
        }
        return $aaData;
    }

    /**
     * Get the biggest current channels
     * @param int $limit
     * @return array of Channel
     */
    public function getChannelBiggest($limit = 10) {
        $secret_mode = Protocol::chan_secret_mode;
        $query = sprintf("SELECT channel, (SELECT COUNT(*) FROM `%s` AS i WHERE c.chanid = i.chanid) AS users, topic, topicauthor AS topic_author,"
                . " topictime AS topic_time, modes, maxusers AS users_max, maxtime AS users_max_time"
                . " FROM `%s` AS c"
                . " LEFT JOIN `%s` AS m ON m.name = c.channel"
                . " WHERE 1 > 0",
                TBL_ISON, TBL_CHAN, TBL_MAXUSERS);
        if ($secret_mode) {
            $query .= sprintf(" AND modes NOT LIKE BINARY '%%%s%%'", $secret_mode);
        }
        $hide_chans = explode(",", $this->cfg->hide_chans);
        for ($i = 0; $i < count($hide_chans); $i++) {
            $query .= " AND LOWER(channel) NOT LIKE " . $this->db->escape(strtolower($hide_chans[$i]));
        }
        $query .= " ORDER BY users DESC LIMIT :limit";
        $ps = $this->db->prepare($query);
        $ps->bindValue(':limit', $limit, PDO::PARAM_INT);
        $ps->execute();
        return $ps->fetchAll(PDO::FETCH_CLASS, 'Channel');
    }

    /**
     * Get the most active current channels
     * @param int $limit
     * @return array of channel stats
     */
    public function getChannelTop($limit = 10) {
        $secret_mode = Protocol::chan_secret_mode;
        $private_mode = Protocol::chan_private_mode;
        $query = sprintf("SELECT chan AS channel, line AS 'lines'"
                . " FROM `%s` AS cs"
                . " JOIN `%s` AS c ON LOWER(cs.chan) = LOWER(c.channel)"
                . " WHERE cs.type = 'daily' AND cs.line >= 1 AND cs.nick = ''",
                TBL_CHANSTATS, TBL_CHAN);
        if ($secret_mode) {
            $query .= sprintf(" AND c.modes NOT LIKE BINARY '%%%s%%'", $secret_mode);
        }
        if ($private_mode) {
            $query .= sprintf(" AND c.modes NOT LIKE BINARY '%%%s%%'", $private_mode);
        }
        $hide_chans = explode(",", $this->cfg->hide_chans);
        for ($i = 0; $i < count($hide_chans); $i++) {
            $query .= " AND cs.chan NOT LIKE " . $this->db->escape(strtolower($hide_chans[$i]));
        }
        $query .= " ORDER BY cs.line DESC LIMIT :limit";
        $ps = $this->db->prepare($query);
        $ps->bindValue(':limit', $limit, PDO::PARAM_INT);
        $ps->execute();
        return $ps->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get the specified channel
     * @param string $chan Channel
     * @return Channel
     */
    public function getChannel($chan) {
        $query = sprintf("SELECT channel, (SELECT COUNT(*) FROM `%s` AS i WHERE c.chanid = i.chanid) AS users, topic, topicauthor AS topic_author,
            topictime AS topic_time, modes, maxusers AS users_max, maxtime AS users_max_time
            FROM `%s` AS c
            LEFT JOIN `%s` AS m ON m.name = c.channel
            WHERE LOWER(channel) = LOWER(:chan)",
                TBL_ISON, TBL_CHAN, TBL_MAXUSERS);
        $ps = $this->db->prepare($query);
        $ps->bindValue(':chan', $chan, PDO::PARAM_STR);
        $ps->execute();
        return $ps->fetchObject('Channel');
    }

    /**
     * Get the users currently in the specified channel
     * @todo implement server-side datatables support
     * @param string $chan Channel
     * @return array of User
     */
    public function getChannelUsers($chan) {
        if ($this->checkChannel($chan) != 200) {
            return null;
        }
        $query = sprintf("SELECT u.nick AS nickname, u.realname, u.host AS hostname, u.chost AS hostname_cloaked, u.vhost AS vhost,
            u.ident AS username, u.signon AS connect_time, u.server, u.away, u.awaymsg AS away_msg, u.version AS client,
            u.geocode AS country_code, u.geocountry AS country, s.ulined AS service, i.modes AS cmodes
            FROM `%s` AS i
            JOIN `%s` AS c ON c.chanid = i.chanid
            JOIN `%s` AS u ON u.nickid = i.nickid
            JOIN `%s` AS s ON s.id = u.servid
            WHERE LOWER(c.channel) = LOWER(:channel)
            ORDER BY u.nick ASC",
                TBL_ISON, TBL_CHAN, TBL_USER, TBL_SERVER);
        $ps = $this->db->prepare($query);
        $ps->bindValue(':channel', $chan, SQL_STR);
        $ps->execute();
        return $ps->fetchAll(PDO::FETCH_CLASS, 'User');
    }

    /**
     * Gets the global channel activity
     * @param int $type 0: total, 1: day, 2: week, 3: month, 4: year
     * @param boolean $datatables true: datatables format, false: standard format
     * @return array Data
     */
    public function getChannelGlobalActivity($type, $datatables = false) {
        $aaData = array();
        $secret_mode = Protocol::chan_secret_mode;
        $private_mode = Protocol::chan_private_mode;

        $where = "cs.letters > 0";
        if ($secret_mode) {
            $where .= sprintf(" AND c.modes NOT LIKE BINARY '%%%s%%'",$secret_mode);
        }
        if ($private_mode) {
            $where .= sprintf(" AND c.modes NOT LIKE BINARY '%%%s%%'",$private_mode);
        }
        $hide_channels = $this->cfg->hide_chans;
        if ($hide_channels) {
            $hide_channels = explode(",", $hide_channels);
            foreach ($hide_channels as $key => $channel) {
                $hide_channels[$key] = $this->db->escape(trim(strtolower($channel)));
            }
            $where .= sprintf(" AND LOWER(cs.chan) NOT IN(%s)", implode(',', $hide_channels));
        }

        $query = sprintf("SELECT SQL_CALC_FOUND_ROWS chan AS name, letters, words, line AS 'lines', actions,
            (smileys_happy + smileys_sad + smileys_other) AS smileys, kicks, cs.modes, topics
            FROM `%s`AS cs
            LEFT JOIN `%s` AS c ON LOWER(cs.chan) = LOWER(c.channel)
            WHERE cs.type = :type AND cs.nick = '' AND %s", TBL_CHANSTATS, TBL_CHAN, $where);
        if ($datatables) {
            $total = $this->db->datatablesTotal($query, array(':type' => $type));
            $filtering = $this->db->datatablesFiltering(array('cs.chan', 'c.topic'));
            $ordering = $this->db->datatablesOrdering();
            $paging = $this->db->datatablesPaging();
            $query .= sprintf("%s %s %s", $filtering ? " AND " . $filtering : "", $ordering, $paging);
        }
        $ps = $this->db->prepare($query);
        $ps->bindValue(':type', $type, PDO::PARAM_STR);
        $ps->execute();
        foreach ($ps->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if ($datatables) {
                $row["DT_RowId"] = $row['name'];
            }
            $aaData[] = $row;
        }
        if ($datatables) {
            $filteredTotal = $this->db->foundRows();
            return $this->db->datatablesOutput($total, $filteredTotal, $aaData);
        }
        return $aaData;
    }

    /**
     * Gets the channel activity for the given channel
     * @param string $chan Channel
     * @param int $type 0: total, 1: day, 2: week, 3: month, 4: year
     * @param boolean $datatables true: datatables format, false: standard format
     * @return User
     */
    public function getChannelActivity($chan, $type, $datatables = false) {
        $aaData = array();
        $query = sprintf("SELECT SQL_CALC_FOUND_ROWS nick AS uname, letters, words, line AS 'lines', actions,"
                . " (smileys_happy + smileys_sad + smileys_other) AS smileys, kicks, modes, topics"
                . " FROM `%s` AS cs"
                . " WHERE chan = :channel AND nick != '' AND type=:type AND letters > 0 ",
                TBL_CHANSTATS);
        if ($datatables) {
            $total = $this->db->datatablesTotal($query, array(':type' => $type, ':channel' => $chan));
            $filtering = $this->db->datatablesFiltering(array('nick'));
            $ordering = $this->db->datatablesOrdering();
            $paging = $this->db->datatablesPaging();
            $query .= sprintf("%s %s %s", $filtering ? " AND " . $filtering : "", $ordering, $paging);
        }
        $ps = $this->db->prepare($query);
        $ps->bindValue(':type', $type, PDO::PARAM_STR);
        $ps->bindValue(':channel', $chan, PDO::PARAM_STR);
        $ps->execute();
        $data = $ps->fetchAll(PDO::FETCH_ASSOC);
        if ($datatables) {
            $filteredTotal = $this->db->foundRows();
        }
        foreach ($data as $row) {
            if ($datatables) {
                $row["DT_RowId"] = $row['uname'];
            }
            // Get country code and online status
            $user = $this->getUser('stats', $row['uname']);
            if (!$user) {
                $user = new User();
                $user->nickname = $row['uname'];
                $user->country = 'Unknown';
                $user->country_code = '';
                $user->online = false;
                $user->away = false;
                $user->bot = false;
                $user->service = false;
                $user->operator = false;
                $user->helper = false;
            }
            foreach ($row as $key => $val) {
                $user->$key = $val;
            }
            $aaData[] = $user;
        }
        if ($datatables) {
            return $this->db->datatablesOutput($total, $filteredTotal, $aaData);
        }
        return $aaData;
    }

    /**
     * Get the hourly average activity for the given channel
     * @param string $chan Channel
     * @param string $type total, monthly, weekly, daily
     * @return mixed
     */
    public function getChannelHourlyActivity($chan, $type) {
        $query = sprintf("SELECT time0, time1, time2, time3, time4, time5, time6, time7, time8, time9, time10, time11,
            time12, time13, time14, time15, time16, time17, time18, time19, time20, time21, time22, time23
            FROM `%s`AS cs
            WHERE chan=:channel AND type=:type AND nick=''",
                TBL_CHANSTATS);
        $ps = $this->db->prepare($query);
        $ps->bindValue(':type', $type, PDO::PARAM_STR);
        $ps->bindValue(':channel', $chan, PDO::PARAM_STR);
        $ps->execute();
        $result = $ps->fetch(PDO::FETCH_NUM);
        if (is_array($result)) {
            foreach ($result as $key => $val) {
                $result[$key] = (int) $val;
            }
            return $result;
        } else {
            return null;
        }
    }

    /**
     * Checks if given channel can be displayed
     * @param string $chan
     * @return int code (200: OK, 404: not existing, 403: denied)
     */
    public function checkChannel($chan) {
        $noshow = array();
        $no = explode(",", $this->cfg->hide_chans);
        for ($i = 0; $i < count($no); $i++) {
            $noshow[$i] = strtolower($no[$i]);
        }
        if (in_array(strtolower($chan), $noshow)) {
            return 403;
        }

        $query = sprintf("SELECT * FROM `%s` AS c"
                . " WHERE LOWER(`channel`) = LOWER(:channel)",
                TBL_CHAN);
        $ps = $this->db->prepare($query);
        $ps->bindValue(':channel', $chan, SQL_STR);
        $ps->execute();
        $data = $ps->fetch();

        if (!$data) {
            return 404;
        }

        list($modes) = explode(' ', $data['modes']);

        if ($this->cfg->block_schans && Protocol::chan_secret_mode && strpos($modes, Protocol::chan_secret_mode) !== false) {
            return 403;
        }
        if ($this->cfg->block_pchans && Protocol::chan_private_mode && strpos($modes, Protocol::chan_private_mode) !== false) {
            return 403;
        }
        if (strpos($modes, 'i') !== false || strpos($modes, 'k') !== false || strpos($modes, 'O') !== false) {
            return 403;
        }

        return 200;
    }

    /**
     * Checks if the given channel is being monitored by chanstats
     * @param string $chan Channel
     * @return boolean true: yes, false: no
     */
    public function checkChannelStats($chan) {
        $query = sprintf("SELECT COUNT(*) FROM `%s` AS cs"
                . " WHERE chan = :channel",
                TBL_CHANSTATS);
        $ps = $this->db->prepare($query);
        $ps->bindValue(':channel', $chan, PDO::PARAM_STR);
        $ps->execute();
        return $ps->fetch(PDO::FETCH_COLUMN) ? true : false;
    }

    /**
     * Get the most active current users
     * @param int $limit
     * @return array of user stats
     */
    public function getUsersTop($limit = 10) {
        $aaData = array();
        $query = sprintf("SELECT nick AS uname, line AS 'lines'"
                . " FROM `%s` AS cs"
                . " WHERE type = 'daily' AND chan = '' AND line > 0"
                . " ORDER BY line DESC LIMIT :limit",
                TBL_CHANSTATS);
        $ps = $this->db->prepare($query);
        $ps->bindValue(':limit', $limit, PDO::PARAM_INT);
        $ps->execute();
        $data = $ps->fetchAll(PDO::FETCH_ASSOC);
        if (is_array($data)) {
            foreach ($data as $row) {
                $user = $this->getUser('stats', $row['uname']);
                if (!$user) {
                    $user = new User();
                    $user->nickname = $row['uname'];
                    $user->country = 'Unknown';
                    $user->country_code = '';
                    $user->online = false;
                    $user->away = false;
                    $user->bot = false;
                    $user->service = false;
                    $user->operator = false;
                    $user->helper = false;
                }
                $user->uname = $row['uname'];
                $user->lines = $row['lines'];
                $aaData[] = $user;
            }
        }
        return $aaData;
    }

    /**
     * Get a user based on its nickname or stats user
     * @param string $mode 'nick': nickname, 'stats': chanstats user
     * @param string $user
     * @return User
     */
    public function getUser($mode, $user) {
        $info = $this->getUserData($mode, $user);
        $query = sprintf("SELECT u.nick AS nickname, u.realname, u.host AS hostname, u.chost AS hostname_cloaked, u.vhost AS vhost,
            u.ident AS username, u.signon AS connect_time, u.server, u.away, u.awaymsg AS away_msg, u.version AS client,
            u.geocode AS country_code, u.geocountry AS country, u.geocity AS city, u.georegion AS region, s.ulined AS service, u.modes AS umodes
            FROM `%s` AS u
            LEFT JOIN `%s` AS s ON s.id = u.servid
            WHERE u.nick = :nickname",
                TBL_USER, TBL_SERVER);
        $ps = $this->db->prepare($query);
        $ps->bindValue(':nickname', $info['nick'], PDO::PARAM_STR);
        $ps->execute();
        $user = $ps->fetchObject('User');
        if ($user) {
            $user->uname = $info['uname'];
            $user->aliases = $info['aliases'];
            return $user;
        } else {
            return null;
        }
    }

    /**
     * Get a list of channels monitored for a specific user
     * @param string $mode 'nick': nickname, 'stats': chanstats user
     * @param string $user
     * @return array of channel names
     */
    public function getUserChannels($mode, $user) {
        $info = $this->getUserData($mode, $user);
        $secret_mode = Protocol::chan_secret_mode;
        $private_mode = Protocol::chan_private_mode;

        $where = "";
        if ($secret_mode) {
            $where .= sprintf(" AND c.modes NOT LIKE BINARY '%%%s%%'", $secret_mode);
        }
        if ($private_mode) {
            $where .= sprintf(" AND c.modes NOT LIKE BINARY '%%%s%%'", $private_mode);
        }
        $hide_channels = $this->cfg->hide_chans;
        if ($hide_channels) {
            $hide_channels = explode(",", $hide_channels);
            foreach ($hide_channels as $key => $channel) {
                $hide_channels[$key] = $this->db->escape(trim(strtolower($channel)));
            }
            $where .= sprintf(" AND LOWER(c.channel) NOT IN(%s)", implode(',', $hide_channels));
        }

        $query = sprintf("SELECT DISTINCT cs.chan
            FROM `%s` AS cs
            JOIN `%s` AS c ON LOWER(c.channel) = LOWER(cs.chan)
            JOIN `%s` AS u ON u.account = cs.nick
            WHERE cs.type = 'total'
            AND cs.nick = :uname %s",
                TBL_CHANSTATS, TBL_CHAN, TBL_USER, $where);
        $ps = $this->db->prepare($query);
        $ps->bindValue(':uname', $info['uname'], PDO::PARAM_STR);
        $ps->execute();
        return $ps->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get the global user activity
     * @param int $type int $type 0: total, 1: day, 2: week, 3: month, 4: year
     * @param boolean $datatables true: datatables format, false: standard format
     * @return array
     */
    public function getUserGlobalActivity($type, $datatables = false) {
        $aaData = array();

        $query = sprintf("SELECT SQL_CALC_FOUND_ROWS nick AS 'uname', letters, words, line AS 'lines',
            actions, (smileys_happy + smileys_sad + smileys_other) AS 'smileys', kicks, modes, topics
            FROM `%s` AS cs
            WHERE type = :type AND letters > 0 and chan = ''",
                TBL_CHANSTATS);
        if ($datatables) {
            $total = $this->db->datatablesTotal($query, array(':type' => $type));
            $filtering = $this->db->datatablesFiltering(array('nick'));
            $ordering = $this->db->datatablesOrdering();
            $paging = $this->db->datatablesPaging();
            $query .= sprintf("%s %s %s", $filtering ? " AND " . $filtering : "", $ordering, $paging);
        }
        $ps = $this->db->prepare($query);
        $ps->bindValue(':type', $type, PDO::PARAM_STR);
        $ps->execute();
        $data = $ps->fetchAll(PDO::FETCH_ASSOC);
        if ($datatables) {
            $filteredTotal = $this->db->foundRows();
        }
        if (is_array($data)) {
            foreach ($data as $row) {
                if ($datatables) {
                    $row["DT_RowId"] = $row['uname'];
                }
                $user = $this->getUser('stats', $row['uname']);
                if (!$user) {
                    $user = new User();
                    $user->nickname = $row['uname'];
                    $user->country = 'Unknown';
                    $user->country_code = '';
                    $user->online = false;
                    $user->away = false;
                    $user->bot = false;
                    $user->service = false;
                    $user->operator = false;
                    $user->helper = false;
                }
                foreach ($row as $key => $val) {
                    $user->$key = $val;
                }
                $aaData[] = $user;
            }
        }
        return $datatables ? $this->db->datatablesOutput($total, $filteredTotal, $aaData) : $aaData;
    }

    /**
     * Get the user activity of the given user
     * @param string $mode stats: user is treated as stats user, nick: user is treated as nickname
     * @param string $user User
     * @param string $chan Channel
     * @return mixed
     */
    public function getUserActivity($mode, $user, $chan) {
        $info = $this->getUserData($mode, $user);
        if ($chan == null) {
            $chan = '';
            $query = sprintf("SELECT type, letters, words, line AS 'lines', actions,
                (smileys_happy + smileys_sad + smileys_other) AS smileys, kicks, cs.modes, topics
                FROM `%s` AS cs
                WHERE nick = :nick AND chan = :chan
                ORDER BY cs.letters DESC",
                TBL_CHANSTATS);
        } else {
            $where = "";
            $hide_channels = $this->cfg->hide_chans;
            if ($hide_channels) {
                $hide_channels = explode(",", $hide_channels);
                foreach ($hide_channels as $key => $channel) {
                    $hide_channels[$key] = $this->db->escape(trim(strtolower($channel)));
                }
                $where .= sprintf(" AND LOWER(channel) NOT IN(%s)", implode(',', $hide_channels));
            }
            $query = sprintf("SELECT type, letters, words, line AS 'lines', actions,
                (smileys_happy + smileys_sad + smileys_other) AS smileys, kicks, cs.modes, topics
                FROM `%s` AS cs
                JOIN `%s` AS c ON LOWER(c.channel) = LOWER(cs.chan)
                WHERE cs.nick = :nick AND cs.chan = :chan %s
                ORDER BY cs.letters DESC",
                    TBL_CHANSTATS, TBL_CHAN, $where);
        }
        $ps = $this->db->prepare($query);
        $ps->bindValue(':nick', $info['uname'], PDO::PARAM_STR);
        $ps->bindValue(':chan', $chan, PDO::PARAM_STR);
        $ps->execute();
        $data = $ps->fetchAll(PDO::FETCH_ASSOC);
        if (!is_array($data)) {
            return null;
        }
        foreach ($data as $key => $type) {
            foreach ($type as $field => $val) {
                $data[$key][$field] = is_numeric($val) ? (int) $val : $val;
            }
        }
        return $data;
    }

    /**
     * Get the average hourly activity for the given user
     * @param string $mode stats: user is treated as stats user, nick: user is treated as nickname
     * @param string $user User
     * @param string $chan Channel
     * @param int $type int $type 0: total, 1: day, 2: week, 3: month, 4: year
     * @return mixed
     */
    public function getUserHourlyActivity($mode, $user, $chan, $type) {
        $info = $this->getUserData($mode, $user);
        if ($chan == null){
            $chan = '';
        }
        $query = sprintf("SELECT time0, time1, time2, time3, time4, time5, time6, time7, time8, time9, time10, time11,
            time12, time13, time14, time15, time16, time17, time18, time19, time20, time21, time22, time23
            FROM `%s` AS cs
            WHERE nick = :nick AND chan = :channel AND type = :type",
                TBL_CHANSTATS);
        $ps = $this->db->prepare($query);
        $ps->bindValue(':type', $type, PDO::PARAM_STR);
        $ps->bindValue(':channel', $chan, PDO::PARAM_STR);
        $ps->bindValue(':nick', $info['uname'], PDO::PARAM_STR);
        $ps->execute();
        $result = $ps->fetch(PDO::FETCH_NUM);
        if (!is_array($result)) {
            return null;
        }
        foreach ($result as $key => $val) {
            $result[$key] = (int) $val;
        }
        return $result;
    }

    /**
     * Checks if the given user exists
     * @param string $user User
     * @param string $mode ('stats': $user is a stats user, 'nick': $user is a nickname)
     * @return boolean true: yes, false: no
     */
    public function checkUser($user, $mode) {
        if ($mode == "stats") {
            $query = sprintf("SELECT nick"
                    . " FROM `%s` AS u"
                    . " WHERE LOWER(nick) = LOWER(:user)"
                    . " LIMIT 1",
                    TBL_CHANSTATS);
        } else {
            $query = sprintf("SELECT nick"
                    . " FROM `%s` AS u"
                    . " WHERE LOWER(nick) = LOWER(:user)",
                    TBL_USER);
        }
        $ps = $this->db->prepare($query);
        $ps->bindValue(':user', $user, SQL_STR);
        $ps->execute();
        return $ps->fetch(PDO::FETCH_COLUMN) ? true : false;
    }

    /**
     * Checks if the given user is being monitored by chanstats
     * @param string $user User
     * @param string $mode ('stats': $user is a stats user, 'nick': $user is a nickname)
     * @return boolean true: yes, false: no
     */
    public function checkUserStats($user, $mode) {
        if ($mode != 'stats') {
            $user = $this->getUnameFromNick($user);
        }
        $query = sprintf("SELECT COUNT(*)"
                . " FROM `%s` AS cs"
                . " WHERE nick = :user",
                TBL_CHANSTATS);
        $ps = $this->db->prepare($query);
        $ps->bindValue(':user', $user, PDO::PARAM_STR);
        $ps->execute();
        return $ps->fetch(PDO::FETCH_COLUMN) ? true : false;
    }

    /**
     * Returns the stats username and all aliases of a user
     * @param string $mode ('stats': $user is a stats user, 'nick': $user is a nickname)
     * @param string $user Nickname or Stats username
     * @return array ('nick' => nickname, 'uname' => stats username, 'aliases' => array of aliases)
     */
    private function getUserData($mode, $user) {
        $uname = ($mode == "stats") ? $user : $this->getUnameFromNick($user);
        $aliases = $this->getUnameAliases($uname);
        if (!$aliases) {
            $aliases = array($uname ? $uname : $user);
        }
        $nick = ($mode == "stats") ? $aliases[0] : $user;
        array_shift($aliases);
        return array('nick' => $nick, 'uname' => $uname, 'aliases' => $aliases);
    }

    /**
     * Get the chanstats username assigned to a nick, if available
     * @param string $nick nickname
     * @return string chanstats username
     */
    private function getUnameFromNick($nick) {
        $query = sprintf("SELECT account"
                . " FROM `%s` AS u"
                . " WHERE nick = :nickname",
                TBL_USER);
        $ps = $this->db->prepare($query);
        $ps->bindValue(':nickname', $nick, PDO::PARAM_STR);
        $ps->execute();
        return $ps->fetch(PDO::FETCH_COLUMN);
    }

    /**
     * Get all nicknames linked to a chanstats user
     * @param string $uname chanstats username
     * @return array of nicknames
     */
    private function getUnameAliases($uname) {
        if (!$uname || $this->cfg->hide_nickaliases) {
            return null;
        }
        $query = sprintf("SELECT u.nick"
                . " FROM `%s` AS u"
                . " WHERE u.account = :uname"
                . " ORDER BY u.signon ASC",
                TBL_USER);
        $ps = $this->db->prepare($query);
        $ps->bindValue(':uname', $uname, PDO::PARAM_STR);
        $ps->execute();
        return $ps->fetchAll(PDO::FETCH_COLUMN);
    }

}
