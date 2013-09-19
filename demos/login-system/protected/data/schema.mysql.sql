DROP TABLE IF EXISTS `<DB_PREFIX>accounts`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) NOT NULL,
  `password` varchar(64) NOT NULL,
  `email` varchar(70) NOT NULL,
  `role` enum('default','admin','owner') NOT NULL DEFAULT 'default',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

INSERT INTO `<DB_PREFIX>accounts` (`id`, `username`, `password`, `email`, `role`) VALUES
(1, '<USERNAME>', '<PASSWORD>', '<EMAIL>', 'default');
