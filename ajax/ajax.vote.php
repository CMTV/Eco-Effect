<?php
/**
 * Обработка действия голосования за фотографию.
 */

require '../load.php';

if(!is_numeric($_POST['photo_id'])) {
    Error_Handler::forbid();
}
$photo_id = intval($_POST['photo_id']);

Photo::vote_for($photo_id);