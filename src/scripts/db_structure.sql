create table `Posts` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT,
	`title` varchar(100) default NULL,
	`image` varchar(500) NOT NULL,
	`created` DATETIME default '0000-00-00 00:00:00',
	`updated` DATETIME default '0000-00-00 00:00:00',
	`source` varchar(16) default NULL,
	primary key  (`id`)
) engine=innodb default charset=utf8;

create table `MetaData` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL,
	`value` varchar(100) NOT NULL,
	`updated` DATETIME default '0000-00-00 00:00:00',
	primary key  (`id`),
	UNIQUE KEY `MetaData_name` (`name`)
) engine=innodb default charset=utf8;