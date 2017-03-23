<?php
/** Ссылка на главную страницу сайта. */
define('REDIRECT_INDEX', SITE_URL);

/** Ссылка на окончание сессии. */
define('REDIRECT_LOGOUT', SITE_URL . 'logout.php');

/** Работа с перенаправлениями на сайте. */
class Redirect {
    /**
     * Перенаправление на определенную страницу сайта.
     *
     * Доступные страницы для перенаправления:
     * * {@see REDIRECT_INDEX} - На главную страницу.
     * * {@see REDIRECT_PROFILE} - На страницу профиля.
     *
     * @todo Добавить больше перенаправлений.
     *
     * @param string $destination Адрес страницы, на которую будет произведена переадресация.
     * @param string|null $msg Дополнительное сообщение принимающей странице.
     */
    public static function redirect_to(string $destination, ?string $msg = null) {
        header('Location: ' . $destination . '?msg=' . $msg);
        die();
    }
}