<?php
/** Скрипт загрузки файлов на Amazon S3. */

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
// Получение и проверка данных от формы. @todo Протестировать на различных данных, когда форма будет готова!
// =====================================================================================================================
$category = null;
if(((int)$_POST['category']) != CATEGORY_TRASH_NO && ((int)$_POST['category']) != CATEGORY_TRASH_YES) {
    Error_Handler::forbid();
}
$category =     (int)$_POST['category'];

$title =        (!empty($_POST['title'])?:null);
$description =  (!empty($_POST['description'])?:null);

$has_marker = null;
$latitude = null;
$longitude = null;
if(!is_null($_POST['has_marker'])) {
    $has_marker = (bool)$_POST['has_marker'];

    if($has_marker) {
        if(is_null($_POST['latitude']) || is_null($_POST['longitude'])) {
            Error_Handler::error('Маркер включен, а его координаты не передаются!', null, false);
        }

        $latitude = (float)$_POST['latitude'];
        $longitude = (float)$_POST['longitude'];
    } else {
        if(!is_null($_POST['latitude']) || !is_null($_POST['longitude'])) {
            Error_Handler::error('Маркер отключен, а его координаты передаются!', null, false);
        }
    }
} else {
    $has_marker = false;
    if(!is_null($_POST['latitude']) || !is_null($_POST['longitude'])) {
        Error_Handler::error('Маркер отключен, а его координаты передаются!', null, false);
    }
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
if($width > THUMBNAIL_WIDTH) {
    $has_thumbnail = true;
    $thumbnail_url = $amazon->upload_thumbnail($category, Utils::make_thumbnail($_FILES['photo']['tmp_name']),
        Utils::get_image_extension($photo_type));
}

$photo_url = $amazon->upload_photo($category, $_FILES['photo']['tmp_name'], Utils::get_image_extension($photo_type));

// =====================================================================================================================
// Загрузка данных фотографии в базу данных.
// =====================================================================================================================
Photo::create_photo(
    $current_user->uid,
    $category,
    $photo_url,
    $has_thumbnail,
    $thumbnail_url,
    $has_marker,
    $latitude,
    $longitude,
    $title,
    $description
);
$current_user->loaded_photo($category);
$session->sync_user();

Redirect::redirect_to(REDIRECT_PROFILE);