<?php
/** Различные вспомагательные функции. */
class Utils {
    /**
     * Получение данных сайта с помощью защищенного подключения.
     *
     * @param string $url Адрес, с которого надо считать данные.
     * @return string Считанные данные.
     */
    public static function get_curl(string $url): ?string {
        if(function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $output = curl_exec($ch);
            echo curl_error($ch);
            curl_close($ch);
            return $output;
        } else {
            return file_get_contents($url);
        }
    }

    /**
     * Получение строки расширения изображения. Доступные типы изображений: PNG, GIF, JPEG.
     *
     * @param int $img_code Код изображения.
     * @return null|string Строка с расширением без точки или null, если расширение картинки не PNG, GIF, JPEG.
     */
    public static function get_image_extension(int $img_code): ?string {
        switch ($img_code) {
            case IMAGETYPE_PNG:     return 'png';
            case IMAGETYPE_GIF:     return 'gif';
            case IMAGETYPE_JPEG:    return 'jpg';
            default:                return null;
        }
    }

    /**
     * Создание миниатюры фотографии.
     *
     * @param string $src Из какой фотографии необходимо создать миниатюру.
     * @param int $width Ширина миниатюры.
     * @param int $height Высота миниатюры.
     * @param int $crop Обрезка миниатюры.
     *
     * @return array Массив, первый элемент которого: массив ширины и высоты, а второй - содержимое миниатюры.
     */
    public static function make_thumbnail(string $src, int $width = THUMBNAIL_WIDTH, int $height = THUMBNAIL_WIDTH, int $crop = 0): array {
        list($w, $h) = getimagesize($src);

        $type = self::get_image_extension(exif_imagetype($src));
        switch($type){
            case 'gif': $img = imagecreatefromgif($src); break;
            case 'jpg': $img = imagecreatefromjpeg($src); break;
            case 'png': $img = imagecreatefrompng($src); break;
            default : return [false];
        }

        // resize
        if($crop){
            if($w < $width or $h < $height) return [false];
            $ratio = max($width/$w, $height/$h);
            $h = $height / $ratio;
            $x = ($w - $width / $ratio) / 2;
            $w = $width / $ratio;
        }
        else{
            if($w < $width and $h < $height) return [false];
            $ratio = $width/$w;
            $width = ceil($w * $ratio);
            $height = ceil($h * $ratio);
            $x = 0;
        }

        $new = imagecreatetruecolor($width, $height);

        // preserve transparency
        if($type == "gif" or $type == "png"){
            imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
            imagealphablending($new, false);
            imagesavealpha($new, true);
        }

        imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);

        switch($type){
            case 'gif':
                ob_start();
                    imagegif($new);
                    $image_data = ob_get_contents();
                ob_end_clean();
                return ['size' => ['width' => $width, 'height' => $height], 'image' => $image_data];
            case 'jpg':
                ob_start();
                    imagejpeg($new);
                    $image_data = ob_get_contents();
                ob_end_clean();
                return ['size' => ['width' => $width, 'height' => $height], 'image' => $image_data];
            case 'png':
                ob_start();
                    imagepng($new);
                    $image_data = ob_get_contents();
                ob_end_clean();
                return ['size' => ['width' => $width, 'height' => $height], 'image' => $image_data];
        }
        return [true];
    }

    /**
     * Преобразует массив данных для MySQL базы данных в массив безопасных данных:
     * * Значения булева типа преобразуются в 0 или 1.
     * * Строки обрамляются одинарными ковычками и экранируются.
     * * NULL значения преобразуются в NULL, приемлемый для базы данных.
     *
     * @param array $data Данные для вставки в базу данных.
     * @return array Безопасные данные для вставки в базу данных.
     */
    public static function escape_data(array $data) {
        global $db;

        $escaped_data = [];

        foreach ($data as $field_name => $field_value) {
            if      (is_bool($field_value)) {
                $field_value = (int)$field_value;
            } elseif(is_string($field_value)) {
                $field_value = '\'' . $db->real_escape_string($field_value) . '\'';
            } elseif(is_null($field_value)) {
                $field_value = 'NULL';
            }

            $escaped_data[$field_name] = $field_value;
        }

        return $escaped_data;
    }

    /**
     * Преобразование безопасных данных в часть UPDATE SQL запроса.
     *
     * @param array $escaped_data Безопасный массив данных. Безопасный массив данных получается из обычного массива
     * данных с помощью функции {@see Utils::escape_data()}.
     *
     * @return string Часть UPDATE SQL запроса.
     */
    public static function data_to_update_string(array $escaped_data) {
        $str = '';

        $i = 1;
        $len = count($escaped_data);

        foreach($escaped_data as $field_name => $field_value) {
            if($i != $len) {
                $str .= "`$field_name` = $field_value, ";
            } else {
                $str .= "`$field_name` = $field_value ";
            }

            $i++;
        }

        return $str;
    }

    /**
     * Разные окончания у числительных.
     *
     * @param int $number Число.
     * @param array $titles Варианты слов с разными окончаниями.
     * @return string Слово с правильным окончанием.
     */
    public static function plural_form(int $number, array $titles = ['комментарий','комментария','комментариев']){
        $cases = array (2, 0, 1, 1, 1, 2);
        return $titles[ ($number%100 >4 && $number%100< 20)? 2 : $cases[min($number%10, 5)] ];
    }

    /**
     * Получение даты в формате "[число] [название месяца]".
     *
     * @param DateTime $date
     * @return string
     */
    public static function get_date_simplified(DateTime $date) : string {
        $months = [
            1 => 'января',
            2 => 'февраля',
            3 => 'марта',
            4 => 'апреля',
            5 => 'мая',
            6 => 'июня',
            7 => 'июля',
            8 => 'августа',
            9 => 'сентября',
            10 => 'октября',
            11 => 'ноября',
            12 => 'декабря'
        ];

        return $date->format('d') . ' ' . $months[$date->format('n')];
    }
}