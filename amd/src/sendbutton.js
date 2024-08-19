define(['jquery', 'core/ajax'], function ($, ajax) {
    return {
        auth_twiliootp: function (pluginname, minrequestperiod) {
            $("#id_submitbutton,#id_phone2").prop('disabled', true);
            $('#otpButton,#otpbox,#verifybutton,.verfied-otp-container,.notverfied-otp-container,.otpsent_message,.otpnotsent_message,.invalid_number,.already_phone,.already_username,.already_email,.expired-otp-container').hide();
            
            function validatePassword(password) {
                var passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
                return passwordRegex.test(password);
            }

            function validateEmail(email) {
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            function updateOtpButtonVisibility() {
                var phoneNumber = $('#id_phone2').val().trim();
                var username = $('#id_username').val().trim();
                var useremail = $('#id_email').val().trim();
                var password = $('#id_password').val().trim();
                var firstname = $('#id_firstname').val().trim();
                var lastname = $('#id_lastname').val().trim();

                var isPhoneValid = /^\d+$/.test(phoneNumber);
                var isUsernameNotEmpty = username !== "";
                var isEmailValid = validateEmail(useremail);
                var isPasswordValid = validatePassword(password);
                var isFirstnameNotEmpty = firstname !== "";
                var isLastnameNotEmpty = lastname !== "";

                if (isPhoneValid && isUsernameNotEmpty && isEmailValid && isPasswordValid && isFirstnameNotEmpty && isLastnameNotEmpty) {
                    $('#otpButton').show(); // Show Send OTP button
                } else {
                    $('#otpButton').hide(); // Hide Send OTP button
                }
            }

            $('#id_country').change(function () {
                if ($(this).val() != "") {
                    $("#id_phone2").prop('disabled', false);
                } else {
                    $("#id_phone2").prop('disabled', true);
                }
                updateOtpButtonVisibility();
            });

            $('#id_phone2, #id_username, #id_email, #id_password, #id_firstname, #id_lastname').on('input', updateOtpButtonVisibility);

            // Restrict OTP input to 4 digits and numeric only
            $('#otpbox').on('input', function (event) {
                var otp = $(this).val().trim();
                // Remove non-numeric characters from input
                var numericOtp = otp.replace(/\D/g, '');
                // Limit input to first 4 characters
                var trimmedOtp = numericOtp.slice(0, 4);
                // Update input value with cleaned and trimmed OTP
                $(this).val(trimmedOtp);
            });

            var otpBtn = $('#otpButton');
            if (otpBtn) {
                otpBtn.click(function (event) {
                    event.preventDefault();
                    $('.otpsent_message,.otpnotsent_message,.invalid_number,.notverfied-otp-container,.already_username,.already_email,.already_phone,.expired-otp-container').hide();
                    $('#loader-container').fadeIn();
                    setTimeout(function () {
                        $('#loader-container').fadeOut(function () {
                            var usermobile = $('#id_phone2').val();
                            var username = $('#id_username').val();
                            var useremail = $('#id_email').val();
                            var usercountry = $('#id_country').val();
                            var promises = ajax.call([{
                                methodname: 'moodle_twiliootp_twiliootp_js_settings',
                                args: { flag: 1, phone: usermobile, username: username, useremail: useremail, usercountry: usercountry },
                            }]);
                            promises[0].then(function (data) {
                                if (data['status'] === 1) { // Success
                                    $('.otpsent_message').show();
                                    $('#mobile-number').text(usermobile);
                                    $('#success-message').fadeIn();

                                    var countdown = minrequestperiod; // Countdown from 6 seconds
                                    $('#otpButton').text('Resend OTP (' + countdown + 's)').prop('disabled', true);

                                    var interval = setInterval(function () {
                                        countdown--;
                                        if (countdown >= 0) {
                                            $('#otpButton').text('Resend OTP (' + countdown + 's)');
                                        } else {
                                            clearInterval(interval);
                                            $('#otpButton').text('Resend OTP').prop('disabled', false);
                                        }
                                    }, 1000); // Update countdown every second (1000 milliseconds)

                                    $('#otpbox, #verifybutton').show();
                                    $('#id_username, #id_email, #id_phone2').prop('disabled', false);

                                } else if (data['status'] === 0) { // General error
                                    data['errors'].forEach(function (errorCode) {
                                        switch (errorCode) {
                                            case 4: // Username Taken
                                                $('.already_username').show();
                                                break;
                                            case 5: // Email Taken
                                                $('.already_email').show();
                                                break;
                                            case 6: // Phone Number Taken
                                                $('.already_phone').show();
                                                break;
                                            case 3: // Invalid Phone Number
                                                $('.invalid_number').show();
                                                break;
                                            case 2: // Twilio Error
                                                $('.otpnotsent_message').show();
                                                $('#otpbox, #verifybutton').hide();
                                                break;
                                            default:
                                                console.error('Unknown error code: ' + errorCode);
                                                break;
                                        }
                                    });
                                } else {
                                    console.error('Unknown status code: ' + data['status']);
                                }
                            });
                        });
                    }, 5000);
                });
            }

            var verifyotpBtn = $('#verifybutton');
            if (verifyotpBtn) {
                verifyotpBtn.click(function (event) {
                    event.preventDefault();
                    var otp = $('#otpbox').val().trim();
                    if (/^\d{4}$/.test(otp)) {
                        //console.log('OTP entered:', otp);
                        // Proceed with verification logic
                    } else {
                        //console.log('Invalid OTP format.');
                        // Handle invalid OTP format
                    }
                    $('.otpsent_message,.notverfied-otp-container,.expired-otp-container').hide();
                    $('#loader-container').fadeIn();
                    setTimeout(function () {
                        $('#loader-container').fadeOut(function () {
                            var usermobile = $('#id_phone2').val();
                            var username = $('#id_username').val();
                            var useremail = $('#id_email').val();
                            var otp = $('#otpbox').val();
                            event.preventDefault();
                            var promises = ajax.call([{
                                methodname: 'moodle_twiliootp_success_twiliootp_url',
                                args: { flag: 1, phone: usermobile, username: username, useremail: useremail, otp: otp },
                            }]);
                            promises[0].then(function (options) {
                                if (options['status'] === 1) { // Success
                                	$('.verfied-otp-container').show();
                                    $('.send-otp-container').hide();
                                    $('.notverfied-otp-container').hide();
                                    $('.expired-otp-container').hide();
                                    $("#id_submitbutton").prop('disabled', false);
                                    $("#id_phone2,#id_country,#id_username,#id_email").css({ 'pointer-events': 'none', 'background-color': '#e9ecef' });
                                } else if (options['status'] === 0) { // General error
                                	options['errors'].forEach(function (errorCode) {
                                        switch (errorCode) {
                                            case 7: // OTP expired
                                                $('#otpbox').val('');
			                                    $('.otpsent_message').hide();
			                                    $('.send-otp-container').show();
			                                    $('.expired-otp-container').show();
                                                break;
                                            case 8: // INVALID_OTP
                                                $('#otpbox').val('');
			                                    $('.otpsent_message').hide();
			                                    $('.send-otp-container').show();
			                                    $('.notverfied-otp-container').show();
                                                break;
                                            case 9: // OTP_RECORD_NOT_FOUND
                                                $('#otpbox').val('');
			                                    $('.otpsent_message').hide();
			                                    $('.send-otp-container').show();
			                                    $('.notverfied-otp-container').show();
                                                break;
                                            default:
                                                console.error('Unknown error code: ' + errorCode);
                                                break;
                                        }
                                    });
                                }
                               
                            });
                        });
                    }, 3000);
                });
            }
        }
    };
});
