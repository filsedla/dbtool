-- Adminer 4.2.0 MySQL dump

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `author`;
CREATE TABLE `author` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `web` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `born` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `author` (`id`, `name`, `web`, `born`) VALUES
(1,	'Jan Novák',	NULL,	1980),
(2,	'Vladimír Krátký',	NULL,	1975),
(12,	'Name changed',	NULL,	1985),
(13,	'Jana Kučerová',	NULL,	1989),
(14,	'Name changed',	NULL,	1985),
(15,	'Jana Kučerová',	NULL,	1989),
(16,	'Name changed',	NULL,	1985),
(17,	'Jana Kučerová',	NULL,	1989);

DROP TABLE IF EXISTS `book`;
CREATE TABLE `book` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `author_id` int(11) unsigned NOT NULL,
  `translator_id` int(11) unsigned DEFAULT NULL,
  `title` varchar(255) CHARACTER SET latin1 NOT NULL,
  `web` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `createdAt` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `author_id` (`author_id`),
  KEY `translator_id` (`translator_id`),
  CONSTRAINT `book_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `author` (`id`),
  CONSTRAINT `book_ibfk_2` FOREIGN KEY (`translator_id`) REFERENCES `author` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `book` (`id`, `active`, `deleted`, `author_id`, `translator_id`, `title`, `web`, `createdAt`) VALUES
(1,	1,	0,	1,	NULL,	'První kniha',	NULL,	'0000-00-00 00:00:00'),
(2,	1,	0,	1,	NULL,	'Druhá kniha',	NULL,	'0000-00-00 00:00:00'),
(3,	1,	0,	2,	NULL,	'Jiná kniha',	NULL,	'0000-00-00 00:00:00');

DROP TABLE IF EXISTS `book_tagging`;
CREATE TABLE `book_tagging` (
  `book_id` int(11) unsigned NOT NULL,
  `tag_id` int(11) unsigned NOT NULL,
  KEY `book_id` (`book_id`),
  KEY `tag_id` (`tag_id`),
  CONSTRAINT `book_tagging_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `book` (`id`),
  CONSTRAINT `book_tagging_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `book_tagging` (`book_id`, `tag_id`) VALUES
(1,	1),
(3,	1),
(2,	2),
(3,	2);

DROP TABLE IF EXISTS `empty`;
CREATE TABLE `empty` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `tag`;
CREATE TABLE `tag` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `tag` (`id`, `name`) VALUES
(1,	'životopis'),
(2,	'bestseller');

-- 2016-03-15 17:35:42
