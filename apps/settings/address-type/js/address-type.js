(function($) {
    'use strict';

    $(function() {
        if($('#address-type-table').length){
            addressTypeTable('#address-type-table');
        }

        $(document).on('click','.delete-address-type',function() {
            const address_type_id = $(this).data('address-type-id');
            const transaction = 'delete address type';
    
            Swal.fire({
                title: 'Confirm Address Type Deletion',
                text: 'Are you sure you want to delete this address type?',
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
                        url: 'apps/settings/address-type/controller/address-type-controller.php',
                        dataType: 'json',
                        data: {
                            address_type_id : address_type_id, 
                            transaction : transaction
                        },
                        success: function (response) {
                            if (response.success) {
                                showNotification(response.title, response.message, response.messageType);
                                reloadDatatable('#address-type-table');
                            }
                            else {
                                if (response.isInactive || response.userNotExist || response.userInactive || response.userLocked || response.sessionExpired) {
                                    setNotification(response.title, response.message, response.messageType);
                                    window.location = 'logout.php?logout';
                                }
                                else if (response.notExist) {
                                    setNotification(response.title, response.message, response.messageType);
                                    reloadDatatable('#address-type-table');
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

        $(document).on('click','#delete-address-type',function() {
            let address_type_id = [];
            const transaction = 'delete multiple address type';

            $('.datatable-checkbox-children').each((index, element) => {
                if ($(element).is(':checked')) {
                    address_type_id.push(element.value);
                }
            });
    
            if(address_type_id.length > 0){
                Swal.fire({
                    title: 'Confirm Multiple Address Types Deletion',
                    text: 'Are you sure you want to delete these address types?',
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
                            url: 'apps/settings/address-type/controller/address-type-controller.php',
                            dataType: 'json',
                            data: {
                                address_type_id: address_type_id,
                                transaction : transaction
                            },
                            success: function (response) {
                                if (response.success) {
                                    showNotification(response.title, response.message, response.messageType);
                                    reloadDatatable('#address-type-table');
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
                showNotification('Deletion Multiple Address Type Error', 'Please select the address types you wish to delete.', 'danger');
            }
        });

        $(document).on('click','#export-data',function() {
            generateExportColumns('address_type');
        });

        $(document).on('click','#submit-export',function() {
            exportData('address_type');
        });

        $('#datatable-search').on('keyup', function () {
            var table = $('#address-type-table').DataTable();
            table.search(this.value).draw();
        });

        $('#datatable-length').on('change', function() {
            var table = $('#address-type-table').DataTable();
            var length = $(this).val(); 
            table.page.len(length).draw();
        });
    });
})(jQuery);

function addressTypeTable(datatable_name) {
    toggleHideActionDropdown();

    const type = 'address type table';
    const page_id = $('#page-id').val();
    const page_link = document.getElementById('page-link').getAttribute('href');

    const columns = [ 
        { data: 'CHECK_BOX' },
        { data: 'ADDRESS_TYPE_NAME' }
    ];

    const columnDefs = [
        { width: '5%', bSortable: false, targets: 0, responsivePriority: 1 },
        { width: 'auto', targets: 1, responsivePriority: 2 }
    ];

    const lengthMenu = [[10, 5, 25, 50, 100, -1], [10, 5, 25, 50, 100, 'All']];

    const settings = {
        ajax: { 
            url: 'apps/settings/address-type/view/_address_type_generation.php',
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