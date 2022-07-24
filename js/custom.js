LazyScript.load('owl.carousel', 'lazyload', 'lightbox', 'tabs', 'swiper', function(global) {
    // 点击pdf按钮打开表单
	$('.send-mail-open-pdf').click(function () {
        $('.send-mail-open-pdf-form').show();
        $('.send-mail-open-pdf-form input[name=pdf]').val($(this).data('url'));
    });

    // 表单提交事件
    $('.send-mail-open-pdf-form').submit(function () {
    	var data = {}, value = $(this).serializeArray();

    	for (var i = 0; i < value.length; i++) {
    		data[value[i].name] = value[i].value;
    	}

    	data.api = 1;

    	$.post($(this).attr('action'), data, function (result) {
    		if (result.status === 1) {
    			$('.send-mail-open-pdf-form').hide();
    			window.location.href = data.pdf;
    		} else {
                var message = [];
                for (let key in result.errors) {
                    message.push(key + ': ' + result.errors[key]);
                }
                alert(message.join('\n'))
            }
    	});
    });
});