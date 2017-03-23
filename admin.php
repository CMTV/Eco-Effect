<?php
/** Действия администраторов. */

require_once 'load.php';

/* ================================================================================================================== */
/* Проверка прав доступа и присутствия всех необходимых параметров. */
/* ================================================================================================================== */

if(!$session->is_admin()) {
    Error_Handler::forbid();
}

if(!is_numeric($_GET['photo'])) {
    Error_Handler::forbid();
}

if(!is_numeric($_GET['uid'])) {
    Error_Handler::forbid();
}

if(empty($_GET['action'])) {
    Error_Handler::forbid();
}

$photo = intval($_GET['photo']);
$uid =  intval($_GET['uid']);
$action = $_GET['action'];

/* ================================================================================================================== */
/* Выполнение действия администратора. */
/* ================================================================================================================== */

switch ($action) {
    case 'delete':
        $photo = Photo::get_photo_by_id($photo);

        require(ABSPATH . 'amazon/aws.phar');
        require(ABSPATH . 'classes/Amazon.php');

        $amazon = new Amazon();

        $amazon->remove_photo($uid, $photo->category);

        Photo::remove_photo($photo->id);

        User::edit_user($uid, ['has_photo_' . $photo->category => false]);
        Redirect::redirect_to(REDIRECT_INDEX, 'Фотография успешно удалена!');
        break;
    case 'ban':
        $user = User::get_user($uid);

        require(ABSPATH . 'amazon/aws.phar');
        require(ABSPATH . 'classes/Amazon.php');

        $amazon = new Amazon();

        if($user->has_photo_0) {
            $amazon->remove_photo($uid, CATEGORY_TRASH_NO);
            Photo::remove_photo($user->photo_0->id);
        }

        if($user->has_photo_1) {
            $amazon->remove_photo($uid, CATEGORY_TRASH_YES);
            Photo::remove_photo($user->photo_1->id);
        }

        User::edit_user($uid, ['has_photo_1' => false, 'has_photo_0' => false, 'is_banned' => true]);
        Redirect::redirect_to(REDIRECT_INDEX, 'Участник успешно забанен!');
        break;
}