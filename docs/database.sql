CREATE TABLE IF NOT EXISTS `all_sms` (
  `id` int(15) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `from` varchar(15) DEFAULT NULL,
  `to` text DEFAULT NULL,
  `text` text,
  `recipient_id` int(15) DEFAULT 0,
  `status_id` int(15) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
