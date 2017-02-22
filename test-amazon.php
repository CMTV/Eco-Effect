<?php
/** Тест работы с Amazon */

require_once('load.php');

?>
<!doctype>
<html>
<head>
    <title>Amazon загрузка!</title>
</head>
<body>

<form enctype="multipart/form-data" action="upload.php" method="post">
    <!-- Идентификатор номинации. -->
    <input type="hidden" name="category" value="<?php echo CATEGORY_TRASH_NO; ?>">

    <!-- Максимальный размер загружаемой фотографии. -->
    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_IMG_SIZE; ?>" />

    <!-- Поле загрузки фотографии. -->
    <input type="file" name="photo" accept="image/png, image/gif, image/jpeg">

    <!-- Поле заголовка фотографии. -->
    <input type="text" name="title" placeholder="Название фотографии...">

    <!-- Область описания фотографии. -->
    <textarea name="description"></textarea>

    <button type="button">Отмена</button>
    <button type="submit">Загрузить фото!</button>
</form>

</body>
</html>
