CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `posts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `date_submitted` datetime NOT NULL,
  `source_url` varchar(512) DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_author` varchar(255) DEFAULT NULL,
  `title` varchar(512) DEFAULT NULL,
  `href` varchar(512) DEFAULT NULL,
  `body` text,
  `in_reply_to` varchar(512) DEFAULT NULL,
  `comments` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
);

