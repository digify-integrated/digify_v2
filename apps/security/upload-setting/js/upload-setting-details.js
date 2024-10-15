(function($) {
    'use strict';

    $(function() {
        displayDetails('get upload setting details');

        if($('#upload-setting-form').length){
            uploadSettingForm();
        }

        if($('#file-extension-assignment-form').length){
            fileExtensionAssignmentForm();
        }

        if($('#assigned-file-extension-table').length){
            assignedFileExtensionTable('#assigned-file-extension-table');
        }

        $(document).on('click','#edit-details',function() {
            displayDetails('get upload setting details');
        });

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
                        url: 'components/upload-setting/controller/upload-setting-controller.php',
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

        $(document).on('click','#assign-file-extension',function() {
            generateDropdownOptions('file extension upload setting dual listbox options');
        });

        $(document).on('click','.delete-file-extension',function() {
            const upload_setting_file_extension_id = $(this).data('upload-setting-file-extension-id');
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
                        url: 'components/upload-setting/controller/upload-setting-controller.php',
                        dataType: 'json',
                        data: {
                            upload_setting_file_extension_id : upload_setting_file_extension_id, 
                            transaction : transaction
                        },
                        success: function (response) {
                            if (response.success) {
                                showNotification(response.title, response.message, response.messageType);
                                reloadDatatable('#assigned-file-extension-table');
                            }
                            else {
                                if (response.isInactive || response.userNotExist || response.userInactive || response.userLocked || response.sessionExpired) {
                                    setNotification(response.title, response.message, response.messageType);
                                    window.location = 'logout.php?logout';
                                }
                                else if (response.notExist) {
                                    setNotification(response.title, response.message, response.messageType);
                                    reloadDatatable('#assigned-file-extension-table');
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

        if($('#log-notes-offcanvas').length){
            $(document).on('click','.view-file-extension-log-notes',function() {
                const upload_setting_file_extension_id = $(this).data('upload-setting-file-extension-id');

                logNotes('upload_setting_file_extension', upload_setting_file_extension_id);
            });
        }

        if($('#log-notes-main').length){
            const upload_setting_id = $('#details-id').text();

            logNotesMain('upload_setting', upload_setting_id);
        }

        if($('#internal-notes').length){
            const upload_setting_id = $('#details-id').text();

            internalNotes('upload_setting', upload_setting_id);
        }

        if($('#internal-notes-form').length){
            const upload_setting_id = $('#details-id').text();

            internalNotesForm('upload_setting', upload_setting_id);
        }
    });
})(jQuery);

function uploadSettingForm(){
    $('#upload-setting-form').validate({
        rules: {
            upload_setting_name: {
                required: true
            },
            max_file_size: {
                required: true
            },
            upload_setting_description: {
                required: true
            }
        },
        messages: {
            upload_setting_name: {
                required: 'Enter the display name'
            },
            max_file_size: {
                required: 'Enter the max file size'
            },
            upload_setting_description: {
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
            const upload_setting_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update upload setting';
          
            $.ajax({
                type: 'POST',
                url: 'components/upload-setting/controller/upload-setting-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&upload_setting_id=' + upload_setting_id,
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('submit-data');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        displayDetails('get upload setting details');
                        $('#upload-setting-modal').modal('hide');
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
                },
                complete: function() {
                    enableFormSubmitButton('submit-data');
                    logNotesMain('upload_setting', upload_setting_id);
                }
            });
        
            return false;
        }
    });
}

function fileExtensionAssignmentForm(){
    $('#file-extension-assignment-form').validate({
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
            const upload_setting_id = $('#details-id').text();
            const transaction = 'assign upload setting file extension';
          
            $.ajax({
                type: 'POST',
                url: 'components/upload-setting/controller/upload-setting-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&upload_setting_id=' + upload_setting_id,
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('submit-data');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        reloadDatatable('#assigned-file-extension-table');
                        $('#file-extension-assignment-modal').modal('hide');
                    }
                    else {
                        if (response.isInactive || response.userNotExist || response.userInactive || response.userLocked || response.sessionExpired) {
                            setNotification(response.title, response.message, response.messageType);
                            window.location = 'logout.php?logout';
                        }
                        else if (response.notExist) {
                            setNotification(response.title, response.message, response.messageType);
                            window.location = 'role.php';
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
                }
            });
        
            return false;
        }
    });
}

function assignedFileExtensionTable(datatable_name, buttons = false, show_all = false){
    const upload_setting_id = $('#details-id').text();
    const type = 'assigned file extension table';
    var settings;

    const column = [ 
        { 'data' : 'FILE_EXTENSION' },
        { 'data' : 'ACTION' }
    ];

    const column_definition = [
        { 'width': 'auto', 'aTargets': 0 },
        { 'width': '15%', 'bSortable': false, 'aTargets': 1 }
    ];

    const length_menu = show_all ? [[-1], ['All']] : [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']];

    settings = {
        'ajax': { 
            'url' : 'components/file-extension/view/_file_extension_generation.php',
            'method' : 'POST',
            'dataType': 'json',
            'data': {'type' : type, 'upload_setting_id' : upload_setting_id},
            'dataSrc' : '',
            'error': function(xhr, status, error) {
                var fullErrorMessage = `XHR status: ${status}, Error: ${error}`;
                if (xhr.responseText) {
                    fullErrorMessage += `, Response: ${xhr.responseText}`;
                }
                showErrorDialog(fullErrorMessage);
            }
        },
        'order': [[ 0, 'asc' ]],
        'columns' : column,
        'fnDrawCallback': function( oSettings ) {
            readjustDatatableColumn();
        },
        'columnDefs': column_definition,
        'lengthMenu': length_menu,
        'language': {
            'emptyTable': 'No data found',
            'searchPlaceholder': 'Search...',
            'search': '',
            'loadingRecords': 'Just a moment while we fetch your data...'
        },
    };

    if (buttons) {
        settings.dom = 'Bfrtip';
        settings.buttons = ['csv', 'excel', 'pdf'];
    }

    destroyDatatable(datatable_name);

    $(datatable_name).dataTable(settings);
}

function displayDetails(transaction){
    switch (transaction) {
        case 'get upload setting details':
            var upload_setting_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href');
            
            $.ajax({
                url: 'components/upload-setting/controller/upload-setting-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    upload_setting_id : upload_setting_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetModalForm('upload-setting-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#upload_setting_name').val(response.uploadSettingName);
                        $('#max_file_size').val(response.maxFileSize);
                        $('#upload_setting_description').val(response.uploadSettingDescription);
                        
                        $('#upload_setting_name_summary').text(response.uploadSettingName);
                        $('#max_file_size_summary').text(response.maxFileSize + ' kb');
                        $('#upload_setting_description_summary').text(response.uploadSettingDescription);
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

function generateDropdownOptions(type){
    switch (type) {
        case 'file extension upload setting dual listbox options':
            const upload_setting_id = $('#details-id').text();
    
            $.ajax({
                url: 'components/file-extension/view/_file_extension_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type,
                    upload_setting_id : upload_setting_id
                },
                success: function(response) {
                    var select = document.getElementById('file_extension_id');
    
                    select.options.length = 0;
    
                    response.forEach(function(opt) {
                        var option = new Option(opt.text, opt.id);
                        select.appendChild(option);
                    });
                },
                error: function(xhr, status, error) {
                    var fullErrorMessage = `XHR status: ${status}, Error: ${error}`;
                    if (xhr.responseText) {
                        fullErrorMessage += `, Response: ${xhr.responseText}`;
                    }
                     showErrorDialog(fullErrorMessage);
                },
                complete: function(){
                    if($('#file_extension_id').length){
                        $('#file_extension_id').bootstrapDualListbox({
                            nonSelectedListLabel: 'Non-selected',
                            selectedListLabel: 'Selected',
                            preserveSelectionOnMove: 'moved',
                            moveOnSelect: false,
                            helperSelectNamePostfix: false
                        });
    
                        $('#file_extension_id').bootstrapDualListbox('refresh', true);
    
                        initializeDualListBoxIcon();
                    }
                }
            });
            break;
    }
}