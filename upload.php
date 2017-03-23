<?php
/** Скрипт загрузки файлов на Amazon S3. */

/** Типы построения формы. */
define('PHOTO_ADD', 0);
define('PHOTO_EDIT', 1);

/** Допустимые расширения загружаемых файлов. */
define('ALLOWED_IMAGE_TYPES', [
    IMAGETYPE_PNG,
    IMAGETYPE_JPEG,
    IMAGETYPE_GIF
]);

require_once('load.php');

if(!$session->is_authorized()) {
    Error_Handler::forbid();
}

// =====================================================================================================================
// Получение и проверка данных от формы.
// =====================================================================================================================

/* --------------------------------------------------------------------------------------- */
/* Техническая информация от сервера. */
/* --------------------------------------------------------------------------------------- */
if($_POST['category'] != '0' && $_POST['category'] != '1') {
    Error_Handler::error('Некорретное заполнение формы!', 'Не передано поле category!', false);
}

$category = intval($_POST['category']);

if($_POST['type'] != '0' && $_POST['type'] != '1') {
    Error_Handler::error('Некорретное заполнение формы!', 'Не передано поле type!', false);
}

$type = intval($_POST['type']);

$photo_id = null;
if($type ==PHOTO_EDIT) {
    if(!is_numeric($_POST['photo-id'])) {
        Error_Handler::error('Не указан идентификатор редактируемой фотографии.', null, false);
    } else {
        $photo_id = intval($_POST['photo-id']);
    }
}

/* --------------------------------------------------------------------------------------- */
/* Маркер на карте: широта и долгота. */
/* --------------------------------------------------------------------------------------- */
if(!is_numeric($_POST['latitude']) || !is_numeric($_POST['longitude'])) {
    $has_marker = false;
    $latitude = $longitude = null;
} else {
    $has_marker = true;
    $latitude = floatval($_POST['latitude']);
    $longitude = floatval($_POST['longitude']);
}

/* --------------------------------------------------------------------------------------- */
/* Дополнительные данные к фотографии: заголовок и описание. */
/* --------------------------------------------------------------------------------------- */
$title =        ( trim($_POST['title']) ?: null );
$description =  ( trim($_POST['description']) ?: null );

// =====================================================================================================================
// Обновление данных фотографии.
// =====================================================================================================================
if($type == PHOTO_EDIT) {
    if(!User::has_photo($current_user, $category)) {
        Error_Handler::error('У вас нет фотографии в этой номинации! Нечего редактировать!', null, false);
    }

    Photo::edit_photo($photo_id, [
        'has_marker'    => $has_marker,
        'latitude'      => $latitude,
        'longitude'     => $longitude,
        'title'         => $title,
        'description'   => $description
    ]);

    $current_user->loaded_photo($category);
    $session->sync_user();

    Redirect::redirect_to(REDIRECT_INDEX);
}

// =====================================================================================================================
// Проверка на то, что фотография уже была загружена.
// =====================================================================================================================
if(User::has_photo($current_user, $category)) {
    Error_Handler::error(
        'У вас уже есть фотография в данной номинации! Удалите предыдущую, прежде чем загружать новую!', null, false);
}

// =====================================================================================================================
// Проверка загруженного файла.
// =====================================================================================================================
if(!$_FILES['photo']['tmp_name']) {
    Error_Handler::error('Не удалось загрузить фотографию!', 'Пустое содержимое tmp_name', false);
}

if(!$_FILES['photo']['size'] > MAX_IMG_SIZE) {
    Error_Handler::error('Размер фотографии слишком большой!', 'size > MAX_IMG_SIZE', false);
}

switch ($_FILES['photo']['error']) {
    case UPLOAD_ERR_INI_SIZE:
        Error_Handler::error('Размер фотографии слишком большой!', 'Код ошибки: ' . (int)UPLOAD_ERR_INI_SIZE, false);
        break;
    case UPLOAD_ERR_FORM_SIZE:
        Error_Handler::error('Размер фотографии слишком большой!', 'Код ошибки: ' . (int)UPLOAD_ERR_FORM_SIZE, false);
        break;
    case UPLOAD_ERR_PARTIAL:
        Error_Handler::error('Фото было загружено только частично!', 'Код ошибки: ' . (int)UPLOAD_ERR_PARTIAL, false);
        break;
    case UPLOAD_ERR_NO_FILE:
        Error_Handler::error('Фото не был загружено!', 'Код ошибки: ' . (int)UPLOAD_ERR_NO_FILE, false);
        break;
    case UPLOAD_ERR_NO_TMP_DIR:
        Error_Handler::error('Не удалось загрузить фотографию!', 'Код ошибки' . (int)UPLOAD_ERR_NO_TMP_DIR, false);
        break;
    case UPLOAD_ERR_CANT_WRITE:
        Error_Handler::error('Не удалось загрузить фотографию!', 'Код ошибки' . (int)UPLOAD_ERR_CANT_WRITE, false);
        break;
    case UPLOAD_ERR_EXTENSION:
        Error_Handler::error('Не удалось загрузить фотографию!', 'Код ошибки' . (int)UPLOAD_ERR_EXTENSION, false);
        break;
}

$photo_type = exif_imagetype($_FILES['photo']['tmp_name']);

if(!in_array($photo_type, ALLOWED_IMAGE_TYPES)) {
    Error_Handler::error('Недопустимый тип загружаемого фото! Разрешенные типы: PNG, JPEG, GIF.', null, false);
}

$photo_data = getimagesize($_FILES['photo']['tmp_name']);

$width =    $photo_data[0];
$height =   $photo_data[1];

if($width + $height > MAX_WIDTH_HEIGHT_SUM) {
    Error_Handler::error('Высота и/или ширина изображения слишком большие!', null, false);
}

// =====================================================================================================================
// Загрузка фотографии и миниатюры на Amazon.
// =====================================================================================================================
require(ABSPATH . 'amazon/aws.phar');
require(ABSPATH . 'classes/Amazon.php');

$amazon = new Amazon();

$has_thumbnail = false;
$thumbnail_url = null;
$thumbnail = null;
if($width > THUMBNAIL_WIDTH) {
    $has_thumbnail = true;
    $thumbnail = Utils::make_thumbnail($_FILES['photo']['tmp_name']);
    $thumbnail_url = $amazon->upload_thumbnail($category, $thumbnail['image'], Utils::get_image_extension($photo_type));
}

$photo_url = $amazon->upload_photo($category, $_FILES['photo']['tmp_name'], Utils::get_image_extension($photo_type));

// =====================================================================================================================
// Загрузка данных фотографии в базу данных.
// =====================================================================================================================
$new_photo = new Photo();

$new_photo->uid =                   $current_user->uid;
$new_photo->category =              $category;
$new_photo->photo_url =             $photo_url;
$new_photo->has_thumbnail =         $has_thumbnail;
$new_photo->thumbnail_url =         $thumbnail_url;
$new_photo->has_marker =            $has_marker;
$new_photo->latitude =              $latitude;
$new_photo->longitude =             $longitude;
$new_photo->title =                 $title;
$new_photo->description =           $description;
$new_photo->photo_size->width =     $width;
$new_photo->photo_size->height =    $height;
$new_photo->thumb_size->width =     $thumbnail['size']['width'];
$new_photo->thumb_size->height =    $thumbnail['size']['height'];

Photo::create_photo($new_photo);

$current_user->loaded_photo($category);
$session->sync_user();

Redirect::redirect_to(REDIRECT_INDEX);