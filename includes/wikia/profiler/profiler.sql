CREATE TABLE `request` (
  `request_id` char(17) NOT NULL,
  `entry_point` varchar(64) default NULL,
  `url` varchar(255) default NULL,
  `server` varchar(64) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE `details` (
  `request_id` char(17) NOT NULL,
  `ct` int(11) unsigned default NULL,
  `pmu` int(11) unsigned default NULL,
  `wt` int(11) unsigned default NULL,
  `cpu` int(11) unsigned default NULL,
  `fname` varchar(255),
  `called_from` varchar(255),
  PRIMARY KEY  (`id`),
  KEY `fname_idx` (`fname`),
  KEY `calledfrom_idx` (`called_from`),
) ENGINE=INNODB DEFAULT CHARSET=utf8;
