<?php
// Universal Login Page
require_once 'init.php';
$pageTitle = "Login | PPA";
$current_page = 'login';

// Get User instance
$user = User::getInstance();

// Debug login process
error_log("Login page accessed - checking if user is already logged in");

// Check if already logged in and redirect based on role
if ($user->isLoggedIn()) {
    $current_user = $user->getCurrentUser();
    error_log("User already logged in, current user data: " . json_encode($current_user));
    
    // 检查用户角色并重定向到适当的控制面板
    if (isset($current_user['role_name'])) {
        if (in_array($current_user['role_name'], ['super_admin', 'admin', 'agent', 'accountant'])) {
            error_log("Redirecting admin user to admin dashboard");
            header('Location: admin/dashboard.php');
            exit;
        } elseif ($current_user['role_name'] == 'customer') {
            error_log("Redirecting customer to user dashboard");
            header('Location: user/dashboard.php');
            exit;
        }
    } else {
        // 没有角色的用户默认为客户
        error_log("User has no role, defaulting to customer and redirecting to user dashboard");
        header('Location: user/dashboard.php');
        exit;
    }
}

// Handle login form submission
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Login form submitted");
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $remember = isset($_POST['remember']) ? (bool)$_POST['remember'] : false;
    
    error_log("Login attempt for email: $email with remember option: " . ($remember ? 'true' : 'false'));
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password';
        error_log("Login error: empty email or password");
    } else {
        // 尝试登录（不检查管理员角色）
        error_log("Attempting to log in user with email: $email");
        $login = $user->login($email, $password, $remember, false);
        
        if ($login === true) {
            error_log("Login successful for email: $email");
            // 根据用户角色重定向
            $current_user = $user->getCurrentUser();
            error_log("Current user data after login: " . json_encode($current_user));
            
            if (isset($current_user['role_name'])) {
                error_log("User role identified: " . $current_user['role_name']);
                if (in_array($current_user['role_name'], ['super_admin', 'admin', 'agent', 'accountant'])) {
                    // 管理员登录活动记录
                    error_log("Admin user logged in - recording activity");
                    $user->logAdminActivity('logged in', 'admin panel', $current_user['id'], 'User logged into admin panel');
                    
                    // 重定向到管理员控制面板
                    error_log("Redirecting admin user to admin dashboard");
                    header('Location: admin/dashboard.php');
                    exit;
                } elseif ($current_user['role_name'] == 'customer') {
                    // 重定向到客户控制面板
                    error_log("Redirecting customer to user dashboard");
                    header('Location: user/dashboard.php');
                    exit;
                }
            } else {
                // 没有角色的用户默认为客户
                error_log("User has no role, defaulting to customer and redirecting to user dashboard");
                header('Location: user/dashboard.php');
                exit;
            }
        } else {
            $error = 'Invalid email or password';
            error_log("Login failed for email: $email - Invalid credentials");
        }
    }
}

