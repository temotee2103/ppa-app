<?php
// 处理头像上传
require_once '../../init.php';

// 设置响应头为JSON
header('Content-Type: application/json');

// 调试模式 - 启用错误输出
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 确保用户已登录
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// 确保是POST请求并上传了文件
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['avatar']) || $_FILES['avatar']['error'] == UPLOAD_ERR_NO_FILE) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
    exit;
}

// 获取用户信息
$user = User::getInstance();
$currentUser = $user->getCurrentUser();
$userId = $currentUser['id'];

// 验证文件类型
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
$uploadedFileType = $_FILES['avatar']['type'];

if (!in_array($uploadedFileType, $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG and GIF are allowed.']);
    exit;
}

// 验证文件大小 (2MB 限制)
$maxFileSize = 2 * 1024 * 1024; // 2MB in bytes
if ($_FILES['avatar']['size'] > $maxFileSize) {
    echo json_encode(['success' => false, 'message' => 'File size exceeds the 2MB limit.']);
    exit;
}

// 创建上传目录(如果不存在)
$uploadDir = '../../uploads/avatars/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// 生成唯一文件名
$filename = 'user_' . $userId . '_' . time() . '_' . uniqid();
$extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
$targetFile = $uploadDir . $filename . '.' . $extension;
$avatarPath = 'uploads/avatars/' . $filename . '.' . $extension;

// 记录调试信息
error_log("Avatar upload - User ID: $userId, File: $targetFile");

// 尝试移动上传的文件
if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFile)) {
    // 更新用户头像路径在数据库中
    try {
        $db = Database::getInstance();
        
        // 检查users表是否有avatar列
        $checkColumnSql = "SHOW COLUMNS FROM users LIKE 'avatar'";
        $columnResult = $db->query($checkColumnSql);
        
        if ($columnResult->num_rows == 0) {
            // avatar列不存在
            error_log("Avatar column does not exist in users table");
            echo json_encode(['success' => false, 'message' => 'Avatar column does not exist in database']);
            unlink($targetFile); // 删除已上传的文件
            exit;
        }
        
        $sql = "UPDATE users SET avatar = ? WHERE id = ?";
        error_log("SQL Query: $sql with params [$avatarPath, $userId]");
        
        // 直接使用Database类的prepareAndExecute方法，但检查返回值
        $result = $db->prepareAndExecute($sql, "si", [$avatarPath, $userId]);
        
        // 对于UPDATE语句，prepareAndExecute返回的不是结果集，而是布尔值
        // 检查是否执行成功
        if ($result !== false) {
            // 成功
            echo json_encode([
                'success' => true, 
                'message' => 'Avatar updated successfully',
                'avatar_url' => '../' . $avatarPath,
                'debug_info' => 'User ID: ' . $userId . ', Avatar Path: ' . $avatarPath
            ]);
            exit;
        } else {
            // 数据库更新失败
            $error = $db->getError();
            error_log("Database update failed: " . $error);
            unlink($targetFile); // 删除已上传的文件
            echo json_encode([
                'success' => false, 
                'message' => 'Failed to update avatar in database: ' . $error,
                'debug_info' => 'User ID: ' . $userId . ', Avatar Path: ' . $avatarPath
            ]);
            exit;
        }
    } catch (Exception $e) {
        // 捕获异常
        error_log("Database exception: " . $e->getMessage());
        unlink($targetFile); // 删除已上传的文件
        echo json_encode([
            'success' => false, 
            'message' => 'Database error: ' . $e->getMessage(),
            'debug_info' => 'User ID: ' . $userId . ', Avatar Path: ' . $avatarPath
        ]);
        exit;
    }
} else {
    // 文件上传失败
    $uploadError = $_FILES['avatar']['error'];
    error_log("File upload failed with error code: $uploadError");
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to upload file. Error code: ' . $uploadError,
        'debug_info' => print_r($_FILES, true)
    ]);
    exit;
} 