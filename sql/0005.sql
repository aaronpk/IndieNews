CREATE TABLE `blocks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(512) DEFAULT NULL,
  `source_url` varchar(512) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `note` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `domain` (`domain`),
  KEY `source_url` (`source_url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
