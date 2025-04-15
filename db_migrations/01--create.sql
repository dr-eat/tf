SHOW CREATE DATABASE `tf`;
CREATE TABLE `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `password` varchar(64) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `created_dt` int(11) NOT NULL,
  `status` enum('new','active','blocked') NOT NULL DEFAULT 'new',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `balance` decimal(10,2) NOT NULL DEFAULT 0,
  `currency` varchar(3) NOT NULL,
  `created_dt` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL,
  `rate` decimal(20,10) NOT NULL DEFAULT 1,
  `account_id_from` int(11) NOT NULL,
  `account_id_to` int(11) NOT NULL,
  `created_dt` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `migrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_dt` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `rates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency_from` varchar(3) NOT NULL,
  `currency_to` varchar(3) NOT NULL,
  `rate` numeric(20,10) NOT NULL DEFAULT 1,
  `created_dt` int(11) NOT NULL,
  PRIMARY KEY (`id`)
)  DEFAULT CHARSET=utf8  COLLATE=utf8_general_ci;

ALTER TABLE `rates` ADD UNIQUE INDEX `rate` (`currency_from`(3),`currency_to`(3));
ALTER TABLE `transactions` ADD COLUMN `status` enum('new','completed','declined') NOT NULL DEFAULT 'new' AFTER `account_id_to`;