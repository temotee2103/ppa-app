<?php
// Admin Users Management Page
// 启用错误显示
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 开启会话（如果还没有开启）
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 包含必要的文件
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once 'classes/Admin.php';

// 设置页面标题和当前页面标识
$pageTitle = "Admin Users | PPA Admin";
$current_page = 'users';
$page_title = 'Admin Users Management';
$page_description = 'Manage admin users and their roles in the system.';

// 确保用户有适当的角色
$user = User::getInstance();
$admin = Admin\Admin::getInstance();

// 检查是否已登录
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// 检查用户是否有super_admin权限
if (!$user->hasRole('super_admin')) {
    header('Location: dashboard.php');
    exit;
}

// 处理表单提交
$success = '';
$error = '';

// 添加新管理员
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $firstName = $_POST['first_name'] ?? '';
    $lastName = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    $roleId = $_POST['role_id'] ?? '';
    
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($roleId)) {
        $error = 'All fields are required.';
    } else {
        $userData = [
            'email' => $email,
            'password' => $password,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $phone
        ];
        
        $result = $user->createAdminUser($userData, $roleId);
        
        if ($result) {
            $success = 'Admin user created successfully.';
            $user->logAdminActivity('created', 'admin user', $result, "Added new admin user with role ID $roleId");
        } else {
            $error = 'Failed to create admin user. Email may already be in use.';
        }
    }
}

// 更新用户角色
if (isset($_POST['action']) && $_POST['action'] === 'update_role') {
    $userId = $_POST['user_id'] ?? '';
    $roleId = $_POST['role_id'] ?? '';
    
    if (empty($userId) || empty($roleId)) {
        $error = 'User ID and Role ID are required.';
    } else {
        $result = $user->updateUserRole($userId, $roleId);
        
        if ($result) {
            $success = 'User role updated successfully.';
            $user->logAdminActivity('updated', 'user role', $userId, "Changed role to ID $roleId");
        } else {
            $error = 'Failed to update user role.';
        }
    }
}

// 获取所有管理员用户
$adminUsers = $user->getAllAdminUsers();

// 获取所有角色
$roles = $user->getAllRoles();

// 设置面包屑
$breadcrumbs = [
    'Admin Users' => null
];

// 包含头部文件
require_once 'includes/header.php';
?>

<!-- Page Title -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800"><?php echo $page_title; ?></h1>
        <p class="mb-0 text-muted"><?php echo $page_description; ?></p>
    </div>
</div>

<!-- Action Buttons -->
<div class="row mb-4">
    <div class="col-md-6">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAdminModal">
            <i class="fas fa-user-plus me-2"></i>Add New Admin User
        </button>
    </div>
</div>

<!-- Success/Error Messages -->
<?php if (!empty($success)): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo $success; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php if (!empty($error)): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?php echo $error; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<!-- Admin Users Table -->
<div class="admin-card">
    <div class="admin-card-header">
        <h5 class="admin-card-title">Admin Users</h5>
    </div>
    <div class="admin-card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle admin-datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($adminUsers as $adminUser): ?>
                    <tr>
                        <td><?php echo $adminUser['id']; ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle avatar-small me-2">
                                    <span class="initials"><?php echo substr($adminUser['first_name'], 0, 1) . substr($adminUser['last_name'], 0, 1); ?></span>
                                </div>
                                <div>
                                    <?php echo htmlspecialchars($adminUser['first_name'] . ' ' . $adminUser['last_name']); ?>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($adminUser['email']); ?></td>
                        <td><?php echo htmlspecialchars($adminUser['phone']); ?></td>
                        <td>
                            <span class="badge bg-primary">
                                <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $adminUser['role_name']))); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($adminUser['status'] === 'active'): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('Y-m-d', strtotime($adminUser['created_at'])); ?></td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Actions
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewUserModal<?php echo $adminUser['id']; ?>">
                                            <i class="fas fa-eye me-2"></i>View Details
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editRoleModal<?php echo $adminUser['id']; ?>">
                                            <i class="fas fa-user-tag me-2"></i>Change Role
                                        </a>
                                    </li>
                                    <?php if ($adminUser['status'] === 'active'): ?>
                                    <li>
                                        <a class="dropdown-item text-danger" href="#" data-confirm="Are you sure you want to deactivate this user?">
                                            <i class="fas fa-user-slash me-2"></i>Deactivate
                                        </a>
                                    </li>
                                    <?php else: ?>
                                    <li>
                                        <a class="dropdown-item text-success" href="#">
                                            <i class="fas fa-user-check me-2"></i>Activate
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($adminUsers)): ?>
                    <tr>
                        <td colspan="8" class="text-center">No admin users found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Admin Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAdminModalLabel">Add New Admin User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" class="admin-form needs-validation" novalidate>
                <input type="hidden" name="action" value="add">
                
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                            <div class="invalid-feedback">Please enter a first name.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                            <div class="invalid-feedback">Please enter a last name.</div>
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                            <div class="invalid-feedback">Please enter a phone number.</div>
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button class="btn btn-outline-secondary password-toggle" type="button" data-target="#password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">Please enter a password.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="role_id" class="form-label">Role</label>
                            <select class="form-select" id="role_id" name="role_id" required>
                                <option value="">Select role</option>
                                <?php foreach ($roles as $role): ?>
                                    <?php if ($role['name'] !== 'customer'): ?>
                                    <option value="<?php echo $role['id']; ?>">
                                        <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $role['name']))); ?>
                                    </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a role.</div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Select appropriate role for this user:
                            <ul class="mt-2">
                                <li><strong>Super Admin:</strong> Full access to all system features including data modification and deletion</li>
                                <li><strong>Admin:</strong> Access to daily management functions without ability to modify critical data</li>
                                <li><strong>Accountant:</strong> Access to financial records and ability to export sales data</li>
                                <li><strong>Agent:</strong> Sales agent with customer management and commission tracking</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- User Details Modal -->
