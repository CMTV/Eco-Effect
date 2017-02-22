<?php
/** Скрипт установки сайта. */

require_once('config.php');

/** Установка соединения с базой данных. */
$db = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
$db->set_charset('utf8');

/**
 * Создание таблицы пользователей.
 */
$db->query(
    "CREATE TABLE `eco_effect`.`users` ( `uid` INT NOT NULL , `first_name` VARCHAR(35) NOT NULL , `second_name` VARCHAR(35) NOT NULL , `domain` VARCHAR(35) NOT NULL , `is_banned` BOOLEAN NOT NULL , `has_avatar` BOOLEAN NOT NULL , `avatar` TEXT NOT NULL, `has_photo_0` BOOLEAN NOT NULL DEFAULT FALSE , `has_photo_1` BOOLEAN NOT NULL DEFAULT FALSE , PRIMARY KEY (`uid`), FULLTEXT(`first_name`, `second_name`, `domain`)) ENGINE = InnoDB;"
);

/**
 * Создание таблицы фотографий.
 */
$db->query(
    "CREATE TABLE `eco_effect`.`photos` ( `id` INT NOT NULL AUTO_INCREMENT , `uid` INT NOT NULL , `title` VARCHAR(255) NULL , `description` TEXT NULL , `has_marker` BOOLEAN NOT NULL , `latitude` FLOAT NULL DEFAULT NULL , `longitude` FLOAT NULL DEFAULT NULL , `address` VARCHAR(255) NULL , `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , `category` TINYINT NOT NULL , `has_thumbnail` BOOLEAN NOT NULL , `thumbnail_url` TEXT NULL , `photo_url` TEXT NOT NULL , `likes` INT NOT NULL DEFAULT '0' , PRIMARY KEY (`id`), INDEX (`uid`)) ENGINE = InnoDB;
");

$db->close();