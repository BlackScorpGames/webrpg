DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `userId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `email`  varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `registrationDate` datetime DEFAULT NULL,
  `lastAction` datetime DEFAULT NULL,
  PRIMARY KEY (`userId`),
  UNIQUE KEY `unique_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `characters`;
CREATE TABLE IF NOT EXISTS `characters` (
  `characterId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `userId` int(11) unsigned NOT NULL,
  `class` varchar(10) NOT NULL,
  `gender` tinyint(1) unsigned NOT NULL,
  `lastAction` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`characterId`),
  UNIQUE KEY `unique_name` (`name`),
  KEY `FK_USER` (`userId`),
  CONSTRAINT `FK_USER` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
