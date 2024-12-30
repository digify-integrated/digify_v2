(function($) {
    'use strict';

    $(function() {
        generateDropdownOptions('file extension options');

        displayDetails('get upload setting details');
        displayDetails('get upload setting file extension details');

        if($('#upload-setting-form').length){
            uploadSettingForm();
        }

        $(document).on('click','#delete-upload-setting',function() {
            const upload_setting_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'delete upload setting';
    
            Swal.fire({
                title: 'Confirm Upload Setting Deletion',
                text: 'Are you sure you want to delete this upload setting?',
                icon: 'warning',
                showCancelButton: !0,
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-danger mt-2',
                    cancelButton: 'btn btn-secondary ms-2 mt-2'
                },
                buttonsStyling: !1
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        type: 'POST',
                        url: 'apps/settings/upload-setting/controller/upload-setting-controller.php',
                        dataType: 'json',
                        data: {
                            upload_setting_id : upload_setting_id, 
                            transaction : transaction
                        },
                        success: function (response) {
                            if (response.success) {
                                setNotification(response.title, response.message, response.messageType);
                                window.location = page_link;
                            }
                            else {
                                if (response.isInactive || response.userNotExist || response.userInactive || response.userLocked || response.sessionExpired) {
                                    setNotification(response.title, response.message, response.messageType);
                                    window.location = 'logout.php?logout';
                                }
                                else if (response.notExist) {
                                    setNotification(response.title, response.message, response.messageType);
                                    window.location = page_link;
                                }
                                else {
                                    showNotification(response.title, response.message, response.messageType);
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            handleSystemError(xhr, status, error);
                        }
                    });
                    return false;
                }
            });
        });

        $(document).on('click','#log-notes-main',function() {
            const upload_setting_id = $('#details-id').text();

            logNotes('upload_setting', upload_setting_id);
        });
    });
})(jQuery);

function uploadSettingForm(){
    $('#upload-setting-form').validate({
        rules: {
            upload_setting_name: {
                required: true
            }
        },
        messages: {
            upload_setting_name: {
                required: 'Enter the display name'
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
            const upload_setting_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update upload setting';
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/upload-setting/controller/upload-setting-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&upload_setting_id=' + encodeURIComponent(upload_setting_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('submit-data');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                    }
                    else {
                        if (response.isInactive || response.userNotExist || response.userInactive || response.userLocked || response.sessionExpired) {
                            setNotification(response.title, response.message, response.messageType);
                            window.location = 'logout.php?logout';
                        }
                        else if (response.notExist) {
                            setNotification(response.title, response.message, response.messageType);
                            window.location = page_link;
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
                    enableFormSubmitButton('submit-data');
                    logNotes('upload_setting', upload_setting_id);
                }
            });
        
            return false;
        }
    });
}

function displayDetails(transaction){
    switch (transaction) {
        case 'get upload setting details':
            var upload_setting_id = $('#details-id').text();
            var page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/settings/upload-setting/controller/upload-setting-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    upload_setting_id : upload_setting_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetForm('upload-setting-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#upload_setting_name').val(response.uploadSettingName);
                        $('#upload_setting_description').val(response.uploadSettingDescription);
                        $('#max_file_size').val(response.maxFileSize);
                    } 
                    else {
                        if (response.isInactive || response.userNotExist || response.userInactive || response.userLocked || response.sessionExpired) {
                            setNotification(response.title, response.message, response.messageType);
                            window.location = 'logout.php?logout';
                        }
                        else if (response.notExist) {
                            setNotification(response.title, response.message, response.messageType);
                            window.location = page_link;
                        }
                        else {
                            showNotification(response.title, response.message, response.messageType);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                }
            });
            break;
        case 'get upload setting file extension details':
            var upload_setting_id = $('#details-id').text();
            var page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/settings/upload-setting/controller/upload-setting-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    upload_setting_id : upload_setting_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetForm('upload-setting-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#allowed_file_extension').val(response.allowedFileExtension).trigger('change');

                        $(document).on('change','#allowed_file_extension',function() {
                            const upload_setting_id = $('#details-id').text();
                            const allowed_file_extension = $('#allowed_file_extension').val();
                            const page_link = document.getElementById('page-link').getAttribute('href'); 

                            const transaction = 'save upload setting file extension';
                    
                            $.ajax({
                                type: 'POST',
                                url: 'apps/settings/upload-setting/controller/upload-setting-controller.php',
                                dataType: 'json',
                                data: {
                                    upload_setting_id : upload_setting_id, 
                                    allowed_file_extension : allowed_file_extension, 
                                    transaction : transaction
                                },
                                success: function (response) {
                                    if (response.success) {
                                        showNotification(response.title, response.message, response.messageType);
                                    }
                                    else {
                                        if (response.isInactive || response.userNotExist || response.userInactive || response.userLocked || response.sessionExpired) {
                                            setNotification(response.title, response.message, response.messageType);
                                            window.location = 'logout.php?logout';
                                        }
                                        else if (response.notExist) {
                                            setNotification(response.title, response.message, response.messageType);
                                            window.location = page_link;
                                        }
                                        else {
                                            showNotification(response.title, response.message, response.messageType);
                                        }
                                    }
                                },
                                error: function(xhr, status, error) {
                                    handleSystemError(xhr, status, error);
                                }
                            });
                            return false;
                        });
                    } 
                    else {
                        if (response.isInactive || response.userNotExist || response.userInactive || response.userLocked || response.sessionExpired) {
                            setNotification(response.title, response.message, response.messageType);
                            window.location = 'logout.php?logout';
                        }
                        else if (response.notExist) {
                            setNotification(response.title, response.message, response.messageType);
                            window.location = page_link;
                        }
                        else {
                            showNotification(response.title, response.message, response.messageType);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                }
            });
            break;
    }
}

function generateDropdownOptions(type){
    switch (type) {
        case 'file extension options':
            
            $.ajax({
                url: 'apps/settings/file-extension/view/_file_extension_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type,
                    multiple : 1
                },
                success: function(response) {
                    $('#allowed_file_extension').select2({
                        data: response
                    })
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                }
            });
            break;
    }
}