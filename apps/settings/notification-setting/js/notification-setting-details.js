(function($) {
    'use strict';

    $(function() {
        displayDetails('get notification setting details');

        if($('#notification-setting-form').length){
            notificationSettingForm();
        }

        if($('#email_notification_template_tinymce').length){
            initializeTinyMCE('email_notification_template_tinymce');
        }

        $(document).on('click', '[data-toggle-section]', function () {
            const section = $(this).data('toggle-section');
            toggleSection(section);
        });

        $(document).on('click','#delete-notification-setting',function() {
            const notification_setting_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'delete notification setting';
    
            Swal.fire({
                title: 'Confirm Notification Setting Deletion',
                text: 'Are you sure you want to delete this notification setting?',
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
                        url: 'apps/settings/notification-setting/controller/notification-setting-controller.php',
                        dataType: 'json',
                        data: {
                            notification_setting_id : notification_setting_id, 
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
            const notification_setting_id = $('#details-id').text();

            logNotes('notification_setting', notification_setting_id);
        });
    });
})(jQuery);

function notificationSettingForm(){
    $('#notification-setting-form').validate({
        rules: {
            notification_setting_name: {
                required: true
            },
            notification_setting_description: {
                required: true
            }
        },
        messages: {
            notification_setting_name: {
                required: 'Enter the display name'
            },
            notification_setting_description: {
                required: 'Enter the description'
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
            const notification_setting_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update notification setting';
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/notification-setting/controller/notification-setting-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&notification_setting_id=' + encodeURIComponent(notification_setting_id),
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
                    logNotesMain('notification_setting', notification_setting_id);
                }
            });
        
            return false;
        }
    });
}

function displayDetails(transaction){
    switch (transaction) {
        case 'get notification setting details':
            var notification_setting_id = $('#details-id').text();
            var page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/settings/notification-setting/controller/notification-setting-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    notification_setting_id : notification_setting_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetForm('notification-setting-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#notification_setting_name').val(response.notificationSettingName);
                        $('#notification_setting_description').val(response.notificationSettingDescription);
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

function toggleSection(section) {
    $(`#${section}_button`).toggleClass('d-none');
    $(`#${section}`).toggleClass('d-none');
    $(`#${section}_edit`).toggleClass('d-none');
}