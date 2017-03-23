<?php
/** Проверка, является ли пользователь администратором. */

require_once '../load.php';

echo (int)$session->is_admin();