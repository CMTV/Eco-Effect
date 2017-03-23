/**
 * Функции и переменные галереи.
 */

/** Получение последних загруженных фотографий. */
const GET_LAST =    1;

/** Получение самых топовых фотографий. */
const GET_TOP =     2;

/** Получение фотографий друзей. */
const GET_FRIENDS = 3;

/** Получение фотографий по поисковому запросу. */
const GET_SEARCH =  5;

/** Количество фотографий, которое необходимо загрузить за одну итерацию. */
const load_amount = 1;

/** Счетчик загруженных в галерею фотографий. */
var photos_loaded = 0;

/** Количество загруженных фотографий в номинации "Мусору НЕТ". */
var photos_0_loaded = 0;

/** Количество загруженных фотографий в номинации "Мусор ЕСТЬ". */
var photos_1_loaded = 0;

/** Происходит ли загрузка фотографий. */
var is_loading_photos = false;

/** Фотографии какого вида сортировки загружены в данный момент. */
var current_get;

/** Ajax объект. */
var ajax;

/** Получение фотографий от сервера.
 *
 * @param options Данные для получения фотографий от сервера. */
function get_photos(options) {
    if(is_loading_photos) {
        return;
    }

    is_loading_photos = true;

    if(ajax)
        ajax.abort();

    var deferred = new $.Deferred();

    switch_indicator_to(GALLERY_INDICATOR_LOADING, 'Зарузка...', 'refresh fa-spin');

    var data = {};

    data.get_type = current_get = options.get_type;
    data.load_amount =  load_amount;
    data.start_at =     photos_loaded;

    if(options.friends !== undefined) {
        data.friends = options.friends;
    }

    if(options.search_query !== undefined) {
        data.search_query = options.search_query;
    }

    ajax = $.ajax({
        method: 'POST',
        url:    '/ajax/ajax.get-photos.php',
        data:   data
    })
        .done(function (response) {
            if(!response || response == 'null') {
                get_photos_error({ error_msg: 'Пустой ответ сервера!' });
                return;
            }

            var result = $.parseJSON(response);

            if(result['error']) {
                get_photos_error(result);
                return;
            }

            deferred.resolve(result);
        })
        .fail(function () {

        })
        .always(function () {
            is_loading_photos = false;
        })
    ;

    return deferred.promise();
}

/**
 * Заполнение галереи фотографиями. Не производит очистку галереи!
 *
 * @param photos Массив с фотографиями.
 */
function fill_gallery(photos) {
    var photos_0 = photos.cat_0;
    var photos_1 = photos.cat_1;

    photos_loaded += Math.max(photos_0.length, photos_1.length);

    if(photos_0.length == 0 && photos_1.length == 0) {
        if(photos_loaded == 0) {
            if($(UI_SEARCH).hasClass(CLASS_ACTIVE)) {
                switch_indicator_to(GALLERY_INDICATOR_NO_MORE, 'Фотографий по вашему запросу не найдено!', 'exclamation');
                return;
            }
            else if($(UI_SORT_FRIEND).hasClass(CLASS_ACTIVE)) {
                switch_indicator_to(GALLERY_INDICATOR_NO_MORE, 'Ваши друзья еще выкладывали фотографии!', 'exclamation');
                return;
            }
            switch_indicator_to(GALLERY_INDICATOR_NO_MORE, 'Фотографии еще не были загружены!', 'exclamation');
            return;
        } else {
            switch_indicator_to(GALLERY_INDICATOR_NO_MORE, 'Больше фотографий не найдено!', 'exclamation');
            return;
        }
    }

    photos_0.forEach(function (photo_0) {
        add_photo_to_gallery(parse_photo_data(photo_0));
    });

    photos_1.forEach(function (photo_1) {
        add_photo_to_gallery(parse_photo_data(photo_1));
    });

    clear_gallery_indicator();
    load_more_photos();
}

/** Отображение сообщения об ошибке при загрузке фотографий из базы данных. */
function get_photos_error(error_obj) {
    switch_indicator_to(GALLERY_INDICATOR_RED, error_obj['error_msg'], 'times');
}

/**
 * Получение HTML кода фотографии участника для добавления в галерею.
 *
 * @param photo_data Данные фотографии.
 */
