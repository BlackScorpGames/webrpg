CREATE TABLE IF NOT EXISTS `inventory` (
  `inventoryId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL DEFAULT '0',
  `slot` int(2) unsigned NOT NULL DEFAULT '0',
  `itemName` varchar(128) NOT NULL DEFAULT '0',
  `amount` int(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`inventoryId`),
  KEY `FK_INVENTORY_USER` (`userId`),
  CONSTRAINT `FK_INVENTORY_USER` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;