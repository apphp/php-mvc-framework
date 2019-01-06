
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
  `ssl_mode` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 - no, 1 - entire site, 2 - admin area, 3 - user area, 4 - payment modules',
  `site_name` varchar(100) NOT NULL,
  `slogan` varchar(250) NOT NULL,
  `footer` varchar(250) NOT NULL,
  `metatag_title` varchar(250) DEFAULT NULL,
  `metatag_keywords` varchar(250) DEFAULT NULL,
  `metatag_description` varchar(250) DEFAULT NULL,
  `is_offline` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `offline_message` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `time_zone` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0'
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `<DB_PREFIX>settings` (`id`, `ssl_mode`, `site_name`, `slogan`, `footer`, `metatag_title`, `metatag_keywords`, `metatag_description`, `is_offline`, `offline_message`, `time_zone`) VALUES
(1, 0, 'Simple CMS', 'your slogan here...', 'ApPHP Simple CMS &copy;', 'Simple CMS', 'apphp framework, cms, apphp', 'ApPHP SimpleCMS - Content Management System', '0', '', '');


DROP TABLE IF EXISTS `<DB_PREFIX>pages`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `header_text` varchar(255) NOT NULL,
  `link_text` varchar(100) NOT NULL,
  `menu_id` smallint(11) DEFAULT '0',
  `page_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_homepage` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `metatag_title` varchar(250) DEFAULT NULL,
  `metatag_keywords` varchar(250) DEFAULT NULL,
  `metatag_description` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menu_id` (`menu_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

INSERT INTO `<DB_PREFIX>pages` (`id`, `header_text`, `link_text`, `menu_id`, `page_text`, `created_at`, `is_homepage`, `metatag_title`, `metatag_keywords`, `metatag_description`) VALUES
(1, 'Home', 'Home', 0, 'Welcome to Simple CMS!<br><br>This simple CMS site demonstrates some advanced features of the framework like: setup and login modules, advanced widgets, work with database and web forms, CRUD operations, form validation, etc. In administration area you could configure all major CMS settings, admin profile info, edit site categories, create and manage pages. On the Front-End visitors can see last the menu and pages. This script may be used as a basis for creating your own advanced application.', '2013-09-03 16:40:53', 1, 'ApPHP Simple CMS - Content Management System', 'apphp framework, cms, apphp', 'ApPHP Simple CMS - Content Management System'),
(2, 'Text Page', 'Text Page', 1, 'This is a test page.', '2013-09-03 16:44:53', 0, 'ApPHP Simple CMS - Content Management System', 'apphp framework, cms, apphp', 'ApPHP Simple CMS - Content Management System');


DROP TABLE IF EXISTS `<DB_PREFIX>menus`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `sort_order` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO `<DB_PREFIX>menus` (`id`, `name`, `sort_order`) VALUES
(1, 'General', 0);


DROP TABLE IF EXISTS `<DB_PREFIX>admins`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) CHARACTER SET latin1 NOT NULL,
  `password` varchar(64) CHARACTER SET latin1 NOT NULL,
  `salt` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `display_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `first_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(80) CHARACTER SET latin1 NOT NULL,
  `role` enum('owner','mainadmin','admin') CHARACTER SET latin1 NOT NULL DEFAULT 'owner',
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  `last_visited_at` datetime NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

INSERT INTO `<DB_PREFIX>admins` (`id`, `username`, `password`, `salt`, `display_name`, `first_name`, `last_name`, `email`, `role`, `created_at`, `updated_at`, `last_visited_at`, `is_active`) VALUES
(1, '<USERNAME>', '<PASSWORD>', '<SALT>', '', '', '', '<EMAIL>', 'owner', '<CREATED_AT>', NULL, NULL, 1);