// Include additional CSS for glass effect
$additional_css = ['modern.css'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/images/ppa-app-logo.png" type="image/x-icon">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <?php if (isset($additional_css) && !empty($additional_css)): ?>
        <?php foreach ($additional_css as $css_file): ?>
        <link rel="stylesheet" href="assets/css/<?php echo $css_file; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            background-image: url('assets/images/pattern-bg.png'), linear-gradient(135deg, #4361ee, #3a0ca3);
            background-size: cover;
            display: flex;
            align-items: center;
            font-family: 'Manrope', sans-serif;
        }
        
        /* 移动设备上禁用所有模糊效果 */
        @media (max-width: 991.98px) {
            .glass-card,
            .glass-input,
            .btn-primary,
            .return-link,
            .floating-shapes div,
            .modal-content {
                backdrop-filter: none !important;
                -webkit-backdrop-filter: none !important;
            }
        }
        
        .login-container {
            max-width: 440px;
            margin: 0 auto;
            width: 100%;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .page-header h1 {
            font-weight: 800;
            color: white;
            font-size: 2.2rem;
            margin-top: 0.5rem;
        }
        
        .page-header p {
            color: rgba(255, 255, 255, 0.8);
        }
        
        .login-logo-wrapper {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
            padding-top: 1rem;
        }
        
        .login-logo {
            max-width: 180px;
            filter: drop-shadow(0 4px 10px rgba(0, 0, 0, 0.2));
        }
        
        .glass-card {
            /* backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px); */
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .glass-card:hover {
            box-shadow: 0 12px 40px rgba(31, 38, 135, 0.3);
            transform: translateY(-5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .glass-input {
            background: rgba(255, 255, 255, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.3);
            /* backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px); */
            color: white;
            border-radius: 12px;
            padding: 12px 15px;
            font-size: 1rem;
        }
        
        .glass-input:focus {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.5);
            box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.15);
            color: white;
        }
        
        .glass-input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        
        .form-label {
            color: white;
            font-weight: 500;
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
        }
        
        .form-check-label {
            color: rgba(255, 255, 255, 0.9);
        }
        
        .form-check-input {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
        }
        
        .form-check-input:checked {
            background-color: #4361ee;
            border-color: #4361ee;
        }
        
        .btn-primary {
            background: rgba(255, 255, 255, 0.2);
            /* backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px); */
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            font-weight: 600;
            padding: 12px 20px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.4);
            transform: translateY(-2px);
        }
        
        .btn-outline-light {
            border-color: rgba(255, 255, 255, 0.3);
            color: white;
            font-weight: 500;
        }
        
        .btn-outline-light:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.5);
        }
        
        .card-body {
            padding: 2.5rem;
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
            color: rgba(255, 255, 255, 0.6);
        }
        
        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.2);
        }
        
        .divider::before {
            margin-right: 1rem;
        }
        
        .divider::after {
            margin-left: 1rem;
        }
        
        .google-btn {
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            border-radius: 12px;
            padding: 12px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
        }
        
        .google-btn:hover {
            background: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .forgot-link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }
        
        .forgot-link:hover {
            color: white;
            text-decoration: underline;
        }
        
        .input-group {
            position: relative;
            display: flex;
            align-items: stretch;
            width: 100%;
        }
        
        .input-group .glass-input {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            flex: 1 1 auto;
        }
        
        .toggle-password-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            width: 46px;
            cursor: pointer;
        }
        
        .toggle-password-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            border-color: rgba(255, 255, 255, 0.4);
        }
        
        .error-message {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid rgba(220, 53, 69, 0.3);
            color: white;
        }
        
        .success-message {
            background: rgba(25, 135, 84, 0.2);
            border: 1px solid rgba(25, 135, 84, 0.3);
            color: white;
        }
        
        .return-link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            font-size: 0.9rem;
            margin-top: 1.5rem;
            padding: 8px 16px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.15);
            /* backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px); */
            transition: all 0.3s ease;
        }
        
        .return-link:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateY(-2px);
        }
        
        .floating-shapes div {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            /* backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px); */
            border-radius: 50%;
            z-index: -1;
        }
        
        .shape1 {
            width: 200px;
            height: 200px;
            top: -100px;
            right: 10%;
            animation: float 15s ease-in-out infinite alternate;
        }
        
        .shape2 {
            width: 150px;
            height: 150px;
            bottom: 5%;
            left: 10%;
            animation: float 12s ease-in-out infinite alternate-reverse;
        }
        
        .shape3 {
            width: 100px;
            height: 100px;
            top: 20%;
            left: 20%;
            animation: float 10s ease-in-out infinite alternate;
        }
        
        @keyframes float {
            0% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-30px) rotate(10deg);
            }
            100% {
                transform: translateY(10px) rotate(-10deg);
            }
        }
        
        /* 防止Bootstrap或其他库添加额外的眼睛图标 */
        .form-control::-ms-reveal,
        .form-control::-ms-clear {
            display: none !important;
        }
        
        .form-control-feedback {
            display: none !important;
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape1"></div>
        <div class="shape2"></div>
        <div class="shape3"></div>
    </div>
    
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="login-container">
                    <div class="login-logo-wrapper">
                        <img src="assets/images/ppa-logo-white.png" alt="PPA Logo" class="login-logo">
                    </div>
                    
                    <!-- Alert for messages -->
                    <?php if (isset($_GET['auth_error']) || !empty($error)): ?>
                        <div class="alert error-message mb-4" role="alert">
                            <?php echo !empty($error) ? htmlspecialchars($error) : htmlspecialchars($_GET['auth_error']); ?>
                        </div>
                    <?php elseif (isset($_GET['auth_success'])): ?>
                        <div class="alert success-message mb-4" role="alert">
                            <?php echo htmlspecialchars($_GET['auth_success']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="glass-card">
                        <div class="card-body">
                            <form method="post" action="login.php">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control glass-input" id="email" name="email" placeholder="Enter your email" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control glass-input" id="password" name="password" placeholder="Enter your password" required>
                                        <button type="button" class="btn toggle-password-btn" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1">
                                        <label class="form-check-label" for="remember">Remember me</label>
                                    </div>
                                    <a href="#" class="forgot-link" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Forgot password?</a>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Log In</button>
                                </div>
                                
                                <div class="divider">Or</div>
                                
                                <div class="d-grid">
                                    <div class="position-relative">
                                        <a href="<?php echo $config['site_url']; ?>api/auth.php?action=google-auth-init" class="btn google-btn d-flex align-items-center justify-content-center gap-2" id="googleBtn">
                                            <img src="<?php echo asset('images/google-icon.png'); ?>" alt="Google" width="18" height="18">
                                            <span>Sign in with Google</span>
                                        </a>
                                        <div class="d-none text-center mt-2 small text-white-50" id="googleMobileWarning">
                                            <i class="fas fa-info-circle me-1"></i> Google login may not work in some mobile apps. Please use email login or open in your browser.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-center mt-4">
                                    <span class="text-white-50">Don't have an account?</span>
                                    <a href="register.php" class="ms-1 text-white">Register</a>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <a href="index.php" class="return-link">
                            <i class="fas fa-arrow-left me-2"></i> Return to Website
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass-card">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-white" id="forgotPasswordModalLabel">Reset Password</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-white">Enter your email address and we'll send you instructions to reset your password.</p>
                    <form id="forgotPasswordForm">
                        <div class="mb-3">
                            <input type="email" class="form-control glass-input" id="resetEmail" name="email" placeholder="Enter your email address" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Send Reset Link</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // 检测移动设备
            function isMobileDevice() {
                return (
                    navigator.userAgent.match(/Android/i) ||
                    navigator.userAgent.match(/webOS/i) ||
                    navigator.userAgent.match(/iPhone/i) ||
                    navigator.userAgent.match(/iPad/i) ||
                    navigator.userAgent.match(/iPod/i) ||
                    navigator.userAgent.match(/BlackBerry/i) ||
                    navigator.userAgent.match(/Windows Phone/i)
                );
            }
            
            // 如果是移动设备，显示警告
            if (isMobileDevice()) {
                $('#googleMobileWarning').removeClass('d-none');
                
                // 为Google按钮添加点击事件，提醒用户可能会有问题
                $('#googleBtn').on('click', function(e) {
                    if (window.navigator.standalone || 
                        window.matchMedia('(display-mode: standalone)').matches ||
                        document.referrer.includes('android-app://')) {
                        // 在独立应用模式下，提示并阻止默认行为
                        e.preventDefault();
                        alert('Google login is not supported in app mode. Please use email login or open in a web browser.');
                    }
                });
            }
            
            // 密码可见性切换 - 多种绑定方式
            $('#togglePassword').on('click touchstart mousedown', function(e) {
                e.preventDefault();
                
                const passwordField = $('#password');
                const icon = $(this).find('i');
                
                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
                
                return false; // 阻止默认行为
            });
            
            // 同时为icon添加事件处理
            $('#togglePassword i').on('click touchstart mousedown', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $('#togglePassword').trigger('click');
                return false;
            });
            
            // Handle forgot password form submission
            $('#forgotPasswordForm').on('submit', function(e) {
                e.preventDefault();
                
                const email = $('#resetEmail').val();
                
                // Show alert
                $('<div class="alert success-message mb-4" role="alert">Processing your request...</div>').insertAfter('.login-logo-wrapper');
                
                // Close modal
                $('#forgotPasswordModal').modal('hide');
                
                // Send reset request
                $.ajax({
                    url: 'api/user.php?action=reset_password',
                    type: 'POST',
                    data: JSON.stringify({ email: email }),
                    contentType: 'application/json',
                    dataType: 'json',
                    success: function(response) {
                        $('.alert').remove();
                        $('<div class="alert success-message mb-4" role="alert">' + response.message + '</div>').insertAfter('.login-logo-wrapper');
                    },
                    error: function(xhr) {
                        let errorMsg = 'An error occurred. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        
                        $('.alert').remove();
                        $('<div class="alert error-message mb-4" role="alert">' + errorMsg + '</div>').insertAfter('.login-logo-wrapper');
                    }
                });
            });
        });
    </script>
</body>
</html> 