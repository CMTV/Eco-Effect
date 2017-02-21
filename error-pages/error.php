<?php
/** Страница для отображения ошибки на сайте.
 *
 * @see Error_Handler Класс для работы с ошибками на сайте.
 * @see Log Класс для работы с логом ошибок.
 *
 * Обычно принимает в виде URL параметров две переменные: message и additional, где message - сообщение для всех
 * пользователей, а additional - сообщение, которое выводится в режиме отладки.
 *
 * @todo Стилизовать
 */

$message =      ($has_message = (bool)$_GET['message']) ? $_GET['message'] : null;
$additional =   ($has_additional = (bool)$_GET['additional']) ? $_GET['additional'] : null;
?>
<!doctype html>
<html>
<head>
    <title>Ошибка на сайте!</title>

    <style>
        body {
            font-family: sans-serif;
        }

        .error-box {
            border: 1px solid #E10000;
            background: #FFE7E7;
            width: 400px;
            margin: auto auto;
            color: #E10000;
            padding: 15px;
        }
        
        .error-box h1 {
            text-align: center;
        }

        .error-box-additional, .error-box-message {
            color: #000;
        }
    </style>

</head>
<body>

<div class="error-box">
    <h1>Ошибка!</h1>

    <?php if($has_message) { ?>
        <h2>Общая информация:</h2>
        <p class="error-box-message"><?php echo $message; ?></p>
    <?php } ?>

    <?php if($has_additional) { ?>
        <h2>Подробно:</h2>
        <p class="error-box-additional"><?php echo $additional; ?></p>
    <?php } ?>

    <p></p>
</div>

</body>
</html>
