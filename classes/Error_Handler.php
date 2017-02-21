<?php
/** Обработка ошибок, возникающих на сайте. */
class Error_Handler {
    /**
     * Перенаправление на страницу ошибки.
     *
     * @param null|string $message Пояснения к ошибке.
     * @param null|string $additional Дополнительная информация по ошибке. Выводится только при активированном режиме
     * отладки.
     * @param bool $add_to_log Нужно ли добавить сообщение об ошибке в лог ошибок.
     */
    public static function error(?string $message = null, ?string $additional = null, bool $add_to_log = true) {
        if($add_to_log) {
            Log::log_add('[ERROR]   [MAIN] ' . $message . ' [ADDITIONAL] ' . $additional);
        }

        header('Location: ' . SITE_URL . 'error-pages/error.php?message=' . $message
            .(IS_DEBUG_MODE ? '&additional=' . $additional : ''));
        die();
    }

    /**
     * Возврат данных об ошибке при Ajax запросе.
     *
     * @param null|string $message Пояснения к ошибке.
     * @param null|string $additional Дополнительная информация по ошибке. Выводится только при активированном режиме
     * отладки.
     * @param bool $add_to_log Нужно ли добавить сообщение об ошибке в лог ошибок.
     *
     * @return string Возвращает JSON массив, состоящий из двух элементов: error = 1 || 0 и
     * сообщения об ошибке error_msg.
     */
    public static function ajax_error(?string $message = null, ?string $additional = null, bool $add_to_log = true): string {
        if($add_to_log) {
            Log::log_add('[AJAX ERROR]   [MAIN] ' . $message . ' [ADDITIONAL] ' . $additional);
        }

        $error_array = [];
        $error_array['error'] = true;
        $error_array['error_msg'] = $message;

        if(IS_DEBUG_MODE) {
            $error_array['error_msg_additional'] = $additional;
        }

        die(json_encode($error_array));
    }

    /** Перенаправление на страницу "Доступ запрещен". */
    public static function forbid() {
        header('Location: ' . SITE_URL . 'error-pages/403.php');
        die();
    }

    /** Перенаправление на страницу "Страница не найдена". */
    public static function page_not_found() {
        header('Location: ' . SITE_URL . 'error-pages/404.php');
        die();
    }
}