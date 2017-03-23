/**
 * Работа с сервером.
 */

/** Объект обращения к серверу. */
var xhr = new XMLHttpRequest();

/**
 * Является ли данный участник администратором конкурса. Функция работает асинхронно => необходимо использовать
 * функцию then(), которая будет содержать bool значение.
 */
function is_admin() {
    var deferred = $.Deferred();

    xhr.abort();

    xhr.open('POST', '/ajax/ajax.is-admin.php');

    xhr.send();

    xhr.onreadystatechange = function () {
        if(xhr.readyState != 4) return;

        if(xhr.status == 200) {
            deferred.resolve(parseInt(xhr.responseText));
        } else {
            deferred.resolve(false);
        }
    };

    return deferred.promise();
}

/**
 *
 *
 * @param photo_id
 */
function vote(photo_id) {
    $.ajax({
        method: 'POST',
        url:    '/ajax/ajax.vote.php',
        data:   { photo_id: photo_id }
    });
}