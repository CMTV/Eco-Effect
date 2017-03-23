<?php
/** Загрузка необходимых для работы сайта файлов. */

// =====================================================================================================================
// Загрузка конфига.
// =====================================================================================================================
require_once('config.php');
require_once('amazon_config.php');

// =====================================================================================================================
// Загрузка модуля для создания логов.
// =====================================================================================================================
require_once('classes/Log.php');

// =====================================================================================================================
// Загрузка обработчика ошибок.
// =====================================================================================================================
require_once('classes/Error_Handler.php');

// =====================================================================================================================
// Установка соединения с базой данных.
// =====================================================================================================================
$db = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
$db->set_charset('utf8');

// =====================================================================================================================
// Подгрузка необходимых классов.
// =====================================================================================================================
require_once(ABSPATH . 'classes/Size.php');
require_once(ABSPATH . 'classes/Redirect.php');
require_once(ABSPATH . 'classes/Utils.php');
require_once(ABSPATH . 'classes/VK.php');
require_once(ABSPATH . 'classes/Photo.php');
require_once(ABSPATH . 'classes/User.php');
require_once(ABSPATH . 'classes/PhotoData.php');

// =====================================================================================================================
// Инициализаия менеджера сессий и объявление глобальной перменной $current_user.
// =====================================================================================================================
require_once(ABSPATH . 'classes/Session.php');
$session = new Session();

/** @var User|null $current_user */
$current_user = ($session->user)?:null;
if($current_user) $session->sync_user();