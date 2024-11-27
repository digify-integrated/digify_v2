(function($) {
    'use strict';

    $(function() {
        displayDetails('get language proficiency details');

        if($('#language-proficiency-form').length){
            languageProficiencyForm();
        }

        $(document).on('click','#delete-language-proficiency',function() {
            const language_proficiency_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'delete language proficiency';
    
            Swal.fire({
                title: 'Confirm Language Proficiency Deletion',
                text: 'Are you sure you want to delete this language proficiency?',
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
                        url: 'apps/settings/language-proficiency/controller/language-proficiency-controller.php',
                        dataType: 'json',
                        data: {
                            language_proficiency_id : language_proficiency_id, 
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

        $(document).on('click','.view-role-permission-log-notes',function() {
            const role_language_proficiency_permission_id = $(this).data('role-permission-id');

            logNotes('role_language_proficiency_permission', role_language_proficiency_permission_id);
        });

        $(document).on('click','#log-notes-main',function() {
            const language_proficiency_id = $('#details-id').text();

            logNotes('language_proficiency', language_proficiency_id);
        });

        $('#datatable-search').on('keyup', function () {
            var table = $('#role-permission-table').DataTable();
            table.search(this.value).draw();
        });

        $('#datatable-length').on('change', function() {
            var table = $('#role-permission-table').DataTable();
            var length = $(this).val(); 
            table.page.len(length).draw();
        });
    });
})(jQuery);

function languageProficiencyForm(){
    $('#language-proficiency-form').validate({
        rules: {
            language_proficiency_name: {
                required: true
            },
            language_proficiency_description: {
                required: true
            }
        },
        messages: {
            language_proficiency_name: {
                required: 'Enter the display name'
            },
            language_proficiency_description: {
                required: 'Ether the description'
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
            const language_proficiency_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update language proficiency';
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/language-proficiency/controller/language-proficiency-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&language_proficiency_id=' + encodeURIComponent(language_proficiency_id),
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
                    logNotes('language_proficiency', language_proficiency_id);
                }
            });
        
            return false;
        }
    });
}

function displayDetails(transaction){
    switch (transaction) {
        case 'get language proficiency details':
            var language_proficiency_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/settings/language-proficiency/controller/language-proficiency-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    language_proficiency_id : language_proficiency_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetForm('language-proficiency-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#language_proficiency_name').val(response.languageProficiencyName);
                        $('#language_proficiency_description').val(response.languageProficiencyDescription);
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