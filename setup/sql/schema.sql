-- 
-- Magirc configuration table
-- $Id$
--

-- 
-- Table structure for table `phpdenora_config`
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
('msg_welcome', '<div class="title_section">Welcome to Magirc</div><p>These are the Web Stats of this IRC Network.<br />You will find detailed information about the network status and the activity of its channels and users.<br />Enjoy your stay!</p>'),
('ircd_type', 'unreal32'),
('theme', 'default'),
('lang', 'en'),
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
('table_user', 'user'),
('table_chan', 'chan'),
('table_chanbans', 'chanbans'),
('table_chanexcepts', 'chanexcept'),
('table_chaninvites', 'chaninvites'),
('table_glines', 'glines'),
('table_sqlines', 'sqline'),
('table_maxvalues', 'maxvalues'),
('table_server', 'server'),
('table_ison', 'ison'),
('table_tld', 'tld'),
('table_cstats', 'cstats'),
('table_ustats', 'ustats'),
('table_current', 'current'),
('table_serverstats', 'serverstats'),
('table_channelstats', 'channelstats'),
('table_userstats', 'stats'),
('table_aliases', 'aliases'),
('base_url', '');
