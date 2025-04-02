<?php
/**
 * 项目初始化文件
 * 用于引入所有必要的类和配置
 */

// 确保会话已启动
if (session_status() === PHP_SESSION_NONE) {
    error_log("Init - Starting new session");
    session_start();
} else {
    error_log("Init - Session already started");
}

// 记录会话信息
if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
    error_log("Init - Session contains user_id: " . $_SESSION['user_id'] . " and role: " . $_SESSION['user_role']);
} else {
    error_log("Init - Session missing user_id or user_role");
}

// 引入配置文件
require_once __DIR__ . '/config/config.php';

// 引入类文件
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/Language.php';
require_once __DIR__ . '/classes/User.php';
require_once __DIR__ . '/classes/Vehicle.php';
require_once __DIR__ . '/classes/Claim.php';
require_once __DIR__ . '/classes/Admin.php';
require_once __DIR__ . '/classes/Plan.php';
require_once __DIR__ . '/classes/Workshop.php';

// 引入公共函数
require_once __DIR__ . '/includes/functions.php';

// 初始化数据库连接
$db = Database::getInstance();

// 初始化语言
$lang = Language::getInstance();

// 如果有语言切换请求
if (isset($_GET['lang'])) {
    $lang->setLanguage($_GET['lang']);
}

// 检查记住我功能的cookie
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $user = User::getInstance();
    $user->checkRememberToken($_COOKIE['remember_token']);
}

// 全局错误处理
function handleError($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        // 如果错误级别未在当前环境中启用，则不处理
        return false;
    }

    // 记录错误
    error_log("Error [$errno]: $errstr in $errfile on line $errline");
    
    // 对于严重错误，显示友好错误页面
    if ($errno == E_USER_ERROR) {
        echo '<div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin: 10px; border-radius: 5px;">';
        echo '<strong>Application Error:</strong> The application encountered an unexpected error. Please try again later.';
        echo '</div>';
        exit(1);
    }
    
    // 允许PHP内部错误处理器继续执行
    return false;
}

// 设置错误处理器
set_error_handler('handleError'); 