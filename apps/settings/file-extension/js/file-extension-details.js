(function($) {
    'use strict';

    $(function() {
        generateDropdownOptions('file type options');

        displayDetails('get file extension details');

        if($('#file-extension-form').length){
            fileExtensionForm();
        }

        $(document).on('click','#delete-file-extension',function() {
            const file_extension_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'delete file extension';
    
            Swal.fire({
                title: 'Confirm File Extension Deletion',
                text: 'Are you sure you want to delete this file extension?',
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
                        url: 'apps/settings/file-extension/controller/file-extension-controller.php',
                        dataType: 'json',
                        data: {
                            file_extension_id : file_extension_id, 
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
            const file_extension_id = $('#details-id').text();

            logNotes('file_extension', file_extension_id);
        });
    });
})(jQuery);

function fileExtensionForm(){
    $('#file-extension-form').validate({
        rules: {
            file_extension_name: {
                required: true
            },
            file_extension: {
                required: true
            },
            file_type_id: {
                required: true
            }
        },
        messages: {
            file_extension_name: {
                required: 'Enter the display name'
            },
            file_extension: {
                required: 'Enter the file extension'
            },
            file_type_id: {
                required: 'Select the file type'
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
            const file_extension_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update file extension';
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/file-extension/controller/file-extension-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&file_extension_id=' + encodeURIComponent(file_extension_id),
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
                    logNotesMain('file_extension', file_extension_id);
                }
            });
        
            return false;
        }
    });
}

function displayDetails(transaction){
    switch (transaction) {
        case 'get file extension details':
            var file_extension_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/settings/file-extension/controller/file-extension-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    file_extension_id : file_extension_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetForm('file-extension-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#file_extension_name').val(response.fileExtensionName);
                        $('#file_extension').val(response.fileExtension);
                        
                        $('#file_type_id').val(response.fileTypeID).trigger('change');
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
        case 'file type options':
            
            $.ajax({
                url: 'apps/settings/file-type/view/_file_type_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type
                },
                success: function(response) {
                    $('#file_type_id').select2({
                        data: response
                    }).on('change', function (e) {
                        $(this).valid()
                    });
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                }
            });
            break;
    }
}