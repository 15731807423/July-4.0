LazyScript.load('jquery', function (global) {
	function SendMailOpenPdf(data) {
		this.button = $(data.button || '.send-mail-open-pdf');
		this.form = $(data.form || '.send-mail-open-pdf-form');

		this.success = url => window.location.href = url;
		this.success = data.success || this.success;

		this.error = messages => {
            var message = [];
            for (let key in messages) {
                message.push(key + ': ' + messages[key]);
            }
            alert(message.join('\n'));
		}
		this.error = data.error || this.error;

		this.build = () => {
			this.button.click(() => {
		        this.form.show();
		        this.form.find('input[name=pdf]').val(this.button.data('url'));
		    });

		    this.form.submit(() => {
		    	var data = {}, value = $(this).serializeArray();

		    	for (var i = 0; i < value.length; i++) {
		    		data[value[i].name] = value[i].value;
		    	}

		    	data.api = 1;

		    	$.post($(this).attr('action'), data, result => {
		    		if (result.status === 1) {
		    			this.form.hide();
		    			this.success(data.pdf);
		    		} else {
		    			this.error(result.errors);
		            }
		    	});
		    });
		}

		this.build();
	}

	global.SendMailOpenPdf = SendMailOpenPdf;
});