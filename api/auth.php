<?php
/**
 * Authentication API
 * Handles login, registration, and Google OAuth
 */

// Include initialization file
require_once '../init.php';

// Log incoming request for debugging
error_log("Auth API request: " . $_SERVER['REQUEST_URI']);
error_log("GET params: " . print_r($_GET, true));

// Get action from request
$action = isset($_GET['action']) ? $_GET['action'] : '';

// If no action but code is present, assume it's Google callback
if (empty($action) && isset($_GET['code'])) {
    error_log("Detected Google callback without action parameter");
    $action = 'google-auth-callback';
}

// For Google callback, use HTML content type
if ($action == 'google-auth-callback') {
    // Set content type to HTML for redirects
    header('Content-Type: text/html');
} else {
    // Set content type to JSON for API responses
    header('Content-Type: application/json');
}

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Initialize User class
$user = new User($db);

// Handle different actions
switch ($action) {
    case 'login':
        handleLogin($user);
        break;
        
    case 'register':
        handleRegister($user);
        break;
        
    case 'google-auth-init':
        handleGoogleAuthInit();
        break;
        
    case 'google-auth-callback':
        handleGoogleAuthCallback($user);
        break;
        
    case 'logout':
        handleLogout($user);
        break;
        
    case 'check-auth':
        checkAuthStatus();
        break;
        
    case 'change_password':
        handlePasswordChange();
        break;
        
    default:
        // Invalid action
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

/**
 * Handle login request
 */
function handleLogin($user) {
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    if (!isset($data['email']) || !isset($data['password'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email and password are required']);
        return;
    }
    
    // Attempt login
    $remember = isset($data['remember']) ? (bool)$data['remember'] : false;
    $result = $user->login($data['email'], $data['password'], $remember);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'redirect' => '../admin/dashboard.php'
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    }
}

/**
 * Handle registration request
 */
function handleRegister($user) {
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $required_fields = ['first_name', 'last_name', 'email', 'password', 'phone'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
            return;
        }
    }
    
    // Validate password strength
    if (strlen($data['password']) < 8) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long']);
        return;
    }
    
    // Validate email format
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        return;
    }
    
    // Register user
    $user_id = $user->register([
        'first_name' => $data['first_name'],
        'last_name' => $data['last_name'],
        'email' => $data['email'],
        'password' => $data['password'],
        'phone' => $data['phone'],
        'address' => $data['address'] ?? '',
        'city' => $data['city'] ?? '',
        'postcode' => $data['postcode'] ?? '',
        'state' => $data['state'] ?? ''
    ]);
    
    if ($user_id) {
        // Auto login after registration
        $user->login($data['email'], $data['password']);
        
        echo json_encode([
            'success' => true,
            'message' => 'Registration successful',
            'user_id' => $user_id
        ]);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Registration failed. Email may already be in use.']);
    }
}

/**
 * Initialize Google OAuth authentication
 */
function handleGoogleAuthInit() {
    // Set content type to HTML for redirects
    header('Content-Type: text/html');
    
    // Debug incoming request
    error_log("Google Auth Init - Query params: " . print_r($_GET, true));
    
    // Load Google API Client library
    require_once '../vendor/autoload.php';
    
    // Create Google Client
    $client = new Google_Client();
    $client->setClientId(GOOGLE_CLIENT_ID);
    $client->setClientSecret(GOOGLE_CLIENT_SECRET);
    $client->setRedirectUri(GOOGLE_REDIRECT_URI);
    $client->addScope("email");
    $client->addScope("profile");
    
    // Check if this is an admin login attempt
    $isAdminLogin = isset($_GET['admin_login']) && $_GET['admin_login'] == 1;
    error_log("Is admin login: " . ($isAdminLogin ? 'Yes' : 'No'));
    
    // 传递admin_login参数
    $state = $isAdminLogin ? 'admin_login=1' : '';
    error_log("Setting state parameter: " . $state);
    
    // Generate auth URL and redirect
    $auth_url = $client->createAuthUrl(['state' => $state]);
    error_log("Generated auth URL: " . $auth_url);
    
    header('Location: ' . $auth_url);
    exit;
}

/**
 * Handle Google OAuth callback
 */
