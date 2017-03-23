<?php
require_once('load.php');

$is_load_message = false;
$load_message = '';
if(!empty($_GET['msg'])) {
    $is_load_message = true;
    $load_message = $_GET['msg'];
}

?>
<!doctype html>
<html>
<head>
    <title>Конкурс "Эко Эффект"</title>

    <!-- Иконка сайта. -->
    <link rel="icon" type="image/png" href="/images/favicon.png">

    <?php include(MODULES . 'head-settings/head_settings.php'); ?>

    <!-- Модуль верхнего  меню. -->
    <style><?php include(MODULES . 'top-menu/top-menu.css'); ?></style>
    <script><?php include(MODULES . 'top-menu/top-menu.js'); ?></script>

    <!-- Модуль переключателя шапок. -->
    <style><?php include(MODULES . 'header-switcher/header-switcher.css'); ?></style>
    <script><?php include(MODULES . 'header-switcher/header-switcher.js'); ?></script>

    <?php if(!$session->is_authorized()) { ?>

        <!-- Модуль шапки-лендинга. -->
        <style><?php include(MODULES . 'header-landing/header-landing.css'); ?></style>
        <script><?php include(MODULES . 'header-landing/header-landing.js'); ?></script>

    <?php } else { ?>

        <!-- Глобальные стили профиля. -->
        <style><?php include(MODULES . 'profile/profile.css'); ?></style>

        <!-- Контроллер и стили форм в профиле участника. -->
        <style><?php include(MODULES . 'profile-forms/profile-forms.css'); ?></style>
        <script><?php include(MODULES . 'profile-forms/profile-forms-controller.js'); ?></script>

    <?php } ?>

    <!-- Модуль таблицы лидеров. -->
    <style><?php include(MODULES . 'winners/winners.css'); ?></style>
    <script><?php include(MODULES . 'winners/winners.js'); ?></script>

    <!-- Модуль галереи. -->
    <style><?php include(MODULES . 'gallery/gallery.css'); ?></style>
    <style><?php include(MODULES . 'gallery/gallery-controls.css'); ?></style>
    <style><?php include(MODULES . 'gallery/gallery-indicator.css'); ?></style>
    <script><?php include(MODULES . 'gallery/gallery.js'); ?></script>
    <script><?php include(MODULES . 'gallery/gallery-controls.js'); ?></script>
    <script><?php include(MODULES . 'gallery/gallery-indicator.js'); ?></script>
    <script>
        /* Базовая загрузка фотографий. */
        $(function () {
            clear_gallery(function () {
                get_photos({ get_type: GET_LAST }).then(function (photos) {
                    fill_gallery(photos);
                });
            });
        });
    </script>
</head>
<body>

<?php if($is_load_message) { ?>
    <div class="load_message">
        <?php echo $load_message; ?>
    </div>
    <script>
        $(function () {
            $('.load_message').fadeIn(200).css('display', 'flex');

            setTimeout(function () {
                $('.load_message').fadeOut(200);
            }, 2500);
        });
    </script>
    <style>
        .load_message {
            display: none;
            z-index: 99999;
            width: 250px;
            height: 150px;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            top: 30px;
            background: #FFF9E1;
            border: 3px solid #e5a222;
            border-radius: 5px;
            color: #e5a222;
            justify-content: center;
            align-items: center;
            font-size: 150%;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        }
    </style>
<?php } ?>

<!-- Модуль верхнего меню. -->
<?php include(MODULES . 'top-menu/top-menu.php'); ?>

<!-- Переключатель шапок сайта. -->
<?php include(MODULES . 'header-switcher/header-switcher.php'); ?>

<!-- Таблица лидеров. -->
<?php include(MODULES . 'winners/winners.php'); ?>

<!-- Модуль галереи. -->
<?php include(MODULES . 'gallery/gallery.php'); ?>

<?php
if(is_numeric($_GET['photo'])) {
    $photo = Photo::get_photo_by_id(intval($_GET['photo']));
    $photo_author = User::get_user($photo->uid);

    $photo_data = (new PhotoData($photo, $photo_author))->export();
?>
    <script>
        open_photo_watcher(parse_photo_data('<?php echo $photo_data; ?>'));
    </script>
<?php } ?>

</body>
</html>
