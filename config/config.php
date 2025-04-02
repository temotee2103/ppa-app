<?php
/**
 * 主配置文件
 * 包含数据库连接、网站设置和语言配置
 */

// 数据库配置
$config['db_host'] = 'localhost';
$config['db_user'] = 'root';
$config['db_pass'] = '';
$config['db_name'] = 'ppa_app';

// 网站设置
$config['site_title'] = 'Malaysia\'s 1st Additional Car Protection';
$config['site_url'] = 'http://localhost/ppa-app/';

// 语言设置
$config['default_lang'] = 'en'; // 默认语言
$config['available_langs'] = [
    'en' => 'English',
    'my' => 'Bahasa Melayu',
    'zh' => '中文'
];

// 路径配置
$config['base_path'] = __DIR__ . '/../';
$config['includes_path'] = $config['base_path'] . 'includes/';
$config['languages_path'] = $config['base_path'] . 'languages/';
$config['pages_path'] = $config['base_path'] . 'pages/';

// Google OAuth 配置
define('GOOGLE_CLIENT_ID', '1072070928309-h2mj3u9l5qo39pgubm0vb4qtn90ge4ka.apps.googleusercontent.com'); // 替换为你的Google Client ID
define('GOOGLE_CLIENT_SECRET', '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'); // 替换为你的Google Client Secret
define('GOOGLE_REDIRECT_URI', 'http://localhost/ppa-app/api/auth.php'); // 简化重定向URI，移除查询参数

// 会话配置
// 不需要在这里启动会话，因为已经在init.php中启动了

// 如果未设置语言，则使用默认语言
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = $config['default_lang'];
}

// 使配置在全局范围内可用
global $config; 