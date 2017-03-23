/**
 * Работа с профилем пользователя.
 */

/** Объекты Google карт. */
var map_0, map_1;

/** Координаты маркера на карте. */
var marker_0_position = null;
var marker_1_position = null;

/** Инициализация Google Maps API. */
function init_0_map() {
    map_0 = new google.maps.Map(document.getElementById('profile-0-part-map-object'), {
        center: {lat: 55.748889, lng: 37.624612},
        scrollwheel: true,
        zoom: 8
    });

    var map_0_Marker = new google.maps.Marker({
        map: map_0,
        draggable: true,
        title: 'Здесь Мусора НЕТ!',
        icon: '/images/marker_0.png'
    });

    google.maps.event.addListener(map_0_Marker, 'dragend', function (event) {
        marker_0_position = event.latLng;
    });

    if(navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
           var pos = {
               lat: position.coords.latitude,
               lng: position.coords.longitude
           };

            map_0_Marker.setPosition(pos);
            map_0.setCenter(pos);
            marker_0_position = pos;
        });
    }

    google.maps.event.addListener(map_0, 'click', function (event) {
        map_0_Marker.setPosition(event.latLng);
        marker_0_position = event.latLng;
    });
}
function init_1_map() {
    map_1 = new google.maps.Map(document.getElementById('profile-1-part-map-object'), {
        center: {lat: 55.748889, lng: 37.624612},
        scrollwheel: true,
        zoom: 8
    });

    var map_1_Marker = new google.maps.Marker({
        map: map_1,
        draggable: true,
        title: 'Здесь Мусор ЕСТЬ!',
        icon: '/images/marker_1.png'
    });

    google.maps.event.addListener(map_1_Marker, 'dragend', function (event) {
        marker_1_position = event.latLng;
    });

    if(navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            var pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };

            map_1_Marker.setPosition(pos);
            map_1.setCenter(pos);
            marker_1_position = pos;
        });
    }

    google.maps.event.addListener(map_1, 'click', function (event) {
        map_1_Marker.setPosition(event.latLng);
        marker_1_position = event.latLng;
    });
}

$(function () {
    // =================================================================================================================
    // Обработка наведения на окно загрузки фотографии.
    // =================================================================================================================
    var part_0_load = $('.profile_0_section .part-load');
    var part_1_load = $('.profile_1_section .part-load');
    var upload_0_element = $('.profile_0_section .photo-add-input');
    var upload_1_element = $('.profile_1_section .photo-add-input');

    upload_0_element.hover(function() {
        $(part_0_load).addClass('part-load-active');
    }, function() {
        $(part_0_load).removeClass('part-load-active');
    });

    upload_0_element.on('dragenter', function () {
        $(part_0_load).addClass('part-load-active');
    });
    upload_0_element.on('dragleave', function () {
        $(part_0_load).removeClass('part-load-active');
    });

    upload_1_element.hover(function() {
        $(part_1_load).addClass('part-load-active');
    }, function() {
        $(part_1_load).removeClass('part-load-active');
    });

    upload_1_element.on('dragenter', function () {
        $(part_1_load).addClass('part-load-active');
    });
    upload_1_element.on('dragleave', function () {
        $(part_1_load).removeClass('part-load-active');
    });

    // =================================================================================================================
    // Обработка загрузки файла в форму.
    // =================================================================================================================
    $('#photo-0').change(function () {upload_photo_locally(CATEGORY_TRASH_NO)});
    $('#photo-1').change(function () {upload_photo_locally(CATEGORY_TRASH_YES)});

    // =================================================================================================================
    // Обработка кнопки далее при указании маркера на карте.
    // =================================================================================================================
    $('.profile_0_section .part-data-maps-complete').click(function () {
        console.log(marker_0_position);
        switch_load_part(CATEGORY_TRASH_NO, PART_DATA);
    });
    $('.profile_1_section .part-data-maps-complete').click(function () {
        switch_load_part(CATEGORY_TRASH_YES, PART_DATA);
    });

    // =================================================================================================================
    // Обработка кнопки Загрузить фотографию.
    // =================================================================================================================
    $('.profile_0_section .upload-button').click(function () {
        switch_load_part(CATEGORY_TRASH_NO, PART_LOADING);
    });
    $('.profile_1_section .upload-button').click(function () {
        switch_load_part(CATEGORY_TRASH_YES, PART_LOADING);
    });
});

/** Загрузка превью фотографии в бразуер (не на сервер). */
function upload_photo_locally(category) {
    if(!is_file_api()) {
        load_part_error(category, 'Ваш браузер не поддерживает File API!');
        return;
    }

    var file = $('#photo-' + category).get(0).files[0];

    if(file.size > UPLOAD_MAX_SIZE) {
        load_part_error(category, 'Превышен максимальный размер фотографии!');
        return;
    }

    switch(file.type) {
        case 'image/jpeg':
        case 'image/png':
        case 'image/gif':
            break;
        default:
            load_part_error(category, 'Допустимые форматы изображений: PNG, GIF, JPG!');
            return;
    }

    switch_load_part(category, PART_LOADING, function () {
        var reader = new FileReader();

        reader.onload = function () {
            $('.part-map-preview').attr('src', reader.result);
            $('.part-data-preview').attr('src', reader.result);
            switch_load_part(category, PART_MAP, function () {
                if(category == CATEGORY_TRASH_NO) {
                    google.maps.event.trigger(map_0, 'resize');
                    map_0.setCenter({lat: 55.748889, lng: 37.624612});
                } else {
                    google.maps.event.trigger(map_1, 'resize');
                    map_1.setCenter({lat: 55.748889, lng: 37.624612});
                }
            });
        };

        reader.readAsDataURL(file);
    });
}

/**
 * @return boolean Работает ли браузер с File API.
 */
function is_file_api() {
    return !!(window.File && window.FileReader && window.FileList && window.Blob);
}

const PART_LOAD =       'load';
const PART_LOADING =    'loading';
const PART_MAP =        'map-marker';
const PART_DATA =       'data';
const PART_ERROR =      'error';

/** Текущее открытое окно добавления фотографии. */
var part_to_hide = PART_LOAD;

/** Происходит ли в данный момент смена окна добавления фотографии. */
var is_switching_load_part = false;

/**
 * Смена этапа добавления фотографии участника.
 *
 * @param category Номинация, в которую добавляется фотография.
 * @param target_part Окно добавления, которое надо открыть.
 * @param callback Функция, которая будет вызвана при окончании загрузки нового окна.
 */
function switch_load_part(category, target_part, callback) {

    if(is_switching_load_part) {
        $('.add-photo-part').finish().hide();
    }

    is_switching_load_part = true;

    $('.profile_' + category + '_section .part-' + part_to_hide).fadeOut(200, function () {
        part_to_hide = target_part;

        $('.profile_' + category + '_section .part-' + target_part).fadeIn(200, function () {
            is_switching_load_part = false;

            if(callback) {
                callback();
            }
        });
    });
}

/**
 * Ошибка при добавлении фотографии участника.
 *
 * @param category Номинация, в которую добавляется фотография.
 * @param error_text Текст ошибки.
 */
function load_part_error(category, error_text) {
    $('.profile_' + category + '_section .part-' + part_to_hide).fadeOut(200, function () {
       part_to_hide = PART_ERROR;

       $('.part-load-text.error-text p').empty().append(error_text);

       $('.profile_' + category + '_section .part-' + PART_ERROR).fadeIn(200);
    });
}

// =================================================================================================================
// =================================================================================================================