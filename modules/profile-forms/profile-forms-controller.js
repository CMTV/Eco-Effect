/**
 * Контроллер форм в профиле участника.
 */

/** Типы форм профиля. */
const PF_FORM_ADD = 0;
const PF_FORM_EDIT = 1;

/** Типы окон форм профиля. */
const PF_WINDOW_SELECT_PHOTO = 'select-photo';
const PF_WINDOW_MAP =          'map';
const PF_WINDOW_PHOTO_DATA =   'photo-data';
const PF_WINDOW_LOADING =      'loading';
const PF_WINDOW_ERROR =        'error';

/**
 * Инициализация контроллера формы профиля. Вызывается автоматически при загрузке страницы.
 *
 * @param category Номинация формы: 0 - Мусору "НЕТ", 1 - Мусор "ЕСТЬ".
 * @param type Тип формы: 0 - Форма добавления фотографии, 1 - Форма обновления данных фотографии.
 */
function init_profile_form_controller(category, type) {
    $('.profile-form').each(function () { $(this).get(0).reset(); });

    if(type == PF_FORM_ADD) {
        $(pf_window_path(PF_WINDOW_SELECT_PHOTO, category, type)).hover(function () {
            $(this).addClass('window-select-photo-active');
        }, function () {
            $(this).removeClass('window-select-photo-active');
        });

        $(pf_window_path(PF_WINDOW_SELECT_PHOTO, category, type)).find('.photo-input').on('dragenter', function () {
            $(pf_window_path(PF_WINDOW_SELECT_PHOTO, category, type)).addClass('window-select-photo-active');
        });

        $(pf_window_path(PF_WINDOW_SELECT_PHOTO, category, type)).find('.photo-input').on('dragleave', function () {
            $(pf_window_path(PF_WINDOW_SELECT_PHOTO, category, type)).removeClass('window-select-photo-active');
        });

        $(pf_window_path(PF_WINDOW_SELECT_PHOTO, category, type) + '.photo-input.photo-' + category).change(function () {
            if(!has_file_api()) {
                pf_error('Ваш браузер не поддерживат <span>File API</span>!', category, type);
                return;
            }

            var selected_file = $(this).get(0).files[0];

            if(selected_file.size > UPLOAD_MAX_SIZE) {
                pf_error('Максимальный размер файла: <span>8МБ</span>span>!', category, type);
                return;
            }

            switch(selected_file.type) {
                case 'image/jpeg':
                case 'image/png':
                case 'image/gif':
                    break;
                default:
                    pf_error('Допустимые форматы: <span>PNG</span>, <span>GIF</span>, <span>JPG</span>!', category, type);
                    return;
            }

            pf_switch_window(PF_WINDOW_MAP, category, type);
        });
    }

    $(pf_window_path(PF_WINDOW_ERROR, category, type)).find('button').click(function () {
        $(pf_form_path(category, type)).get(0).reset();
        if(type == PF_FORM_ADD) {
            pf_switch_window(PF_WINDOW_SELECT_PHOTO, category, type);
        } else {
            pf_switch_window(PF_WINDOW_MAP, category, type);
        }
    });

    $(pf_window_path(PF_WINDOW_MAP, category, type)).find('.decide-button.skip').click(function () {
        pf_switch_window(PF_WINDOW_PHOTO_DATA, category, type);
    });

    $(pf_window_path(PF_WINDOW_MAP, category, type)).find('.decide-button.specify').click(function () {

        var map_container_id = 'profile-form-map-' + category + '-' + type;

        var popup_map_html =
            '<div class="profile-form-map-popup">' +
            '   <div class="profile-form-map-popup-top">' +
            '           <form class="map-popup-search-form">' +
            '               <input type="text" class="map-popup-search-field" placeholder="Найти место...">' +
            '               <button type="submit" class="map-popup-search-field-button"><i class="fa fa-search" aria-hidden="true"></i></button>' +
            '           </form>' +
            '       <div class="profile-form-map-popup-controls">' +
            '           <button type="button" class="map-popup-button cancel">Отмена</button>' +
            '           <button type="button" class="map-popup-button no-marker apply cat_' + category + '">Готово!</button>' +
            '       </div>' +
            '   </div>' +
            '   <div class="profile-form-map-popup-desc">Кликните на карте — зафиксируйте место съемки</div>' +
            '   <div class="profile-form-map-popup-map" id="' + map_container_id + '"></div>' +
            '</div>';

        $.magnificPopup.open({
            closeOnBgClick: false,
            items: {
                src: popup_map_html,
                type: 'inline'
            },
            callbacks: {
                open: function() {
                    var latitude_input = $('#latitude-' + category + '-' + type);
                    var longitude_input = $('#longitude-' + category + '-' + type);

                    var start_post_lat;
                    var start_post_lng;

                    if(latitude_input.val() != '') {
                        start_post_lat = parseInt(latitude_input.val());
                        start_post_lng = parseInt(longitude_input.val());
                    } else {
                        start_post_lat = 59.598316;
                        start_post_lng = 57.919922;
                    }

                    var google_map = new google.maps.Map(document.getElementById(map_container_id), {
                        center: { lat: start_post_lat, lng: start_post_lng },
                        zoom: 4,
                        streetViewControl: false
                    });

                    if(latitude_input.val() && longitude_input.val()) {
                        pf_add_photo_marker(category, google_map, { lat: parseFloat(latitude_input.val()), lng: parseFloat(longitude_input.val()) });
                    }

                    google.maps.event.addListener(google_map, 'click', function (event) {
                        pf_clear_markers();

                        pf_add_photo_marker(category, google_map, event.latLng);
                    });

                    $('.map-popup-button.cancel').click(function () {
                        $.magnificPopup.close();
                    });

                    $('.map-popup-search-form').submit(function (e) {
                        e.preventDefault();

                        $('.map-popup-search-field-button').find('i').removeClass('fa-search').addClass('fa-spin fa-refresh');

                        pf_google_map_search($('.map-popup-search-field').val().trim(), google_map, category);
                    });

                    $('.map-popup-button.apply').click(function () {
                        if(!$(this).hasClass('no-marker')) {
                            $.magnificPopup.close();

                            pf_switch_window(PF_WINDOW_PHOTO_DATA, category, type);
                        }
                    });
                },
                close: function() {
                    $('#latitude-' + category + '-' + type).val(marker_position.latitude);
                    $('#longitude-' + category + '-' + type).val(marker_position.longitude);
                }
            }
        });
    });

    $(pf_window_path(PF_WINDOW_PHOTO_DATA, category, type)).find('.decide-button').click(function () {
        pf_switch_window(PF_WINDOW_LOADING, category, type);
    });
}

