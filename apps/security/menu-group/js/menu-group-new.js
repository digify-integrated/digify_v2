(function($) {
    'use strict';

    $(function() {
        generateDropdownOptions('app module options');

        if($('#menu-group-form').length){
            menuGroupForm();
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
            const transaction = 'add menu group';
            const page_link = document.getElementById('page-link').getAttribute('href');
          
            $.ajax({
                type: 'POST',
                url: 'apps/security/menu-group/controller/menu-group-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction,
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('submit-data');
                },
                success: function (response) {
                    if (response.success) {
                        setNotification(response.title, response.message, response.messageType);
                        window.location = page_link + '&id=' + response.menuGroupID;
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
                    enableFormSubmitButton('submit-data');
                }
            });
        
            return false;
        }
    });
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