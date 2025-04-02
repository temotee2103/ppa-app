<?php
/**
 * User class for handling user authentication and management
 */
class User {
    private $db;
    private $user_data = null;
    private static $instance = null;
    
    /**
     * Constructor - initialize database connection
     * Supports both singleton pattern and direct instantiation
     */
    public function __construct($db = null) {
        if ($db !== null) {
            $this->db = $db;
        } else {
            $this->db = Database::getInstance();
        }
    }
    
    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Check remember token for persistent login
     * 
     * @param string $token Remember token from cookie
     * @return bool True if valid token and user logged in, false otherwise
     */
    public function checkRememberToken($token) {
        if (empty($token)) {
            return false;
        }
        
        $sql = "SELECT id, email, first_name, last_name FROM users WHERE remember_token = ? AND status = 'active'";
        $result = $this->db->prepareAndExecute($sql, "s", [$token]);
        
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            
            return true;
        }
        
        // Invalid token, clear cookie
        setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        return false;
    }
    
    /**
     * Register a new user
     * 
     * @param array $user_data User data including email, password, first_name, last_name, phone
     * @return bool|int False on failure, user ID on success
     */
    public function register($user_data) {
        // Validate required fields
        $required_fields = ['email', 'password', 'first_name', 'last_name', 'phone'];
        foreach ($required_fields as $field) {
            if (!isset($user_data[$field]) || empty($user_data[$field])) {
                return false;
            }
        }
        
        // Check if email already exists
        $sql = "SELECT id FROM users WHERE email = ?";
        $result = $this->db->prepareAndExecute($sql, "s", [$user_data['email']]);
        
        if ($result && $result->num_rows > 0) {
            return false; // Email already exists
        }
        
        // Hash the password
        $hashed_password = password_hash($user_data['password'], PASSWORD_DEFAULT);
        
        // Insert new user
        $sql = "INSERT INTO users (email, password, first_name, last_name, phone, address, city, postcode, state, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
        $params = [
            $user_data['email'],
            $hashed_password,
            $user_data['first_name'],
            $user_data['last_name'],
            $user_data['phone'],
            $user_data['address'] ?? '',
            $user_data['city'] ?? '',
            $user_data['postcode'] ?? '',
            $user_data['state'] ?? '',
            'active' // Default status
        ];
        
        $result = $this->db->prepareAndExecute($sql, "ssssssssss", $params);
        
        if ($result) {
            return $this->db->getLastInsertId();
        }
        
        return false;
    }
    
    /**
     * Login a user with email and password
     * 
     * @param string $email User email
     * @param string $password User password
     * @param bool $remember Whether to remember login
     * @param bool $adminCheck Whether to check for admin role
     * @return bool True on success, false on failure
     */
    public function login($email, $password, $remember = false, $adminCheck = false) {
        error_log("Login attempt for email: {$email} - adminCheck: " . ($adminCheck ? 'true' : 'false'));
        
        $sql = "SELECT u.id, u.email, u.password, u.first_name, u.last_name, u.status, u.role_id, r.name as role_name 
                FROM users u
                LEFT JOIN user_roles r ON u.role_id = r.id
                WHERE u.email = ?";
                
        $result = $this->db->prepareAndExecute($sql, "s", [$email]);
        
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            error_log("User found in database: ID {$user['id']}, Role: " . ($user['role_name'] ?? 'undefined'));
            
            // Check if account is active
            if ($user['status'] !== 'active') {
                error_log("Login failed: User account is not active (status: {$user['status']})");
                return false;
            }
            
            // If admin check is enabled, verify user has admin role
            if ($adminCheck && (!isset($user['role_name']) || !in_array($user['role_name'], ['super_admin', 'admin', 'agent', 'accountant']))) {
                error_log("Login failed: Admin check enabled but user has non-admin role: " . ($user['role_name'] ?? 'undefined'));
                return false;
            }
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                error_log("Password verification successful for user ID: {$user['id']}");
                
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                if (isset($user['role_name'])) {
                    $_SESSION['user_role'] = $user['role_name'];
                    error_log("Session set with user_role: {$user['role_name']}");
                } else {
                    error_log("No role set for user in session");
                }
                
                // Set remember me cookie if requested
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $expires = time() + (86400 * 30); // 30 days
                    
                    // Store token in database
                    $sql = "UPDATE users SET remember_token = ? WHERE id = ?";
                    $this->db->prepareAndExecute($sql, "si", [$token, $user['id']]);
                    
                    // Set cookie
                    setcookie('remember_token', $token, $expires, '/', '', false, true);
                    error_log("Remember me token created for user ID: {$user['id']}");
                }
                
                // Update last login time
                $sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
                $this->db->prepareAndExecute($sql, "i", [$user['id']]);
                error_log("Updated last_login time for user ID: {$user['id']}");
                
                error_log("Login successful for user ID: {$user['id']}, Email: {$user['email']}");
                return true;
            } else {
                error_log("Login failed: Password verification failed for email: {$email}");
            }
        } else {
            error_log("Login failed: No user found with email: {$email}");
        }
        
        return false;
    }
    
    /**
     * Google OAuth login/registration
     * 
     * @param array $user_info User information from Google
     * @return bool True on success, false on failure
     */
    public function googleLogin($user_info) {
        // Validate required fields
        if (!isset($user_info['email']) || empty($user_info['email'])) {
            return false;
        }
        
        // Check if user exists by email
        $sql = "SELECT u.id, u.email, u.first_name, u.last_name, u.status, u.google_id, u.role_id, r.name as role_name 
                FROM users u
                LEFT JOIN user_roles r ON u.role_id = r.id
                WHERE u.email = ?";
        $result = $this->db->prepareAndExecute($sql, "s", [$user_info['email']]);
        
        if ($result && $result->num_rows > 0) {
            // User exists, login
            $user = $result->fetch_assoc();
            
            // Check if account is active
            if ($user['status'] !== 'active') {
                return false;
            }
            
            // If user doesn't have a Google ID yet, update it
            if (empty($user['google_id']) && isset($user_info['google_id'])) {
                $sql = "UPDATE users SET google_id = ? WHERE id = ?";
                $this->db->prepareAndExecute($sql, "si", [$user_info['google_id'], $user['id']]);
            }
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['oauth_login'] = 'google';
            
            // Add role to session if exists
            if (isset($user['role_name']) && !empty($user['role_name'])) {
                $_SESSION['user_role'] = $user['role_name'];
            }
            
            // Update last login time
            $sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
            $this->db->prepareAndExecute($sql, "i", [$user['id']]);
            
            return true;
        } else {
            // Register new user
            $user_data = [
                'email' => $user_info['email'],
                'password' => bin2hex(random_bytes(16)), // Random password
                'first_name' => $user_info['first_name'] ?? '',
                'last_name' => $user_info['last_name'] ?? '',
                'phone' => '',
                'google_id' => $user_info['google_id'] ?? '',
                'status' => 'active'
            ];
            
            // Insert new user with Google ID
            $sql = "INSERT INTO users (email, password, first_name, last_name, phone, google_id, status, created_at, last_login) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
                    
            $params = [
                $user_data['email'],
                password_hash($user_data['password'], PASSWORD_DEFAULT),
                $user_data['first_name'],
                $user_data['last_name'],
                $user_data['phone'],
                $user_data['google_id'],
                $user_data['status']
            ];
            
            $result = $this->db->prepareAndExecute($sql, "sssssss", $params);
            
            if ($result) {
                $user_id = $this->db->getLastInsertId();
                
                // Set session variables
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_email'] = $user_data['email'];
                $_SESSION['user_name'] = $user_data['first_name'] . ' ' . $user_data['last_name'];
                $_SESSION['oauth_login'] = 'google';
                
                // New user will not have a role yet
                
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if a user is logged in
     * 
     * @return bool True if logged in, false otherwise
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Get current user data
     * 
     * @param bool $refresh 是否强制从数据库刷新用户数据
     * @return array|null User data or null if not logged in
     */
    public function getCurrentUser($refresh = false) {
        if (!$this->isLoggedIn()) {
            error_log("getCurrentUser - Not logged in");
            return null;
        }
        
        error_log("getCurrentUser - User ID: " . $_SESSION['user_id']);
        
        if ($this->user_data === null || $refresh) {
            error_log("getCurrentUser - Fetching user data from database");
            
            $sql = "SELECT u.id, u.email, u.first_name, u.last_name, u.phone, u.address, u.city, u.postcode, u.state, 
                    u.google_id, u.created_at, u.last_login, u.role_id, u.avatar, r.name as role_name 
                    FROM users u 
                    LEFT JOIN user_roles r ON u.role_id = r.id 
                    WHERE u.id = ?";
            $result = $this->db->prepareAndExecute($sql, "i", [$_SESSION['user_id']]);
            
            if ($result && $result->num_rows > 0) {
                $this->user_data = $result->fetch_assoc();
                
                // 确保google_id字段存在，即使它是NULL
                if (!array_key_exists('google_id', $this->user_data)) {
                    $this->user_data['google_id'] = null;
                }
                
                // 确保role_name字段存在，即使它是NULL
                if (!array_key_exists('role_name', $this->user_data)) {
                    $this->user_data['role_name'] = null;
                }
                
                // 确保avatar字段存在，即使它是NULL
                if (!array_key_exists('avatar', $this->user_data)) {
                    $this->user_data['avatar'] = null;
                }
                
                // 确保last_login字段存在，即使它是NULL
                if (!array_key_exists('last_login', $this->user_data)) {
                    $this->user_data['last_login'] = null;
                }
                
                error_log("getCurrentUser - User data fetched: " . print_r($this->user_data, true));
            } else {
                error_log("getCurrentUser - Failed to fetch user data for ID: " . $_SESSION['user_id']);
            }
        } else {
            error_log("getCurrentUser - Using cached user data");
        }
        
        return $this->user_data;
    }
    
    /**
     * Check if current user has a specific role
     * 
     * @param string|array $roles Role name or array of role names
     * @return bool True if user has any of the specified roles
     */
    public function hasRole($roles) {
        $userData = $this->getCurrentUser();
        
        if (!$userData || empty($userData['role_name'])) {
            error_log("hasRole - No user data or role_name is empty: " . print_r($userData, true));
            return false;
        }
        
        error_log("hasRole - Checking if user role [" . $userData['role_name'] . "] is in " . (is_array($roles) ? implode(',', $roles) : $roles));
        
        if (is_array($roles)) {
            $result = in_array($userData['role_name'], $roles);
            error_log("hasRole - Array check result: " . ($result ? 'true' : 'false'));
            return $result;
        }
        
        $result = $userData['role_name'] === $roles;
        error_log("hasRole - Single role check result: " . ($result ? 'true' : 'false'));
        return $result;
    }
    
    /**
     * Check if current user is admin (any admin role)
     * 
     * @return bool True if user is an admin
     */
    public function isAdmin() {
        return $this->hasRole(['super_admin', 'admin', 'accountant', 'agent']);
    }
    
    /**
     * Check if current user has admin access privileges
     * 
     * @return bool True if user has admin access
     */
    public function hasAdminAccess() {
        error_log("hasAdminAccess - Checking admin access privileges");
        
        if (!$this->isLoggedIn()) {
            error_log("hasAdminAccess - User not logged in");
            return false;
        }
        
        $current_user = $this->getCurrentUser();
        if (!$current_user) {
            error_log("hasAdminAccess - Could not get current user data");
            return false;
        }
        
        $has_access = $this->hasRole(['super_admin', 'admin', 'accountant', 'agent']);
        error_log("hasAdminAccess - User role: " . ($current_user['role_name'] ?? 'undefined') . ", Has access: " . ($has_access ? 'Yes' : 'No'));
        return $has_access;
    }
    
    /**
     * Check if current user is super admin
     * 
     * @return bool True if user is a super admin
     */
    public function isSuperAdmin() {
        return $this->hasRole('super_admin');
    }
    
    /**
     * Get all available user roles
     * 
     * @return array Array of role data
     */
    public function getAllRoles() {
        $sql = "SELECT id, name, description FROM user_roles ORDER BY id";
        $result = $this->db->query($sql);
        
        $roles = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $roles[] = $row;
            }
        }
        
        return $roles;
    }
    
    /**
     * Update user role
     * 
     * @param int $userId User ID
     * @param int $roleId Role ID
     * @return bool True on success, false on failure
     */
    public function updateUserRole($userId, $roleId) {
        // Only super_admin can change roles
        if (!$this->isSuperAdmin()) {
            return false;
        }
        
        $sql = "UPDATE users SET role_id = ? WHERE id = ?";
        $result = $this->db->prepareAndExecute($sql, "ii", [$roleId, $userId]);
        
        return $result !== false;
    }
    
    /**
     * Get all admin users (excluding customers)
     * 
     * @return array Array of admin user data
     */
    public function getAllAdminUsers() {
        $sql = "SELECT u.id, u.email, u.first_name, u.last_name, u.phone, u.status, 
                r.name as role_name, u.created_at 
                FROM users u 
                JOIN user_roles r ON u.role_id = r.id 
                WHERE r.name IN ('super_admin', 'admin', 'accountant', 'agent') 
                ORDER BY u.id";
        $result = $this->db->query($sql);
        
        $users = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }
        
        return $users;
    }
    
    /**
     * Create a new admin user
     * 
     * @param array $userData User data
     * @param int $roleId Role ID
     * @return bool|int False on failure, user ID on success
     */
    public function createAdminUser($userData, $roleId) {
        // Only super_admin can create admin users
        if (!$this->isSuperAdmin()) {
            return false;
        }
        
        // Start with regular registration
        $userId = $this->register($userData);
        
        if ($userId) {
            // Update role
            $this->updateUserRole($userId, $roleId);
            return $userId;
        }
        
        return false;
    }
    
    /**
     * Log admin activity
     * 
     * @param string $action Action performed
     * @param string $entityType Type of entity affected
     * @param int $entityId ID of entity affected
     * @param string $details Additional details
     * @return bool True on success, false on failure
     */
    public function logAdminActivity($action, $entityType, $entityId = null, $details = null) {
        if (!$this->isAdmin()) {
            return false;
        }
        
        $sql = "INSERT INTO admin_activity_log (admin_id, action, entity_type, entity_id, details, ip_address) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        
        $result = $this->db->prepareAndExecute(
            $sql, 
            "ississ", 
            [$_SESSION['user_id'], $action, $entityType, $entityId, $details, $ipAddress]
        );
        
        return $result !== false;
    }
    
    /**
     * Logout the current user
     */
    public function logout() {
        // Remove remember token if exists
        if (isset($_COOKIE['remember_token'])) {
            $sql = "UPDATE users SET remember_token = NULL WHERE id = ?";
            $this->db->prepareAndExecute($sql, "i", [$_SESSION['user_id']]);
            
            // Remove cookie
            setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        }
        
        // Unset session variables
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        unset($_SESSION['oauth_login']);
        
        // Reset user data
        $this->user_data = null;
        
        return true;
    }
    
    /**
     * Update user basic profile
     * 
     * @param array $user_data User data to update
     * @return bool True on success, false on failure
     */
    public function updateBasicProfile($user_data) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        $sql = "UPDATE users SET 
                first_name = ?, 
                last_name = ?, 
                phone = ?, 
                address = ?, 
                city = ?, 
                postcode = ?, 
                state = ? 
                WHERE id = ?";
                
        $params = [
            $user_data['first_name'],
            $user_data['last_name'],
            $user_data['phone'],
            $user_data['address'] ?? '',
            $user_data['city'] ?? '',
            $user_data['postcode'] ?? '',
            $user_data['state'] ?? '',
            $_SESSION['user_id']
        ];
        
        $result = $this->db->prepareAndExecute($sql, "sssssssi", $params);
        
        if ($result) {
            // Update session name
            $_SESSION['user_name'] = $user_data['first_name'] . ' ' . $user_data['last_name'];
            
            // Reset cached user data
            $this->user_data = null;
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Update user profile
     * 
     * @param int $user_id User ID
     * @param string $firstName First name
     * @param string $lastName Last name
     * @param string $email Email
     * @param string $phone Phone number
     * @param string $address Address
     * @param string $city City
     * @param string $state State
     * @param string $postcode Postcode
     * @return bool True on success, false on failure
     */
    public function updateProfile($user_id, $firstName, $lastName, $email, $phone = '', $address = '', $city = '', $state = '', $postcode = '') {
        if (!$this->isLoggedIn() || $_SESSION['user_id'] != $user_id) {
            return false;
        }
        
        // Check if email is being changed and if it already exists
        if ($email != $this->getCurrentUser()['email']) {
            $sql = "SELECT id FROM users WHERE email = ? AND id != ?";
            $result = $this->db->prepareAndExecute($sql, "si", [$email, $user_id]);
            
            if ($result && $result->num_rows > 0) {
                return false; // Email already exists
            }
        }
        
        $sql = "UPDATE users SET 
                email = ?,
                first_name = ?, 
                last_name = ?, 
                phone = ?, 
                address = ?, 
                city = ?, 
                state = ?,
                postcode = ?
                WHERE id = ?";
                
        $params = [
            $email,
            $firstName,
            $lastName,
            $phone,
            $address,
            $city,
            $state,
            $postcode,
            $user_id
        ];
        
        $result = $this->db->prepareAndExecute($sql, "ssssssssi", $params);
        
        if ($result) {
            // Update session variables
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $firstName . ' ' . $lastName;
            
            // Reset cached user data
            $this->user_data = null;
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Change user password
     * 
     * @param string $current_password Current password
     * @param string $new_password New password
     * @return bool True on success, false on failure
     */
    public function changePassword($current_password, $new_password) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        // Get current user password
        $sql = "SELECT password FROM users WHERE id = ?";
        $result = $this->db->prepareAndExecute($sql, "i", [$_SESSION['user_id']]);
        
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Verify current password
            if (password_verify($current_password, $user['password'])) {
                // Hash the new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Update password
                $sql = "UPDATE users SET password = ? WHERE id = ?";
                $result = $this->db->prepareAndExecute($sql, "si", [$hashed_password, $_SESSION['user_id']]);
                
                return $result ? true : false;
            }
        }
        
        return false;
    }
    
    /**
     * 更新用户信息
     * 
     * @param int $userId 用户ID
     * @param array $userData 要更新的用户数据
     * @return bool 更新成功返回true，失败返回false
     */
    public function updateUser($userId, $userData) {
        // 验证必要的数据
        if (empty($userId) || empty($userData)) {
            return false;
        }
        
        // 构建SQL更新语句
        $updateFields = [];
        $params = [];
        $types = '';
        
        // 处理可能的更新字段
        $allowedFields = ['first_name', 'last_name', 'email', 'phone', 'address', 'city', 'postcode', 'state'];
        
        foreach ($allowedFields as $field) {
            if (isset($userData[$field])) {
                $updateFields[] = "$field = ?";
                $params[] = $userData[$field];
                $types .= 's'; // 所有字段都当作字符串处理
            }
        }
        
        // 如果没有要更新的字段，直接返回
        if (empty($updateFields)) {
            return false;
        }
        
        // 组装SQL语句
        $sql = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE id = ?";
        $params[] = $userId;
        $types .= 'i'; // id是整数
        
        // 执行更新
        $result = $this->db->prepareAndExecute($sql, $types, $params);
        
        if ($result) {
            // 更新成功，清除缓存的用户数据，以便下次获取最新数据
            $this->user_data = null;
            return true;
        }
        
        return false;
    }
} 