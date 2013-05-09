CREATE TABLE IF NOT EXISTS `all_sms` (
  `id` bigint(20) unsigned NOT NULL,
  `user_id` int(15) unsigned NOT NULL,
  `from` varchar(25) DEFAULT NULL,
  `to` varchar(25) DEFAULT NULL,
  `text` text,
  `encoding` varchar(15) NOT NULL,
  `byte_length` varchar(15) NOT NULL,
  `char_length` varchar(15) NOT NULL,
  `sms_unit` varchar(15) NOT NULL,
  `date` varchar(20) NOT NULL,
  `recipient_id` int(15) DEFAULT 0,
  `status_id` int(15) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(15) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(50) NOT NULL,
  `role` varchar(10) NOT NULL DEFAULT  'user',
  `credit` int(15) DEFAULT 0,
  `utf8_fee` float NOT NULL,
  `ascii_fee` float NOT NULL,
  `url` varchar(200) DEFAULT NULL,
  `numbers` varchar (200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO  `sms_relay`.`users` (
	`id` ,
	`username` ,
	`password` ,
	`credit` ,
	`url` ,
	`numbers`
	)
	VALUES (
	NULL ,  'test',  'test',  '0',  'www.test.com/ws',  '50001,50002,50003'
);

CREATE TABLE IF NOT EXISTS `replys` (
  `id` int(15) unsigned NOT NULL AUTO_INCREMENT,
  `from` varchar(25) DEFAULT NULL,  
  `to` varchar(25) NOT NULL,
  `text` text,
  `encoding` varchar(15) NOT NULL,  
  `byte_length` varchar(15) NOT NULL,
  `char_length` varchar(15) NOT NULL,
  `date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;