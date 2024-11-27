(function($) {
    'use strict';

    $(function() {
        generateDropdownOptions('city options');
        generateDropdownOptions('currency options');

        displayDetails('get company details');

        if($('#company-form').length){
            companyForm();
        }

        $(document).on('click','#delete-company',function() {
            const company_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'delete company';
    
            Swal.fire({
                title: 'Confirm Company Deletion',
                text: 'Are you sure you want to delete this company?',
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
                        url: 'apps/settings/company/controller/company-controller.php',
                        dataType: 'json',
                        data: {
                            company_id : company_id, 
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

        $(document).on('change','#company_logo',function() {
            if ($(this).val() !== '' && $(this)[0].files.length > 0) {
                const transaction = 'update company logo';
                const company_id = $('#details-id').text();
                var formData = new FormData();
                formData.append('company_logo', $(this)[0].files[0]);
                formData.append('transaction', transaction);
                formData.append('company_id', company_id);
        
                $.ajax({
                    type: 'POST',
                    url: 'apps/settings/company/controller/company-controller.php',
                    dataType: 'json',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            showNotification(response.title, response.message, response.messageType);
                            displayDetails('get company details');
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
            }
        });

        $(document).on('click','#log-notes-main',function() {
            const company_id = $('#details-id').text();

            logNotes('company', company_id);
        });
    });
})(jQuery);

function companyForm(){
    $('#company-form').validate({
        rules: {
            company_name: {
                required: true
            },
            address: {
                required: true
            },
            city_id: {
                required: true
            }
        },
        messages: {
            company_name: {
                required: 'Enter the display name'
            },
            address: {
                required: 'Enter the display name'
            },
            city_id: {
                required: 'Choose the city'
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
            const company_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update company';
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/company/controller/company-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&company_id=' + encodeURIComponent(company_id),
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
                    logNotes('company', company_id);
                }
            });
        
            return false;
        }
    });
}

function displayDetails(transaction){
    switch (transaction) {
        case 'get company details':
            var company_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/settings/company/controller/company-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    company_id : company_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetForm('company-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#company_name').val(response.companyName);
                        $('#address').val(response.address);
                        $('#tax_id').val(response.taxID);
                        $('#phone').val(response.phone);
                        $('#telephone').val(response.telephone);
                        $('#email').val(response.email);
                        $('#website').val(response.website);
                        
                        $('#city_id').val(response.cityID).trigger('change');
                        $('#currency_id').val(response.currencyID).trigger('change');

                        document.getElementById('compay_logo_thumbnail').style.backgroundImage = `url(${response.companyLogo})`;
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

function generateDropdownOptions(type){
    switch (type) {
        case 'city options':
            
            $.ajax({
                url: 'apps/settings/city/view/_city_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type
                },
                success: function(response) {
                    $('#city_id').select2({
                        data: response
                    }).on('change', function (e) {
                        $(this).valid()
                    });
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                }
            });
            break;
        case 'currency options':
            
            $.ajax({
                url: 'apps/settings/currency/view/_currency_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type
                },
                success: function(response) {
                    $('#currency_id').select2({
                        data: response
                    }).on('change', function (e) {
                        $(this).valid()
                    });
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                }
            });
            break;
    }
}