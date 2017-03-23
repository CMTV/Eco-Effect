<?php
/** Шапка сайта. */

$users =    User::get_users_amount();
$photos =   Photo::get_photos_amount();
$likes =    Photo::get_likes_amount();

?>
<header id="landing-header">
    <div class="content-wrapper">
        <section class="take-part-container">
            <h1 class="landing-title">Взгляни на мир по-новому!</h1>
            <a href="<?php echo VK::vk_authorize_link(); ?>" id="take-part" title="Выиграй приз!"><i class="fa fa-camera" aria-hidden="true"></i><span>Прими участие!</span></a>
        </section>
    </div>

    <div class="content-wrapper">
        <section class="header-stats">

            <div class="stats-container">
                <i class="fa fa-users stats-icon" aria-hidden="true"></i>
                <div class="stats-content">
                    <span class="stats-num"><?php echo number_format($users, 0, '.', ' '); ?></span><br>
                    <span class="stats-label"><?php echo Utils::plural_form($likes, ['Участник', 'Участника', 'Участников']); ?></span>
                </div>
            </div>

            <div class="stats-container">
                <i class="fa fa-camera stats-icon" aria-hidden="true"></i>
                <div class="stats-content">
                    <span class="stats-num"><?php echo number_format($photos, 0, '.', ' '); ?></span><br>
                    <span class="stats-label"><?php echo Utils::plural_form($likes, ['Фотография', 'Фотографии', 'Фотографий']); ?></span>
                </div>
            </div>

            <div class="stats-container">
                <i class="fa fa-heart stats-icon" aria-hidden="true"></i>
                <div class="stats-content">
                    <span class="stats-num"><?php echo number_format($likes, 0, '.', ' '); ?></span><br>
                    <span class="stats-label"><?php echo Utils::plural_form($likes, ['Голос', 'Голоса', 'Голосов']); ?></span>
                </div>
            </div>

        </section>
    </div>
</header>