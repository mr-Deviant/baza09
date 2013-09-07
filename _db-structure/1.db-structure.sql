CREATE DATABASE IF NOT EXISTS `base09` DEFAULT CHARSET = utf8 DEFAULT COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `base09_countries` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL DEFAULT '',
	`code` varchar(5) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	UNIQUE KEY `name_uk` (`name`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 DEFAULT COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `base09_cities` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`country_id` int(11) NOT NULL,
	`name` varchar(255) NOT NULL DEFAULT '',
	`code` varchar(5) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	UNIQUE KEY `name_uk` (`country_id`, `name`),
	FOREIGN KEY `country_fk` (`country_id`)
        REFERENCES `base09_countries`(`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8 DEFAULT COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `base09_locations` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`city_id` int(11) NOT NULL,
	`name` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	UNIQUE KEY `name_uk` (`city_id`, `name`),
	FOREIGN KEY `city_fk` (`city_id`)
        REFERENCES `base09_cities`(`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8 DEFAULT COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `base09_streets` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`location_id` int(11) NOT NULL,
	`type` ENUM('ул.', 'пр-т', 'б-р', 'пл.', 'пер.', ''),
	`name` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	UNIQUE KEY `name_uk` (`location_id`, `type`, `name`),
	FOREIGN KEY `location_fk` (`location_id`)
        REFERENCES `base09_locations`(`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8 DEFAULT COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `base09_houses` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`street_id` int(11) NOT NULL,
	`num` varchar(9) NOT NULL DEFAULT '0',
	`block` varchar(10) NOT NULL DEFAULT '',
	`lat` varchar(15) NOT NULL DEFAULT '',
	`lon` varchar(15) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	UNIQUE KEY `name_uk` (`street_id`, `num`, `block`),
	FOREIGN KEY `street_fk` (`street_id`)
        REFERENCES `base09_streets`(`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8 DEFAULT COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `base09_persons` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`second_name` varchar(255) NOT NULL DEFAULT '',
	`first_name` varchar(255) NOT NULL DEFAULT '',
	`middle_name` varchar(255) NOT NULL DEFAULT '',
	`house_id` int(11) NOT NULL,
	`room_num` varchar(6) NOT NULL DEFAULT '0',
	`room_letter` varchar(4) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	UNIQUE KEY `names_uk` (`second_name`, `first_name`, `middle_name`, `house_id`, `room_num`, `room_letter`),
	FOREIGN KEY `house_fk` (`house_id`)
        REFERENCES `base09_houses`(`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8 DEFAULT COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `base09_phones` (
	`person_id` int(11) NOT NULL,
	`phone_number` char(7) NOT NULL DEFAULT '',
	UNIQUE KEY `phone_uk` (`person_id`, `phone_number`),
	FOREIGN KEY `person_fk` (`person_id`)
        REFERENCES `base09_persons`(`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8 DEFAULT COLLATE = utf8_general_ci;

# Indexes for import

ALTER TABLE `base09_phones` ADD INDEX `phone_number_k` (`phone_number`);