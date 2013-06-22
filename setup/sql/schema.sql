--
-- Magirc configuration table
--

--
-- Table structure for table `magirc_config`
--

CREATE TABLE IF NOT EXISTS `magirc_config` (
  `parameter` varchar(32) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY (`parameter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `magirc_config`
--

INSERT IGNORE INTO `magirc_config` (`parameter`, `value`) VALUES ('db_version', '13'),
('base_url', ''),
('net_name', 'MyNetwork'),
('net_url', 'http://www.mynet.tld/'),
('net_roundrobin', ''),
('net_port', '6667'),
('net_port_ssl', ''),
('welcome_mode', 'statuspage'),
('timezone', 'UTC'),
('denora_version', '1.4'),
('ircd_type', 'unreal32'),
('theme', 'default'),
('locale', 'en_US'),
('block_spchans', '0'),
('hide_ulined', '1'),
('hide_servers', ''),
('hide_chans', '#opers,#services'),
('debug_mode', '0'),
('live_interval', '15'),
('cdn_enable', '0'),
('rewrite_enable', '0'),
('service_adsense_id', ''),
('service_adsense_channel', ''),
('service_searchirc', ''),
('service_netsplit', ''),
('service_webchat', ''),
('service_mibbit', ''),
('service_mibbitid', ''),
('service_addthis', '0'),
('version_show', '1');

--
-- Table structure for table `magirc_content`
--

CREATE TABLE IF NOT EXISTS `magirc_content` (
  `name` varchar(16) NOT NULL default '',
  `text` text NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `magirc_content`
--

INSERT IGNORE INTO `magirc_content` (`name`, `text`) VALUES ('welcome', '<h1>Welcome to Magirc</h1><p>These are the Web Stats of this IRC Network.<br />You will find detailed information about the network status and the activity of its channels and users.<br />Enjoy your stay!</p>');

--
-- Table structure for table `magirc_admin`
--

CREATE TABLE IF NOT EXISTS `magirc_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(16) NOT NULL default '',
  `password` varchar(255) NOT NULL default '',
  `realname` varchar(32) NOT NULL default '',
  `email` varchar(32) NOT NULL default '',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
