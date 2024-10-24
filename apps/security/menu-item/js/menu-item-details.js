(function($) {
    'use strict';

    $(function() {
        generateDropdownOptions('menu item options');

        displayDetails('get app module details');

        if($('#app-module-form').length){
            appModuleForm();
        }

        if($('#app-logo-form').length){
            updateAppLogoForm();
        }

        $(document).on('click','#edit-details',function() {
            displayDetails('get app module details');
        });

        $(document).on('click','#delete-app-module',function() {
            const app_module_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'delete app module';
    
            Swal.fire({
                title: 'Confirm App Module Deletion',
                text: 'Are you sure you want to delete this app module?',
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
                        url: 'apps/security/app-module/controller/app-module-controller.php',
                        dataType: 'json',
                        data: {
                            app_module_id : app_module_id, 
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
            const app_module_id = $('#details-id').text();

            logNotes('app_module', app_module_id);
        });

        $(document).on('change','#app_logo',function() {
            if ($(this).val() !== '' && $(this)[0].files.length > 0) {
                const transaction = 'update app logo';
                const app_module_id = $('#details-id').text();
                var formData = new FormData();
                formData.append('app_logo', $(this)[0].files[0]);
                formData.append('transaction', transaction);
                formData.append('app_module_id', app_module_id);
        
                $.ajax({
                    type: 'POST',
                    url: 'apps/security/app-module/controller/app-module-controller.php',
                    dataType: 'json',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            showNotification(response.title, response.message, response.messageType);
                            displayDetails('get app module details');
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
            }
        });

        if($('#internal-notes').length){
            const app_module_id = $('#details-id').text();

            internalNotes('app_module', app_module_id);
        }

        if($('#internal-notes-form').length){
            const app_module_id = $('#details-id').text();

            internalNotesForm('app_module', app_module_id);
        }
    });
})(jQuery);

function appModuleForm(){
    $('#app-module-form').validate({
        rules: {
            app_module_name: {
                required: true
            },
            app_module_description: {
                required: true
            },
            menu_item_id: {
                required: true
            },
            order_sequence: {
                required: true
            }
        },
        messages: {
            app_module_name: {
                required: 'Enter the display name'
            },
            app_module_description: {
                required: 'Enter the description'
            },
            menu_item_id: {
                required: 'Select the default page'
            },
            order_sequence: {
                required: 'Enter the order sequence'
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
            const app_module_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update app module';
          
            $.ajax({
                type: 'POST',
                url: 'apps/security/app-module/controller/app-module-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&app_module_id=' + encodeURIComponent(app_module_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('submit-data');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        displayDetails('get app module details');
                        $('#app-module-modal').modal('hide');
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
                    logNotesMain('app_module', app_module_id);
                }
            });
        
            return false;
        }
    });
}

function updateAppLogoForm(){
    $('#app-logo-form').validate({
        rules: {
            app_logo: {
                required: true
            }
        },
        messages: {
            app_logo: {
                required: 'Choose the app logo'
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
            const app_module_id = $('#details-id').text();
            const transaction = 'update app logo';

            var formData = new FormData(form);
            formData.append('app_module_id', encodeURIComponent(app_module_id));
            formData.append('transaction', transaction);
        
            $.ajax({
                type: 'POST',
                url: 'apps/security/app-module/controller/app-module-controller.php',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('submit-app-logo');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        displayDetails('get app module details');
                        $('#app-logo-modal').modal('hide');
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
                    enableFormSubmitButton('submit-app-logo');
                }
            });
        
            return false;
        }
    });
}

function displayDetails(transaction){
    switch (transaction) {
        case 'get app module details':
            var app_module_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/security/app-module/controller/app-module-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    app_module_id : app_module_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetModalForm('app-module-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#app_module_name').val(response.appModuleName);
                        $('#app_module_description').val(response.appModuleDescription);
                        $('#order_sequence').val(response.orderSequence);
                        
                        $('#menu_item_id').val(response.menuItemID).trigger('change');

                        document.getElementById('app_thumbnail').style.backgroundImage = `url(${response.appLogo})`;
                        
                        $('#app_module_name_summary').text(response.appModuleName);
                        $('#app_module_description_summary').text(response.appModuleDescription);
                        $('#menu_item_summary').text(response.menuItemName);
                        $('#order_sequence_summary').text(response.orderSequence);
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
        case 'menu item options':
            
            $.ajax({
                url: 'apps/security/menu-item/view/_menu_item_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type
                },
                success: function(response) {
                    $('#menu_item_id').select2({
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