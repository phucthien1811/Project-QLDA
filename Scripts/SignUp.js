// Đăng ký handler
$(document).ready(function() {
    // Check if user already logged in
    getCurrentUser((user) => {
        if(user) {
            // Already logged in, redirect to home
            window.location.href = 'Index.php';
        }
    });

    // Handle register form submission
    $('#registerForm').submit(function(e) {
        e.preventDefault();
        
        const firstName = $('#firstName').val().trim();
        const lastName = $('#lastName').val().trim();
        const email = $('#email').val().trim();
        const phone = $('#phone').val().trim();
        const password = $('#password').val();
        const confirmPassword = $('#confirmPassword').val();
        const dob = $('#dob').val();
        const address = $('#address').val().trim();
        const termsAccepted = $('#termsAndConditions').is(':checked');
        const marketingConsent = $('#marketingConsent').is(':checked');
        const personalizedAds = $('#personalizedAds').is(':checked');
        
        // Validation
        if(!firstName || !lastName) {
            Swal.fire({
                type: 'warning',
                title: 'Thiếu thông tin',
                text: 'Vui lòng nhập họ và tên'
            });
            return;
        }

        if(!email) {
            Swal.fire({
                type: 'warning',
                title: 'Thiếu thông tin',
                text: 'Vui lòng nhập email'
            });
            return;
        }

        // Validate email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if(!emailRegex.test(email)) {
            Swal.fire({
                type: 'warning',
                title: 'Email không hợp lệ',
                text: 'Vui lòng nhập đúng định dạng email'
            });
            return;
        }

        if(!phone) {
            Swal.fire({
                type: 'warning',
                title: 'Thiếu thông tin',
                text: 'Vui lòng nhập số điện thoại'
            });
            return;
        }

        // Validate phone (Vietnamese format)
        const phoneRegex = /^(0|\+84)[0-9]{9,10}$/;
        if(!phoneRegex.test(phone.replace(/\s/g, ''))) {
            Swal.fire({
                type: 'warning',
                title: 'Số điện thoại không hợp lệ',
                text: 'Vui lòng nhập đúng định dạng số điện thoại'
            });
            return;
        }

        if(!password) {
            Swal.fire({
                type: 'warning',
                title: 'Thiếu thông tin',
                text: 'Vui lòng nhập mật khẩu'
            });
            return;
        }

        // Validate password strength
        if(password.length < 8) {
            Swal.fire({
                type: 'warning',
                title: 'Mật khẩu yếu',
                text: 'Mật khẩu phải có ít nhất 8 ký tự'
            });
            return;
        }

        const hasUpperCase = /[A-Z]/.test(password);
        const hasLowerCase = /[a-z]/.test(password);
        const hasNumber = /[0-9]/.test(password);

        if(!hasUpperCase || !hasLowerCase || !hasNumber) {
            Swal.fire({
                type: 'warning',
                title: 'Mật khẩu không đủ mạnh',
                text: 'Mật khẩu phải có chữ hoa, chữ thường và số'
            });
            return;
        }

        if(password !== confirmPassword) {
            Swal.fire({
                type: 'warning',
                title: 'Mật khẩu không khớp',
                text: 'Xác nhận mật khẩu không giống với mật khẩu'
            });
            return;
        }

        if(!dob) {
            Swal.fire({
                type: 'warning',
                title: 'Thiếu thông tin',
                text: 'Vui lòng chọn ngày sinh'
            });
            return;
        }

        // Check age (must be at least 13 years old)
        const birthDate = new Date(dob);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }

        if(age < 13) {
            Swal.fire({
                type: 'warning',
                title: 'Độ tuổi không hợp lệ',
                text: 'Bạn phải ít nhất 13 tuổi để đăng ký'
            });
            return;
        }

        if(!termsAccepted) {
            Swal.fire({
                type: 'warning',
                title: 'Chưa đồng ý điều khoản',
                text: 'Vui lòng đồng ý với điều khoản dịch vụ'
            });
            return;
        }

        // Show loading
        Swal.fire({
            title: 'Đang xử lý...',
            text: 'Vui lòng đợi',
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            showConfirmButton: false,
            onOpen: () => {
                Swal.showLoading();
            }
        });

        // Call API to register
        $.ajax({
            url: 'Api/ProcessAccount.php',
            type: 'POST',
            dataType: 'json',
            data: {
                request: 'register',
                firstName: firstName,
                lastName: lastName,
                email: email,
                phone: phone,
                password: password,
                dob: dob,
                address: address,
                marketingConsent: marketingConsent,
                personalizedAds: personalizedAds
            },
            success: function(response) {
                if(response.success) {
                    Swal.fire({
                        type: 'success',
                        title: 'Đăng ký thành công!',
                        text: 'Chào mừng bạn đến với Samsung',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Redirect to login or home
                        window.location.href = 'SignIn.php';
                    });
                } else {
                    Swal.fire({
                        type: 'error',
                        title: 'Đăng ký thất bại',
                        text: response.message || 'Có lỗi xảy ra, vui lòng thử lại'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    type: 'error',
                    title: 'Lỗi',
                    text: 'Có lỗi xảy ra, vui lòng thử lại sau'
                });
            }
        });
    });
});

// Register with Google
function registerWithGoogle() {
    Swal.fire({
        type: 'info',
        title: 'Chức năng đang phát triển',
        text: 'Đăng ký bằng Google sẽ sớm được cập nhật'
    });
}

