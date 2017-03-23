/**
 * Индикатор галереи.
 */

/** Контейнер галереи. */
var gallery_indicator;

/** Внутренний контейнер индикатора. */
var gallery_indicator_inner;

/** Стили индикатора галереи. */
const GALLERY_INDICATOR_GREEN =     'green';
const GALLERY_INDICATOR_RED =       'red';
const GALLERY_INDICATOR_YELLOW =    'yellow';
const GALLERY_INDICATOR_LOADING =   'loading';
const GALLERY_INDICATOR_NO_MORE =   'no-more';

/**
 * Сокрытие индикатора галереи и удаление его содержимого.
 *
 * @param callback Функция, вызываемая после сокрытия и удаления содержимого индикатора галереи.
 */
function clear_gallery_indicator(callback) {
    gallery_indicator_inner.finish().fadeOut(200, function () {
        $(this).empty();

        gallery_indicator.removeClass('green red yellow loading no-more');

        if(typeof callback == 'function') {
            callback();
        }
    });
}

/**
 * Отображение индикатора галереи.
 *
 * @param style Стиль индикатора. Отвечает за цвет фона, текста и границы.
 * @param message Сообщение индикатора.
 * @param icon Иконка справа от сообщения индикатора.
 * @param callback Функция, вызываемая после отображения индикатора.
 */
function show_gallery_indicator(style, message, icon, callback) {

    gallery_indicator.addClass(style);

    var indicator_html = '<i class="fa fa-' + icon + '"></i><span>' + message + '</span>';

    gallery_indicator_inner.append(indicator_html).finish().fadeIn(200, function () {
        if(typeof callback == 'function') {
            callback();
        }
    });
}

/**
 * Очистка предыдущего индикатора и вывод нового.
 *
 * @param style Стиль индикатора. Отвечает за цвет фона, текста и границы.
 * @param message Сообщение индикатора.
 * @param icon Иконка справа от сообщения индикатора.
 * @param callback Функция, вызываемая после отображения индикатора.
 */
function switch_indicator_to(style, message, icon, callback) {
    clear_gallery_indicator(function () {
        show_gallery_indicator(style, message, icon, callback);
    });
}

/** Инициализация глобальных переменных. */
$(function () {
    gallery_indicator = $('.gallery-indicator');
    gallery_indicator_inner = gallery_indicator.find('div');
});