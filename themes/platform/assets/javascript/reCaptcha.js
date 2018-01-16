// Coogle recaptcha
var onloadCallback = function () {
    $(".g-recaptcha").each(function () {
        var el = $(this);
        var captcha = grecaptcha.render($(el).attr("id"), {

            "sitekey": '6Lfux0AUAAAAAFNtyFNr0VBkfI-sZfbHgkjcuzPv',
            "callback": function (token) {
                $(el).parent().find(".g-recaptcha-response").val(token);
                $(el).parent().submit();
                grecaptcha.reset(captcha);
            }
        });
    });
};
