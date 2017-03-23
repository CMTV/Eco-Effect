<?php
/** Переключатель шапок. */

function get_sponsors_block_html() {
?>
    <div class="sponsors">
        <h2>Спонсоры</h2>

        <div class="sponsors-container">
            <?php for($i = 0; $i < count(SPONSORS); $i++) { ?>
                <div class="sponsor">
                    <a target="_blank" title="<?php echo SPONSORS[$i]['title']; ?>" href="<?php echo SPONSORS[$i]['url']; ?>"><img width="100" src="<?php echo SITE_URL . 'images/sponsors/' . SPONSORS[$i]['img']; ?>"></a>
                </div>
            <?php } ?>
        </div>
    </div>
<?php
}

?>
<div id="top-backgrounds">
    <div id="background-linear-gradient"></div>
    <div id="background-image"></div>
    <div id="background-overlay"></div>
</div>

<div class="headers">

    <?php if(!$session->is_authorized()) { ?>
    <div class="header header-must-have header-landing">
        <?php include(MODULES . 'header-landing/header-landing.php'); ?>
    </div>
    <?php } else { ?>
    <div class="header header-must-have header-profile">
        <?php include(MODULES . 'profile/profile.php'); ?>
    </div>
    <?php } ?>

    <div class="header header-prizes">
        <div class="content-wrapper">
            <?php get_sponsors_block_html(); ?>
            <p>Взгляни на мир по-новому!</p>
            <p>
                Победители определяются каждый день на основе количества лайков.<br>
                Затем мы отправляем им электронные переводы и ценные подарки.<br>
            </p>
            <p>
                Минимальная сумма выигрыша — 1 000 рублей.<br>
                Увеличение суммы зависит от спонсоров :)
            </p>
            <p>
                А ценные подарки бывают разными:<br>
                Эко-вкусняшки, эко-полезняшки и эко-сувенирчики.<br>
                Количество и состав тоже зависят от спонсоров :D
            </p>
            <p>Также ждем вас в <a target="_blank" href="https://vk.com/ecothinkerskonkurs">ВКонтакте</a>.</p>
        </div>
    </div>

    <div class="header header-about">
        <div class="content-wrapper">
            <?php get_sponsors_block_html(); ?>
            <p>Взгляни на мир по-новому!</p>
            <p>Не будь равнодушным — заметь красоту и беспорядок, чистоту и мусор!</p>
            <p>Не проходи мимо — сфотографируй и поделись своим фото на этом сайте.</p>
            <p>Надеемся, что эти снимки смогут вдохновить многих на реальные дела!</p>
            <p>Именно вы своими лайками определяете победителей.<br>А мы ежедневно в 24:00 подводим итоги.<br>И награждаем победителей денежными призами и подарками.</p>
            <p>Конкурс продлится 10 дней: с <?php echo Utils::get_date_simplified(new DateTime(COMPETITION_START)); ?> по <?php echo Utils::get_date_simplified((new DateTime(COMPETITION_START))->add(new DateInterval('P10D'))); ?> 2017 года.</p>
            <p>Также ждем вас в <a target="_blank" href="https://vk.com/ecothinkerskonkurs">ВКонтакте</a>.</p>
        </div>
    </div>

</div>

