/**
 * Кнопки и поле поиска для управления галереей.
 */

/** Скоращения классов элементов управления галереей. */
const UI_SEARCH =       '.gc-search-field';
const UI_SEARCH_LABEL = '.gc-search-field-label';
const UI_SORT_LAST =    '.gc-sort-last';
const UI_SORT_TOP =     '.gc-sort-top';
const UI_SORT_FRIEND =  '.gc-sort-friend';

/** CSS класс, преобразующий элемент управления в возбужденное состояние. */
const CLASS_ACTIVE = 'gc-active';

/** CSS класс кнопки на панели управления галереей. */
const CLASS_BUTTON = 'gc-button';

/** Поле поиска участников. */
var search_field;

/** Иконка поля поиска участников. */
var search_field_label;

/** Кнопка последних участников. */
var button_sort_last;

/** Кнопка топовых участников. */
var button_sort_top;

/** Кнопка фотографий друзей. */
var button_sort_friend;

/** Элементы панели управления галереей. */
var gallery_controls;

/* Инициализация переменных панели управления галереей. */
$(function () {
    search_field =          $(UI_SEARCH);
    search_field_label =    $(UI_SEARCH_LABEL);
    button_sort_last =      $(UI_SORT_LAST);
    button_sort_top =       $(UI_SORT_TOP);
    button_sort_friend =    $(UI_SORT_FRIEND);

    gallery_controls = [
        search_field,
        search_field_label,
        button_sort_last,
        button_sort_top,
        button_sort_friend
    ];
});

/** Друзья пользователя. */
var friends = [];

/**
 * Сброс активного состояния со всех элементов управления галереей.
 */
function reset_controls() {
    gallery_controls.forEach(function (gallery_control_element) {
        gallery_control_element.removeClass(CLASS_ACTIVE);
    });
}

/**
 * Пометка кнопки, как активной.
 *
 * @param sort_type Тип сортировки фотографий. Зависит от того, на какую кнопку кликнули.
 */
function mark_button(sort_type) {
    reset_controls();
    $(sort_type).addClass(CLASS_ACTIVE);
}

/**
 * Поиск участников по введенной строке.
 *
 * @param search_query Строка с именем/ID/адресом участника.
 */
function search_for_users(search_query) {
    clear_gallery(function () {
        get_photos({get_type: GET_SEARCH, search_query: search_query}).then(function (photos) {
            fill_gallery(photos);
        });
    });
}

/* Обработка действий с галереей. */
$(function () {
    /* Клик по кнопке сортировки по последним фотографиям. */
    $(UI_SORT_LAST).click(function () {
        if($(UI_SORT_LAST).hasClass(CLASS_ACTIVE)) { return; }

        mark_button(UI_SORT_LAST);

        clear_gallery(function () {
            get_photos({ get_type: GET_LAST }).then(function (photos) {
                fill_gallery(photos);
            });
        });
    });

    /* Клик по кнопке сортировки по топовым фотографиям. */
    $(UI_SORT_TOP).click(function () {
        if($(UI_SORT_TOP).hasClass(CLASS_ACTIVE)) { return; }

        mark_button(UI_SORT_TOP);

        clear_gallery(function () {
            get_photos({ get_type: GET_TOP }).then(function (photos) {
                fill_gallery(photos);
            });
        });
    });

    /** Есть ли возможность кликнуть на эту кнопку снова. */
    var sort_friend_can_click = true;

    /* Клик по кнопке сортировки по фотографиям друзей. */
    $(UI_SORT_FRIEND).click(function () {
        if(!sort_friend_can_click) {
            return;
        }

        sort_friend_can_click = false;

        mark_button(UI_SORT_FRIEND);

        get_vk_friends(function (vk_answer) {

            if(vk_answer.error_type == 'auth') {
                clear_gallery();
                switch_indicator_to(GALLERY_INDICATOR_RED, 'Не был получен ваш ID. Список друзей недоступен!', 'times');
                sort_friend_can_click = true;
                return;
            }

            if(vk_answer.error) {
                clear_gallery();
                switch_indicator_to(GALLERY_INDICATOR_RED, 'Не удалось получить список друзей!', 'times');
                sort_friend_can_click = true;
                return;
            }

            clear_gallery(function () {
                friends = vk_answer.response;
                get_photos({
                    get_type:   GET_FRIENDS,
                    friends:    friends
                }).then(function (photos) {
                    fill_gallery(photos);
                });
            });

        });

    });

    /* Ввода в поле поиска участников. */
    var input_timeout = 0;
    $(UI_SEARCH).on('input', function () {
        clearTimeout(input_timeout);

        var search_val = $(this).val().trim();

        if(search_val.length != 0) {
            input_timeout = setTimeout(search_for_users, 500, search_val);
        }
    });

    /* Фокус на поле поиска участников фокуса. */
    $(UI_SEARCH).focus(function () {
        reset_controls();

        $(UI_SEARCH).addClass(CLASS_ACTIVE);
        $(UI_SEARCH_LABEL).addClass(CLASS_ACTIVE);
    });

    /* Потеря полем поиска участников фокуса. */
    $(UI_SEARCH).blur(function (e) {
        if($(this).val().trim().length > 0) { return; }

        $(this).val('');

        var target = e.relatedTarget;

        if(!$(target).hasClass(CLASS_BUTTON)) {
            $(UI_SORT_LAST).click();
        }
    });

    /* Прокрутка страницы. */
    var scrollTimeout = 0;
    $(window).scroll(function () {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(load_more_photos, 100);
    });
});

/**
 * Загрузка дополнительных фотографий в галерею (без очистки).
 */
function load_more_photos() {
    if(is_loading_photos) {
        return;
    }

    if(gallery_indicator.hasClass('no-more')) {
        return;
    }

    if(!is_element_in_viewport(gallery_indicator.get(0))) {
        return;
    }

    switch(current_get) {
        case GET_LAST:
            get_photos({ get_type: GET_LAST }).then(function (photos) {
                fill_gallery(photos)
            });
            return;
        case GET_TOP:
            get_photos({ get_type: GET_TOP }).then(function (photos) {
                fill_gallery(photos)
            });
            return;
        case GET_FRIENDS:
            get_photos({
                get_type:   GET_FRIENDS,
                friends:    friends
            }).then(function (photos) {
                fill_gallery(photos);
            });
            return;
        case GET_SEARCH:
            get_photos({get_type: GET_SEARCH, search_query: $(UI_SEARCH).val().trim() }).then(function (photos) {
                fill_gallery(photos);
            });
            return;
    }
}

/**
 * Проверка, находится ли данный DOM элемент в видимой части окна браузера.
 *
 * @param element Элемент для проверки.
 * @param fullyInView Находится ли элемент в видимой части целиком.
 *
 * @returns {boolean}
 */
function is_element_in_viewport(element, fullyInView) {
    var pageTop = $(window).scrollTop();
    var pageBottom = pageTop + $(window).height();
    var elementTop = $(element).offset().top;
    var elementBottom = elementTop + $(element).height();

    if (fullyInView === true) {
        return ((pageTop < elementTop) && (pageBottom > elementBottom));
    } else {
        return ((elementTop <= pageBottom) && (elementBottom >= pageTop));
    }
}