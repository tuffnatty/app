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

CREATE TABLE IF NOT EXISTS `wiki_page_category` (
  `wiki_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL ,
  `category` char(64) NOT NULL,
  PRIMARY KEY (`wiki_id`, `page_id`),
  KEY `idx__wiki_id__category` (`wiki_id`, `category`)
) ENGINE=InnoDB DEFAULT CHARSET=`UTF8`;
