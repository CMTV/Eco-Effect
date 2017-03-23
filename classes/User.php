<?php
/** Путь к стандартной аватаре участника. */
define('AVATAR_DEFAULT_URL', SITE_URL . 'images/default_avatar.gif');

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

    /** @return string Полное имя участника конкурса. */
    public function full_name() {
        return $this->first_name . ' ' . $this->second_name;
    }

    /**
     * Пометка в базе данных о том, что пользователь загрузил фотографию в данной номинации.
     *
     * @param int $category Номинация загруженной фотографии.
     */
    public function loaded_photo(int $category) {
        global $db;

        if($category == CATEGORY_TRASH_NO)
            $db->query("UPDATE `users` SET `has_photo_0` = 1 WHERE `uid` = $this->uid");
        elseif($category == CATEGORY_TRASH_YES)
            $db->query("UPDATE `users` SET `has_photo_1` = 1 WHERE `uid` = $this->uid");

        if($db->error) {
            Error_Handler::error('Не удалось указать, что участник загрузил фотографию!', $db->error);
        }
    }

    /**
     * Пометка в базе данных о том, что пользователь удалил фотографию в данной номинации.
     *
     * @param int $category Номинация удаленной фотографии.
     */
    public function removed_photo(int $category) {
        global $db;

        if($category == CATEGORY_TRASH_NO)
            $db->query("UPDATE `users` SET `has_photo_0` = 0 WHERE `uid` = $this->uid");
        elseif($category == CATEGORY_TRASH_YES)
            $db->query("UPDATE `users` SET `has_photo_1` = 0 WHERE `uid` = $this->uid");

        if($db->error) {
            Error_Handler::error('Не удалось указать, что участник удалил фотографию!', $db->error);
        }
    }

    /**
     * Возвращает фотографию пользователя в заданной категории. Если фотографии в категории нет, то возвращает null.
     *
     * @param int $category Номинация загруженной фотографии.
     * @return null|Photo Объект фотографии пользователя
     */
    public function get_photo(int $category): ?Photo {
        if($category == CATEGORY_TRASH_NO) {
            if($this->has_photo_0) {
                return $this->photo_0;
            } else {
                return null;
            }
        } elseif($category == CATEGORY_TRASH_YES) {
            if($this->has_photo_1) {
                return $this->photo_1;
            } else {
                return null;
            }
        }
        return null;
    }

    /**
     * Есть ли у участника загруженная фотография в данной категории.
     *
     * @param int $category Номинация загруженной фотографии.
     * @return bool true - фотография есть, false - фотографии нет или неверная категория.
     */
    public function has_self_photo(int $category): bool {
        if($category == CATEGORY_TRASH_NO) {
            return $this->has_photo_0;
        } elseif($category == CATEGORY_TRASH_YES) {
            return $this->has_photo_1;
        }
        return false;
    }

    /**
     * Имеется ли у участника фотография в заданной номинации.
     *
     * @param User|int $user Объект или идентификатор ВКонтакте пользователя, для которого осуществляется проверка.
     * @param int $category Идентификатор номинации: 0 - Мусору "НЕТ", 1 - Мусор "ЕСТЬ".
     *
     * @return bool|null Возвращает true, если фотография в данной номинации имеется у участника, false, если нет.
     * Возвращает null, если не удалось найти пользователя с таким uid или идентификатор номинации некорректный.
     */
    public static function has_photo($user, int $category): ?bool {
        if(is_int($user)) {
            $user = self::get_user($user);
        }

        if(!$user) { return null; }

        if($category == CATEGORY_TRASH_NO) {
            return $user->has_photo_0;
        } elseif($category == CATEGORY_TRASH_YES) {
            return $user->has_photo_1;
        }

        return null;
    }

    /**
     * Получение ссылки на VK профиль пользователя.
     *
     * @param User|int $user Объект участника конкурса или его идентификатор.
     *
     * @return string|null Может вернуть null, если переданный параметр не является ни идентификатором, ни объектом
     * пользователя, а также в том случае, если по данному идентификатору пользователь найден не был.
     */
    public static function get_vk_link($user): ?string {
        $link = 'https://vk.com/';

        if(is_int($user)) {
            $user = self::get_user($user);
        }

        if(!$user) { return null; }

        return $link . $user->domain;
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

        return self::get_user_from_row($user_data);
    }

    /**
     * Создание объекта участника из строки-массива, полученной из базы даных.
     *
     * @param array $user_row Строка-массив из базы данных.
     * @param bool $include_photos Включать ли получение фотографий данного участника из базы данных.
     *
     * @return User Объект участника конкурса.
     */
    public static function get_user_from_row(array $user_row, bool $include_photos = true) {
        $user = new User();

        $user->uid =            $user_row['uid'];
        $user->first_name =     $user_row['first_name'];
        $user->second_name =    $user_row['second_name'];
        $user->domain =         $user_row['domain'];
        $user->is_banned =      $user_row['is_banned'];
        $user->has_avatar =     $user_row['has_avatar'];
        $user->avatar =         $user_row['avatar'];
        $user->has_photo_0 =    $user_row['has_photo_0'];
        $user->has_photo_1 =    $user_row['has_photo_1'];

        if($include_photos) {
            $user->photo_0 =        Photo::get_photo($user->uid, CATEGORY_TRASH_NO);
            $user->photo_1 =        Photo::get_photo($user->uid, CATEGORY_TRASH_YES);
        }

        return $user;
    }

    /**
     * Создание пользователя и добавление его в базу данных.
     *
     * @param User $new_user Объект добавляемого пользователя.
     */
    public static function create_user(User $new_user) {
        global $db;

        $uid =          $new_user->uid;
        $first_name =   $db->real_escape_string($new_user->first_name);
        $second_name =  $db->real_escape_string($new_user->second_name);
        $domain =       $db->real_escape_string($new_user->domain);
        $is_banned =    (int)($new_user->is_banned ?: false);
        $has_avatar =   $new_user->has_avatar;
        $avatar =       $db->real_escape_string($new_user->avatar);
        $has_photo_0 =  (int)($new_user->has_photo_0 ?: false);
        $has_photo_1 =  (int)($new_user->has_photo_1 ?: false);

        $db->query(
            "INSERT INTO `users` ".
            "(`uid`, `first_name`, `second_name`, `domain`, `is_banned`, `has_avatar`, `avatar`, `has_photo_0`, `has_photo_1`) VALUES ".
            "($uid, '$first_name', '$second_name', '$domain', $is_banned, $has_avatar, '$avatar', $has_photo_0, $has_photo_1)"
        );

        if($db->error) {
            Error_Handler::error('Не удалось добавить пользователя в базу данных!', $db->error);
        }
    }

    /**
     * Проверка, имеется ли пользователь в базе данных (был ли уже зарегистрирован).
     *
     * @param int $uid Идентификатор ВКонтакте участника конкурса.
     *
     * @return bool true, если пользователь был зарегистрирован и false, если это новый участник.
     */
    public static function is_user_exists(int $uid): bool {
        global $db;

        $result = $db->query(
            "SELECT `uid` FROM `users` WHERE `uid` = $uid"
        );

        return ($result->num_rows != 0);
    }

    /**
     * Проверка, забанен ли пользователь (может ли принимать участие в конкусре).
     * Перед проверкой на бан убедитесь, что пользователь с заданным идентификатором вообще существует!
     *
     * @see User::is_user_exists()
     *
     * @param int $uid Идентификатор ВКонтакте участника конкурса.
     * @return bool true, если пользователь забанен и false, если пользователь не забанен или его не существует.
     */
    public static function is_user_banned(int $uid): bool {
        global $db;

        $result = $db->query(
            "SELECT `is_banned` FROM `users` WHERE `uid` = $uid"
        );

        if($result->num_rows == 0) {
            return false;
        }

        return ($result->fetch_assoc())['is_banned'];
    }

    /**
     * Обновление данных участника конкурса.
     *
     * @param int $uid Идентификатор ВКонтакте пользователя.
     * @param array $data Массив данных участника вида: [ 'поле_класса' => 'новое_значение', ...]
     */
    public static function edit_user(int $uid, array $data) {
        global $db;

        $db->query(
            "UPDATE `users` SET " . Utils::data_to_update_string(Utils::escape_data($data)) . ' '.
            "WHERE `uid` = $uid"
        );

        if($db->error) {
            Error_Handler::error('Не удалось обновить данные участника!', $db->error);
        }
    }

    /**
     * Получение количества участников конкурса.
     *
     * @return int Количество участников конкурса.
     */
    public static function get_users_amount() : int {
        global $db;

        $result = $db->query(
            "SELECT COUNT(*) FROM `users`"
        );

        if($db->error) {
            Error_Handler::error('Ошибка при получении общего количества участников конкурса!', $db->error);
        }

        return ($result->fetch_array())[0];
    }
}