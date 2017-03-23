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

    /**
     * Получение ссылки "Поделиться" для данной фотографии.
     *
     * @param int|Photo $photo Идентификатор или объект фотографии, которой надо поделиться.
     * @return string Ссылка на фотографию.
     */
    public static function get_share_link($photo): string {

        if(is_int($photo)) {
            $photo = Photo::get_photo_by_id($photo);
        }

        if(!$photo) {
            return '';
        }

        $user = User::get_user($photo->uid);

        $url = SITE_URL . '?photo=' . $photo->id;
        $title = $user->full_name() . '. Проголосовать за фото.';
        $description = 'Проголосуйте за мою фотографию на конкурсе ЭКО-ЭФФЕКТ!';
        $image = $photo->thumbnail_url;

        return "
            <script>
                document.write(
                    VK.Share.button({
                        url: \"$url\",
                        title: '$title',
                        description: '$description',
                        image: '$image',
                        noparse: true
                    }, {
                        type: 'custom',
                        text: '<div class=\"profile-photo-share\" title=\"Поделиться фотографией!\"><i class=\"fa fa-bullhorn\" aria-hidden=\"true\"></i></div>'
                    })
                );
            </script>
        ";
    }

    /**
     * Получение информации о лайках на данную фотографию.
     *
     * @param int $photo_id Идентификатор фотографии в базе данных.
     *
     * @return string Ответ ВКонтакте.
     */
    public static function get_votes(int $photo_id) : string {
        $v =            VK_VERSION;
        $method_name =  'likes.getList';
        $page_url =     urlencode(SITE_URL . 'index.php?photo=' . $photo_id . '&salt=' . VK_WIDGETS_SALT);
        $parameters =  "type=sitepage&owner_id=" . VK_CLIENT_ID . "&v=$v&page_url=$page_url";

        $vk_data = file_get_contents("https://api.vk.com/method/$method_name?$parameters");

        return $vk_data;
    }
}