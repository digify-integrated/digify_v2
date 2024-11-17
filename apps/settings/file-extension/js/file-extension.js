(function($) {
    'use strict';

    $(function() {        
        generateDropdownOptions('file type options');

        if($('#file-extension-table').length){
            fileExtensionTable('#file-extension-table');
        }

        $(document).on('click','.delete-file-extension',function() {
            const file_extension_id = $(this).data('file-extension-id');
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
                        url: 'apps/settings/file-extension/controller/file-extension-controller.php',
                        dataType: 'json',
                        data: {
                            file_extension_id : file_extension_id, 
                            transaction : transaction
                        },
                        success: function (response) {
                            if (response.success) {
                                showNotification(response.title, response.message, response.messageType);
                                reloadDatatable('#file-extension-table');
                            }
                            else {
                                if (response.isInactive || response.userNotExist || response.userInactive || response.userLocked || response.sessionExpired) {
                                    setNotification(response.title, response.message, response.messageType);
                                    window.location = 'logout.php?logout';
                                }
                                else if (response.notExist) {
                                    setNotification(response.title, response.message, response.messageType);
                                    reloadDatatable('#file-extension-table');
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

        $(document).on('click','#delete-file-extension',function() {
            let file_extension_id = [];
            const transaction = 'delete multiple file extension';

            $('.datatable-checkbox-children').each((index, element) => {
                if ($(element).is(':checked')) {
                    file_extension_id.push(element.value);
                }
            });
    
            if(file_extension_id.length > 0){
                Swal.fire({
                    title: 'Confirm Multiple File Extensions Deletion',
                    text: 'Are you sure you want to delete these file extensions?',
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
                            url: 'apps/settings/file-extension/controller/file-extension-controller.php',
                            dataType: 'json',
                            data: {
                                file_extension_id: file_extension_id,
                                transaction : transaction
                            },
                            success: function (response) {
                                if (response.success) {
                                    showNotification(response.title, response.message, response.messageType);
                                    reloadDatatable('#file-extension-table');
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
                showNotification('Deletion Multiple File Extension Error', 'Please select the file extensions you wish to delete.', 'danger');
            }
        });

        $(document).on('click','#export-data',function() {
            generateExportColumns('file_extension');
        });

        $(document).on('click','#submit-export',function() {
            exportData('file_extension');
        });

        $('#datatable-search').on('keyup', function () {
            var table = $('#file-extension-table').DataTable();
            table.search(this.value).draw();
        });

        $('#datatable-length').on('change', function() {
            var table = $('#file-extension-table').DataTable();
            var length = $(this).val(); 
            table.page.len(length).draw();
        });

        $(document).on('click','#apply-filter',function() {
            fileExtensionTable('#file-extension-table');
        });

        $(document).on('click', '#reset-filter', function() {
            $('#file_type_filter').val(null).trigger('change');
            
            fileExtensionTable('#file-extension-table');
        });
    });
})(jQuery);

function fileExtensionTable(datatable_name) {
    toggleHideActionDropdown();

    const type = 'file extension table';
    const page_id = $('#page-id').val();
    const file_type_filter = $('#file_type_filter').val();
    const page_link = document.getElementById('page-link').getAttribute('href');

    const columns = [ 
        { data: 'CHECK_BOX' },
        { data: 'FILE_EXTENSION' },
        { data: 'FILE_TYPE' }
    ];

    const columnDefs = [
        { width: '5%', bSortable: false, targets: 0, responsivePriority: 1 },
        { width: 'auto', targets: 1, responsivePriority: 2 },
        { width: 'auto', targets: 2, responsivePriority: 3 }
    ];

    const lengthMenu = [[10, 5, 25, 50, 100, -1], [10, 5, 25, 50, 100, 'All']];

    const settings = {
        ajax: { 
            url: 'apps/settings/file-extension/view/_file_extension_generation.php',
            method: 'POST',
            dataType: 'json',
            data: {
                type: type,
                page_id: page_id,
                page_link: page_link,
                file_type_filter: file_type_filter
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

function generateDropdownOptions(type){
    switch (type) {
        case 'file type options':
            
            $.ajax({
                url: 'apps/settings/file-type/view/_file_type_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type,
                    multiple : 1
                },
                success: function(response) {
                    $('#file_type_filter').select2({
                        data: response
                    });
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                }
            });
            break;
    }
}