(function($) {
    'use strict';

    $(function() {
        generateDropdownOptions('subscription tier options');
        generateDropdownOptions('billing cycle options');

        if($('#subscriber-table').length){
            subscriberTable('#subscriber-table');
        }

        $(document).on('click','.delete-subscriber',function() {
            const subscriber_id = $(this).data('subscriber-id');
            const transaction = 'delete subscriber';
    
            Swal.fire({
                title: 'Confirm Subscriber Deletion',
                text: 'Are you sure you want to delete this subscriber?',
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
                        url: 'apps/subscription/subscriber/controller/subscriber-controller.php',
                        dataType: 'json',
                        data: {
                            subscriber_id : subscriber_id, 
                            transaction : transaction
                        },
                        success: function (response) {
                            if (response.success) {
                                showNotification(response.title, response.message, response.messageType);
                                reloadDatatable('#subscriber-table');
                            }
                            else {
                                if (response.isInactive || response.userNotExist || response.userInactive || response.userLocked || response.sessionExpired) {
                                    setNotification(response.title, response.message, response.messageType);
                                    window.location = 'logout.php?logout';
                                }
                                else if (response.notExist) {
                                    setNotification(response.title, response.message, response.messageType);
                                    reloadDatatable('#subscriber-table');
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

        $(document).on('click','#delete-subscriber',function() {
            let subscriber_id = [];
            const transaction = 'delete multiple subscriber';

            $('.datatable-checkbox-children').each((index, element) => {
                if ($(element).is(':checked')) {
                    subscriber_id.push(element.value);
                }
            });
    
            if(subscriber_id.length > 0){
                Swal.fire({
                    title: 'Confirm Multiple Subscribers Deletion',
                    text: 'Are you sure you want to delete these subscribers?',
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
                            url: 'apps/subscription/subscriber/controller/subscriber-controller.php',
                            dataType: 'json',
                            data: {
                                subscriber_id: subscriber_id,
                                transaction : transaction
                            },
                            success: function (response) {
                                if (response.success) {
                                    showNotification(response.title, response.message, response.messageType);
                                    reloadDatatable('#subscriber-table');
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
                showNotification('Deletion Multiple Subscriber Error', 'Please select the subscribers you wish to delete.', 'danger');
            }
        });

        $(document).on('click','#export-data',function() {
            generateExportColumns('subscriber');
        });

        $(document).on('click','#submit-export',function() {
            exportData('subscriber');
        });

        $('#datatable-search').on('keyup', function () {
            var table = $('#subscriber-table').DataTable();
            table.search(this.value).draw();
        });

        $('#datatable-length').on('change', function() {
            var table = $('#subscriber-table').DataTable();
            var length = $(this).val(); 
            table.page.len(length).draw();
        });

        $(document).on('click','#apply-filter',function() {
            subscriberTable('#subscriber-table');
        });

        $(document).on('click', '#reset-filter', function() {
            $('#subscription_tier_filter').val(null).trigger('change');
            $('#billing_cycle_filter').val(null).trigger('change');
            
            subscriberTable('#subscriber-table');
        });
    });
})(jQuery);

function subscriberTable(datatable_name) {
    toggleHideActionDropdown();

    const type = 'subscriber table';
    const page_id = $('#page-id').val();
    const page_link = document.getElementById('page-link').getAttribute('href');
    const subscription_tier_filter = $('#subscription_tier_filter').val();
    const billing_cycle_filter = $('#billing_cycle_filter').val();


    const columns = [ 
        { data: 'CHECK_BOX' },
        { data: 'SUBSCRIBER' },
        { data: 'PHONE' },
        { data: 'EMAIL' },
        { data: 'SUBSCRIPTION_TIER' },
        { data: 'BILLING_CYCLE' },
        { data: 'SUBSCRIBER_STATUS' }
    ];

    const columnDefs = [
        { width: '5%', bSortable: false, targets: 0, responsivePriority: 1 },
        { width: 'auto', targets: 1, responsivePriority: 2 },
        { width: 'auto', targets: 2, responsivePriority: 3 },
        { width: 'auto', targets: 3, responsivePriority: 4 },
        { width: 'auto', targets: 4, responsivePriority: 5 }
    ];

    const lengthMenu = [[10, 5, 25, 50, 100, -1], [10, 5, 25, 50, 100, 'All']];

    const settings = {
        ajax: { 
            url: 'apps/subscription/subscriber/view/_subscriber_generation.php',
            method: 'POST',
            dataType: 'json',
            data: {
                type: type,
                page_id: page_id,
                page_link: page_link,
                subscription_tier_filter: subscription_tier_filter,
                billing_cycle_filter: billing_cycle_filter
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
        case 'subscription tier options':
            
            $.ajax({
                url: 'apps/subscription/subscription-tier/view/_subscription_tier_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type,
                    multiple : 1
                },
                success: function(response) {
                    $('#subscription_tier_filter').select2({
                        data: response
                    });
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                }
            });
            break;
        case 'billing cycle options':
            
            $.ajax({
                url: 'apps/subscription/billing-cycle/view/_billing_cycle_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type,
                    multiple : 1
                },
                success: function(response) {
                    $('#billing_cycle_filter').select2({
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