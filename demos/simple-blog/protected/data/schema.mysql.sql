
DROP TABLE IF EXISTS `<DB_PREFIX>modules`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>modules` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `class_code` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(40) CHARACTER SET latin1 NOT NULL,
  `description` varchar(255) CHARACTER SET latin1 NOT NULL,
  `version` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `icon` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `show_on_dashboard` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `show_in_menu` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_installed` tinyint(1) NOT NULL DEFAULT '0',
  `is_system` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `installed_at` datetime NULL DEFAULT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  `has_test_data` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 - no, 1 - yes',
  `sort_order` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;


DROP TABLE IF EXISTS `<DB_PREFIX>settings`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>settings` (
  `id` int(11) unsigned NOT NULL,
  `blog_name` varchar(100) NOT NULL,
  `slogan` varchar(250) NOT NULL,
  `footer` varchar(250) NOT NULL,
  `post_max_chars` int(11) NOT NULL DEFAULT '0',
  `metatag_title` varchar(250) DEFAULT NULL,
  `metatag_keywords` varchar(250) DEFAULT NULL,
  `metatag_description` varchar(250) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `<DB_PREFIX>settings` (`id`, `blog_name`, `slogan`, `footer`, `post_max_chars`, `metatag_title`, `metatag_keywords`, `metatag_description`) VALUES
(1, 'Simple Blog', 'your slogan here...', 'ApPHP Simple Blog &copy;', 300, 'Simple Blog', 'apphp framework, blog, apphp', 'ApPHP SimpleBlog - Personal PHP Web Blog developed with ApPHP MVC Framework');


DROP TABLE IF EXISTS `<DB_PREFIX>posts`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `header` varchar(100) NOT NULL,
  `category_id` smallint(11) DEFAULT '0',
  `post_text` text NOT NULL,
  `author_id` int(11) NOT NULL DEFAULT '0',
  `post_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `metatag_title` varchar(250) DEFAULT NULL,
  `metatag_keywords` varchar(250) DEFAULT NULL,
  `metatag_description` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `author_id` (`author_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO `<DB_PREFIX>posts` (`id`, `header`, `category_id`, `post_text`, `author_id`, `post_datetime`, `metatag_title`, `metatag_keywords`, `metatag_description`) VALUES
(1, 'Welcome Post!', 1, 'Welcome to Simple Blog! This blog is created on ApPHP MVC Framework - PHP framework designed to provide modern and rapid development of websites, web applications and web services. ', 1, '2013-04-01 16:40:53', 'Simple Blog', 'apphp framework, blog, apphp, yes', 'ApPHP Simple Blog - Personal PHP Web Blog based on ApPHP MVC Framework');


DROP TABLE IF EXISTS `<DB_PREFIX>categories`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `posts_count` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO `<DB_PREFIX>categories` (`id`, `name`, `posts_count`) VALUES
(1, 'Personal', 1);


DROP TABLE IF EXISTS `<DB_PREFIX>authors`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>authors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(25) NOT NULL,
  `password` varchar(64) NOT NULL,
  `salt` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL,
  `role` enum('default','admin','owner') NOT NULL DEFAULT 'owner',
  `about_text` varchar(300) NOT NULL,
  `avatar_file` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `<DB_PREFIX>authors` (`id`, `login`, `password`, `salt`, `email`, `role`, `about_text`, `avatar_file`) VALUES
(1, '<USERNAME>', '<PASSWORD>', '<SALT>', '<EMAIL>', 'owner', 'Hi! My name is Jack. I''m a programmer and I like music, sport, computers and many other... Enjoy with my personal blog!', 'admin_avatar.png');


ALTER TABLE `<DB_PREFIX>posts`
  ADD CONSTRAINT `<DB_PREFIX>posts_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `<DB_PREFIX>categories` (`id`),
  ADD CONSTRAINT `<DB_PREFIX>posts_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `<DB_PREFIX>authors` (`id`);

  