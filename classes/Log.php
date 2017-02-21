<?php
/** Управление логом. */
class Log {
    /**
     * Добавление записи в лог ошибок.
     *
     * @param string $message Сообщение об ошибке, которое будет записано в лог.
     */
    public static function log_add(string $message) {
        $date = date('| d.m.y | H:i:s |   ');
        file_put_contents(ABSPATH . 'logs/' . date('d-m-y') . '.log', $date . $message . "\n", FILE_APPEND);
    }
}