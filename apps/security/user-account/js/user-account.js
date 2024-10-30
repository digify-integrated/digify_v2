(function($) {
    'use strict';

    $(function() {
        if($('#user-account-table').length){
            userAccountTable('#user-account-table');
        }

        $(document).on('click','.delete-user-account',function() {
            const user_account_id = $(this).data('user-account-id');
            const transaction = 'delete user account';
    
            Swal.fire({
                title: 'Confirm User Account Deletion',
                text: 'Are you sure you want to delete this user account?',
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
                        url: 'components/user-account/controller/user-account-controller.php',
                        dataType: 'json',
                        data: {
                            user_account_id : user_account_id, 
                            transaction : transaction
                        },
                        success: function (response) {
                            if (response.success) {
                                showNotification(response.title, response.message, response.messageType);
                                reloadDatatable('#user-account-table');
                            }
                            else {
                                if (response.isInactive || response.userNotExist || response.userInactive || response.userLocked || response.sessionExpired) {
                                    setNotification(response.title, response.message, response.messageType);
                                    window.location = 'logout.php?logout';
                                }
                                else if (response.notExist) {
                                    setNotification(response.title, response.message, response.messageType);
                                    reloadDatatable('#user-account-table');
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

        $(document).on('click','#delete-user-account',function() {
            let user_account_id = [];
            const transaction = 'delete multiple user account';

            $('.datatable-checkbox-children').each((index, element) => {
                if ($(element).is(':checked')) {
                    user_account_id.push(element.value);
                }
            });
    
            if(user_account_id.length > 0){
                Swal.fire({
                    title: 'Confirm Multiple User Accounts Deletion',
                    text: 'Are you sure you want to delete these user accounts?',
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
                            url: 'apps/security/user-account/controller/user-account-controller.php',
                            dataType: 'json',
                            data: {
                                user_account_id: user_account_id,
                                transaction : transaction
                            },
                            success: function (response) {
                                if (response.success) {
                                    showNotification(response.title, response.message, response.messageType);
                                    reloadDatatable('#user-account-table');
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
                                handleSystemError(xhr, status, error);
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
                showNotification('Deletion Multiple User Account Error', 'Please select the user accounts you wish to delete.', 'danger');
            }
        });

        $(document).on('click','#export-data',function() {
            generateExportColumns('user_account');
        });

        $(document).on('click','#submit-export',function() {
            exportData('user_account');
        });

        $('#datatable-search').on('keyup', function () {
            var table = $('#user-account-table').DataTable();
            table.search(this.value).draw();
        });

        $('#datatable-length').on('change', function() {
            var table = $('#user-account-table').DataTable();
            var length = $(this).val(); 
            table.page.len(length).draw();
        });

        $(document).on('click','#apply-filter',function() {
            userAccountTable('#user-account-table');
        });

        $(document).on('click', '#reset-filter', function() {
            $('#user_account_status_filter').val(null).trigger('change');
            $('#user_account_lock_status_filter').val(null).trigger('change');
            $('#password_expiry_date_filter').val(null);
            $('#last_connection_date_filter').val(null);
            
            userAccountTable('#user-account-table');
        });
    });
})(jQuery);

function userAccountTable(datatable_name) {
    toggleHideActionDropdown();

    const type = 'user account table';
    const page_id = $('#page-id').val();
    const page_link = document.getElementById('page-link').getAttribute('href');
    const user_account_status_filter = $('#user_account_status_filter').val();
    const user_account_lock_status_filter = $('#user_account_lock_status_filter').val();
    const password_expiry_date_filter = $('#password_expiry_date_filter').val();
    const last_connection_date_filter = $('#last_connection_date_filter').val();


    const columns = [ 
        { data: 'CHECK_BOX' },
        { data: 'USER_ACCOUNT' },
        { data: 'USER_ACCOUNT_STATUS' },
        { data: 'LOCK_STATUS' },
        { data: 'LAST_CONNECTION_DATE' },
        { data: 'PASSWORD_EXPIRY_DATE' }
    ];

    const columnDefs = [
        { width: '1%', bSortable: false, targets: 0, responsivePriority: 1 },
        { width: 'auto', targets: 1, responsivePriority: 2 },
        { width: 'auto', targets: 2, responsivePriority: 3 },
        { width: 'auto', targets: 3, responsivePriority: 4 },
        { width: 'auto', targets: 4, responsivePriority: 5 },
        { width: 'auto', targets: 5, responsivePriority: 6 },
    ];

    const lengthMenu = [[10, 5, 25, 50, 100, -1], [10, 5, 25, 50, 100, 'All']];

    const settings = {
        ajax: { 
            url: 'apps/security/user-account/view/_user_account_generation.php',
            method: 'POST',
            dataType: 'json',
            data: {
                type: type,
                page_id: page_id,
                page_link: page_link,
                user_account_status_filter: user_account_status_filter,
                user_account_lock_status_filter: user_account_lock_status_filter,
                password_expiry_date_filter: password_expiry_date_filter,
                last_connection_date_filter: last_connection_date_filter
                
            },
            dataSrc: '',
            error: function(xhr, status, error) {
                handleSystemError(xhr, status, error);
            }
        },
        lengthChange: false,
        order: [[1, 'asc']],
        columns: columns,
        columnDefs: columnDefs,
        lengthMenu: lengthMenu,
        autoWidth: false,
        language: {
            emptyTable: 'No data found',
            sLengthMenu: '_MENU_',
            info: '_START_ - _END_ of _TOTAL_ items',
            loadingRecords: 'Just a moment while we fetch your data...'
        },
        fnDrawCallback: function(oSettings) {
            readjustDatatableColumn();

            $(`${datatable_name} tbody`).on('click', 'tr td:nth-child(n+2)', function () {
                const rowData = $(datatable_name).DataTable().row($(this).closest('tr')).data();
                if (rowData && rowData.LINK) {
                    window.location.href = rowData.LINK;
                }
            });
        }
    };

    destroyDatatable(datatable_name);
    $(datatable_name).dataTable(settings);
}