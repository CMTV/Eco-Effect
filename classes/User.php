<?php
/** Путь к стандартной аватаре участника. */
define('AVATAR_DEFAULT_URL', SITE_URL . 'avatars/default.png');

/** Класс, содержащий данные участника конкурса. */
class User {
    /** @var int Индентификатор пользователя ВКонтакте. */
    public $uid;

    /** @var string Имя участника. */
    public $first_name;

    /** @var string Фамилия участника. */
    public $second_name;

    /** @var bool Заблокирован ли участник в конкурсе. */
    public $is_banned;

    /**
     * Короткий и уникальный домен страницы пользователя ВКонтакте. Возвращается строка, содержащая короткий адрес
     * страницы (например, *andrew*). Если он не назначен, возвращается **"id"+user_id**, например, *id35828305*.
     *
     * @var string
     */
    public $domain;

    /** @var bool Имеется ли у пользователя аватара ВКонтакте. */
    public $has_avatar;

    /**
     * Ссылка на загруженную аватару участника конкурса.
     * Если аватары у участника нет, то указывается стандартная аватара.
     *
     * @see AVATAR_DEFAULT_URL
     *
     * @var string
     */
    public $avatar;

    /** @var bool Есть ли у участника загруженное фото в категории "Мусору НЕТ" */
    public $has_photo_0;

    /**
     * Экземпляр класса {@see Photo} в номинации "Мусору НЕТ".
     * Если {@see User::$has_photo_0} равно 0, то содержит null.
     *
     * @see Photo
     *
     * @var Photo|null
     */
    public $photo_0 = null;

    /** @var bool Есть ли у участника загруженное фото в категории "Мусор ЕСТЬ" */
    public $has_photo_1;

    /**
     * Экземпляр класса {@see Photo} в номинации "Мусор ЕСТЬ".
     * Если {@see User::$has_photo_1} равно 0, то содержит null.
     *
     * @see Photo
     *
     * @var Photo|null
     */
    public $photo_1 = null;

    /**
     * Получение ссылки на VK профиль пользователя.
     *
     * @param user|int $user Объект участника конкурса или его идентификатор.
     *
     * @return string|null Может вернуть null, если переданный параметр не является ни идентификатором, ни объектом
     * пользователя, а также в том случае, если по данному идентификатору пользователь найден не был.
     */
    public static function get_vk_link($user): ?string {
        $link = 'https://vk.com/';

        if($user instanceof User) {
            return $link . $user->domain;
        }

        if(is_int($user)) {
            if($user = self::get_user($user)) {
                return $link . $user->domain;
            }
        }

        return null;
    }

    /**
     * Получение участника конкурса из базы данных.
     *
     * @param int $uid Идентификатор ВКонтакте участника.
     *
     * @return User|null Возвращает объект класса {@see User} или null, если пользователь был не найден.
     */
    public static function get_user(int $uid): ?User {
        global $db;

        $user = new User();

        $user_result = $db->query(
            "SELECT * FROM `users` WHERE `uid` = $uid"
        );

        if($db->error) {
            Error_Handler::error('Не удалось получить пользователя из базы данных!', $db->error);
        }

        if($user_result->num_rows == 0) {
            return null;
        }

        $user_data = $user_result->fetch_assoc();

        $user->uid =            $uid;
        $user->first_name =     $user_data['first_name'];
        $user->second_name =    $user_data['second_name'];
        $user->domain =         $user_data['domain'];
        $user->is_banned =      $user_data['is_banned'];
        $user->has_avatar =     $user_data['has_avatar'];
        $user->avatar =         $user_data['avatar'];
        $user->has_photo_0 =    $user_data['has_photo_0'];
        $user->has_photo_1 =    $user_data['has_photo_1'];
        $user->photo_0 =        Photo::get_photo($uid, CATEGORY_TRASH_NO);
        $user->photo_1 =        Photo::get_photo($uid, CATEGORY_TRASH_YES);

        return $user;
    }

    /**
     * Создание пользователя и добавление его в базу данных.
     *
     * @param int $uid Идентификатор ВКонтакте пользователя.
     * @param string $first_name Имя пользователя.
     * @param string $second_name Фамилия пользователя.
     * @param string $domain Короткое доменное имя пользователя.
     * @param bool $has_avatar Есть ли у пользователя аватара. При указании true необходимо в обязательном порядке
     * указать параметр $avatar.
     * @param string|null $avatar Ссылка на аватару.
     */
    public static function create_user(int $uid, string $first_name, string $second_name, string $domain, bool $has_avatar, string $avatar = AVATAR_DEFAULT_URL) {
        global $db;

        $first_name =     $db->real_escape_string($first_name);
        $second_name =    $db->real_escape_string($second_name);
        $domain =         $db->real_escape_string($domain);

        $has_avatar = (int)$has_avatar;

        if($has_avatar) {
            $avatar = $db->real_escape_string($avatar);
        }

        $is_banned = $has_photo_0 = $has_photo_1 = 0;

        $db->query(
            "INSERT INTO `users` (`uid`, `first_name`, `second_name`, `is_banned`, `domain`, `has_avatar`, `avatar`, `has_photo_0`, `has_photo_1`) "
          . "VALUES ($uid, '$first_name', '$second_name', $is_banned, '$domain', $has_avatar, '$avatar', $has_photo_0, $has_photo_1)"
        );

        if($db->error) {
            Error_Handler::error('Не удалось добавить пользователя в базу данных!', $db->error);
        }
    }
}