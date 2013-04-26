#dataware db

CREATE TABLE IF NOT EXISTS `wiki_subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wiki_id` int(11) NOT NULL,
  `type` char(26) NOT NULL,
  `name` char(240) NOT NULL,
  `name_norm` char(240) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `wiki_id` (`wiki_id`,`type`,`name_norm`)
) ENGINE=InnoDB DEFAULT CHARSET=`UTF8` AUTO_INCREMENT=1 ;