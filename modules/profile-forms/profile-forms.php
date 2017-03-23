<?php

/** Типы построения формы. */
define('FORM_ADD', 0);
define('FORM_EDIT', 1);

/**
 * Получение формы для загрузки фотографии.
 *
 * @param int $category Номинация формы: 0 - Мусору "НЕТ", 1 - Мусор "ЕСТЬ".
 */
function add_profile_form(int $category) {
    forms_builder($category, FORM_ADD);
}

/**
 * Получение формы для редактирования данных загруженной фотографии.
 *
 * @param int $category Номинация формы: 0 - Мусору "НЕТ", 1 - Мусор "ЕСТЬ".
 */
function edit_profile_form(int $category) {
    forms_builder($category, FORM_EDIT);
}

/**
 * Конструктор HTML формы для добавления/редактирования фотографии участника.
 *
 * @param int $category Номинация формы: 0 - Мусору "НЕТ", 1 - Мусор "ЕСТЬ".
 * @param int $type Тип формы: 0 - Добавление фотографии, 1 - Редактирование фотографии.
 */
function forms_builder(int $category, int $type) {
    global $current_user;
?>
    <!-- Форма добавления/редактирования фотографии участника. -->
    <form
        action="<?php echo SITE_URL . 'upload.php' ?>"
        <?php if($type == FORM_EDIT) echo 'style="display: none;"'; ?>
        method="post"
        class="profile-form profile-form-<?php echo $category; ?>"
        enctype="multipart/form-data"
        id="<?php echo 'profile-form-' . $category . '-' . $type; ?>"
    >
        <!-- Техническая информация для сервера. -->
        <div style="display: none">

            <!-- Номинация фотографии. -->
            <input type="hidden" name="category" value="<?php echo $category; ?>">

            <!-- Тип действия: добавление или обновление. -->
            <input type="hidden" name="type" value="<?php echo $type; ?>">

            <?php if($type == FORM_EDIT) { ?>

            <!-- Идентификатор обновляемой фотографии. -->
            <input type="hidden" name="photo-id" value="<?php echo ($category ? $current_user->photo_1->id : $current_user->photo_0->id); ?>">

            <?php } ?>

        </div>

        <!-- Окна формы (часть формы, видимая на сайте). -->
        <div class="profile-form-windows">

            <?php if($type == FORM_ADD) { ?>

            <!-- Окно выбора фотографии на сайт. -->
            <div class="window-select-photo profile-form-window profile-form-window-active">

                <!-- Выбор файла в виде невидимого оверлея. -->
                <input class="photo-input photo-<?php echo $category; ?>" type="file" name="photo" accept="image/jpeg, image/gif, image/png">

                <!-- Видимый контент окна. -->
                <i class="upload-icon fa fa-cloud-upload" aria-hidden="true"></i>
                <div>Форматы: <span>PNG</span>, <span>GIF</span>, <span>JPG</span></div>
                <div>Максимальный размер: <span>8МБ</span></div>

            </div>

            <?php } ?>

            <!-- Окно выбора позиции на карте. -->
            <div class="window-map profile-form-window <?php if($type == FORM_EDIT) { echo 'profile-form-window-active' ;} ?>">

                <!-- Данные маркера на Google Maps карте. -->
                <div style="display: none;">

                    <!-- Широта. -->
                    <input id="<?php echo 'latitude-' . $category . '-' . $type; ?>" type="hidden" name="latitude" <?php if($type == FORM_EDIT) echo 'value="' . $current_user->get_photo($category)->latitude . '"'; ?>>

                    <!-- Долгота. -->
                    <input id="<?php echo 'longitude-' . $category . '-' . $type; ?>" type="hidden" name="longitude" <?php if($type == FORM_EDIT) echo 'value="' . $current_user->get_photo($category)->longitude . '"'; ?>>

                </div>

                <!-- Иконка карты. -->
                <i class="map-icon fa fa-map-marker" aria-hidden="true"></i>

                <!-- Сообщение о указании места съемки. -->
                <?php if($category == CATEGORY_TRASH_NO) { ?>
                    <div>Покажите на карте, где такая красота!</div>
                <?php } else { ?>
                    <div>Покажите на карте, где скрывается мусор!</div>
                <?php } ?>

                <!-- Кнопки решения: пропустить или указать местоположение. -->
                <div class="decide-buttons">
                    <button type="button" class="decide-button skip">Пропустить</button>
                    <button type="button" class="decide-button specify">Указать на карте</button>
                </div>

            </div>

            <!-- Окно данных фотографии. -->
            <div class="window-photo-data profile-form-window">

                <?php
                if($category == CATEGORY_TRASH_NO) {
                    $title_placeholder = 'Какая красота в парке!';
                    $description_placeholder = 'Невероятно приятно видеть красивые и чистые места сбора мусора. Редкость в наши дни...';
                } else {
                    $title_placeholder = 'Какой ужас в парке!';
                    $description_placeholder = 'Настоящий произвол — все завалено мусором! Не могу больше спокойно на это смотреть!';
                }
                ?>

                <label class="photo-data-label" for="title-<?php echo $category . '-' . $type; ?>">Заголовок фото</label>
                <input class="photo-data-title cat_<?php echo $category; ?>" id="title-<?php echo $category . '-' . $type; ?>" type="text" maxlength="70" name="title" placeholder="<?php echo $title_placeholder; ?>"
                    <?php if($type == FORM_EDIT) echo 'value="' . htmlspecialchars($current_user->get_photo($category)->title) . '"'; ?>>

                <label class="photo-data-label" for="description-<?php echo $category . '-' . $type; ?>">Описание</label>
                <textarea class="photo-data-description cat_<?php echo $category; ?>" id="description-<?php echo $category . '-' . $type; ?>" maxlength="200" name="description" placeholder="<?php echo $description_placeholder; ?>"><?php if($type == FORM_EDIT) echo $current_user->get_photo($category)->description; ?></textarea>

                <!-- Кнопки решения: пропустить или указанить данные фотографии. -->
                <div class="decide-buttons">
                    <button type="submit" class="decide-button skip">Пропустить</button>
                    <button type="submit" class="decide-button specify"><?php if($type == FORM_ADD) echo 'Опубликовать!'; else echo 'Изменить!'; ?></button>
                </div>

            </div>

            <!-- Техническое окно: загрузка. -->
            <div class="window-loading profile-form-window">

                <!-- Иконка загрузки. -->
                <i class="loading-icon fa fa-refresh fa-spin fa-fw"></i>

                <!-- Сообщение (опционально). -->
                <div>Загрузка вашей фотографии!</div>

            </div>

            <!-- Техническое окно: ошибка. -->
            <div class="window-error profile-form-window">

                <!-- Иконка ошибки. -->
                <i class="error-icon fa fa-times" aria-hidden="true"></i>

                <!-- Сообщение ошибки (опционально). -->
                <div></div>

                <!-- Кнопка "Еще раз". -->
                <button type="button">Еще раз</button>

            </div>

        </div>

    </form>

    <!-- Инициализация JS-контроллера формы. -->
    <script>
        $(function () {
            init_profile_form_controller(<?php echo $category . ',' . $type; ?>);
        });
    </script>

    <?php if($type == FORM_EDIT) echo '<style>.profile_' . $category . '_section .window-map { display: flex; }</style>'; ?>
<?php
}

