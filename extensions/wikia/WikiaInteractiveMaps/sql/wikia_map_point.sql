CREATE TABLE IF NOT EXISTS wikia_map_point (
  page_id int(8) unsigned not null comment 'field linking data in this table with data in MW page table',
  x int(8) not null default 0 comment 'coordinate on x axis',
  y int(8) not null default 0 comment 'coordinate on y axis',
  flag tinyint(2) not null default 0,
  key coordinates ( x, y ),
  foreign key point_page ( page_id ) references page ( page_id ) on delete cascade on update no action
) ENGINE=InnoDB;
