<?php
/**
 * Получение фотографий из базы данных.
 */

require '../load.php';

/** Получение последних загруженных фотографий. */
define('GET_LAST',      1);

/** Получение самых топовых фотографий. */
define('GET_TOP',       2);

/** Получение фотографий друзей. */
define('GET_FRIENDS',   3);

/** Получение своих и ближайших фотографий. */
define('GET_ME',        4);

/** Получение фотографий по поисковому запросу. */
define('GET_SEARCH',    5);

// =====================================================================================================================
// Получение и проверка данных.
// =====================================================================================================================

if(!is_numeric($_POST['get_type'])) {
    Error_Handler::ajax_error('Ошибка при обращении к серверу!', 'Параметр get_type не указан!');
}
$get_type = intval($_POST['get_type']);

if(!is_numeric($_POST['load_amount'])) {
    Error_Handler::ajax_error('Ошибка при обращении к серверу!', 'Параметр load_amount не указан!');
}
$load_amount = intval($_POST['load_amount']);

if(!is_numeric($_POST['start_at'])) {
    Error_Handler::ajax_error('Ошибка при обращении к серверу!', 'Параметр start_at не указан!');
}
$start_at = intval($_POST['start_at']);

if($get_type == GET_FRIENDS) {
    if(is_null($_POST['friends'])) {
        Error_Handler::ajax_error('Ошибка при обращении к серверу!', 'Параметр friends не указан!');
    }
    $friends = $_POST['friends'];
}

if($get_type == GET_SEARCH) {
    if(is_null($_POST['search_query'])) {
        Error_Handler::ajax_error('Ошибка при обращении к серверу', 'Параметр search_query не указан!');
    }
    $search_query = $_POST['search_query'];
}

// =====================================================================================================================
// Определение типа обращения к базе данных для получения фотографий.
// =====================================================================================================================

switch($get_type) {
    case GET_LAST:
        die(json_encode(get_last_photos()));
        break;
    case GET_TOP:
        die(json_encode(get_top_photos()));
        break;
    case GET_FRIENDS:
        die(json_encode(get_friends_photos()));
        break;
    case GET_SEARCH:
        die(json_encode(get_search_photos()));
        break;
}

// =====================================================================================================================
// Функции получения фотографий из базы данных.
// =====================================================================================================================

/**
 * Получение последних фотографий.
 *
 * @return array Массив с фотографиями.
 */
function get_last_photos() {
    global $db, $load_amount, $start_at;

    $photos_0_result = $db->query(
        "SELECT p.*, u.* FROM `photos` AS p JOIN `users` AS u ON p.`uid` = u.`uid` WHERE p.`category` = 0 ORDER BY p.`date` DESC LIMIT $start_at, $load_amount"
    );

    if($db->error) {
        Error_Handler::ajax_error('Ошибка получения фотографий с сервера!', $db->error);
    }

    $photos_0 = [];

    while($photo_0_row = $photos_0_result->fetch_assoc()) {
        $photos_0[] = (new PhotoData(Photo::get_photo_from_row($photo_0_row), User::get_user_from_row($photo_0_row, false)))->export();
    }

    $photos_1_result = $db->query(
        "SELECT p.*, u.* FROM `photos` AS p JOIN `users` AS u ON p.`uid` = u.`uid` WHERE p.`category` = 1 ORDER BY p.`date` DESC LIMIT $start_at, $load_amount"
    );

    if($db->error) {
        Error_Handler::ajax_error('Ошибка получения фотографий с сервера!', $db->error);
    }

    $photos_1 = [];

    while($photo_1_row = $photos_1_result->fetch_assoc()) {
        $photos_1[] = (new PhotoData(Photo::get_photo_from_row($photo_1_row), User::get_user_from_row($photo_1_row, false)))->export();
    }

    $photos = [
        'cat_0' =>      $photos_0,
        'cat_1' =>      $photos_1
    ];

    return $photos;
}

/**
 * Получение самых топовых фотографий.
 *
 * @return array Массив с фотографиями.
 */
