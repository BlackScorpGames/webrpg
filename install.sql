DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(254) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(254) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `registrationDate` datetime DEFAULT NULL,
  `lastAction` datetime DEFAULT NULL,
  PRIMARY KEY (`userId`),
  UNIQUE KEY `unique` (`email`,`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;