/** Маркеры на Google Maps карте в данный момент. */
var pf_map_markers = [];

/** Позиция маркера на карте. */
var marker_position = {
    latitude: null,
    longitude: null
};

/**
 * Добавление маркера снятой фотографии на Google Maps карту.
 *
 * @param category Номинация формы: 0 - Мусору "НЕТ", 1 - Мусор "ЕСТЬ".
 * @param map Объект Google Maps карты.
 * @param position Позиция маркера.
 */
function pf_add_photo_marker(category, map, position) {
    var photo_marker = new google.maps.Marker({
        map: map,
        position: position,
        icon: {
            url: (category ? MAP_MARKER_1 : MAP_MARKER_0),
            anchor: new google.maps.Point(20, 64),
            draggable: true
        }
    });

    marker_position.latitude = position.lat;
    marker_position.longitude = position.lng;

    photo_marker.addListener('click', function () {
        pf_clear_markers();
        pf_add_photo_marker(category, map, position);
    });

    pf_map_markers.push(photo_marker);
    $('.map-popup-button').removeClass('no-marker');
}

/**
 * Добавление маркера найденного через поиск места.
 *
 * @param category Номинация формы: 0 - Мусору "НЕТ", 1 - Мусор "ЕСТЬ".
 * @param map Объект Google Maps карты.
 * @param position Позиция маркера.
 */
function pf_add_search_marker(category, map, position) {
    var search_marker = new google.maps.Marker({
        map: map,
        position: position,
        icon: {
            url: MAP_MARKER_SEARCH,
            anchor: new google.maps.Point(12.5, 12.5)
        }
    });

    search_marker.addListener('click', function () {
        pf_clear_markers();
        pf_add_photo_marker(category, map, position);
    });

    pf_map_markers.push(search_marker);
}

/**
 * Удаление всех маркеров с Google Maps карты.
 */
function pf_clear_markers() {
    pf_map_markers.forEach(function (marker) {
        marker.setMap(null);
    });

    pf_map_markers = [];

    $('.map-popup-button').addClass('no-marker');
}

/**
 * Поиск и установка маркеров на Google Maps карту по поисковому запросу.
 *
 * @param query Текст поискового запроса.
 * @param map Объект Google Maps карты.
 * @param category Номинация формы: 0 - Мусору "НЕТ", 1 - Мусор "ЕСТЬ".
 */
