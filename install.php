<?php
/** Скрипт установки сайта. */

require_once('config.php');

if(is_file('install.lock')) {
    Error_Handler::error('Скрипт установки заблокирован!', 'Удалите файл install.lock из корневой директории сайта!');
}

/** Установка соединения с базой данных. */
$db = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
$db->set_charset('utf8');

/**
 * Создание таблицы участников.
 */
$db->query(
    "CREATE TABLE `users` ( `uid` int(11) NOT NULL, `first_name` varchar(35) NOT NULL, `second_name` varchar(35) NOT NULL, `domain` varchar(35) NOT NULL, `is_banned` tinyint(1) NOT NULL DEFAULT '0', `has_avatar` tinyint(1) NOT NULL, `avatar` text NOT NULL, `has_photo_0` tinyint(1) NOT NULL DEFAULT '0', `has_photo_1` tinyint(1) NOT NULL DEFAULT '0', PRIMARY KEY (`uid`), FULLTEXT KEY `first_name` (`first_name`,`second_name`,`domain`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
);

/**
 * Создание таблицы фотографий.
 */
$db->query(
    "CREATE TABLE `photos` ( `id` int(11) NOT NULL AUTO_INCREMENT, `uid` int(11) NOT NULL, `title` varchar(255) DEFAULT NULL, `description` text, `has_marker` tinyint(1) NOT NULL, `latitude` float DEFAULT NULL, `longitude` float DEFAULT NULL, `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP, `category` tinyint(4) NOT NULL, `has_thumbnail` tinyint(1) NOT NULL, `thumbnail_url` text NOT NULL, `photo_url` text NOT NULL, `likes` int(11) NOT NULL DEFAULT '0', `photo_size` blob NOT NULL, `thumb_size` blob NOT NULL, PRIMARY KEY (`id`), KEY `uid` (`uid`) ) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8ж"
);

/**
 * Создание таблиц победителей.
 */
$db->multi_query(
    "
    CREATE TABLE `winners_0` ( `id` int(11) NOT NULL AUTO_INCREMENT, `pid` int(11) DEFAULT NULL, `uid` int(11) DEFAULT NULL, `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`) ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
    CREATE TABLE `winners_1` ( `id` int(11) NOT NULL AUTO_INCREMENT, `pid` int(11) DEFAULT NULL, `uid` int(11) DEFAULT NULL, `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`) ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
    "
);

/**
 * Включение планировщика.
 */
$db->query(
    "SET GLOBAL event_scheduler = ON;"
);

/**
 * Ежедневное определение победителей.
 */
$db->multi_query(
    "delimiter | CREATE EVENT get_winners ON SCHEDULE EVERY " . GET_WINNER_HOUR . " DAY_HOUR DO BEGIN INSERT INTO `winners_0` (`pid`, `uid`, `date`) SELECT `id`, `uid`, `date` FROM `photos` WHERE `category` = 0 ORDER BY `likes` DESC LIMIT 1; INSERT INTO `winners_1` (`pid`, `uid`, `date`) SELECT `id`, `uid`, `date` FROM `photos` WHERE `category` = 1 ORDER BY `likes` DESC LIMIT 1; END | delimiter ;"
);

$db->close();

/**
 * Добавление файла, блокирующего повторный запуск скрипта установки.
 */
file_put_contents('install.lock', '');