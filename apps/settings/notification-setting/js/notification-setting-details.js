(function($) {
    'use strict';

    $(function() {
        generateDropdownOptions('email setting options');

        displayDetails('get notification setting details');
        displayDetails('get system notification template details');
        displayDetails('get email notification template details');
        displayDetails('get sms notification template details');

        if($('#notification-setting-form').length){
            notificationSettingForm();
        }

        if($('#update-system-notification-template-form').length){
            systemNotificationTemplateForm();
        }

        if($('#update-email-notification-template-form').length){
            emailNotificationTemplateForm();
        }

        if($('#update-sms-notification-template-form').length){
            smsNotificationTemplateForm();
        }

        if($('#email_notification_body').length){
            initializeTinyMCE('email_notification_body', $('#email_notification_body').is(':disabled') ? 1 : undefined);
        }

        $(document).on('click','#system-notification',function() {
            const notification_setting_id = $('#details-id').text();
            const transaction = 'update system notification';
            const system_notification = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                type: 'POST',
                url: 'apps/settings/notification-setting/controller/notification-setting-controller.php',
                dataType: 'json',
                data: {
                    notification_setting_id : notification_setting_id, 
                    system_notification : system_notification, 
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
        });

        $(document).on('click','#email-notification',function() {
            const notification_setting_id = $('#details-id').text();
            const transaction = 'update email notification';
            const email_notification = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                type: 'POST',
                url: 'apps/settings/notification-setting/controller/notification-setting-controller.php',
                dataType: 'json',
                data: {
                    notification_setting_id : notification_setting_id, 
                    email_notification : email_notification, 
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
        });

        $(document).on('click','#sms-notification',function() {
            const notification_setting_id = $('#details-id').text();
            const transaction = 'update sms notification';
            const sms_notification = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                type: 'POST',
                url: 'apps/settings/notification-setting/controller/notification-setting-controller.php',
                dataType: 'json',
                data: {
                    notification_setting_id : notification_setting_id, 
                    sms_notification : sms_notification, 
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

function systemNotificationTemplateForm(){
    $('#update-system-notification-template-form').validate({
        rules: {
            system_notification_title: {
                required: true
            },
            system_notification_message: {
                required: true
            }
        },
        messages: {
            system_notification_title: {
                required: 'Enter the title'
            },
            system_notification_message: {
                required: 'Enter the message'
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
            const transaction = 'update system notification template';
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/notification-setting/controller/notification-setting-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&notification_setting_id=' + encodeURIComponent(notification_setting_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('submit-system-notification-template');
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
                    enableFormSubmitButton('submit-system-notification-template');
                }
            });
        
            return false;
        }
    });
}

function emailNotificationTemplateForm(){
    $('#update-email-notification-template-form').validate({
        rules: {
            email_notification_subject: {
                required: true
            },
            email_setting_id: {
                required: true
            }
        },
        messages: {
            email_notification_subject: {
                required: 'Enter the subject'
            },
            email_setting_id: {
                required: 'Choose the email sender'
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
            const email_notification_body = encodeURIComponent(tinymce.get('email_notification_body').getContent());
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update email notification template';
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/notification-setting/controller/notification-setting-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&notification_setting_id=' + encodeURIComponent(notification_setting_id) + '&email_notification_body=' + email_notification_body,
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('submit-email-notification-template');
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
                    enableFormSubmitButton('submit-email-notification-template');
                }
            });
        
            return false;
        }
    });
}

function smsNotificationTemplateForm(){
    $('#update-sms-notification-template-form').validate({
        rules: {
            sms_notification_message: {
                required: true
            }
        },
        messages: {
            sms_notification_message: {
                required: 'Enter the message'
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
            const transaction = 'update sms notification template';
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/notification-setting/controller/notification-setting-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&notification_setting_id=' + encodeURIComponent(notification_setting_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('submit-sms-notification-template');
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
                    enableFormSubmitButton('submit-sms-notification-template');
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

                        $('#system-notification').prop('checked', response.systemNotification == 1);
                        $('#email-notification').prop('checked', response.emailNotification == 1);
                        $('#sms-notification').prop('checked', response.smsNotification == 1);

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
        case 'get system notification template details':
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
                        $('#system_notification_title').val(response.systemNotificationTitle);
                        $('#system_notification_message').val(response.systemNotificationMessage);
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
        case 'get email notification template details':
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
                        $('#email_notification_subject').val(response.emailNotificationSubject);
                        $('#email_notification_body').val(response.emailNotificationBody);
                        
                        $('#email_setting_id').val(response.emailSettingID).trigger('change');
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
        case 'get sms notification template details':
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
                        $('#sms_notification_message').text(response.smsNotificationMessage);
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
        case 'email setting options':
            
            $.ajax({
                url: 'apps/settings/email-setting/view/_email_setting_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type
                },
                success: function(response) {
                    $('#email_setting_id').select2({
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