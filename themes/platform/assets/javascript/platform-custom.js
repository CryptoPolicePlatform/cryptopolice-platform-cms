$(function () {

    $('.js-file').change(function () {
        //$parent = $(this).parent();
        $(this).parent().css("color", "#28a745");
    });
});

function setMultipleField(block) {

    event.preventDefault();

    var div = $('#' + block);
    var input = div.find("input");

    var count = input.length + 1;
    var title = input.first().attr('id');
    var placeholder = input.last().attr('placeholder');

    div.append('<input style="margin-top:20px" class="input" type="text" name="' + title + '_' + count + '" placeholder="' + placeholder + '">');

}

function removeField(block) {

    event.preventDefault();
    var div = $('#' + block);
    if (div.find("input").length >= 2) {
        div.find("input").last().remove();
    }

}


$(document).ready(function () {

    $('.breadcrumbs a[href^="#"]').on('click', function (e) {
        e.preventDefault();

        var target = this.hash;
        var $target = $(target);

        $('html, body').stop().animate({
            'scrollTop': $target.offset().top
        }, 900, 'swing', function () {
            window.location.hash = target;
        });

        $('html, body').stop().animate({
            'scrollTop': $target.offset().top
        }, 900, 'swing');
    });

    $('.card__date a[href^="#"]').on('click', function (e) {
        e.preventDefault();
        $('#comment_parent').val($(this).attr('id'));
    });

    $('.notify_title a[href^="#"]').on('click', function (e) {

        e.preventDefault();
        var element = $('#description_' + $(this).attr('id'));
        ($(element).is(":visible")) ? element.hide(400) : element.show(400);
    });

});
