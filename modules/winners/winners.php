<?php
/** Модуль таблицы лидеров. */

?>
<div class="content-wrapper">

    <h1 class="winners-title"><i class="fa fa-trophy" aria-hidden="true"></i><span>Ежедневные победители!</span></h1>

    <section class="winners">

        <?php

        $previous = null;

        $start_date = DateTime::createFromFormat('d.m.Y', COMPETITION_START);

        for($i = 1; $i <= 10; $i++) {
            $current_date = $start_date->add(new DateInterval('P1D'));

            $day_state = '';

            if(!is_null(WINNERS[$i])) {
                $day_state = 'past';

                $winner_0 = User::get_user(WINNERS[$i]['winner_0']['uid']);
                $winner_1 = User::get_user(WINNERS[$i]['winner_1']['uid']);

                $winner_0_avatar = $winner_0->avatar;
                $winner_1_avatar = $winner_1->avatar;

                $photo_0 = Photo::get_photo_by_id(WINNERS[$i]['winner_0']['photo']);
                $photo_1 = Photo::get_photo_by_id(WINNERS[$i]['winner_1']['photo']);

                $photo_0_data = (new PhotoData($photo_0, $winner_0))->export();
                $photo_1_data = (new PhotoData($photo_1, $winner_1))->export();
            } else {
                if($i != 1) {
                    if(!is_null(WINNERS[$i-1])) {
                        $day_state = 'current';

                        $winner_0_avatar = SITE_URL . 'images/default_avatar_0.png';
                        $winner_1_avatar = SITE_URL . 'images/default_avatar_1.png';
                    } else {
                        $day_state = 'future';

                        $winner_0_avatar = SITE_URL . 'images/default_avatar_blind.png';
                        $winner_1_avatar = SITE_URL . 'images/default_avatar_blind.png';
                    }
                } else {
                    $day_state = 'current';

                    $winner_0_avatar = SITE_URL . 'images/default_avatar_0.png';
                    $winner_1_avatar = SITE_URL . 'images/default_avatar_1.png';
                }
            }
        ?>
            <div class="day day-<?php echo $i; ?> day-<?php echo $day_state; ?>">
                <div class="winner winner-0 <?php if($day_state == 'past') { echo 'photo-watch'; } ?>">
                    <img src="<?php echo $winner_0_avatar; ?>">
                </div>

                <div class="day-separator">
                    <?php echo Utils::get_date_simplified($current_date); ?>
                </div>

                <div class="winner winner-1 <?php if($day_state == 'past') { echo 'photo-watch'; } ?>">
                    <img src="<?php echo $winner_1_avatar; ?>">
                </div>
            </div>
            <?php if($day_state == 'past') { ?>
            <script>
                $('.day-<?php echo $i; ?> .winner-0.photo-watch').data('photoData', parse_photo_data('<?php echo $photo_0_data; ?>'));
                $('.day-<?php echo $i; ?> .winner-1.photo-watch').data('photoData', parse_photo_data('<?php echo $photo_1_data; ?>'));
            </script>
            <? } ?>
        <?php } ?>

    </section>
</div>
