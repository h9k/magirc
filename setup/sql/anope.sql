CREATE TABLE `anope_history` (
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `users` int(10) unsigned DEFAULT NOT NULL,
  `channels` mediumint(8) unsigned DEFAULT NOT NULL,
  `servers` tinyint(3) unsigned DEFAULT NOT NULL,
  PRIMARY KEY (`datetime`)
) ENGINE=InnoDB;

DELIMITER $$
CREATE PROCEDURE `anope_history_update`()
  BEGIN
    INSERT INTO `anope_history` (`users`, `channels`, `servers`) VALUES(
      (SELECT COUNT(*) FROM `anope_user`),
      (SELECT COUNT(*) FROM `anope_chan`),
      (SELECT COUNT(*) FROM `anope_server`)
    );
  END$$
DELIMITER ;

CREATE EVENT anope_history_update
ON SCHEDULE EVERY '1:00:00' HOUR_SECOND
DO CALL anope_history_update();
