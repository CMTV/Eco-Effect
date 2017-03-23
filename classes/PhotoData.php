<?php
/**
 * Объект для передачи данных о фотографии и ее авторе в JS на сайт.
 */
class PhotoData {
    /**
     * @var bool Есть ли данные фотографии.
     */
    public $is_photo;

    /**
     * @var int $photo_id
     * @see Photo::$id
     */
    public $photo_id;

    /**
     * @var string $photo_url
     * @see Photo::$photo_url
     */
    public $photo_url;

    /**
     * @var string|null $thumb_url
     * @see Photo::$thumbnail_url
     */
    public $thumb_url;

    /**
     * @var Size $photo_size
     * @see Photo::$photo_size
     */
    public $photo_size;

    /**
     * @var Size $thumb_size
     * @see Photo::$thumb_size
     */
    public $thumb_size;

    /**
     * @var DateTime $date
     * @see Photo::$date
     */
    public $date;

    /**
     * @var int $category
     * @see Photo::$category
     */
    public $category;

    /**
     * @var string|null $title
     * @see Photo::$title
     */
    public $title;

    /**
     * @var string|null $description
     * @see Photo::$description
     */
    public $description;

    /**
     * @var bool $has_marker
     * @see Photo::$has_marker
     */
    public $has_marker;

    /**
     * @var float|null $latitude
     * @see Photo::$latitude
     */
    public $latitude;

    /**
     * @var float|null $longitude
     * @see Photo::$longitude
     */
    public $longitude;

    /**
     * @var int $user_id
     * @see User::uid
     */
    public $user_id;

    /**
     * @var string $user_full_name
     * @see User::full_name()
     */
    public $user_full_name;

    /**
     * @var string $user_avatar_url
     * @see User::$avatar
     */
    public $user_avatar_url;

    /**
     * @var string $user_vk_link
     * @see User::get_vk_link()
     */
    public $user_vk_link;

    /**
     * Конструктор PhotoData.
     *
     * @param Photo|null $photo Фотография, данные которой будут переданы в JS.
     * @param User|null $user Пользователь, данные которого будут переданы в JS.
     */
    public function __construct(?Photo $photo, ?User $user) {

        if(is_null($photo) || is_null($user)) {
            $this->is_photo = 0;
            return;
        }

        $this->is_photo =           1;
        $this->photo_id =           $photo->id;
        $this->photo_url =          $photo->photo_url;
        $this->thumb_url =          $photo->thumbnail_url;
        $this->photo_size =         $photo->photo_size;
        $this->thumb_size =         $photo->thumb_size;
        $this->date =               $photo->date->format('Y-m-d H:i:s');
        $this->category =           $photo->category;
        $this->title =              $photo->title;
        $this->description =        $photo->description;
        $this->has_marker =         (int)$photo->has_marker;
        $this->latitude =           $photo->latitude;
        $this->longitude =          $photo->longitude;
        $this->user_id =            $user->uid;
        $this->user_full_name =     $user->full_name();
        $this->user_avatar_url =    $user->avatar;
        $this->user_vk_link =       User::get_vk_link($user);
    }

    public function export() {
        return json_encode($this);
    }
}