SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE IF NOT EXISTS `authors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `comment` text CHARACTER SET utf8 COLLATE utf8_bin,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title_id` (`title_id`,`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `owners` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `ratings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `mark` decimal(10,0) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title_id` (`title_id`,`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `session` (
  `id` varchar(64) NOT NULL,
  `data` varchar(255) DEFAULT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `titles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('book','audio','other') CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `title` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `author_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `surname` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `password` varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;