/**
 * Блок фотографии участника.
 *
 * @param int $category Номинация формы: 0 - Мусору "НЕТ", 1 - Мусор "ЕСТЬ".
 */
function profile_photo_block(int $category) {
    global $current_user;

    $photo = $category ? $current_user->photo_1 : $current_user->photo_0;

?>
    <!-- Блок фотографии участника. -->
    <div class="profile-photo-container" id="profile-photo-container-<?php echo $category; ?>">

        <!-- Контейнер фотографии участника. -->
        <div class="profile-photo-img-container photo-watch" id="profile-photo-watch-<?php echo $category; ?>">

            <!-- Миниатюра фотографии участника. -->
            <img class="profile-photo-img" src="<?php echo $photo->thumbnail_url; ?>">

        </div>

        <!-- Данные фотографии участника. -->
        <div class="profile-photo-data-container">

            <!-- Статистика фотографии и кнопка "Поделиться". -->
            <div class="profile-photo-stats-container">

                <!-- Количество лайков. -->
                <div class="profile-photo-stats"><i class="fa fa-heart" aria-hidden="true"></i><span><?php echo number_format($photo->likes, 0, '.', ' '); ?></span></div>

                <!-- Позиция в топе. -->
                <div class="profile-photo-stats"><i class="fa fa-signal" aria-hidden="true"></i><span><?php echo number_format(Photo::get_top_position($photo), 0, '.', ' '); ?></span></div>

                <!-- Кнопка "Поделиться". -->
                <?php echo VK::get_share_link($current_user->get_photo($category)); ?>

            </div>

            <!-- Кнопки управления фотографией. -->
            <div class="profile-photo-controls">

                <!-- Кнопка "Редактировать". -->
                <div class="profile-photo-edit" id="profile-photo-edit-<?php echo $category; ?>" title="Редактировать данные фотографии"><i class="fa fa-pencil" aria-hidden="true"></i></div>

                <!-- Кнопка "Удалить". -->
                <a class="profile-photo-remove-link cat-<?php echo $category; ?>" href="<?php echo SITE_URL . 'remove-photo.php?category=' . $category; ?>"><div class="profile-photo-remove" title="Удалить фотографию"><i class="fa fa-trash" aria-hidden="true"></i></div></a>

            </div>

        </div>
    </div>

    <!-- Инициализация компонентов и отслеживание событий фотографии участника. -->
    <script>
        $(function () {

            var profile_photo_data = parse_photo_data('<?php echo (new PhotoData($photo, $current_user))->export(); ?>');

            $('#profile-photo-watch-<?php echo $category; ?>').data('photoData', profile_photo_data);

            $('#profile-photo-edit-<?php echo $category; ?>').click(function () {
                $('#profile-photo-container-<?php echo $category; ?>').fadeOut(200, function () {
                    $('#<?php echo 'profile-form-' . $category . '-' . FORM_EDIT; ?>').fadeIn(200);
                });
            });

            $('.profile-photo-remove-link.cat-<?php echo $category; ?>').click(function (e) {
                if(!confirm('Фотография, ее данные и голоса будут удалены навсегда!')) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    </script>
<?
}