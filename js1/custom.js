LazyScript.load('owl.carousel', 'lazyload', 'lightbox', 'tabs', 'swiper', 'sendMailOpenPdf', 'member', 'scrollPopup', function(global) {
    // 配置点击按钮提交表单打开pdf
    new SendMailOpenPdf({
        button: '.send-mail-open-pdf',
        form: '.send-mail-open-pdf-form'
    });

    new Member({
        registerForm: '#register',
        registerContainer: '.register',
        loginForm: '#login',
        loginContainer: '.login',
        logoutButton: '#logout',
        userContainer: '.user',
        username: '.username'
    });

    //  首页弹窗
    const popup = new ScrollPopup({
        selector: '.popup',
        form: '.popup .form form',
        result: '.popup .result',
        submit: data => {
            $('.popup .result .message').html(data.message);
            $('.popup .form').hide();
            $('.popup .result').show();

            if (data.status) {
                $('.popup .result .back').hide();
            } else {

            }
        },
        slideDown: 0,
        closeButton: '.popup .form .close, .popup .result .close',
        animationDuration: 1,
        mask: {
            close: true
        }
    });

    $('.popup .result .back').click(e => {
        $('.popup .result').hide();
        $('.popup .form').show();
    });
});