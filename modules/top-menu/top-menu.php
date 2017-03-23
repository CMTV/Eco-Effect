<?php
/** Модуль верхнего меню. */
?>
<div class="content-wrapper">
    <nav id="top-menu">
        <?php if($session->is_authorized()) { ?>
            <div class="tm-button tm-profile tm-active">
                <img width="35" src="<?php echo $current_user->avatar; ?>">
                Мой профиль
            </div>
        <?php } else { ?>
            <div class="tm-button tm-main tm-active">Главная</div>
        <?php } ?>
        <div class="tm-button tm-prizes">Призы</div>
        <div class="tm-button tm-about">О конкурсе</div>
        <?php if(!$session->is_authorized()) { ?>
            <a href="<?php echo VK::vk_authorize_link(); ?>" class="tm-button">Войти</a>
        <?php } else { ?>
            <a href="<?php echo SITE_URL . 'logout.php'; ?>" class="tm-button">Выйти</a>
        <?php } ?>
    </nav>

    <a id="index-link" href="<?php echo SITE_URL; ?>"><img id="logotype" width="60" src="<?php echo SITE_URL . 'images/logotype.png'; ?>"><h1 id="site-title">ЭКО ЭФФЕКТ</h1></a>
</div>