function pf_google_map_search(query, map, category) {
    if(query.length == 0) {
        $('.map-popup-search-field-button').find('i').removeClass('fa-spin fa-refresh').addClass('fa-search');
        return;
    }

    pf_clear_markers();

    var google_maps_service = new google.maps.places.PlacesService(map);

    google_maps_service.textSearch({
        bounds: map.getBounds(),
        query: query
    }, function (results, status) {
        if(status == google.maps.places.PlacesServiceStatus.OK) {
            for(var i = 0; i < results.length; i++) {
                var place = results[i];

                pf_add_search_marker(category, map, place.geometry.location);

                if(i == 0) {
                    map.panTo(place.geometry.location);
                }
            }
        }

        $('.map-popup-search-field-button').find('i').removeClass('fa-spin fa-refresh').addClass('fa-search');
    });
}

/**
 * Смена окна формы профиля.
 *
 * @param target_window Целевое окно, которое должно стать активным.
 * @param category Номинация формы: 0 - Мусору "НЕТ", 1 - Мусор "ЕСТЬ".
 * @param type Тип формы: 0 - Форма добавления фотографии, 1 - Форма обновления данных фотографии.
 * @param callback Функция, которая выполнится после смены окна.
 */
function pf_switch_window(target_window, category, type, callback) {
    var profile_form_windows_container = $(pf_form_path(category, type)).find('.profile-form-windows');

    var is_window_switching = profile_form_windows_container.hasClass('profile-form-switching');

    if(is_window_switching) {
        profile_form_windows_container.find('.profile-form-window').finish().hide();
    }

    profile_form_windows_container.addClass('profile-form-switching');

    var window_to_hide = $(pf_form_path(category, type)).find('.profile-form-window-active');

    window_to_hide.removeClass('profile-form-window-active').fadeOut(200, function () {
        $(pf_window_path(target_window, category, type)).addClass('profile-form-window-active').css('display', 'flex').hide().fadeIn(200, function () {
            profile_form_windows_container.removeClass('profile-form-switching');

            if(callback) { callback(); }
        });
    });
}

/**
 * Показ сообщения об ошибке в форме профиля.
 *
 * @param error_msg Сообщение об ошибке. По умолчанию "Произошла ошибка!".
 * @param category Номинация формы: 0 - Мусору "НЕТ", 1 - Мусор "ЕСТЬ".
 * @param type Тип формы: 0 - Форма добавления фотографии, 1 - Форма обновления данных фотографии.
 */
function pf_error(error_msg, category, type) {
    if(!error_msg) {
        error_msg = 'Произошла ошибка!';
    }

    $(pf_window_path(PF_WINDOW_ERROR, category, type) + ' div').empty().append(error_msg);

    pf_switch_window(PF_WINDOW_ERROR, category, type);
}

/**
 * Строка, отображающая путь по DOM к необходимому окну формы профиля.
 *
 * Пример:
 * <pre><code>
 * pf_window_path(PF_WINDOW_MAP, 0, 0);
 * // Выводит: '#profile-form-0-0 .window-map'
 * </code></pre>
 *
 * Пример использования:
 * <pre><code>
 * $ ( pf_window_path(PF_WINDOW_MAP, 0, 0) + 'selector' ).click(...);
 * </code></pre>
 *
 * @param window Идентификатор окна, для которого отображается путь.
 * @param category Номинация формы профиля: 0 - Мусору "НЕТ", 1 - Мусор "ЕСТЬ".
 * @param type Тип формы: 0 - Форма добавления фотографии, 1 - Форма обновления данных фотографии.
 * @param space true (по умолчанию) - добавить в выходную строку пробел, false - без пробела.
 *
 * @returns {string}
 */
function pf_window_path(window, category, type, space) {
    if(space === undefined) {
        space = true;
    }

    return '#profile-form-' + category + '-' + type + ' .window-' + window + ((space) ? ' ' : '');
}

/**
 * Строка, отображающая путь по DOM к необходимой форме.
 *
 * Пример:
 * <pre><code>
 * pf_form_path(0, 0);
 * // Выводит: '#profile-form-0-0'
 * </code></pre>
 *
 * @param category Номинация формы профиля: 0 - Мусору "НЕТ", 1 - Мусор "ЕСТЬ".
 * @param type Тип формы: 0 - Форма добавления фотографии, 1 - Форма обновления данных фотографии.
 * @param space true (по умолчанию) - добавить в выходную строку пробел, false - без пробела.
 *
 * @returns {string}
 */
function pf_form_path(category, type, space) {
    if(space === undefined) {
        space = true;
    }

    return '#profile-form-' + category + '-' + type + ((space) ? ' ' : '');
}