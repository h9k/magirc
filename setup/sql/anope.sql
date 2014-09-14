-- IMPORTANT: refer to README.md documentation about setting up Anope before running this!

DROP VIEW IF EXISTS anope_currentusage;
CREATE VIEW `anope_currentusage` AS
  SELECT NOW() AS 'datetime',
         (SELECT COUNT(*) FROM anope_server WHERE online = 'Y') AS 'servers',
         (SELECT COUNT(*) FROM anope_chan) AS 'channels',
         (SELECT COUNT(*) FROM anope_user) AS 'users',
         (SELECT COUNT(*) FROM anope_user WHERE oper = 'Y') AS 'operators';

CREATE TABLE IF NOT EXISTS `anope_history` (
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `servers` tinyint(3) unsigned NOT NULL,
  `channels` mediumint(8) unsigned NOT NULL,
  `users` int(10) unsigned NOT NULL,
  `operators` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`datetime`)
);

DROP EVENT IF EXISTS anope_history_update;
CREATE EVENT `anope_history_update`
ON SCHEDULE EVERY 1 HOUR STARTS '2014-09-01 00:00:00'
DO
INSERT INTO `anope_history` (`servers`, `channels`, `users`, `operators`) SELECT `servers`, `channels`, `users`, `operators` FROM `anope_currentusage`;
