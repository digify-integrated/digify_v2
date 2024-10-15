(function($) {
    'use strict';

    $(function() {
        if($('#role-table').length){
            roleTable('#role-table');
        }

        $(document).on('click','.delete-role',function() {
            const role_id = $(this).data('role-id');
            const transaction = 'delete role';
    
            Swal.fire({
                title: 'Confirm Role Deletion',
                text: 'Are you sure you want to delete this role?',
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
                        url: 'components/role/controller/role-controller.php',
                        dataType: 'json',
                        data: {
                            role_id : role_id, 
                            transaction : transaction
                        },
                        success: function (response) {
                            if (response.success) {
                                showNotification(response.title, response.message, response.messageType);
                                reloadDatatable('#role-table');
                            }
                            else {
                                if (response.isInactive || response.userNotExist || response.userInactive || response.userLocked || response.sessionExpired) {
                                    setNotification(response.title, response.message, response.messageType);
                                    window.location = 'logout.php?logout';
                                }
                                else if (response.notExist) {
                                    setNotification(response.title, response.message, response.messageType);
                                    reloadDatatable('#role-table');
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

        $(document).on('click','#delete-role',function() {
            let role_id = [];
            const transaction = 'delete multiple role';

            $('.datatable-checkbox-children').each((index, element) => {
                if ($(element).is(':checked')) {
                    role_id.push(element.value);
                }
            });
    
            if(role_id.length > 0){
                Swal.fire({
                    title: 'Confirm Multiple Roles Deletion',
                    text: 'Are you sure you want to delete these roles?',
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
                            url: 'apps/security/role/controller/role-controller.php',
                            dataType: 'json',
                            data: {
                                role_id: role_id,
                                transaction : transaction
                            },
                            success: function (response) {
                                if (response.success) {
                                    showNotification(response.title, response.message, response.messageType);
                                    reloadDatatable('#role-table');
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
                showNotification('Deletion Multiple Role Error', 'Please select the roles you wish to delete.', 'danger');
            }
        });

        $(document).on('click','#export-data',function() {
            generateExportColumns('role');
        });

        $(document).on('click','#submit-export',function() {
            exportData('role');
        });

        $('#datatable-search').on('keyup', function () {
            var table = $('#role-table').DataTable();
            table.search(this.value).draw();
        });

        $('#datatable-length').on('change', function() {
            var table = $('#role-table').DataTable();
            var length = $(this).val(); 
            table.page.len(length).draw();
        });

        $(document).on('click','#apply-filter',function() {
            roleTable('#role-table');
            $('#filter-offcanvas').offcanvas('hide');
        });
    });
})(jQuery);

function roleTable(datatable_name) {
    toggleHideActionDropdown();

    const type = 'role table';
    const page_id = $('#page-id').val();
    const page_link = document.getElementById('page-link').getAttribute('href');

    const columns = [ 
        { data: 'CHECK_BOX' },
        { data: 'ROLE_NAME' },
        { data: 'ROLE_DESCRIPTION' }
    ];

    const columnDefs = [
        { width: '1%', bSortable: false, targets: 0, responsivePriority: 1 },
        { width: '29%', targets: 1, responsivePriority: 1 },
        { width: '70%', targets: 1, responsivePriority: 1 },
    ];

    const lengthMenu = [[10, 5, 25, 50, 100, -1], [10, 5, 25, 50, 100, 'All']];

    const settings = {
        ajax: { 
            url: 'apps/security/role/view/_role_generation.php',
            method: 'POST',
            dataType: 'json',
            data: {
                type: type,
                page_id: page_id,
                page_link: page_link
            },
            dataSrc: '',
            error: function(xhr, status, error) {
                handleSystemError(xhr, status, error);
            }
        },
        dom: 'Brtip',
        lengthChange: false,
        order: [[1, 'asc']],
        columns: columns,
        columnDefs: columnDefs,
        lengthMenu: lengthMenu,
        responsive: {
            details: {
                type: 'inline',
                display: $.fn.dataTable.Responsive.display.childRow,
                renderer: function (api, rowIdx, columns) {
                    let data = $.map(columns, function (col) {
                        return col.hidden ? `<tr><td>${col.title}:</td><td>${col.data}</td></tr>` : '';
                    }).join('');
                    return data ? $('<table/>').append(data) : false;
                }
            }
        },
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