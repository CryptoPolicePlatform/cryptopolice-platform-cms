
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
    div.find("input").last().remove();
}