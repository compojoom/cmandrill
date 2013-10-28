CREATE TABLE IF NOT EXISTS `#__cmandrill_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `state` int(11) NOT NULL,
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `template` varchar(255) NOT NULL,
  `component` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `class_name` varchar(255) NOT NULL,
  `function_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `component` (`component`)
) DEFAULT CHARSET=utf8;