/**
 * Глобальный файл с общими функциями и константами.
 */

/** Номинация "Мусору НЕТ". */
const CATEGORY_TRASH_NO =   0;

/** Номинация "Мусор ЕСТЬ". */
const CATEGORY_TRASH_YES =  1;

/**
 * @returns boolean Поддерживат ли браузер JS File API.
 */
function has_file_api() {
    return !!(window.File && window.FileReader && window.FileList && window.Blob);
}

/** Перевод библиотеки "Magnific Popup". */
$.extend(true, $.magnificPopup.defaults, {
    tClose: 'Закрыть (Esc)', // Alt text on close button
    tLoading: 'Загрузка...', // Text that is displayed during loading. Can contain %curr% and %total% keys
    gallery: {
        tPrev: 'Предыдущая (Стрелка влево)', // Alt text on left arrow
        tNext: 'Следующая (Стрелка вправо)', // Alt text on right arrow
        tCounter: '%curr% из %total%' // Markup for "1 of 7" counter
    },
    image: {
        tError: '<a href="%url%">Изображение</a> не было загружено.' // Error message when image could not be loaded
    },
    ajax: {
        tError: '<a href="%url%">Данные</a> не были загружены.' // Error message when ajax request failed
    }
});

/**
 * Обработка объекта, хранящего данные о фотографии и ее авторе.
 *
 * @param photoDataJSON Принятый от PHP JSON код данных о фотографии и ее авторе.
 * @returns {{}} Данные фотографии и ее автора.
 */
function parse_photo_data(photoDataJSON) {
    var parsed_json = $.parseJSON(photoDataJSON);

    var pd = {};

    pd.is_photo =           parseInt(parsed_json['is_photo']);

    if(!pd.is_photo) {
        return pd;
    }

    pd.photo_id =           parseInt(parsed_json['photo_id']);
    pd.photo_url =          parsed_json['photo_url'];
    pd.thumb_url =          parsed_json['thumb_url'];
    pd.photo_size =         { width: parsed_json['photo_size']['width'], height: parsed_json['photo_size']['height'] };
    pd.thumb_size =         { width: parsed_json['thumb_size']['width'], height: parsed_json['thumb_size']['height'] };
    pd.date =               parse_data(parsed_json['date']);
    pd.category =           parseInt(parsed_json['category']);
    pd.title =              (parsed_json['title'] ? parsed_json['title'] : null);
    pd.description =        (parsed_json['description'] ? parsed_json['description'] : null);
    pd.has_marker =         parseInt(parsed_json['has_marker']);
    pd.latitude =           (parsed_json['latitude'] ? parseFloat(parsed_json['latitude']) : null);
    pd.longitude =          (parsed_json['longitude'] ? parseFloat(parsed_json['longitude']) : null);
    pd.user_id =            parseInt(parsed_json['user_id']);
    pd.user_full_name =     parsed_json['user_full_name'];
    pd.user_avatar_url =    parsed_json['user_avatar_url'];
    pd.user_vk_link =       parsed_json['user_vk_link'];

    return pd;
}

/**
 * Заполнение данными глобальной переменной, которая содержит данные авторизованного пользователя.
 *
 * @param uid Идентификатор ВКонтакте пользователя.
 * @param first_name Имя пользователя.
 * @param second_name Фамилия пользователя.
 */
function init_user(uid, first_name, second_name) {
    User.is_inited =    true;
    User.uid =          uid;
    User.first_name =   first_name;
    User.second_name =  second_name;
}

/**
 * Создание объекта класса Data из строкового представления даты в MySQL.
 *
 * @param date_string Строковое представление даты в MySQL
 * @returns {Date} Готовый объект даты.
 */
function parse_data(date_string) {
    var t = date_string.split(/[- :]/);

    return new Date(Date.UTC(t[0], t[1]-1, t[2], t[3], t[4], t[5]));
}

/**
 * Получение даты в виде дня и полном названии месяца.
 *
 * @param date Объект даты.
 * @returns {string} Дата в формате "11 Марта".
 */
function get_gallery_date(date) {
    var options = {
        month: 'long',
        day: 'numeric'
    };

    return (date.toLocaleString('ru', options)).toUpperCase();
}

function get_watcher_date(date) {
    var options = {
        month: 'long',
        day: 'numeric'
    };

    return date.toLocaleString('ru', options);
}

function get_full_date(date) {
    var options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
        second: 'numeric'
    };

    return date.toLocaleString('ru', options);
}

/**
 * Проверка, являются ли даты одинаковыми с точностью до дня.
 *
 * @param date_1
 * @param date_2
 * @returns {boolean}
 */
function dates_equal(date_1, date_2) {
    var is_same_day = date_1.getDay() == date_2.getDay();
    var is_same_month = date_1.getMonth() == date_2.getMonth();

    return (is_same_day && is_same_month);
}