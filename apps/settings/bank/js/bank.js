(function($) {
    'use strict';

    $(function() {
        if($('#bank-table').length){
            bankTable('#bank-table');
        }

        $(document).on('click','#delete-bank',function() {
            let bank_id = [];
            const transaction = 'delete multiple bank';

            $('.datatable-checkbox-children').each((index, element) => {
                if ($(element).is(':checked')) {
                    bank_id.push(element.value);
                }
            });
    
            if(bank_id.length > 0){
                Swal.fire({
                    title: 'Confirm Multiple Banks Deletion',
                    text: 'Are you sure you want to delete these banks?',
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
                            url: 'apps/settings/bank/controller/bank-controller.php',
                            dataType: 'json',
                            data: {
                                bank_id: bank_id,
                                transaction : transaction
                            },
                            success: function (response) {
                                if (response.success) {
                                    showNotification(response.title, response.message, response.messageType);
                                    reloadDatatable('#bank-table');
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
                showNotification('Deletion Multiple Banks Error', 'Please select the Banks you wish to delete.', 'danger');
            }
        });

        $(document).on('click','#export-data',function() {
            generateExportColumns('bank');
        });

        $(document).on('click','#submit-export',function() {
            exportData('bank');
        });

        $('#datatable-search').on('keyup', function () {
            var table = $('#bank-table').DataTable();
            table.search(this.value).draw();
        });

        $('#datatable-length').on('change', function() {
            var table = $('#bank-table').DataTable();
            var length = $(this).val(); 
            table.page.len(length).draw();
        });
    });
})(jQuery);

function bankTable(datatable_name) {
    toggleHideActionDropdown();

    const type = 'bank table';
    const page_id = $('#page-id').val();
    const page_link = document.getElementById('page-link').getAttribute('href');


    const columns = [ 
        { data: 'CHECK_BOX' },
        { data: 'BANK_NAME' },
        { data: 'BANK_IDENTIFIER_CODE' }
    ];

    const columnDefs = [
        { width: '1%', bSortable: false, targets: 0, responsivePriority: 1 },
        { width: 'auto', targets: 1, responsivePriority: 2 },
        { width: 'auto', targets: 2, responsivePriority: 3 }
    ];

    const lengthMenu = [[10, 5, 25, 50, 100, -1], [10, 5, 25, 50, 100, 'All']];

    const settings = {
        ajax: { 
            url: 'apps/settings/bank/view/_bank_generation.php',
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