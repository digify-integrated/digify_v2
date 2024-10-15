$(document).ready(function () {
    otpForm();
    
    $('.otp-input').on('input', function() {
        var maxLength = parseInt($(this).attr('maxlength'));
        var currentLength = $(this).val().length;

        if (currentLength === maxLength) {
            $(this).next('.otp-input').focus();
        }
    });

    $('.otp-input').on('paste', function(e) {
        e.preventDefault();
        
        var pastedData = (e.originalEvent || e).clipboardData.getData('text/plain');
        
        var filteredData = pastedData.replace(/[^a-zA-Z0-9]/g, '');

        for (var i = 0; i < filteredData.length; i++) {
            if (i < 6) {
                $('#otp_code_' + (i + 1)).val(filteredData.charAt(i));
            }
        }
    });

    $('.otp-input').on('keydown', function(e) {
        if (e.which === 8 && $(this).val().length === 0) {
            $(this).prev('.otp-input').focus();
        }
    });

    $('#resend-link').on('click', function(e) {
        resetCountdown(60);
    });
});

function otpForm(){
    $('#otp-form').validate({
        rules: {
            otp_code_1: {
                required: true,
            },
            otp_code_2: {
                required: true
            },
            otp_code_3: {
                required: true
            },
            otp_code_4: {
                required: true
            },
            otp_code_5: {
                required: true
            },
            otp_code_6: {
                required: true
            }
        },
        messages: {
            otp_code_1: {
                required: 'Enter the security code',
            },
            otp_code_2: {
                required: 'Enter the security code'
            },
            otp_code_3: {
                required: 'Enter the security code'
            },
            otp_code_4: {
                required: 'Enter the security code'
            },
            otp_code_5: {
                required: 'Enter the security code'
            },
            otp_code_6: {
                required: 'Enter the security code'
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
            const transaction = 'otp verification';
    
            $.ajax({
                type: 'POST',
                url: 'apps/security/authentication/controller/authentication-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction,
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('verify');
                },
                success: function(response) {
                    if (response.success) {
                        window.location.href = response.redirectLink;
                    } 
                    else {
                        if(response.otpMaxFailedAttempt || response.incorrectOTPCode || response.expiredOTP){
                            showNotification(response.title, response.message, response.messageType);
                        }
                        else{
                            setNotification(response.title, response.message, response.messageType);
                            window.location.href = 'index.php';
                        }
                    }
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                },
                complete: function() {
                    enableFormSubmitButton('verify');
                }
            });
    
            return false;
        }
    });
}

function startCountdown(countdownValue) {
    $('#countdown').removeClass('d-none');
    $('#resend-link').addClass('d-none');

    countdownTimer = setInterval(function () {
        document.getElementById('countdown').innerHTML = countdownValue;
        countdownValue--;

        if (countdownValue < 0) {
            clearInterval(countdownTimer);
            $('#countdown').addClass('d-none');
            $('#resend-link').removeClass('d-none');
        }
    }, 1000);
}

function resetCountdown(countdownValue) {
    const user_account_id = $('#user_account_id').val();
    const transaction = 'resend otp';

    $.ajax({
        type: 'POST',
        url: 'apps/security/authentication/controller/authentication-controller.php',
        dataType: 'json',
        data: {
            user_account_id : user_account_id, 
            transaction : transaction
        },
        beforeSend: function() {
            $('#countdown').removeClass('d-none');
            $('#resend-link').addClass('d-none');

            document.getElementById('countdown').innerHTML = countdownValue;

            startCountdown(countdownValue);
        },
        success: function (response) {
            if (!response.success) {
                window.location.href = 'index.php';
                setNotification(response.title, response.message, response.messageType);
            }
        },
        error: function(xhr, status, error) {
            handleSystemError(xhr, status, error);
        }
    });
}