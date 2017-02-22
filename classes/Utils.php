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
     * @return string Содержимое изображения.
     */
    public static function make_thumbnail(string $src, int $width = THUMBNAIL_WIDTH, int $height = THUMBNAIL_WIDTH, int $crop = 0): string {
        list($w, $h) = getimagesize($src);

        $type = self::get_image_extension(exif_imagetype($src));
        switch($type){
            case 'gif': $img = imagecreatefromgif($src); break;
            case 'jpg': $img = imagecreatefromjpeg($src); break;
            case 'png': $img = imagecreatefrompng($src); break;
            default : return false;
        }

        // resize
        if($crop){
            if($w < $width or $h < $height) return false;
            $ratio = max($width/$w, $height/$h);
            $h = $height / $ratio;
            $x = ($w - $width / $ratio) / 2;
            $w = $width / $ratio;
        }
        else{
            if($w < $width and $h < $height) return false;
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
                return $image_data;
            case 'jpg':
                ob_start();
                    imagejpeg($new);
                    $image_data = ob_get_contents();
                ob_end_clean();
                return $image_data;
            case 'png':
                ob_start();
                    imagepng($new);
                    $image_data = ob_get_contents();
                ob_end_clean();
                return $image_data;
        }
        return true;
    }
}