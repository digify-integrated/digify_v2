(function($) {
    'use strict';

    $(function() {
        displayDetails('get notification setting details');
        displayDetails('get system notification template details');
        displayDetails('get email notification template details');
        displayDetails('get sms notification template details');

        if($('#notification-setting-form').length){
            notificationSettingForm();
        }

        if($('#system-notification-template-form').length){
            systemNotificationTemplateForm();
        }

        if($('#email-notification-template-form').length){
            emailNotificationTemplateForm();
        }

        if($('#sms-notification-template-form').length){
            smsNotificationTemplateForm();
        }

        $(document).on('click','#edit-details',function() {
            displayDetails('get notification setting details');
        });

        $(document).on('click','#delete-notification-setting',function() {
            const notification_setting_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href');
            const transaction = 'delete notification setting';
    
            Swal.fire({
                title: 'Confirm User Account Deletion',
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
                        url: 'components/notification-setting/controller/notification-setting-controller.php',
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
                            var fullErrorMessage = `XHR status: ${status}, Error: ${error}`;
                            if (xhr.responseText) {
                                fullErrorMessage += `, Response: ${xhr.responseText}`;
                            }
                            showErrorDialog(fullErrorMessage);
                        }
                    });
                    return false;
                }
            });
        });

        $(document).on('change','#system-notification',function() {
            const notification_setting_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href');
            var checkbox = document.getElementById('system-notification');
            var transaction = (checkbox).checked ? 'enable system notification channel' : 'disable system notification channel';

            $.ajax({
                type: 'POST',
                url: 'components/notification-setting/controller/notification-setting-controller.php',
                data: {
                    notification_setting_id : notification_setting_id,
                    transaction : transaction
                },
                dataType: 'json',
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
                    var fullErrorMessage = `XHR status: ${status}, Error: ${error}`;
                    if (xhr.responseText) {
                        fullErrorMessage += `, Response: ${xhr.responseText}`;
                    }
                    showErrorDialog(fullErrorMessage);
                }
            });
        });

        $(document).on('change','#email-notification',function() {
            const notification_setting_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href');
            var checkbox = document.getElementById('email-notification');
            var transaction = (checkbox).checked ? 'enable email notification channel' : 'disable email notification channel';

            $.ajax({
                type: 'POST',
                url: 'components/notification-setting/controller/notification-setting-controller.php',
                data: {
                    notification_setting_id : notification_setting_id,
                    transaction : transaction
                },
                dataType: 'json',
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
                    var fullErrorMessage = `XHR status: ${status}, Error: ${error}`;
                    if (xhr.responseText) {
                        fullErrorMessage += `, Response: ${xhr.responseText}`;
                    }
                    showErrorDialog(fullErrorMessage);
                }
            });
        });

        $(document).on('change','#sms-notification',function() {
            const notification_setting_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href');
            var checkbox = document.getElementById('sms-notification');
            var transaction = (checkbox).checked ? 'enable sms notification channel' : 'disable sms notification channel';

            $.ajax({
                type: 'POST',
                url: 'components/notification-setting/controller/notification-setting-controller.php',
                data: {
                    notification_setting_id : notification_setting_id,
                    transaction : transaction
                },
                dataType: 'json',
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
                    var fullErrorMessage = `XHR status: ${status}, Error: ${error}`;
                    if (xhr.responseText) {
                        fullErrorMessage += `, Response: ${xhr.responseText}`;
                    }
                    showErrorDialog(fullErrorMessage);
                }
            });
        });

        if($('#log-notes-offcanvas').length){
            $(document).on('click','#view-email-notification-template-log-notes',function() {
                const notification_setting_id = $('#details-id').text();

                logNotes('notification_setting_email_template', notification_setting_id);
            });

            $(document).on('click','#view-system-notification-template-log-notes',function() {
                const notification_setting_id = $('#details-id').text();

                logNotes('notification_setting_system_template', notification_setting_id);
            });

            $(document).on('click','#view-sms-notification-template-log-notes',function() {
                const notification_setting_id = $('#details-id').text();

                logNotes('notification_setting_sms_template', notification_setting_id);
            });
        }

        if($('#log-notes-main').length){
            const notification_setting_id = $('#details-id').text();

            logNotesMain('notification_setting', notification_setting_id);
        }

        if($('#internal-notes').length){
            const notification_setting_id = $('#details-id').text();

            internalNotes('notification_setting', notification_setting_id);
        }

        if($('#internal-notes-form').length){
            const notification_setting_id = $('#details-id').text();

            internalNotesForm('notification_setting', notification_setting_id);
        }
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
            showNotification('Attention Required: Error Found', error, 'error', 2000);
        },
        highlight: function(element) {
            var inputElement = $(element);
            if (inputElement.hasClass('select2-hidden-accessible')) {
                inputElement.next().find('.select2-selection').addClass('is-invalid');
            }
            else {
                inputElement.addClass('is-invalid');
            }
        },
        unhighlight: function(element) {
            var inputElement = $(element);
            if (inputElement.hasClass('select2-hidden-accessible')) {
                inputElement.next().find('.select2-selection').removeClass('is-invalid');
            }
            else {
                inputElement.removeClass('is-invalid');
            }
        },
        submitHandler: function(form) {
            const notification_setting_id = $('#details-id').text();
            const transaction = 'update notification setting';
          
            $.ajax({
                type: 'POST',
                url: 'components/notification-setting/controller/notification-setting-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction +'&notification_setting_id=' + notification_setting_id,
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('submit-data');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        displayDetails('get notification setting details');
                        $('#notification-setting-modal').modal('hide');
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
                    var fullErrorMessage = `XHR status: ${status}, Error: ${error}`;
                    if (xhr.responseText) {
                        fullErrorMessage += `, Response: ${xhr.responseText}`;
                    }
                    showErrorDialog(fullErrorMessage);
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
    $('#system-notification-template-form').validate({
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
                required: 'Enter the notification title'
            },
            system_notification_message: {
                required: 'Enter the notification message'
            }
        },
        errorPlacement: function(error, element) {
            showNotification('Attention Required: Error Found', error, 'error', 2000);
        },
        highlight: function(element) {
            var inputElement = $(element);
            if (inputElement.hasClass('select2-hidden-accessible')) {
                inputElement.next().find('.select2-selection').addClass('is-invalid');
            }
            else {
                inputElement.addClass('is-invalid');
            }
        },
        unhighlight: function(element) {
            var inputElement = $(element);
            if (inputElement.hasClass('select2-hidden-accessible')) {
                inputElement.next().find('.select2-selection').removeClass('is-invalid');
            }
            else {
                inputElement.removeClass('is-invalid');
            }
        },
        submitHandler: function(form) {
            const notification_setting_id = $('#details-id').text();
            const transaction = 'update system notification template';
          
            $.ajax({
                type: 'POST',
                url: 'components/notification-setting/controller/notification-setting-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction +'&notification_setting_id=' + notification_setting_id,
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('system-notification-template-submit-data');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        displayDetails('get system notification template details');
                        $('#system-notification-template-modal').modal('hide');
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
                    var fullErrorMessage = `XHR status: ${status}, Error: ${error}`;
                    if (xhr.responseText) {
                        fullErrorMessage += `, Response: ${xhr.responseText}`;
                    }
                    showErrorDialog(fullErrorMessage);
                },
                complete: function() {
                    enableFormSubmitButton('system-notification-template-submit-data');
                }
            });
        
            return false;
        }
    });
}

