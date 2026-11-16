// Đăng nhập handler
$(document).ready(function() {
    // Check if user already logged in
    getCurrentUser((user) => {
        if(user) {
            // Already logged in, redirect to home
            window.location.href = 'Index.php';
        }
    });

    // Handle login form submission
    $('#loginForm').submit(function(e) {
        e.preventDefault();
        
        const username = $('#username').val().trim();
        const password = $('#password').val().trim();
        const rememberMe = $('#rememberMe').is(':checked');
        
        if(!username) {
            Swal.fire({
                type: 'warning',
                title: 'Thiếu thông tin',
                text: 'Vui lòng nhập tên tài khoản'
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

        // Show loading
        Swal.fire({
            title: 'Đang đăng nhập...',
            text: 'Vui lòng đợi',
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            showConfirmButton: false,
            onOpen: () => {
                Swal.showLoading();
            }
        });

        // Call API to login
        $.ajax({
            url: 'Api/ProcessAccount.php',
            type: 'POST',
            dataType: 'json',
            data: {
                request: 'login',
                username: username,
                password: password,
                rememberMe: rememberMe
            },
            success: function(response) {
                if(response.success) {
                    Swal.fire({
                        type: 'success',
                        title: 'Đăng nhập thành công!',
                        text: 'Chào mừng bạn trở lại',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        // Redirect to home or previous page
                        const redirect = new URLSearchParams(window.location.search).get('redirect');
                        window.location.href = redirect || 'Index.php';
                    });
                } else {
                    Swal.fire({
                        type: 'error',
                        title: 'Đăng nhập thất bại',
                        text: response.message || 'Tên tài khoản hoặc mật khẩu không chính xác'
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

// Login with Google
function loginWithGoogle() {
    // Redirect to Google login
    window.location.href = 'Api/GoogleLogin.php';
}

