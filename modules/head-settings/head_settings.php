<?php
/** Подключение различных JS библиотек и таблиц стилей. */
?>
<!-- JQuery. -->
<script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>

<!-- Иконки. -->
<script src="https://use.fontawesome.com/bd1d4be260.js"></script>

<!-- Глобальная таблица стилей. -->
<link rel="stylesheet" href="<?php echo SITE_URL . 'styles/global.css'; ?>">

<!-- Подключение VK API. -->
<script src="https://vk.com/js/api/openapi.js?139" type="text/javascript"></script>

<!-- Google Maps API. -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAUAAJjs1T751i6-4U__XnNCSQz-xXAelI&libraries=places" async defer></script>

<!-- Magnific Popup. -->
<link rel="stylesheet" href="<?php echo SITE_URL . 'js/libs/magnific-popup/magnific-popup.css'?>">
<script src="<?php echo SITE_URL . 'js/libs/magnific-popup/jquery.magnific-popup.min.js'?>"></script>

<!-- Библиотека "Поделиться" от ВКонтакте. -->
<script type="text/javascript" src="https://vk.com/js/api/share.js?93" charset="windows-1251"></script>

<!-- Глобальный скрипт сайта. -->
<script src="<?php echo SITE_URL . 'js/global.js'; ?>"></script>
<script>

    /* Объект участника, авторизованного в данный момент. */
    var User = {};

    <?php if($session->is_authorized()) { ?>
        init_user(<?php echo $current_user->uid; ?>, '<?php echo $current_user->first_name; ?>', '<?php echo $current_user->second_name; ?>');
    <?php } ?>

    /** Абсолютный адрес сайта. */
    const SITE_URL = '<?php echo SITE_URL; ?>';

    /** Соль для VK виджетов. */
    const VK_WIDGETS_SALT = '<?php echo VK_WIDGETS_SALT; ?>';

    /** Включены ли комментарии. */
    const ALLOW_COMMENTS = <?php echo (int)ALLOW_COMMENTS; ?>;

    /** Максимальный размер добавляемой на сайт фотографии. */
    const UPLOAD_MAX_SIZE = <?php echo MAX_IMG_SIZE; ?>;

    /** Изображение Google Maps маркера для поиска. */
    const MAP_MARKER_SEARCH = '<?php echo SITE_URL . 'images/search_marker_icon.png'; ?>';

    /** Изображения Google Maps маркеров фотографий. */
    const MAP_MARKER_0 = '<?php echo SITE_URL . 'images/map_marker_0.png'; ?>';
    const MAP_MARKER_1 = '<?php echo SITE_URL . 'images/map_marker_1.png'; ?>';

    VK.init({
        apiId: <?php echo VK_CLIENT_ID; ?>
    });
</script>

<!-- Контроллер ВКонтакте виджетов на странице. -->
<script src="<?php echo SITE_URL . 'js/VK.js'; ?>"></script>

<!-- Работа с сервером. -->
<script src="<?php echo SITE_URL . 'js/ajax.js'; ?>"></script>

<!-- Контроллер всплывающих окон с фотографиями участников. -->
<link rel="stylesheet" href="<?php echo SITE_URL . 'styles/photo-watcher.css'; ?>">
<script src="<?php echo SITE_URL . 'js/photo-watcher.js'; ?>"></script>