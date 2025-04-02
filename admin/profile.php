<?php
// Admin Profile Page
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
$pageTitle = "My Profile | PPA Admin";
$current_page = 'profile';
$page_title = 'My Profile';
$page_description = 'View and update your profile information';

// 检查是否已登录
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// 获取用户实例和当前用户信息
$user = User::getInstance();
$admin = \Admin\Admin::getInstance();
$currentUser = $user->getCurrentUser(true); // 强制刷新用户数据以获取最新信息

// 处理表单提交 - 更新个人资料
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $newPassword = trim($_POST['new_password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');
    
    // 验证基本字段
    if (empty($firstName) || empty($lastName) || empty($email) || empty($phone)) {
        $message = 'All required fields must be filled';
        $messageType = 'danger';
    } elseif (!empty($newPassword) && $newPassword !== $confirmPassword) {
        $message = 'New password and confirmation do not match';
        $messageType = 'danger';
    } else {
        // 创建更新数据数组
        $updateData = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => $phone
        ];
        
        // 如果提供了新密码，添加到更新数据中
        if (!empty($newPassword)) {
            $updateData['password'] = $newPassword;
        }
        
        // 处理头像上传
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/avatars/';
            
            // 确保上传目录存在
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileInfo = pathinfo($_FILES['avatar']['name']);
            $extension = strtolower($fileInfo['extension']);
            
            // 检查文件类型
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                $message = 'Only JPG, JPEG, PNG or GIF image files are allowed';
                $messageType = 'danger';
            } else {
                // 生成唯一文件名
                $fileName = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;
                $targetFile = $uploadDir . $fileName;
                
                // 移动上传的文件
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFile)) {
                    $updateData['avatar'] = 'uploads/avatars/' . $fileName;
                } else {
                    $message = 'Error uploading avatar';
                    $messageType = 'danger';
                }
            }
        }
        
        // 仅当没有错误消息时才更新用户资料
        if (empty($message)) {
            $result = $admin->updateUser($_SESSION['user_id'], $updateData);
            
            if ($result) {
                $message = 'Profile updated successfully';
                $messageType = 'success';
                
                // 重新获取更新后的用户信息
                $currentUser = $user->getCurrentUser();
            } else {
                $message = 'Error updating profile';
                $messageType = 'danger';
            }
        }
    }
}

// 包含头部文件
require_once 'includes/header.php';
?>

<div class="container-fluid">
    <!-- 页面标题 -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800"><?php echo $page_title; ?></h1>
            <p class="mb-0 text-muted"><?php echo $page_description; ?></p>
        </div>
    </div>
    
    <?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <div class="row">
        <!-- 个人资料信息卡片 -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Profile Information</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <?php if (isset($currentUser['avatar']) && !empty($currentUser['avatar']) && file_exists('../' . $currentUser['avatar'])): ?>
                            <img class="img-profile rounded-circle mx-auto d-block" 
                                 src="<?php echo '../' . $currentUser['avatar'] . '?v=' . time(); ?>" 
                                 alt="User Avatar" style="width: 150px; height: 150px; object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded-circle mx-auto d-block bg-primary text-white d-flex align-items-center justify-content-center" 
                                 style="width: 150px; height: 150px; font-size: 4rem;">
                                <?php echo strtoupper(substr($currentUser['first_name'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <h5 class="mb-1"><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></h5>
                    <p class="text-muted mb-3"><?php echo ucfirst(str_replace('_', ' ', $currentUser['role_name'])); ?></p>
                    
                    <hr>
                    
                    <div class="text-start">
                        <div class="mb-3">
                            <label class="text-muted small mb-1">Email:</label>
                            <p class="mb-0"><?php echo htmlspecialchars($currentUser['email']); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small mb-1">Phone:</label>
                            <p class="mb-0"><?php echo htmlspecialchars($currentUser['phone'] ?? 'Not provided'); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small mb-1">Account Created:</label>
                            <p class="mb-0"><?php echo date('F j, Y', strtotime($currentUser['created_at'])); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small mb-1">Last Login:</label>
                            <p class="mb-0">
                                <?php
                                if (isset($currentUser['last_login']) && !empty($currentUser['last_login'])) {
                                    echo date('F j, Y g:i A', strtotime($currentUser['last_login']));
                                } else {
                                    echo 'Just now';
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 编辑个人资料表单 -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Profile</h6>
                </div>
                <div class="card-body">
                    <form action="profile.php" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       value="<?php echo htmlspecialchars($currentUser['first_name']); ?>" required>
                                <div class="invalid-feedback">Please enter your first name</div>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="<?php echo htmlspecialchars($currentUser['last_name']); ?>" required>
                                <div class="invalid-feedback">Please enter your last name</div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($currentUser['email']); ?>" required>
                                <div class="invalid-feedback">Please enter a valid email</div>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($currentUser['phone'] ?? ''); ?>" required>
                                <div class="invalid-feedback">Please enter your phone number</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="avatar" class="form-label">Profile Picture</label>
                            <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                            <div class="form-text">Upload a profile picture (JPG, PNG or GIF, max 2MB)</div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h5 class="mb-3">Change Password</h5>
                        <p class="text-muted small mb-3">Leave password fields empty if you don't want to change it</p>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="new_password" class="form-label">New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="new_password" name="new_password">
                                    <button class="btn btn-outline-secondary password-toggle" type="button" data-target="#new_password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                    <button class="btn btn-outline-secondary password-toggle" type="button" data-target="#confirm_password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Password toggle functionality
    $('.password-toggle').on('click', function() {
        var targetSelector = $(this).data('target');
        var inputField = $(targetSelector);
        var icon = $(this).find('i');
        
        if (inputField.attr('type') === 'password') {
            inputField.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            inputField.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    
    // Form validation
    (function() {
        'use strict';
        
        var forms = document.querySelectorAll('.needs-validation');
        
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                form.classList.add('was-validated');
            }, false);
        });
    })();
});
</script>

<?php
// 包含底部文件
require_once 'includes/footer.php';
?> 