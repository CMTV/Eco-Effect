<?php
/** Регистрация/Авторизация участника конкурса через ВКонтакте. */

require_once('load.php');

if($session->is_authorized()) {
    Redirect::redirect_to(REDIRECT_PROFILE);
}

if(!($code = $_GET['code'])) {
    Error_Handler::forbid();
}

// =====================================================================================================================
// Получение данных пользователя от VK API.
// =====================================================================================================================
$vk_access_token_data = json_decode(VK::get_access_token($code));

if($vk_access_token_data->error) {
    Error_Handler::error('Не удалось авторизоваться с помощью ВКонтакте!', $vk_access_token_data->error_description);
}

$access_token = $vk_access_token_data->access_token;

$vk_user_data = json_decode(VK::get_user_data($access_token));

if($vk_user_data->error) {
    Error_Handler::error('Не удалось авторизоваться с помощью ВКонтакте!', $vk_user_data->error->error_msg);
}

$vk_user_data = $vk_user_data->response[0];

// =====================================================================================================================
// Создание/обновление участника и перенаправление его в профиль (или в ошибку, если он забанен).
// =====================================================================================================================
if(User::is_user_exists($vk_user_data->id)) {
    if(User::is_user_banned($vk_user_data->id)) {
        Error_Handler::error('Вы заблокированы и не можете участвовать в конкурсе!', null, false);
    }
    User::update_user(
        $vk_user_data->id,
        $vk_user_data->first_name,
        $vk_user_data->last_name,
        $vk_user_data->domain,
        $vk_user_data->has_photo,
        ($vk_user_data->has_photo) ? $vk_user_data->photo_50 : AVATAR_DEFAULT_URL
    );
} else {
    User::create_user(
        $vk_user_data->id,
        $vk_user_data->first_name,
        $vk_user_data->last_name,
        $vk_user_data->domain,
        $vk_user_data->has_photo,
        ($vk_user_data->has_photo) ? $vk_user_data->photo_50 : AVATAR_DEFAULT_URL
    );
}

$session->init(User::get_user($vk_user_data->id));

Redirect::redirect_to(REDIRECT_PROFILE);