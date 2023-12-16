$('form[name=mail]').each(function () {
    $(this).submit(function (event) {
        event.preventDefault();
        const formData = new FormData();
        $(this).find('input, textarea, select').each(function () {
            if ($(this).attr('type') == 'file') {
                if ($(this).prop('multiple')) {
                    if ($(this)[0].files.length) {
                        for (let i = 0; i < $(this)[0].files.length; i++) {
                            formData.append($(this).attr('name') + '[]', $(this)[0].files[i]);
                        }
                    }
                } else {
                    if ($(this)[0].files.length) {
                        formData.append($(this).attr('name'), $(this)[0].files[0]);
                    }
                }
            } else {
                formData.append($(this).attr('name'), $(this).val());
            }
        });

        $.ajax({
            type: 'post',
            data: formData,
            contentType: false,
            processData: false,
            url: $(this).attr('action'),
            success: data => {
                alert(data)
            },
            error: (jqXHR, textStatus, errorThrown) => {
                alert(errorThrown)
            }
        });
    });
});