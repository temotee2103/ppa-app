<?php
/**
 * 公共函数文件
 * 包含语言翻译和页面辅助函数
 */

/**
 * 翻译文本
 * 
 * @param string $key 翻译键
 * @param string $default 默认文本（可选）
 * @return string 翻译后的文本
 */
function __($key, $default = null) {
    $lang = Language::getInstance();
    return $lang->get($key, $default);
}

/**
 * 获取当前语言代码
 * 
 * @return string 当前语言代码
 */
function getCurrentLanguage() {
    $lang = Language::getInstance();
    return $lang->getCurrentLanguage();
}

/**
 * 生成语言切换URL
 * 
 * @param string $lang 目标语言代码
 * @return string 语言切换URL
 */
function getLangSwitchUrl($lang) {
    $current_url = $_SERVER['REQUEST_URI'];
    $url_parts = parse_url($current_url);
    
    $query = [];
    if (isset($url_parts['query'])) {
        parse_str($url_parts['query'], $query);
    }
    
    $query['lang'] = $lang;
    
    $url = $url_parts['path'] . '?' . http_build_query($query);
    return $url;
}

/**
 * 创建资源URL
 * 
 * @param string $path 资源路径
 * @return string 完整的资源URL
 */
function asset($path) {
    global $config;
    return $config['site_url'] . 'assets/' . ltrim($path, '/');
}

/**
 * 创建页面URL
 * 
 * @param string $page 页面名称
 * @param array $params 查询参数（可选）
 * @return string 完整的页面URL
 */
function url($page, $params = []) {
    global $config;
    $url = $config['site_url'] . $page;
    
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    
    return $url;
}

/**
 * 输出带有HTML转义的文本
 * 
 * @param string $text 要输出的文本
 * @return void
 */
function e($text) {
    echo htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * 重定向到指定URL
 * 
 * @param string $url 目标URL
 * @return void
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * 检查用户是否已登录
 * 
 * @return bool 是否已登录
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * 要求用户登录，否则重定向到登录页面
 * 
 * @return void
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        redirect(url('login.php'));
    }
} 