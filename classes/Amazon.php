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
                'Key'   =>      $folder . $current_user->uid . '_' . $category . '.' . $extension,
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
                'Key'   =>      $folder . $current_user->uid . '_' . $category . '_thumb.' . $extension,
                'Body' =>       $image_data,
                'ACL'    =>     'public-read'
            ]);
        } catch (Exception $e) {
            Error_Handler::error('Не удалось загрузить фото на облако Amazon!', $e->getMessage());
        }

        return $upload_result['ObjectURL'];
    }
}