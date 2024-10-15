(function($) {
    'use strict';

    $(function() {
        generateDropdownOptions('app module options');

        displayDetails('get menu group details');

        if($('#menu-group-form').length){
            menuGroupForm();
        }

        $(document).on('click','#edit-details',function() {
            displayDetails('get menu group details');
        });

        $(document).on('click','#delete-menu-group',function() {
            const menu_group_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'delete menu group';
    
            Swal.fire({
                title: 'Confirm Menu Group Deletion',
                text: 'Are you sure you want to delete this menu group?',
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
                        url: 'apps/security/menu-group/controller/menu-group-controller.php',
                        dataType: 'json',
                        data: {
                            menu_group_id : menu_group_id, 
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

        if($('#log-notes-main').length){
            const menu_group_id = $('#details-id').text();

            logNotesMain('menu_group', menu_group_id);
        }

        if($('#internal-notes').length){
            const menu_group_id = $('#details-id').text();

            internalNotes('menu_group', menu_group_id);
        }

        if($('#internal-notes-form').length){
            const menu_group_id = $('#details-id').text();

            internalNotesForm('menu_group', menu_group_id);
        }
    });
})(jQuery);

function menuGroupForm(){
    $('#menu-group-form').validate({
        rules: {
            menu_group_name: {
                required: true
            },
            app_module_id: {
                required: true
            },
            order_sequence: {
                required: true
            }
        },
        messages: {
            menu_group_name: {
                required: 'Enter the display name'
            },
            app_module_id: {
                required: 'Choose the app module'
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
            const menu_group_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update menu group';
          
            $.ajax({
                type: 'POST',
                url: 'apps/security/menu-group/controller/menu-group-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&menu_group_id=' + encodeURIComponent(menu_group_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('submit-data');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        displayDetails('get menu group details');
                        $('#menu-group-modal').modal('hide');
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
                    logNotesMain('menu_group', menu_group_id);
                }
            });
        
            return false;
        }
    });
}

function displayDetails(transaction){
    switch (transaction) {
        case 'get menu group details':
            var menu_group_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/security/menu-group/controller/menu-group-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    menu_group_id : menu_group_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetModalForm('menu-group-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#menu_group_name').val(response.menuGroupName);
                        $('#order_sequence').val(response.orderSequence);

                        $('#app_module_id').val(response.appModuleID).trigger('change');
                        
                        $('#menu_group_name_summary').text(response.menuGroupName);
                        $('#app_module_name_summary').text(response.appModuleName);
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
        case 'app module options':
            
            $.ajax({
                url: 'apps/security/app-module/view/_app_module_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type
                },
                success: function(response) {
                    $('#app_module_id').select2({
                        data: response,
                        dropdownParent: $('#app_module_id').closest('.modal')
                    }).on('change', function (e) {
                        $(e.target).valid()
                    });
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                }
            });
            break;
    }
}