<?php foreach ($adminUsers as $adminUser): ?>
<div class="modal fade" id="viewUserModal<?php echo $adminUser['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="avatar-circle mx-auto" style="width: 80px; height: 80px; font-size: 32px;">
                        <span class="initials"><?php echo substr($adminUser['first_name'], 0, 1) . substr($adminUser['last_name'], 0, 1); ?></span>
                    </div>
                    <h4 class="mt-3"><?php echo htmlspecialchars($adminUser['first_name'] . ' ' . $adminUser['last_name']); ?></h4>
                    <span class="badge bg-primary">
                        <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $adminUser['role_name']))); ?>
                    </span>
                </div>
                
                <div class="mb-3">
                    <h6>Contact Information</h6>
                    <p>
                        <i class="fas fa-envelope me-2"></i> <?php echo htmlspecialchars($adminUser['email']); ?><br>
                        <i class="fas fa-phone me-2"></i> <?php echo htmlspecialchars($adminUser['phone']); ?>
                    </p>
                </div>
                
                <div class="mb-3">
                    <h6>Account Status</h6>
                    <p>
                        <i class="fas fa-user-shield me-2"></i> 
                        <?php if ($adminUser['status'] === 'active'): ?>
                            <span class="text-success">Active</span>
                        <?php else: ?>
                            <span class="text-danger">Inactive</span>
                        <?php endif; ?>
                        <br>
                        <i class="fas fa-calendar-alt me-2"></i> 
                        Created: <?php echo date('F j, Y', strtotime($adminUser['created_at'])); ?>
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- Edit Role Modal -->
<?php foreach ($adminUsers as $adminUser): ?>
<div class="modal fade" id="editRoleModal<?php echo $adminUser['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change User Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST">
                <input type="hidden" name="action" value="update_role">
                <input type="hidden" name="user_id" value="<?php echo $adminUser['id']; ?>">
                
                <div class="modal-body">
                    <p>Current user: <strong><?php echo htmlspecialchars($adminUser['first_name'] . ' ' . $adminUser['last_name']); ?></strong></p>
                    <p>Current role: <strong><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $adminUser['role_name']))); ?></strong></p>
                    
                    <div class="mb-3">
                        <label for="role_id_<?php echo $adminUser['id']; ?>" class="form-label">New Role</label>
                        <select class="form-select" id="role_id_<?php echo $adminUser['id']; ?>" name="role_id" required>
                            <option value="">Select role</option>
                            <?php foreach ($roles as $role): ?>
                                <?php if ($role['name'] !== 'customer'): ?>
                                <option value="<?php echo $role['id']; ?>" <?php echo ($role['name'] === $adminUser['role_name']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $role['name']))); ?>
                                </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Changing a user's role will modify their access permissions in the system.
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Role</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php
include_once("includes/footer.php");
?> 