function get_top_photos() {
    global $db, $load_amount, $start_at;

    $photos_0_result = $db->query(
        "SELECT p.*, u.* FROM `photos` AS p JOIN `users` AS u ON p.`uid` = u.`uid` WHERE p.`category` = 0 ORDER BY p.`likes` DESC LIMIT $start_at, $load_amount"
    );

    if($db->error) {
        Error_Handler::ajax_error('Ошибка получения фотографий с сервера!', $db->error);
    }

    $photos_0 = [];

    while($photo_0_row = $photos_0_result->fetch_assoc()) {
        $photos_0[] = (new PhotoData(Photo::get_photo_from_row($photo_0_row), User::get_user_from_row($photo_0_row, false)))->export();
    }

    $photos_1_result = $db->query(
        "SELECT p.*, u.* FROM `photos` AS p JOIN `users` AS u ON p.`uid` = u.`uid` WHERE p.`category` = 1 ORDER BY p.`likes` DESC LIMIT $start_at, $load_amount"
    );

    if($db->error) {
        Error_Handler::ajax_error('Ошибка получения фотографий с сервера!', $db->error);
    }

    $photos_1 = [];

    while($photo_1_row = $photos_1_result->fetch_assoc()) {
        $photos_1[] = (new PhotoData(Photo::get_photo_from_row($photo_1_row), User::get_user_from_row($photo_1_row, false)))->export();
    }

    $photos = [
        'cat_0' =>      $photos_0,
        'cat_1' =>      $photos_1
    ];

    return $photos;
}

/**
 * Получение фотографий друзей.
 *
 * @return array Массив с фотографиями.
 */
function get_friends_photos() {
    global $db, $friends, $load_amount, $start_at;

    $photos_0_result = $db->query(
        "SELECT p.*, u.* FROM `photos` AS p JOIN `users` AS u ON p.`uid` = u.`uid` WHERE p.`category` = 0 AND p.`uid` IN ( " . implode(',', array_map('intval', $friends)) . ") LIMIT $start_at, $load_amount"
    );

    if($db->error) {
        Error_Handler::ajax_error('Ошибка получения фотографий с сервера!', $db->error);
    }

    $photos_0 = [];

    while($photo_0_row = $photos_0_result->fetch_assoc()) {
        $photos_0[] = (new PhotoData(Photo::get_photo_from_row($photo_0_row), User::get_user_from_row($photo_0_row, false)))->export();
    }

    $photos_1_result = $db->query(
        "SELECT p.*, u.* FROM `photos` AS p JOIN `users` AS u ON p.`uid` = u.`uid` WHERE p.`category` = 1 AND p.`uid` IN ( " . implode(',', array_map('intval', $friends)) . ") LIMIT $start_at, $load_amount"
    );

    if($db->error) {
        Error_Handler::ajax_error('Ошибка получения фотографий с сервера!', $db->error);
    }

    $photos_1 = [];

    while($photo_1_row = $photos_1_result->fetch_assoc()) {
        $photos_1[] = (new PhotoData(Photo::get_photo_from_row($photo_1_row), User::get_user_from_row($photo_1_row, false)))->export();
    }

    $photos = [
        'cat_0' =>      $photos_0,
        'cat_1' =>      $photos_1
    ];

    return $photos;
}

/**
 * Получение фотографий по поисковому запросу.
 *
 * @return array Массив с фотографиями.
 */
function get_search_photos() {
    global $search_query;

    if(is_numeric(str_replace(' ','',$search_query))) {
        return search_by_uid(intval(str_replace(' ','',$search_query)));
    } else {
        return search_by_string($search_query);
    }
}

// =====================================================================================================================
// Дополнительные функции.
// =====================================================================================================================

/**
 * Поиск участников конкурса по идентификатору ВКонтакте.
 *
 * @param int $uid Идентификатор ВКонтакте участника.
 *
 * @return array Массив с фотографиями.
 */
