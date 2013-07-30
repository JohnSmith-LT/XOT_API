CREATE TABLE IF NOT EXISTS `$api_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `consumer_key` varchar(22) NOT NULL,
  `consumer_secret` varchar(41) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `description` text NOT NULL,
  `created` datetime DEFAULT NULL,
  `last_modified` datetime DEFAULT NULL,
  `last_used` datetime DEFAULT NULL,
  `uses_count` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `$api_nonces` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `consumer_key` varchar(22) NOT NULL,
  `nonce` varchar(32) NOT NULL,
  `timestamp` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `$api_options` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `value` longtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `option_name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

INSERT INTO `api_options` (`id`, `name`, `value`) VALUES
(1, 'user_access_rights', '*'),
(2, 'enabled', 'yes');

INSERT INTO `api_keys` (`id`, `user_id`, `consumer_key`, `consumer_secret`, `active`, `description`, `created`, `last_modified`, `last_used`, `uses_count`) VALUES
(1, 0, 'IQKbtAYlXLripLGPWd0HUA', 'GgDYlkSvaPxGxC4X8liwpUoqKwwr3lCADbz8A7ADU', 1, 'This is the test key/secret that all tests were done with.', '2013-06-21 09:00:00', '2013-06-21 09:00:00', '2013-06-30 17:59:38', 44);