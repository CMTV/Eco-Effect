/**
 * Контроллер всплывающего окна с фотографией.
 */

$(function () {
    $('body').on('click', '.photo-watch', function () {
        var photo_data = $(this).data('photoData');

        open_photo_watcher(photo_data);
    });
});

/** Переменные, используемые при инициализации окна с фотографией. */
var watcher_vars = {};

/**
 * Открыть фотографию в развернутом виде.
 *
 * @param photo_data Данные фотографии.
 */
function open_photo_watcher(photo_data) {
    if(!photo_data.is_photo) {

        var modal_html = '' +
            '<div class="no-photo-modal">' +
            'Участник удалил или не загрузил фотографию!' +
            '</div>';

        $.magnificPopup.open({
            type: 'inline',
            items: {
                src: modal_html
            }
        });

        return;
    }

    $.magnificPopup.open({
        items: { type: 'inline', src: get_watcher_markup(photo_data) },
        callbacks: {
            open: function () {
                $('.mfp-inline-holder .mfp-content, .mfp-ajax-holder .mfp-content').css('width', 'auto');

                var photo = $('img.mfp-img').get(0);

                $(photo).on('load', function () {
                    $('.mfp-wrap .loading-spinner').fadeOut(150, function () {
                        $(photo).hide().css('max-height', window.innerHeight + 'px').show();
                    });
                });

                photo.src = photo_data.photo_url;

                /* Инициализация Google Maps карты. */
                if(photo_data.has_marker) {
                    init_google_map(photo_data.latitude, photo_data.longitude, photo_data.category);
                }

                /* Инициализация виджета голосования ВКонтакте. */
                add_vote_widget(watcher_vars.vk_widget_id, photo_data);

                /* Инициализация блока комментариев ВКонтакте. */
                add_comments_widget(watcher_vars.vk_comments_id, photo_data);

                /* Действия администраторов. */
                is_admin().then(function (is_admin) {
                    if(is_admin) {
                        $('.pwd-admin-actions').append('' +
                            '<a href="/admin.php?photo=' + photo_data.photo_id + '&uid=' + photo_data.user_id + '&action=delete">Удалить</a>' +
                            '<a href="/admin.php?photo=' + photo_data.photo_id + '&uid=' + photo_data.user_id + '&action=ban">Заблокировать</a>'
                        );
                    } else {
                        $('.pwd-admin-actions').hide();
                    }
                });
            }
        }
    });
}

/**
 * Инициализация Google Maps карты, на которой будет отображаться маркер места съемки фотографии.
 */
function init_google_map(latitude, longitude, category) {
    var map_options = {
        center: {lat: latitude, lng: longitude},
        streetViewControl: false,
        scrollwheel: false,
        zoom: 8
    };

    var map = new google.maps.Map(document.getElementById(watcher_vars.map_id), map_options);

    new google.maps.Marker({
        map: map,
        position: {lat: latitude, lng: longitude},
        icon: {
            url: (category ? MAP_MARKER_1 : MAP_MARKER_0),
            anchor: new google.maps.Point(20, 64),
            draggable: true
        }
    });
}

/**
 * Получение HTML кода всплывающего окна с развернутой фотографией.
 *
 * @param photo_data Данные развертываемой фотографии.
 * @returns {string} HTML код всплывающего окна.
 */
function get_watcher_markup(photo_data) {
    /** Идентификатор DOM элемента виджета "Мне нравится" ВКонтакте. */
    watcher_vars.vk_widget_id = 'photo-watcher-' + photo_data.photo_id;

    /** Идентификатор DOM элемента Google Maps карты. */
    watcher_vars.map_id = 'map-' + photo_data.photo_id;

    /** Идентификатор DOM элемента комментариев ВКонтакте. */
    watcher_vars.vk_comments_id = 'photo-watcher-comments-' + photo_data.photo_id;

    return '' +
        '<div class="photo-watcher mfp-figure">' +
        '   <button title="Закрыть (Esc)" type="button" class="mfp-close">×</button>' +
        '   <div class="loading-spinner cat-' + photo_data.category + '"><div><i class="fa fa-refresh fa-spin fa-fw"></i></div></div>' +
        '   <img class="mfp-img" style="display: none">' +
        '   <div class="mfp-bottom-bar">' +
        '       <div class="photo-watcher-data-container">' +
        '           <div class="photo-watcher-data">' +
        '<!-- ====================== Данные фотографии -->' +
        '' +
        '<!-- Шапка данных фотографии. -->' +
        '<div class="pwd-header">' +
        '   <div class="pwd-vote-container cat-' + photo_data.category + '"><div data-vk-widget-num="' + vk_widgets_counter + '" id="' + watcher_vars.vk_widget_id + '"></div></div>' +
        '   <a target="_blank" href="' + photo_data.user_vk_link + '"><img class="pwd-author-avatar" src="' + photo_data.user_avatar_url + '"></a>' +
        '   <div class="pwd-title-author-container">' +
        '       <h2 class="' + (photo_data.title ? 'title' : 'author') + '">' + (photo_data.title ? photo_data.title : '<a target="_blank" href="' + photo_data.user_vk_link + '">' + photo_data.user_full_name + '</a>') + '</h2>' +
        '       <div class="pwd-author-date">' + (photo_data.title ? '<a target="_blank" href="' + photo_data.user_vk_link + '">' + photo_data.user_full_name + '</a>' : '') + (photo_data.title ?  ' • ' : '') + '<span title="' + get_full_date(photo_data.date) + '">' + get_watcher_date(photo_data.date) + '</span>' + '</div>' +
        '   </div>' +
        '</div>' +
        '' +
        '<!-- Действия администраторов. -->' +
        '<div class="pwd-admin-actions"></div>' +
        '' +
        '<!-- Описание фотографии. -->' +
        '<div class="pwd-description">' + (photo_data.description ? '<hr>' + photo_data.description : '') + '</div>' +
        '' +
        '<!-- Карта с маркером места съемки фотографии. -->' +
        (photo_data.has_marker ? '<div class="pwd-map" id="' + watcher_vars.map_id + '"></div>' : '') +
        '' +
        '<!-- ВКонтакте комментарии к фотографии. -->' +
        '<div class="pwd-comments"><div id="' + watcher_vars.vk_comments_id + '"></div></div>' +
        '' +
        '<!-- ======================================== -->' +
        '           </div>' +
        '       </div>' +
        '   </div>' +
        '</div>';
}