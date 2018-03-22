
if ($('#textarea_form').length) {
    var simplemde = new SimpleMDE({
        element: $("#textarea_form")[0],
        tabSize: 4,
        placeholder: "Type here...",
        forceSync: true,
        toolbar: ["bold", "italic", "strikethrough", "|", "heading-1", "heading-2", "heading-3", "|", "quote", "unordered-list", "ordered-list", "|", "clean-block", "table", "horizontal-rule", "|", "preview"]
    });
}

$(function () {


    $('.js-file').change(function () {
        //$parent = $(this).parent();
        $(this).parent().css("color", "#28a745");
    });

    // Bounty campaigns anchor
    $('.breadcrumbs a[href^="#"]').on('click', function (e) {
        e.preventDefault();

        var target = this.hash;
        var $target = $(target);

        $('html, body').stop().animate({
            'scrollTop': $target.offset().top - $(window).height()/2
        }, 900, 'swing', function () {
            window.location.hash = target;
        });

        $('html, body').stop().animate({
            'scrollTop': $target.offset().top - $(window).height()/2
        }, 900, 'swing');
    });


    // Comment Reply
    $('.card__date a[href^="#"]').on('click', function (e) {

        e.preventDefault();

        $('#replies_to').html('Reply to: <strong>' + $(this).closest('.card__date').find("strong").text() + '</strong> message');
        $('#comment_parent').val($(this).attr('id'));
        $('html, body').stop().animate({
            'scrollTop': $('#comment_form').offset().top - $(window).height()/2
        }, 500);

    });

    // Notifications blocks
    $('.notify_title a[href^="#"]').on('click', function (e) {

        e.preventDefault();

        var element = $('#description_' + $(this).attr('id'));
        ($(element).is(":visible")) ? element.fadeOut( "fast" ) : element.fadeIn( "fast" );

    });

    $('#scam-category').on('change', function () {

        $(this).request('onCategoryChange', {
            data: {
                category_id: this.value
            },
            update: {
                'Scams::form-fields': '#form_fields'
            }
        });
    });
});

// Bounty report from fields
function setMultipleField(block) {

    event.preventDefault();

    var div = $('#' + block);
    var input = div.find("input");

    var title = input.first().attr('id');
    var placeholder = input.last().attr('placeholder');

    div.append('<input style="margin-top:20px" class="input" name="' + title + '[]" placeholder="' + placeholder + '">');

}

// Bounty report from fields
function removeField(block) {

    event.preventDefault();

    var div = $('#' + block);

    if (div.find("input").length >= 2) {
        div.find("input").last().remove();
    }
}


