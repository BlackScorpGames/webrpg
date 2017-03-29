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
  `map` varchar(64) NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `lastAction` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`characterId`),
  UNIQUE KEY `name` (`name`),
  KEY `FK_USER` (`userId`),
  CONSTRAINT `FK_USER` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `inventory`;
CREATE TABLE IF NOT EXISTS `inventory` (
  `inventoryId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `characterId` int(11) unsigned NOT NULL DEFAULT '0',
  `slot` int(2) unsigned NOT NULL DEFAULT '0',
  `itemName` varchar(128) NOT NULL DEFAULT '0',
  `amount` int(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`inventoryId`),
  KEY `FK_INVENTORY_USER` (`characterId`),
  CONSTRAINT `FK_INVENTORY_CHARACTER` FOREIGN KEY (`characterId`) REFERENCES `characters` (`characterId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

