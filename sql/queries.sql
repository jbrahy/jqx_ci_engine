CREATE TABLE `queries` (
  `query_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `query_type_id` int(11) DEFAULT NULL,
  `query_name` varchar(255) NOT NULL DEFAULT '',
  `query_sql` text NOT NULL,
  `status_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`query_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
