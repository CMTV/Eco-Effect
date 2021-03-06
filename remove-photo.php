<?php
/**
 * Удаление загруженной участником фотографии.
 */

require_once('load.php');

if(!$session->is_authorized()) {
    Error_Handler::forbid();
}

if(!is_numeric($_GET['category'])) {
    Error_Handler::forbid();
}

$category = $_GET['category'];

// =====================================================================================================================
// Существует ли вообще удаляемая фотография?
// =====================================================================================================================

if($category) {
    if(!$current_user->has_photo_1) {
        Error_Handler::error('Вы не загружали фотографию в этой номинации!', null, false);
    }
} else {
    if(!$current_user->has_photo_0) {
        Error_Handler::error('Вы не загружали фотографию в этой номинации!', null, false);
    }
}

// =====================================================================================================================
// Удаление фотографии из Amazon облака.
// =====================================================================================================================

require(ABSPATH . 'amazon/aws.phar');
require(ABSPATH . 'classes/Amazon.php');

$amazon = new Amazon();

$amazon->remove_photo($current_user->uid, $category);

// =====================================================================================================================
// Удаление фотографии из базы данных.
// =====================================================================================================================

if($category) {
    Photo::remove_photo($current_user->photo_1->id);
} else {
    Photo::remove_photo($current_user->photo_0->id);
}

$current_user->removed_photo($category);

// =====================================================================================================================
// Синхронизация и перенаправление на главную страницу.
// =====================================================================================================================

$session->sync_user();

Redirect::redirect_to(REDIRECT_INDEX);
