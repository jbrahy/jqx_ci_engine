CREATE TABLE `query_types` (
  `query_type_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `query_type` varchar(255) DEFAULT NULL,
  `order_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`query_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
