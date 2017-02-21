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

    /** @var string|null Место съемки фотографии. */
    public $address = null;

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

        $photo = new Photo();

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

        $photo->id =            $photo_data['id'];
        $photo->uid =           $uid;
        $photo->title =         $photo_data['title'];
        $photo->description =   $photo_data['description'];
        $photo->address =       $photo_data['address'];
        $photo->date =          new DateTime($photo_data['date']);
        $photo->category =      $category;
        $photo->has_thumbnail = $photo_data['has_thumbnail'];
        $photo->thumbnail_url = $photo_data['thumbnail_url'];
        $photo->photo_url =     $photo_data['photo_url'];
        $photo->likes =         $photo_data['likes'];

        return $photo;
    }
}