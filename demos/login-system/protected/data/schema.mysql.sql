
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

DROP TABLE IF EXISTS `<DB_PREFIX>accounts`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `password` varchar(64) CHARACTER SET latin1 NOT NULL,
  `salt` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(70) NOT NULL,
  `role` enum('default','admin','owner') NOT NULL DEFAULT 'owner',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

INSERT INTO `<DB_PREFIX>accounts` (`id`, `username`, `password`, `salt`, `email`, `role`) VALUES
(1, '<USERNAME>', '<PASSWORD>', '<SALT>', '<EMAIL>', 'owner');

