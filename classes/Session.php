<?php
/** Управление сессией на сайте. */
class Session {
    /**
     * Объект текущего авторизованного участника. Для доступа к этим данным рекомендуется использовать глобальную
     * переменную $current_user, которая всегда инициализирована в том случае, если участник авторизован.
     *
     * @var User Текущий авторизованный участник.
     */
    public $user;

    function __construct() {
        session_start();

        $_SESSION['signed'] = $_SESSION['signed'] ?: false;

        if($_SESSION['current_user'] instanceof User) {
            $this->user = $_SESSION['current_user'];
        }
    }

    /**
     * Инициализация сессии.
     *
     * @param User $user Данные текущего авторизованного участника.
     */
    public function init(User $user) {
        $_SESSION['signed'] = true;
        $_SESSION['current_user'] = $this->user = $user;
    }

    /**
     * Авторизован ли участник.
     *
     * @return bool true, если авторизован и false, если нет.
     */
    public function is_authorized(): bool {
        return $_SESSION['signed'];
    }

    /**
     * Является ли текущий участник администратором.
     *
     * @return bool true, если текущий участник администратор и false, если нет.
     */
    public function is_admin(): bool {
        return ($this->is_authorized() && in_array($this->user->uid, ADMINS));
    }

    /** Завершение и полное уничтожение сессии */
    public function logout() {
        session_unset();

        if(ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
    }
}