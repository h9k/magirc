CREATE VIEW `anope_currentusage` AS
  SELECT NOW() AS 'datetime',
         (SELECT COUNT(*) FROM anope_server WHERE online = 'Y') AS 'servers',
         (SELECT COUNT(*) FROM anope_chan) AS 'channels',
         (SELECT COUNT(*) FROM anope_user) AS 'users',
         (SELECT COUNT(*) FROM anope_user WHERE oper = 'Y') AS 'operators';

CREATE TABLE `anope_history` (
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `servers` tinyint(3) unsigned NOT NULL,
  `channels` mediumint(8) unsigned NOT NULL,
  `users` int(10) unsigned NOT NULL,
  `operators` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`datetime`)
) ENGINE=InnoDB;

CREATE EVENT `anope_history_update`
ON SCHEDULE EVERY 1 HOUR
DO
INSERT INTO `anope_history` (`servers`, `channels`, `users`, `operators`) VALUES(
(SELECT COUNT(*) FROM `anope_server` WHERE online = 'Y'),
(SELECT COUNT(*) FROM `anope_chan`),
(SELECT COUNT(*) FROM `anope_user`),
(SELECT COUNT(*) FROM `anope_user` WHERE `oper` = 'Y')
);
