<?php
/** Идентификатор номинации "Мусору НЕТ". */
define('CATEGORY_TRASH_NO', 0);

/** Идентификатор номинации "Мусор ЕСТЬ". */
define('CATEGORY_TRASH_YES', 1);

/** Класс, содержащий данные фотографии для конкурса. */
class Photo {
    /** @var int Идентификатор фотографии в базе данных. Также является идентификатором виджета ВКонтакте. */
    public $id;

    /**
     * @see User
     *
     * @var int Идентификатор пользователя ВКонтакте.
     */
    public $uid;

    /** @var string|null Заголовок к загруженной фотографии. */
    public $title = null;

    /** @var string|null Описание к загруженной фотографии. */
    public $description = null;

    /** @var bool Указан ли маркер на Google Maps. */
    public $has_marker;

    /** @var float|null Координата широты Google Maps маркера. */
    public $latitude = null;

    /** @var float|null Координата долготы Google Maps маркера. */
    public $longitude = null;

    /** @var DateTime Время съемки фотографии. */
    public $date;

    /**
     * Идентификатор номинации. Может принимать следующие значения:
     * * 0 - Номинация "Мусору НЕТ";
     * * 1 - Номинация "Мусор ЕСТЬ".
     *
     * @var int
     */
    public $category;

    /** @var bool Есть ли у фотографии миниатюра. */
    public $has_thumbnail;

    /**
     * Ссылка на миниатюру фотографии. Содержит null, если миниатюры нет, и поле {@see Photo::$has_thumbnail} равно 0.
     *
     * @var string|null
     */
    public $thumbnail_url = null;

    /** @var string Ссылка на полную версию фотографии. */
    public $photo_url;

    /** @var int Количество лайков у фотографии. */
    public $likes;

    /** @var Size Объект размера фотографии участника. */
    public $photo_size;

    /** @var Size Объект размера миниатюры участника. */
    public $thumb_size;

    /**
     * Конструктор объекта класса Photo.
     */
    public function __construct() {
        $this->date = new DateTime();

        $this->photo_size = new Size();
        $this->thumb_size = new Size();
    }

    /**
     * Получение фотографии участника в заданной номинации.
     *
     * @param int $uid Идентификатор ВКонтакте участника.
     * @param int $category Номер номинации: 0 - "Мусору НЕТ", 1 - "Мусор ЕСТЬ".
     *
     * @return Photo|null Возвращает объект класса {@see Photo} или null, если фотография была не найдена.
     */
    public static function get_photo(int $uid, int $category): ?Photo {
        global $db;

        $photo_result = $db->query(
            "SELECT * FROM `photos` WHERE `uid` = $uid AND `category` = $category"
        );

        if($db->error) {
            Error_Handler::error('Не удалось получить фотографию из базы данных!', $db->error);
        }

        if($photo_result->num_rows == 0) {
            return null;
        }

        $photo_data = $photo_result->fetch_assoc();

        return self::get_photo_from_row($photo_data);
    }

    /**
     * Получение объекта фотографии из массива данных полученных из базы данных.
     *
     * @param array $photo_row Массив данных, полученный через fetch_assoc.
     * @return Photo Объект фотографии, полученный из строки данных базы данных.
     */
    public static function get_photo_from_row(array $photo_row) {
        $photo = new Photo();

        $photo->id =            $photo_row['id'];
        $photo->uid =           $photo_row['uid'];
        $photo->title =         $photo_row['title'];
        $photo->description =   $photo_row['description'];
        $photo->has_marker =    $photo_row['has_marker'];
        $photo->latitude =      $photo_row['latitude'];
        $photo->longitude =     $photo_row['longitude'];
        $photo->date =          new DateTime($photo_row['date']);
        $photo->category =      (int)$photo_row['category'];
        $photo->has_thumbnail = $photo_row['has_thumbnail'];
        $photo->thumbnail_url = $photo_row['thumbnail_url'];
        $photo->photo_url =     $photo_row['photo_url'];
        $photo->likes =         $photo_row['likes'];
        $photo->photo_size =    unserialize($photo_row['photo_size']);
        $photo->thumb_size =    unserialize($photo_row['thumb_size']);

        return $photo;
    }

    /**
     * Получение фотографии по ее цифровому идентификатору.
     *
     * @param int $id Цифровой идентификатор фотографии.
     * @return null|Photo Возвращает фотографию или null, если такой фотографии не существует.
     */
    public static function get_photo_by_id(int $id) {
        global $db;

        $photo = new Photo();

        $photo_result = $db->query(
            "SELECT * FROM `photos` WHERE `id` = $id"
        );

        if($db->error) {
            Error_Handler::error('Не удалось получить фотографию из базы данных!', $db->error);
        }

        if($photo_result->num_rows == 0) {
            return null;
        }

        $photo_data = $photo_result->fetch_assoc();

        $photo->id =            $photo_data['id'];
        $photo->uid =           $photo_data['uid'];
        $photo->title =         $photo_data['title'];
        $photo->description =   $photo_data['description'];
        $photo->has_marker =    $photo_data['has_marker'];
        $photo->latitude =      $photo_data['latitude'];
        $photo->longitude =     $photo_data['longitude'];
        $photo->date =          new DateTime($photo_data['date']);
        $photo->category =      $photo_data['category'];
        $photo->has_thumbnail = $photo_data['has_thumbnail'];
        $photo->thumbnail_url = $photo_data['thumbnail_url'];
        $photo->photo_url =     $photo_data['photo_url'];
        $photo->likes =         $photo_data['likes'];
        $photo->photo_size =    unserialize($photo_data['photo_size']);
        $photo->thumb_size =    unserialize($photo_data['thumb_size']);

        return $photo;
    }

