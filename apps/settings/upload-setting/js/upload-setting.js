(function($) {
    'use strict';

    $(function() {
        if($('#upload-setting-table').length){
            uploadSettingTable('#upload-setting-table');
        }

        $(document).on('click','.delete-upload-setting',function() {
            const upload_setting_id = $(this).data('upload-setting-id');
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
                                showNotification(response.title, response.message, response.messageType);
                                reloadDatatable('#upload-setting-table');
                            }
                            else {
                                if (response.isInactive || response.userNotExist || response.userInactive || response.userLocked || response.sessionExpired) {
                                    setNotification(response.title, response.message, response.messageType);
                                    window.location = 'logout.php?logout';
                                }
                                else if (response.notExist) {
                                    setNotification(response.title, response.message, response.messageType);
                                    reloadDatatable('#upload-setting-table');
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

        $(document).on('click','#delete-upload-setting',function() {
            let upload_setting_id = [];
            const transaction = 'delete multiple upload setting';

            $('.datatable-checkbox-children').each((index, element) => {
                if ($(element).is(':checked')) {
                    upload_setting_id.push(element.value);
                }
            });
    
            if(upload_setting_id.length > 0){
                Swal.fire({
                    title: 'Confirm Multiple Upload Settings Deletion',
                    text: 'Are you sure you want to delete these upload settings?',
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
                                upload_setting_id: upload_setting_id,
                                transaction : transaction
                            },
                            success: function (response) {
                                if (response.success) {
                                    showNotification(response.title, response.message, response.messageType);
                                    reloadDatatable('#upload-setting-table');
                                }
                                else {
                                    if (response.isInactive || response.userNotExist || response.userInactive || response.userLocked || response.sessionExpired) {
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
                            complete: function(){
                                toggleHideActionDropdown();
                            }
                        });
                        
                        return false;
                    }
                });
            }
            else{
                showNotification('Deletion Multiple Upload Setting Error', 'Please select the upload settings you wish to delete.', 'danger');
            }
        });

        $('#datatable-search').on('keyup', function () {
            var table = $('#upload-setting-table').DataTable();
            table.search(this.value).draw();
        });
    });
})(jQuery);

function uploadSettingTable(datatable_name, buttons = false, show_all = false){
    toggleHideActionDropdown();

    const type = 'upload setting table';
    const page_id = $('#page-id').val();
    const page_link = document.getElementById('page-link').getAttribute('href');

    var settings;

    const column = [ 
        { 'data' : 'CHECK_BOX' },
        { 'data' : 'UPLOAD_SETTING' },
        { 'data' : 'MAX_FILE_SIZE' },
        { 'data' : 'ACTION' }
    ];

    const column_definition = [
        { 'width': '1%','bSortable': false, 'aTargets': 0 },
        { 'width': 'auto', 'aTargets': 1 },
        { 'width': 'auto', 'aTargets': 2 },
        { 'width': '15%','bSortable': false, 'aTargets': 3 }
    ];

    const length_menu = show_all ? [[-1], ['All']] : [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']];

    settings = {
        'ajax': { 
            'url' : 'components/upload-setting/view/_upload_setting_generation.php',
            'method' : 'POST',
            'dataType': 'json',
            'data': {
                'type' : type,
                'page_id' : page_id,
                'page_link' : page_link
            },
            'dataSrc' : '',
            'error': function(xhr, status, error) {
                var fullErrorMessage = `XHR status: ${status}, Error: ${error}`;
                if (xhr.responseText) {
                    fullErrorMessage += `, Response: ${xhr.responseText}`;
                }
                showErrorDialog(fullErrorMessage);
            }
        },
        'dom': 'Brtip',
        'lengthChange': false,
        'order': [[ 1, 'asc' ]],
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