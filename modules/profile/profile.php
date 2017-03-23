<?php
/** Профиль участника. */

include(MODULES . 'profile-forms/profile-forms.php');

?>
<div class="content-wrapper">
    <div class="profile-photos">

        <div class="profile_0_section">
            <!-- Фотография в категории "Мусору НЕТ". -->
            <?php
                if($current_user->has_photo_0) {
                    profile_photo_block(CATEGORY_TRASH_NO);
                    edit_profile_form(CATEGORY_TRASH_NO);
                } else {
                    add_profile_form(CATEGORY_TRASH_NO);
                }
            ?>
        </div>

        <div class="profile_1_section">
            <!-- Фотография в категории "Мусор ЕСТЬ". -->
            <?php
                if($current_user->has_photo_1) {
                    profile_photo_block(CATEGORY_TRASH_YES);
                    edit_profile_form(CATEGORY_TRASH_YES);
                } else {
                    add_profile_form(CATEGORY_TRASH_YES);
                }
            ?>
        </div>
    </div>
</div>