<?php
use Aws\S3\S3Client;

/** Название корзины файлов для сайта. */
define('BUCKET', 'eco-effect');

/** Директория фотографий в номинации "Мусору НЕТ". */
define('BUCKET_TRASH_NO', 'trash_NO');

/** Директория миниатюр фотографий в номинации "Мусору НЕТ". */
define('BUCKET_TRASH_NO_THUMB', 'trash_NO_thumb');

/** Директория фотографий в номинации "Мусор ЕСТЬ". */
define('BUCKET_TRASH_YES', 'trash_YES');

/** Директория миниатюр фотографий в номинации "Мусор ЕСТЬ". */
define('BUCKET_TRASH_YES_THUMB', 'trash_YES_thumb');

/** Работа с хранилищем Amazon S3. */
class Amazon {
    /** @var S3Client Объект клиента для работы с Amazon. */
    public $client;

    /** Подключение к сервисам Amazon и инициализация клиента для работы с ним. */
    public function __construct() {
        try {
            $this->client = new S3Client([
                'version' =>    'latest',
                'region' =>     'us-east-1',
                'credentials' => [
                    'key' =>    AWS_ACCESS_KEY_ID,
                    'secret' => AWS_SECRET_ACCESS_KEY
                ]
            ]);
        } catch (Exception $e) {
            Error_Handler::error('Произошла ошибка при подключении к облаку Amazon!', $e->getMessage());
        }
    }

    /**
     * Загрузка фотографии в хранилище на Amazon.
     *
     * @param int $category Идентификатор номинации: 0 - Мусору "НЕТ", 1 - Мусор "ЕСТЬ".
     * @param string $path_to_file Пусть к файлу, который необходимо загрузить.
     * @param string $extension Расширение загружаемой фотографии.
     *
     * @return string Ссылка на успешно загруженную фотографию.
     */
    public function upload_photo(int $category, string $path_to_file, string $extension): string {
        global $current_user;

        $bucket = BUCKET;
        $folder = (($category) ? BUCKET_TRASH_YES : BUCKET_TRASH_NO) . '/';

        $upload_result = '';
        try {
            $upload_result = $this->client->putObject([
                'Bucket' =>     $bucket,
                'Key'   =>      $folder . $current_user->uid . '_' . $category . '_' . time() . '.' . $extension,
                'SourceFile' => $path_to_file,
                'ACL'    =>     'public-read'
            ]);
        } catch (Exception $e) {
            Error_Handler::error('Не удалось загрузить фото на облако Amazon!', $e->getMessage());
        }

        return $upload_result['ObjectURL'];
    }

    /**
     * Загрузка миниатюры фотографии в хранилище на Amazon.
     *
     * @param int $category Идентификатор номинации: 0 - Мусору "НЕТ", 1 - Мусор "ЕСТЬ".
     * @param string $image_data Содержимое миниатюры.
     * @param string $extension Расширение миниатюры.
     *
     * @return string Ссылка на успешно загруженную миниатюру.
     */
    public function upload_thumbnail(int $category, string $image_data, string $extension): string {
        global $current_user;

        $bucket = BUCKET;
        $folder = (($category) ? BUCKET_TRASH_YES_THUMB : BUCKET_TRASH_NO_THUMB) . '/';

        $upload_result = '';
        try {
            $upload_result = $this->client->putObject([
                'Bucket' =>     $bucket,
                'Key'   =>      $folder . $current_user->uid . '_' . $category . '_thumb_' . time() . '.' . $extension,
                'Body' =>       $image_data,
                'ACL'    =>     'public-read'
            ]);
        } catch (Exception $e) {
            Error_Handler::error('Не удалось загрузить фото на облако Amazon!', $e->getMessage());
        }

        return $upload_result['ObjectURL'];
    }

    /**
     * Удаление фотографии участника из Amazon S3.
     *
     * @param int $uid Идентификатор ВКонтакте пользователя.
     * @param int $category Идентификатор номинации: 0 - Мусору "НЕТ", 1 - Мусор "ЕСТЬ".
     */
    public function remove_photo(int $uid, int $category) {
        $user = User::get_user($uid);

        if(!$user) {
            Error_Handler::error('Невозможно удалить фото! Пользователя не существует!', null);
        }

        $bucket = BUCKET;

        $photo_folder = (($category) ? BUCKET_TRASH_YES : BUCKET_TRASH_NO) . '/';
        $thumb_folder = (($category) ? BUCKET_TRASH_YES_THUMB : BUCKET_TRASH_NO_THUMB) . '/';

        if($category) {
            $filename = end(explode('/', $user->photo_1->photo_url));

            try {
                $this->client->deleteObject([
                    'Bucket' => $bucket,
                    'Key' => $photo_folder . $filename
                ]);

                if($user->photo_1->has_thumbnail) {
                    $thumb_filename = end(explode('/', $user->photo_1->thumbnail_url));

                    $this->client->deleteObject([
                        'Bucket' => $bucket,
                        'Key' => $thumb_folder . $thumb_filename
                    ]);
                }
            } catch (Exception $e) {
                Error_Handler::error('Не удалось удалить фото из облака Amazon!', $e->getMessage());
            }
        } else {
            $filename = end(explode('/', $user->photo_0->photo_url));

            try {
                $this->client->deleteObject([
                    'Bucket' => $bucket,
                    'Key' => $photo_folder . $filename
                ]);

                if($user->photo_0->has_thumbnail) {
                    $thumb_filename = end(explode('/', $user->photo_0->thumbnail_url));

                    $this->client->deleteObject([
                        'Bucket' => $bucket,
                        'Key' => $thumb_folder . $thumb_filename
                    ]);
                }
            } catch (Exception $e) {
                Error_Handler::error('Не удалось удалить фото из облака Amazon!', $e->getMessage());
            }
        }
    }
}