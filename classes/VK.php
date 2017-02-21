<?php
/** Используемая версия VK API. */
define('VK_VERSION', 5.62);

/** Адрес перенаправления при авторизации ВКонтакте. */
define('VK_REDIRECT_URI', SITE_URL . 'registration.php');

/** Методы для работы с VK API. */
class VK {
    /**
     * Получение ссылки на авторизацию через ВКонтакте.
     *
     * @return string Ссылка на ВК сервер авторизации со всеми необходимыми параметрами.
     */
    public static function vk_authorize_link(): string {
        $client_ID =        VK_CLIENT_ID;
        $redirect_uri =     VK_REDIRECT_URI;
        $display =          'page';
        $response_type =    'code';
        $v =                VK_VERSION;

        return "https://oauth.vk.com/authorize?client_id=$client_ID&display=$display&redirect_uri=$redirect_uri&response_type=$response_type&v=$v";
    }

    /**
     * Получение access_token при авторизации ВКонтакте.
     *
     * @param string $code Специальный код, который передается ВКонтакте.
     *
     * @return string Ответ ВКонтакте.
     */
    public static function get_access_token(string $code): string {
        $client_ID =        VK_CLIENT_ID;
        $redirect_uri =     VK_REDIRECT_URI;
        $client_secret =    VK_SECRET;

        $vk_data = Utils::get_curl("https://oauth.vk.com/access_token?client_id=$client_ID&client_secret=$client_secret&redirect_uri=$redirect_uri&code=$code");

        return $vk_data;
    }

    /**
     * Получение данных пользователя от VK.
     *
     * @param string $access_token Ключ доступа к данным пользователя.
     * @return string Ответ ВКонтакте.
     */
    public static function get_user_data(string $access_token): string {
        $v =            VK_VERSION;
        $method_name =  'users.get';
        $parameters =   'fields=has_photo,photo_50,domain';

        $vk_data = Utils::get_curl("https://api.vk.com/method/$method_name?$parameters&access_token=$access_token&v=$v");

        return $vk_data;
    }
}