function emailNotificationTemplateForm(){
    $('#email-notification-template-form').validate({
        rules: {
            email_notification_subject: {
                required: true
            }
        },
        messages: {
            email_notification_subject: {
                required: 'Enter the email subject'
            }
        },
        errorPlacement: function(error, element) {
            showNotification('Attention Required: Error Found', error, 'error', 2000);
        },
        highlight: function(element) {
            var inputElement = $(element);
            if (inputElement.hasClass('select2-hidden-accessible')) {
                inputElement.next().find('.select2-selection').addClass('is-invalid');
            }
            else {
                inputElement.addClass('is-invalid');
            }
        },
        unhighlight: function(element) {
            var inputElement = $(element);
            if (inputElement.hasClass('select2-hidden-accessible')) {
                inputElement.next().find('.select2-selection').removeClass('is-invalid');
            }
            else {
                inputElement.removeClass('is-invalid');
            }
        },
        submitHandler: function(form) {
            const notification_setting_id = $('#details-id').text();
            const email_notification_body = encodeURIComponent(tinymce.get('email_notification_body').getContent());
            const transaction = 'update email notification template';
          
            $.ajax({
                type: 'POST',
                url: 'components/notification-setting/controller/notification-setting-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction +'&notification_setting_id=' + notification_setting_id +'&email_notification_body=' + email_notification_body,
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('email-notification-template-submit-data');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        displayDetails('get email notification template details');
                        $('#email-notification-template-modal').modal('hide');
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
                    var fullErrorMessage = `XHR status: ${status}, Error: ${error}`;
                    if (xhr.responseText) {
                        fullErrorMessage += `, Response: ${xhr.responseText}`;
                    }
                    showErrorDialog(fullErrorMessage);
                },
                complete: function() {
                    enableFormSubmitButton('email-notification-template-submit-data');
                }
            });
        
            return false;
        }
    });
}