function get_photo_html(photo_data) {
    return '' +
        '<div id="' + photo_data.photo_id + '-container" data-photo-num="' + (photo_data.category ? photos_1_loaded : photos_0_loaded) + '" class="gallery-photo">' +
        '   <div class="photo-container photo-watch">' +
        '       <div class="loading-spinner" style="height:' + photo_data.thumb_size.height + 'px"><i class="fa fa-refresh fa-spin fa-fw"></i></div>' +
        '   </div>' +
        '   <div class="photo-data">' +
        '       <a target="_blank" href="' + photo_data.user_vk_link + '"><img width="47" class="author-avatar" src="' + photo_data.user_avatar_url + '"></a>' +
        '       <div class="photo-author-text">' +
        '           <h2 class="' + (photo_data.title ? 'title' : 'author') + '">' + (photo_data.title ? photo_data.title : '<a target="_blank" href="' + photo_data.user_vk_link + '">' + photo_data.user_full_name + '</a>') + '</h2>' +
        '       ' + (photo_data.title ? '<a class="author" target="_blank" href="' + photo_data.user_vk_link + '">' + photo_data.user_full_name + '</a>' : '') +
        '       </div>' +
        '       <div class="vote-photo-widget" data-vk-widget-num="' + vk_widgets_counter + '" id="vote-photo-' + photo_data.photo_id + '"></div>' +
        '   </div>' +
        '</div>';
}

/**
 * Получение HTML кода разделителя галереи с датой.
 *
 * @param date Объект даты.
 */
function get_date_sep_html(date) {
    var output_date;

    if(date === 'string') {
        output_date = date;
    } else {
        output_date = get_gallery_date(date);
    }

    return '' +
        '<div class="date-sep">' + output_date + '</div>';
}

/**
 * Прикрепление данных фотографии к самой фотографии. Позволяет открывать в фото в развернутом виде.
 *
 * @param photo_data Данные фотографии.
 */
function attach_photoData_to_photo(photo_data) {
    $('#' + photo_data.photo_id + '-container .photo-container.photo-watch').data('photoData', photo_data);
}

/**
 * Добавление фотографии в галерею.
 *
 * @param photo_data Данные фотографии.
 */
function add_photo_to_gallery(photo_data) {

    var gallery_container = $('.gallery_' + photo_data.category);

    /* Увеличение счетчика фотографий и получение предыдущей фото. */
    var previous_photo_data;
    if(photo_data.category == 0) {
        if($(UI_SORT_LAST).hasClass(CLASS_ACTIVE)) {
            if(photos_0_loaded != 0) {
                previous_photo_data = $('.gallery_0 .gallery-photo[data-photo-num=' + photos_0_loaded + '] .photo-watch').data('photoData');

                if(!dates_equal(previous_photo_data.date, photo_data.date)) {
                    gallery_container.append(get_date_sep_html(photo_data.date));
                }
            } else {
                if(dates_equal(new Date(), photo_data.date)) {
                    gallery_container.append(get_date_sep_html('СЕГОДНЯ'));
                } else {
                    gallery_container.append(get_date_sep_html(photo_data.date));
                }
            }
        }

        photos_0_loaded++;
    } else {
        if($(UI_SORT_LAST).hasClass(CLASS_ACTIVE)) {
            if(photos_1_loaded != 0) {
                previous_photo_data = $('.gallery_1 .gallery-photo[data-photo-num=' + photos_1_loaded + '] .photo-watch').data('photoData');

                if(!dates_equal(previous_photo_data.date, photo_data.date)) {
                    gallery_container.append(get_date_sep_html(photo_data.date));
                }
            } else {
                if(dates_equal(new Date(), photo_data.date)) {
                    gallery_container.append(get_date_sep_html('СЕГОДНЯ'));
                } else {
                    gallery_container.append(get_date_sep_html(photo_data.date));
                }
            }
        }

        photos_1_loaded++;
    }

    /* Получение HTML кода блока фотографии участника. */
    var html = get_photo_html(photo_data);

    /* Добавление фотографии в галерею. */
    gallery_container.append(html);

    /* Прикрепление данных фотографии к блоку фотографии. */
    attach_photoData_to_photo(photo_data);

    /* Инициализация ВКонтакте виджета голосования. */
    add_vote_widget('vote-photo-' + photo_data.photo_id, photo_data, true);

    /* Инициализация объекта фотографии. */
    var photo_image = new Image();

    /* Добавление фотографии в контейнер фотографии */
    $('#' + photo_data.photo_id + '-container').find('.photo-container').append(photo_image);
    $(photo_image).addClass('photo-img');

    /* Действия при полной загрузке фотографии. */
    $(photo_image).on('load', function () {
        $('#' + photo_data.photo_id + '-container').find('.loading-spinner').fadeOut(150, function () {
            $('#' + photo_data.photo_id + '-container').find('.photo-img').hide().fadeIn(150);
        });
    });

    /* Назначение фотографии фотографию. */
    photo_image.src = photo_data.thumb_url;

    /* Плавное появление блока фотографии. */
    $('#' + photo_data.photo_id + '-container.gallery-photo').hide().fadeIn(200);
}

/**
 * Полная очистка всей галереи от фотографий.
 *
 * @param callback Функция, которая будет выполнена после очистки галереи.
 */
function clear_gallery(callback) {
    $('.gallery-photo, .date-sep').fadeOut(200).promise().done(function () {
        $(this).remove();

        photos_loaded = 0;
        photos_0_loaded = 0;
        photos_1_loaded = 0;

        if(typeof callback == 'function') {
            callback();
        }
    });
}