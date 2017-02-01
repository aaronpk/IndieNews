CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `posts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lang` char(2) DEFAULT 'en',
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `date_submitted` datetime NOT NULL,
  `post_date` datetime DEFAULT NULL,
  `tzoffset` int(11) NOT NULL DEFAULT 0,
  `post_author` varchar(255) DEFAULT NULL,
  `title` varchar(512) DEFAULT NULL,
  `href` varchar(512) DEFAULT NULL,
  `body` text,
  `comments` int(11) NOT NULL DEFAULT '0',
  `in_reply_to` varchar(512) DEFAULT NULL,
  `source_url` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

