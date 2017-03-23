/**
 * Работа с переключателем шапок и верхним меню.
 */

/** Типы лендингов. */
const HEADER_MAIN =     'must-have';
const HEADER_LANDING =  'landing';
const HEADER_PROFILE =  'profile';
const HEADER_PRIZES =   'prizes';
const HEADER_ABOUT =    'about';

/** Шапка, которая активна в данный момент и будет скрыта. */
var header_to_hide = HEADER_MAIN;

/** Проихсодит ли в данный момент смена шапки. */
var is_switching = false;

/** Плавно скрывает текущую шапку и показывает необходимую за 400 миллисекунд. Можно использовать с помощью then(). */
function switch_header(target_header) {
    if(is_switching) {
        $('.header').finish().hide();
    }

    is_switching = true;

    $('.header-' + header_to_hide).fadeOut(200, function () {
        header_to_hide = target_header;

        $('.header-' + target_header).fadeIn(200, function () {
            is_switching = false;
        });
    });
}