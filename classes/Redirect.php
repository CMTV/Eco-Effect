<?php
/** Ссылка на главную страницу сайта. */
define('REDIRECT_INDEX', SITE_URL);

/** Ссылка на профиль участника. */
define('REDIRECT_PROFILE', SITE_URL . 'profile.php');

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
     * @param string $destination
     */
    public static function redirect_to(string $destination) {
        header('Location: ' . $destination);
        die();
    }
}