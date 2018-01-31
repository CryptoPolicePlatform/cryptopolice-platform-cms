
$(function () {

    $('.js-file').change(function () {
        //$parent = $(this).parent();
        $(this).parent().css("color", "#28a745");
    });
});

function setMultipleField(title, description) {

    var buf = Math.random().toString(3);
    event.preventDefault();
    $('#mul_' + title).append('<input style="margin-top:20px" class="input" type="text" name="' + title + '_' + randomString() + '" placeholder="' + ' New ' + description + '">');

}

function randomString() {

    var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
    var string_length = 4;
    var randomstring = '';

    for (var i = 0; i < string_length; i++) {
        var rnum = Math.floor(Math.random() * chars.length);
        randomstring += chars.substring(rnum, rnum + 1);
    }

    return randomstring;
}