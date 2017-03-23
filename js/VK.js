/**
 * Контроллер ВКонтакте виджетов.
 */

/** Счетчик ВКонтакте виджетов. */
var vk_widgets_counter = 1;

/**
 * Генерация уникальной ссылки на страницу фотографии для каждой фотографии.
 *
 * @param photo_id Идентификатор фотографии в базе данных.
 * @returns {string} Ссылка, которую необходимо использовать как значение параметра pageUrl при инициализации виджета
 * ВКонтакте.
 */
function gen_pageUrl(photo_id) {
    return SITE_URL + 'index.php?photo=' + photo_id + '&salt=' + VK_WIDGETS_SALT;
}

/**
 * Инициализация ВКонтакте виджета "Мне нравится".
 *
 * @param widget_id ID DOM элемента, который будет содержать виджет "Мне нравится".
 * @param photo_data Данные о фотографии и о участнике.
 * @param is_small Стоит ли инициализировать уменьшенную версию виджета?
 *
 * @todo Текст поделиться
 */
function add_vote_widget(widget_id, photo_data, is_small) {
    if(is_small === undefined) {
        is_small = false;
    }

    VK.Widgets.Like(widget_id, {
        type: 'vertical',
        pageUrl: gen_pageUrl(photo_data.photo_id),
        height: (is_small ? 20 : 22)
    });

    vk_widgets_counter++;
}

/**
 * Инициализация блока комментариев ВКонтакте.
 *
 * @param widget_id ID DOM элемента, который будет содержать виджет "Мне нравится".
 * @param photo_data Данные о фотографии и о участнике.
 */
function add_comments_widget(widget_id, photo_data) {
    VK.Widgets.Comments(widget_id, {
        pageUrl: gen_pageUrl(photo_data.photo_id)
    });

    vk_widgets_counter++;
}

/**
 * Получение массива ВКонтакте идентификаторов друзей пользователя.
 *
 * @param callback Функция, которая будет вызвана после подключения к VK. Содержит ответ социальной сети.
 */
function get_vk_friends(callback) {
    if(User.is_inited) {
        /* Сразу получаем друзей. */
        VK.Api.call('friends.get', {
            user_id: User.uid,
            order: 'hints'
        }, function (vk_answer) {
            callback(vk_answer);
        });
    } else {
        /* Выводим окно авторизации. */
        VK.Auth.login(function (vk_answer) {
            if(vk_answer.status != 'connected') {
                callback({error: true, error_type: 'auth'});
                return;
            }

            init_user(parseInt(vk_answer.session.user.id), vk_answer.session.user.first_name, vk_answer.session.user.last_name);

            /* Получаем друзей. */
            VK.Api.call('friends.get', {
                user_id: User.uid,
                order: 'hints'
            }, function (vk_answer) {
                callback(vk_answer);
            });
        });
    }
}

/**
 * Замена виджета голосования в галерее при голосовании в развернутом виде.
 *
 * @param photo_id
 */
function replace_liked_in_watcher(photo_id) {
    $('#vote-photo-' + photo_id).empty();
    VK.Widgets.Like('vote-photo-' + photo_id, {
        type: 'vertical',
        pageUrl: gen_pageUrl(photo_id),
        height: 20
    });

    vk_widgets_counter++;
}

/**
 * Регистрация лайков.
 */
$(function () {
    VK.Observer.subscribe("widgets.like.liked", function (likes, vk_widget_number) {
        /* Ищем элемент, соответствующий данному номеру виджета. */
        var vote_button = $('body').find("[data-vk-widget-num='" + vk_widget_number + "']");

        var photo_id = vote_button.attr('id').split('-').pop();

        if(vote_button.attr('id') == ('photo-watcher-' + photo_id)) {
            replace_liked_in_watcher(photo_id);
        }

        vote(photo_id);
    });
    VK.Observer.subscribe("widgets.like.unliked", function (likes, vk_widget_number) {
        /* Ищем элемент, соответствующий данному номеру виджета. */
        var vote_button = $('body').find("[data-vk-widget-num='" + vk_widget_number + "']");

        var photo_id = vote_button.attr('id').split('-').pop();

        if(vote_button.attr('id') == ('photo-watcher-' + photo_id)) {
            replace_liked_in_watcher(photo_id);
        }

        vote(photo_id);
    });
});