function search_by_uid(int $uid) {
    global $db, $load_amount, $start_at;

    $photos_0_result = $db->query(
        "SELECT p.*, u.* FROM `photos` AS p JOIN `users` AS u ON p.`uid` = u.`uid` WHERE p.`category` = 0 AND p.`uid` = $uid LIMIT $start_at, $load_amount"
    );

    if($db->error) {
        Error_Handler::ajax_error('Ошибка получения фотографий с сервера!', $db->error);
    }

    $photos_0 = [];

    while($photo_0_row = $photos_0_result->fetch_assoc()) {
        $photos_0[] = (new PhotoData(Photo::get_photo_from_row($photo_0_row), User::get_user_from_row($photo_0_row, false)))->export();
    }

    $photos_1_result = $db->query(
        "SELECT p.*, u.* FROM `photos` AS p JOIN `users` AS u ON p.`uid` = u.`uid` WHERE p.`category` = 1 AND p.`uid` = $uid LIMIT $start_at, $load_amount"
    );

    if($db->error) {
        Error_Handler::ajax_error('Ошибка получения фотографий с сервера!', $db->error);
    }

    $photos_1 = [];

    while($photo_1_row = $photos_1_result->fetch_assoc()) {
        $photos_1[] = (new PhotoData(Photo::get_photo_from_row($photo_1_row), User::get_user_from_row($photo_1_row, false)))->export();
    }

    $photos = [
        'cat_0' =>      $photos_0,
        'cat_1' =>      $photos_1
    ];

    return $photos;
}

/**
 * Поиск участников по строке.
 *
 * @param string $search_query Строка поиска.
 *
 * @return array Массив с фотографиями.
 */
function search_by_string(string $search_query) {
    global $db, $load_amount, $start_at;

    $search_query = trim(preg_replace('/[+\-><\(\)~*\"@]+/', '', $search_query));

    $old_search_terms = explode(' ', $search_query);
    $search_terms = [];
    foreach($old_search_terms as $search_term) {
        if(empty($search_term)) {
            continue;
        }

        $search_terms[] = trim(preg_replace('/[+\-><\(\)~*\"@]+/', '', $search_term)) . '*';
    }

    $search = $db->real_escape_string(implode(' ', $search_terms));

    $uids_result = $db->query(
        "SELECT `uid` FROM `users` WHERE MATCH(`first_name`, `second_name`, `domain`) AGAINST ('$search' IN BOOLEAN MODE)"
    );

    if($db->error) {
        Error_Handler::ajax_error('Ошбика при получении данных из базы данных!', $db->error);
    }

    $uids = [];
    while($uid_row = $uids_result->fetch_assoc()) {
        $uids[] = $uid_row['uid'];
    }
    $uids = implode(', ', $uids);

    if(!$uids) {
        return ['cat_0' => [], 'cat_1' => []];
    }

    $photos_0_result = $db->query(
        "SELECT p.*, u.* FROM `photos` AS p JOIN `users` AS u ON p.`uid` = u.`uid` WHERE p.`category` = 0 AND p.`uid` IN ($uids) LIMIT $start_at, $load_amount"
    );

    if($db->error) {
        Error_Handler::ajax_error('Ошибка получения фотографий с сервера!', $db->error);
    }

    $photos_0 = [];

    while($photo_0_row = $photos_0_result->fetch_assoc()) {
        $photos_0[] = (new PhotoData(Photo::get_photo_from_row($photo_0_row), User::get_user_from_row($photo_0_row, false)))->export();
    }

    $photos_1_result = $db->query(
        "SELECT p.*, u.* FROM `photos` AS p JOIN `users` AS u ON p.`uid` = u.`uid` WHERE p.`category` = 1 AND p.`uid` IN ($uids) LIMIT $start_at, $load_amount"
    );

    if($db->error) {
        Error_Handler::ajax_error('Ошибка получения фотографий с сервера!', $db->error);
    }

    $photos_1 = [];

    while($photo_1_row = $photos_1_result->fetch_assoc()) {
        $photos_1[] = (new PhotoData(Photo::get_photo_from_row($photo_1_row), User::get_user_from_row($photo_1_row, false)))->export();
    }

    $photos = [
        'cat_0' =>      $photos_0,
        'cat_1' =>      $photos_1
    ];

    return $photos;
}