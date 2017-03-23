/**
 * Работа с верхним меню.
 */

$(function () {

    $('.tm-button').click(function () {
        if(!$(this).hasClass('tm-active')) {
            $('.tm-button').removeClass('tm-active');
            $(this).addClass('tm-active');

            if($(this).hasClass('tm-main')) {
                switch_header(HEADER_MAIN);
            } else if($(this).hasClass('tm-prizes')) {
                switch_header(HEADER_PRIZES);
            } else if($(this).hasClass('tm-about')) {
                switch_header(HEADER_ABOUT);
            } else if($(this).hasClass('tm-profile')) {
                switch_header(HEADER_PROFILE);
            }
        }
    });

});

