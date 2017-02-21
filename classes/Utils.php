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
}