    /**
     * Добавление фотографии в базу данных.
     *
     * @param Photo $new_photo Объект фотографии, который будет добавлен в базу данных.
     */
    public static function create_photo(Photo $new_photo) {
        global $db;

        $uid =              $new_photo->uid;
        $title =            $new_photo->title ? '\'' . $db->real_escape_string($new_photo->title) . '\'' : 'NULL';
        $description =      $new_photo->description ? '\'' . $db->real_escape_string($new_photo->description) . '\'' : 'NULL';
        $has_marker =       (int)$new_photo->has_marker;
        $latitude =         $has_marker ? $new_photo->latitude : 'NULL';
        $longitude =        $has_marker ? $new_photo->longitude : 'NULL';
        $date =             $new_photo->date->format('Y-m-d H:i:s');
        $category =         $new_photo->category;
        $has_thumbnail =    (int)$new_photo->has_thumbnail;
        $thumbnail_url =    $has_thumbnail ? $db->real_escape_string($new_photo->thumbnail_url) : $db->real_escape_string($new_photo->photo_url);
        $photo_url =        $db->real_escape_string($new_photo->photo_url);
        $likes =            $new_photo->likes ?: 0;
        $photo_size =       serialize($new_photo->photo_size);
        $thumb_size =       $has_thumbnail ? serialize($new_photo->thumb_size) : $photo_size;

        $db->query(
            "INSERT INTO `photos` ".
            "(`uid`, `title`, `description`, `has_marker`, `latitude`, `longitude`, `date`, `category`, `has_thumbnail`, `thumbnail_url`, `photo_url`, `likes`, `photo_size`, `thumb_size`) VALUES ".
            "($uid, $title, $description, $has_marker, $latitude, $longitude, '$date', $category, $has_thumbnail, '$thumbnail_url', '$photo_url', $likes, '$photo_size', '$thumb_size')"
        );

        if($db->error) {
            Error_Handler::error('Не удалось добавить фотографию в базу данных!', $db->error);
        }
    }

    /**
     * Обновление данных загруженной фотографии в базе данных.
     *
     * @param int $id Идентификатор фотографии в базе данных.
     * @param array $data Обновленная информация о фотографии.
     */
    public static function edit_photo(int $id, array $data) {
        global $db;

        $db->query(
            "UPDATE `photos` SET " . Utils::data_to_update_string(Utils::escape_data($data)) . ' '.
            "WHERE `id` = $id"
        );

        if($db->error) {
            Error_Handler::error('Не удалось обновить данные участника!', $db->error);
        }
    }

    /**
     * Удаление данных о загруженной фотографии из базы данных.
     *
     * @param int $id Идентификатор фотографии в базе данных.
     */
    public static function remove_photo(int $id) {
        global $db;

        $db->query(
            "DELETE FROM `photos` WHERE `id` = $id"
        );

        if($db->error) {
            Error_Handler::error('Не удалось удалить данные фотографии из базы данных!', $db->error);
        }
    }

    /**
     * Пересчитать голоса для данной фотографии.
     *
     * @param Photo|int $photo Идентификатор или объект фотографии, для которой будет произведен пересчет голосов.
     */
    public static function vote_for($photo) {
        if($photo instanceof Photo) {
            $photo = $photo->id;
        }

        $vk_data = json_decode(VK::get_votes($photo));

        if($vk_data->error) {
            Error_Handler::error('Не удалось засчитать голос!', $vk_data->error->error_msg);
        }

        $vk_data = $vk_data->response;

        $likes_amount = $vk_data->count;

        self::edit_photo($photo, ['likes' => $likes_amount]);
    }

    /**
     * Получение позиции фотографии в топе.
     *
     * @param Photo|int $photo Фотография, для которой надо узнать позицию (объект или ее идентификатор).
     * @return int Позиция фотографии в топе.
     */
    public static function get_top_position($photo) : int {
        global $db;

        if(is_int($photo)) {
            $photo = Photo::get_photo_by_id($photo);
        }

        $uid = $photo->uid;
        $category = $photo->category;

        $result = $db->query('SELECT
                               rank
                            FROM ( 
                              SELECT 
                               *
                               , (@rank := @rank + 1) AS rank
                              FROM 
                               `photos`
                              CROSS JOIN( 
                               SELECT
                                 @rank := 0
                               )  
                               AS 
                                init_var_var
                                WHERE `category`=' . $category . '
                              ORDER BY
                               `photos`.`likes` DESC  
                            )
                             AS logins_ordered_ranked
                            WHERE
                              `uid` = ' . $uid);

        if($db->error) {
            Error_Handler::error('Не удалось получить позицию фотографии в топе!', $db->error);
        }

        return ($result->fetch_assoc())['rank'];
    }

    /**
     * Получение количества загруженных на сайт фотографий.
     *
     * @return int Число загруженных на сайт фотографий.
     */
    public static function get_photos_amount() : int {
        global $db;

        $result = $db->query(
            "SELECT COUNT(*) FROM `photos`"
        );

        if($db->error) {
            Error_Handler::error('Не удалось получить число всех фотографий!', $db->error);
        }

        return ($result->fetch_array())[0];
    }

    /**
     * Получение общего числа голосов.
     *
     * @return int Общее число голосов.
     */
    public static function get_likes_amount() : int {
        global $db;

        $result = $db->query(
            "SELECT SUM(`likes`) FROM `photos`"
        );

        if($db->error) {
            Error_Handler::error('Не удалось получить общее число голосов!', $db->error);
        }

        return ($result->fetch_array())[0];
    }
}