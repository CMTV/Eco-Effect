<?php
/** Выход с сайта. Завершение сессии. */

require_once('load.php');

$session->logout();

Redirect::redirect_to(REDIRECT_INDEX);