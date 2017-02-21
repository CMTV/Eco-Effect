<?php
/** Загрузка необходимых для работы сайта файлов. */

/** Загрузка конфига. */
require_once('config.php');

/** Загрузка модуля для создания логов. */
require_once('classes/Log.php');

/** Загрузка обработчика ошибок */
require_once('classes/Error_Handler.php');

/** Установка соединения с базой данных. */
$db = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
$db->set_charset('utf8');

/** Подгрузка необходимых классов */
require_once(ABSPATH . 'classes/Photo.php');
require_once(ABSPATH . 'classes/User.php');