(function($) {
    'use strict';

    $(function() {
        generateDropdownOptions('country options');

        if($('#state-table').length){
            stateTable('#state-table');
        }

        $(document).on('click','#delete-state',function() {
            let state_id = [];
            const transaction = 'delete multiple state';

            $('.datatable-checkbox-children').each((index, element) => {
                if ($(element).is(':checked')) {
                    state_id.push(element.value);
                }
            });
    
            if(state_id.length > 0){
                Swal.fire({
                    title: 'Confirm Multiple States Deletion',
                    text: 'Are you sure you want to delete these states?',
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
                            url: 'apps/settings/state/controller/state-controller.php',
                            dataType: 'json',
                            data: {
                                state_id: state_id,
                                transaction : transaction
                            },
                            success: function (response) {
                                if (response.success) {
                                    showNotification(response.title, response.message, response.messageType);
                                    reloadDatatable('#state-table');
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
                showNotification('Deletion Multiple States Error', 'Please select the states you wish to delete.', 'danger');
            }
        });

        $(document).on('click','#export-data',function() {
            generateExportColumns('state');
        });

        $(document).on('click','#submit-export',function() {
            exportData('state');
        });

        $('#datatable-search').on('keyup', function () {
            var table = $('#state-table').DataTable();
            table.search(this.value).draw();
        });

        $('#datatable-length').on('change', function() {
            var table = $('#state-table').DataTable();
            var length = $(this).val(); 
            table.page.len(length).draw();
        });

        $(document).on('click','#apply-filter',function() {
            stateTable('#state-table');
        });

        $(document).on('click', '#reset-filter', function() {
            $('#country_filter').val(null).trigger('change');
            
            stateTable('#state-table');
        });
    });
})(jQuery);

function stateTable(datatable_name) {
    toggleHideActionDropdown();

    const type = 'state table';
    const page_id = $('#page-id').val();
    const page_link = document.getElementById('page-link').getAttribute('href');
    const country_filter = $('#country_filter').val();


    const columns = [ 
        { data: 'CHECK_BOX' },
        { data: 'STATE_NAME' },
        { data: 'COUNTRY_NAME' }
    ];

    const columnDefs = [
        { width: '1%', bSortable: false, targets: 0, responsivePriority: 1 },
        { width: 'auto', targets: 1, responsivePriority: 2 },
        { width: 'auto', targets: 2, responsivePriority: 3 }
    ];

    const lengthMenu = [[10, 5, 25, 50, 100, -1], [10, 5, 25, 50, 100, 'All']];

    const settings = {
        ajax: { 
            url: 'apps/settings/state/view/_state_generation.php',
            method: 'POST',
            dataType: 'json',
            data: {
                type: type,
                page_id: page_id,
                page_link: page_link,
                country_filter: country_filter
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
        case 'country options':
            
            $.ajax({
                url: 'apps/settings/country/view/_country_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type,
                    multiple : 1
                },
                success: function(response) {
                    $('#country_filter').select2({
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