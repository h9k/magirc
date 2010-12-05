-- 
-- Magirc configuration table
-- $Id$
--

-- 
-- Table structure for table `magirc_config`
-- 

CREATE TABLE IF NOT EXISTS `magirc_config` (
  `parameter` varchar(32) NOT NULL default '',
  `value` varchar(1024) NOT NULL default '',
  PRIMARY KEY (`parameter`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `magirc_config`
-- 

INSERT IGNORE INTO `magirc_config` (`parameter`, `value`) VALUES ('db_version', '1'),
('net_name', 'MyNetwork'),
('net_url', 'http://www.mynet.tld/'),
('msg_welcome', '<h1>Welcome to Magirc</h1><p>These are the Web Stats of this IRC Network.<br />You will find detailed information about the network status and the activity of its channels and users.<br />Enjoy your stay!</p>'),
('ircd_type', 'unreal32'),
('theme', 'default'),
('locale', 'en_US'),
('chanstats_sort', 'line'),
('chanstats_type', '3'),
('hide_ulined', '1'),
('hide_servers', ''),
('hide_chans', '#opers,#services'),
('list_limit', '20'),
('top_limit', '10'),
('search_min_chars', '3'),
('status_lookup', '1'),
('tld_stats', '1'),
('client_stats', '1'),
('chan_invites', '0'),
('chan_bans', '0'),
('chan_excepts', '0'),
('net_graphs', '1'),
('mirc', '0'),
('mirc_url', ''),
('webchat', '0'),
('webchat_url', ''),
('remote', '1'),
('netsplit_id', ''),
('netsplit_graphs', '0'),
('netsplit_years', '0'),
('netsplit_history', '0'),
('searchirc_id', ''),
('searchirc_ranking', '0'),
('searchirc_graphs', '0'),
('adsense', '0'),
('adsense_id', ''),
('adsense_channel', ''),
('graph_cache', '0'),
('graph_cache_path', ''),
('net_cache_time', '60'),
('pie_cache_time', '1'),
('bar_cache_time', '1'),
('gzip', '0'),
('debug_mode', '0'),
('show_exec_time', '0'),
('show_validators', '0'),
('base_url', '');

--
-- Table structure for table `magirc_admin`
--

CREATE TABLE IF NOT EXISTS `magirc_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(16) NOT NULL,
  `password` varchar(255) NOT NULL,
  `realname` varchar(32) NOT NULL,
  `email` varchar(32) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
