(function($) {
    'use strict';

    $(function() {
        if($('#upload-form').length){
            importFormPreview();
        }

        $(document).on('click','#reset-import',function() {
            $('.upload-file-default-preview').removeClass('d-none');
            $('.upload-file-preview').addClass('d-none');
            resetModalForm('upload-form');

            document.getElementById('upload-file-preview-table').innerHTML = '';
        });
    });
})(jQuery);

function importFormPreview(){
    $('#upload-form').validate({
        rules: {
            import_file: {
                required: true
            }
        },
        messages: {
            import_file: {
                required: 'Choose the import file'
            }
        },
        errorPlacement: function(error, element) {
            showNotification('Action Needed: Issue Detected', error, 'error', 2500);
        },
        highlight: function(element) {
            const $element = $(element);
            const $target = $element.hasClass('select2-hidden-accessible') ? $element.next().find('.select2-selection') : $element;
            $target.addClass('is-invalid');
        },
        unhighlight: function(element) {
            const $element = $(element);
            const $target = $element.hasClass('select2-hidden-accessible') ? $element.next().find('.select2-selection') : $element;
            $target.removeClass('is-invalid');
        },
        submitHandler: function(form) {
            const page_link = document.getElementById('page-link').getAttribute('href');
            
            if ($('.upload-file-preview').hasClass('d-none')) {
                var transaction = 'import data preview';
            }
            else {
                var transaction = 'import data';
            }

            var formData = new FormData(form);
            formData.append('transaction', transaction);
        
            $.ajax({
                type: 'POST',
                url: 'components/controller/import-controller.php',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('submit-upload');
                },
                success: function (response) {
                    if (response.success) {
                        if ($('.upload-file-preview').hasClass('d-none')) {
                            $('.upload-file-default-preview').addClass('d-none');
                            $('.upload-file-preview').removeClass('d-none');

                            document.getElementById('upload-file-preview-table').innerHTML = response.table;
                            $('#upload-modal').modal('hide');
                        }
                        else {
                            setNotification(response.title, response.message, response.messageType);
                            window.location = page_link;
                        }
                    }
                    else {
                        if (response.isInactive || response.notExist || response.userInactive || response.userLocked || response.sessionExpired) {
                            setNotification(response.title, response.message, response.messageType);
                            window.location = 'logout.php?logout';
                        }
                        else {
                            showNotification(response.title, response.message, response.messageType);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                },
                complete: function() {
                    enableFormSubmitButton('submit-upload');
                }
            });
        
            return false;
        }
    });
}