function smsNotificationTemplateForm(){
    $('#sms-notification-template-form').validate({
        rules: {
            sms_notification_message: {
                required: true
            }
        },
        messages: {
            sms_notification_message: {
                required: 'Enter the notification message'
            }
        },
        errorPlacement: function(error, element) {
            showNotification('Attention Required: Error Found', error, 'error', 2000);
        },
        highlight: function(element) {
            var inputElement = $(element);
            if (inputElement.hasClass('select2-hidden-accessible')) {
                inputElement.next().find('.select2-selection').addClass('is-invalid');
            }
            else {
                inputElement.addClass('is-invalid');
            }
        },
        unhighlight: function(element) {
            var inputElement = $(element);
            if (inputElement.hasClass('select2-hidden-accessible')) {
                inputElement.next().find('.select2-selection').removeClass('is-invalid');
            }
            else {
                inputElement.removeClass('is-invalid');
            }
        },
        submitHandler: function(form) {
            const notification_setting_id = $('#details-id').text();
            const transaction = 'update sms notification template';
          
            $.ajax({
                type: 'POST',
                url: 'components/notification-setting/controller/notification-setting-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction +'&notification_setting_id=' + notification_setting_id,
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('sms-notification-template-submit-data');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        displayDetails('get sms notification template details');
                        $('#sms-notification-template-modal').modal('hide');
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
                    var fullErrorMessage = `XHR status: ${status}, Error: ${error}`;
                    if (xhr.responseText) {
                        fullErrorMessage += `, Response: ${xhr.responseText}`;
                    }
                    showErrorDialog(fullErrorMessage);
                },
                complete: function() {
                    enableFormSubmitButton('sms-notification-template-submit-data');
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
                url: 'components/notification-setting/controller/notification-setting-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    notification_setting_id : notification_setting_id, 
                    transaction : transaction
                },
                success: function(response) {
                    if (response.success) {
                        $('#notification_setting_name').val(response.notificationSettingName);
                        $('#notification_setting_description').val(response.notificationSettingDescription);
                        
                        $('#notification_setting_name_summary').text(response.notificationSettingName);
                        $('#notification_setting_description_summary').text(response.notificationSettingDescription);

                        document.getElementById('system-notification').checked = response.systemNotification === 1;
                        document.getElementById('email-notification').checked = response.emailNotification === 1;
                        document.getElementById('sms-notification').checked = response.smsNotification === 1;
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
                    var fullErrorMessage = `XHR status: ${status}, Error: ${error}`;
                    if (xhr.responseText) {
                        fullErrorMessage += `, Response: ${xhr.responseText}`;
                    }
                    showErrorDialog(fullErrorMessage);
                }
            });
            break;
        case 'get system notification template details':
            var notification_setting_id = $('#details-id').text();
            var page_link = document.getElementById('page-link').getAttribute('href');
            
            $.ajax({
                url: 'components/notification-setting/controller/notification-setting-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    notification_setting_id : notification_setting_id, 
                    transaction : transaction
                },
                success: function(response) {
                    if (response.success) {
                        $('#system_notification_title').val(response.systemNotificationTitle);
                        $('#system_notification_message').val(response.systemNotificationMessage);
                        
                        $('#system_notification_title_preview').text(response.systemNotificationTitle);
                        $('#system_notification_message_preview').text(response.systemNotificationMessage);
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
                    var fullErrorMessage = `XHR status: ${status}, Error: ${error}`;
                    if (xhr.responseText) {
                        fullErrorMessage += `, Response: ${xhr.responseText}`;
                    }
                    showErrorDialog(fullErrorMessage);
                }
            });
            break;
        case 'get email notification template details':
            var notification_setting_id = $('#details-id').text();
            var page_link = document.getElementById('page-link').getAttribute('href');
            
            $.ajax({
                url: 'components/notification-setting/controller/notification-setting-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    notification_setting_id : notification_setting_id, 
                    transaction : transaction
                },
                success: function(response) {
                    if (response.success) {
                        $('#email_notification_subject').val(response.emailNotificationSubject);

                        // Prevent Bootstrap dialog from blocking focusin
                         document.addEventListener('focusin', (e) => {
                                if (e.target.closest(".tox-tinymce-aux, .moxman-window, .tam-assetmanager-root") !== null) {
                                e.stopImmediatePropagation();
                        }
                        });

                        tinymce.init({
                            height: '300',
                            selector: '#email_notification_body',
                            init_instance_callback: (editor) => {
                                editor.setContent(response.emailNotificationBody, { format: 'html' });
                            },
                            menubar: false,
                            toolbar: [
                                'styleselect fontselect fontsizeselect',
                                'undo redo | cut copy paste | bold italic | link image | alignleft aligncenter alignright alignjustify',
                                'bullist numlist | outdent indent | blockquote subscript superscript | advlist | autolink | lists charmap | print preview |  code'
                            ],
                            plugins: 'advlist autolink link image lists charmap print preview code',
                            license_key: 'gpl'
                        });
                        
                        $('#email_notification_subject_preview').text(response.emailNotificationSubject);
                        $('#email_notification_body_preview').html(response.emailNotificationBody);
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
                    var fullErrorMessage = `XHR status: ${status}, Error: ${error}`;
                    if (xhr.responseText) {
                        fullErrorMessage += `, Response: ${xhr.responseText}`;
                    }
                    showErrorDialog(fullErrorMessage);
                }
            });
            break;
        case 'get sms notification template details':
            var notification_setting_id = $('#details-id').text();
            var page_link = document.getElementById('page-link').getAttribute('href');
            
            $.ajax({
                url: 'components/notification-setting/controller/notification-setting-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    notification_setting_id : notification_setting_id, 
                    transaction : transaction
                },
                success: function(response) {
                    if (response.success) {
                        $('#sms_notification_message').val(response.smsNotificationMessage);
                        
                        $('#sms_notification_message_preview').text(response.smsNotificationMessage);
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
                    var fullErrorMessage = `XHR status: ${status}, Error: ${error}`;
                    if (xhr.responseText) {
                        fullErrorMessage += `, Response: ${xhr.responseText}`;
                    }
                    showErrorDialog(fullErrorMessage);
                }
            });
            break;
    }
}