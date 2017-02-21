<?php
require_once('load.php');

echo 'Авторизован: ' . (int)$session->is_authorized(). '; ';

if($session->is_authorized()) {
    echo 'Данные авторизованного участника: ' . $current_user->first_name . '; ';
}

if($session->is_admin()) {
    echo '[ADMIN!!!]';
}

?>

<a href="<?php echo VK::vk_authorize_link(); ?>">Войти чере ВКонтакте</a>
