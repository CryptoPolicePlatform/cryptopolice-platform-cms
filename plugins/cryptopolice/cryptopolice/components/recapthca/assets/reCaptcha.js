// reCaptcha
var onloadCallback = function () {
    $(".g-recaptcha").each(function () {
        var el = $(this);
        var captcha = grecaptcha.render($(el).attr("id"), {
            "sitekey": '6Ld4s0AUAAAAAKwVMVLLOuPt6xUcsQd1a_-bQTtJ',
            "callback": function (token) {
                $(el).parent().find(".g-recaptcha-response").val(token);
                $(el).parent().submit();
                grecaptcha.reset(captcha);
            }
        });
    });
};