function handleGoogleAuthCallback($user) {
    // Set content type to HTML for redirects
    header('Content-Type: text/html');
    
    // Access global config
    global $config;
    
    error_log("=== Starting Google Auth Callback ===");
    
    // Check for error
    if (isset($_GET['error'])) {
        error_log("Google Auth Error: " . $_GET['error']);
        header('Location: ../admin/login.php?auth_error=' . urlencode('Google authentication was denied: ' . $_GET['error']));
        exit;
    }
    
    // Check for authorization code
    if (!isset($_GET['code'])) {
        error_log("Google Auth Error: No code parameter");
        header('Location: ../admin/login.php?auth_error=' . urlencode('Invalid authentication response: No authorization code received'));
        exit;
    }
    
    error_log("Google Auth Code received: " . substr($_GET['code'], 0, 10) . "...");
    
    // Check if this is an admin login from state parameter
    $isAdminLogin = false;
    
    // Debug state parameter
    error_log("State parameter: " . (isset($_GET['state']) ? $_GET['state'] : 'Not set'));
    
    if (isset($_GET['state']) && !empty($_GET['state'])) {
        // Parse state parameter
        parse_str($_GET['state'], $state_params);
        error_log("Parsed state params: " . print_r($state_params, true));
        $isAdminLogin = isset($state_params['admin_login']) && $state_params['admin_login'] == 1;
    }
    
    // For backward compatibility, also check query parameter
    if (!$isAdminLogin) {
        $isAdminLogin = isset($_GET['admin_login']) && $_GET['admin_login'] == 1;
    }
    
    error_log("Admin login determined: " . ($isAdminLogin ? 'Yes' : 'No'));
    
    try {
        // Load Google API Client library
        error_log("Loading Google client library");
        if (file_exists('../vendor/autoload.php')) {
            require_once '../vendor/autoload.php';
            error_log("Loaded from autoload.php");
        } else {
            require_once '../vendor/google-client.php';
            error_log("Loaded from google-client.php directly");
        }
        
        // Create Google Client
        error_log("Creating Google client and configuring parameters");
        $client = new Google_Client();
        $client->setClientId(GOOGLE_CLIENT_ID);
        $client->setClientSecret(GOOGLE_CLIENT_SECRET);
        $client->setRedirectUri(GOOGLE_REDIRECT_URI);
        
        // Exchange auth code for access token
        error_log("Exchanging auth code for access token");
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        
        // Check for token errors
        if (isset($token['error'])) {
            error_log("Token Error: " . $token['error']);
            $redirect = $isAdminLogin ? '../admin/login.php' : '../admin/login.php';
            header('Location: ' . $redirect . '?auth_error=' . urlencode('Authentication failed: ' . $token['error_description']));
            exit;
        }
        
        // Set access token
        $client->setAccessToken($token);
        
        // Get user profile
        error_log("Getting user profile from Google");
        $google_oauth = new Google_Service_Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();
        
        // Prepare user info
        $user_info = [
            'email' => $google_account_info->email,
            'first_name' => $google_account_info->givenName,
            'last_name' => $google_account_info->familyName,
            'google_id' => $google_account_info->id,
            'avatar' => $google_account_info->picture
        ];
        
        error_log("Google user info: " . print_r($user_info, true));
        
        // Attempt to login
        $login_success = $user->googleLogin($user_info);
        
        error_log("Google login result: " . ($login_success ? 'Success' : 'Failed'));

        if ($login_success) {
            // 无论用户角色如何，如果是从admin页面发起的登录，总是重定向到admin/dashboard.php
            if ($isAdminLogin) {
                error_log("Admin login flow detected, will redirect to admin dashboard");
                
                // Get current user for logging purposes
                $current_user = $user->getCurrentUser();
                
                // 检查用户角色，如果是customer，则重定向到用户仪表板
                if (isset($current_user['role_name']) && $current_user['role_name'] === 'customer') {
                    error_log("User has customer role, redirecting to user dashboard");
                    header('Location: ../user/dashboard.php');
                    exit;
                }
                
                // Even if not an admin role, we still redirect to admin dashboard, which will handle access control
                error_log("Redirecting to admin dashboard regardless of role");
                $user->logAdminActivity('attempted login', 'admin panel', $current_user['id'] ?? 0, 'User attempted to log into admin panel via Google');
                
                // Direct admin redirect - the dashboard will handle access control
                $adminDashboard = $config['site_url'] . "admin/dashboard.php";
                error_log("Admin redirect URL: " . $adminDashboard);
                header("Location: " . $adminDashboard);
                exit;
            } else {
                // Regular user login - redirect to appropriate dashboard based on role
                $current_user = $user->getCurrentUser();
                
                if (isset($current_user['role_name']) && in_array($current_user['role_name'], ['super_admin', 'admin', 'accountant', 'agent'])) {
                    // Admin users go to admin dashboard
                    error_log("Regular login for admin user, redirecting to admin dashboard");
                    header('Location: ../admin/dashboard.php');
                } else {
                    // Regular users go to user dashboard
                    error_log("Regular user login successful, redirecting to user dashboard");
                    header('Location: ../user/dashboard.php');
                }
                exit;
            }
        } else {
            // Login failed
            error_log("Login failed");
            $redirect = $isAdminLogin ? '../admin/login.php' : '../admin/login.php';
            header('Location: ' . $redirect . '?auth_error=' . urlencode('Could not log in with your Google account. Please try again.'));
            exit;
        }
    } catch (Exception $e) {
        error_log("Google Auth Exception: " . $e->getMessage());
        $redirect = $isAdminLogin ? '../admin/login.php' : '../admin/login.php';
        header('Location: ' . $redirect . '?auth_error=' . urlencode('Authentication error: ' . $e->getMessage()));
        exit;
    }
}

/**
 * Handle logout request
 */
function handleLogout($user) {
    $user->logout();
    echo json_encode([
        'success' => true,
        'message' => 'Logout successful',
        'redirect' => '../index.php'
    ]);
}

/**
 * Check authentication status
 */
function checkAuthStatus() {
    global $user;
    
    if ($user->isLoggedIn()) {
        $user_data = $user->getCurrentUser();
        
        echo json_encode([
            'success' => true,
            'authenticated' => true,
            'user' => [
                'id' => $user_data['id'],
                'name' => $user_data['first_name'] . ' ' . $user_data['last_name'],
                'email' => $user_data['email']
            ]
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'authenticated' => false
        ]);
    }
}

/**
 * Handle password change
 */
function handlePasswordChange() {
    global $user;
    
    // Check if user is logged in
    if (!$user->isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'User not authenticated']);
        return;
    }
    
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate input
    if (!isset($data['current_password']) || !isset($data['new_password'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Current password and new password are required']);
        return;
    }
    
    // Validate new password
    if (strlen($data['new_password']) < 8) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'New password must be at least 8 characters']);
        return;
    }
    
    // Attempt password change
    $result = $user->changePassword($data['current_password'], $data['new_password']);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
    }
} 