<?php
/**
 * 网站公共头部
 * 包含导航栏和语言切换器
 */

// 处理语言切换请求
if (isset($_GET['lang'])) {
    $lang = Language::getInstance();
    $lang->setLanguage($_GET['lang']);
}
?>
<!DOCTYPE html>
<html lang="<?php echo getCurrentLanguage(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo $config['site_title']; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo asset('images/ppa-app-logo.png'); ?>">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
    
    <?php if (isset($additional_css) && !empty($additional_css)): ?>
        <?php foreach ($additional_css as $css_file): ?>
        <link rel="stylesheet" href="<?php echo asset('css/' . $css_file); ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        main {
            flex: 1;
        }
        
        .navbar {
            background-color: rgba(255, 255, 255, 0.96);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            padding: 12px 0;
        }
        
        .navbar-brand img {
            max-height: 40px;
        }
        
        .nav-link {
            font-weight: 500;
            color: var(--bs-dark);
            position: relative;
            padding: 0.5rem 0.7rem !important;
            margin: 0 0.1rem;
            font-size: 0.95rem;
            white-space: nowrap;
        }
        
        .nav-link:hover {
            color: var(--bs-primary);
        }
        
        .nav-link.active {
            color: var(--bs-primary);
            font-weight: 600;
        }
        
        .nav-link.active:after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 3px;
            background-color: var(--bs-primary);
            border-radius: 10px;
        }
        
        .language-selector {
            background-color: transparent;
            border: none;
            color: var(--bs-dark);
            font-weight: 500;
        }
        
        .avatar-circle-sm {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: var(--bs-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }
        
        .initials-sm {
            text-transform: uppercase;
        }
        
        .btn-login {
            border-radius: 20px;
            padding: 8px 24px;
            font-weight: 600;
            box-shadow: 0 4px 8px rgba(var(--bs-primary-rgb), 0.2);
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(var(--bs-primary-rgb), 0.3);
        }
        
        /* 用户下拉菜单样式 */
        .user-btn {
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            color: white;
            border-radius: 30px;
            padding: 8px 16px;
            border: none;
            box-shadow: 0 4px 10px rgba(67, 97, 238, 0.25);
            transition: all 0.3s ease;
        }
        
        .user-btn:hover, .user-btn:focus {
            background: linear-gradient(135deg, #3a56e5, #3209a0);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.3);
        }
        
        .user-dropdown-menu {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
            padding: 8px 0;
            min-width: 280px;
            margin-top: 10px;
        }
        
        .user-email-header {
            color: #6c757d;
            font-size: 0.9rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .dropdown-item {
            padding: 8px 16px;
            font-size: 0.95rem;
            border-radius: 5px;
            margin: 0 5px;
            width: calc(100% - 10px);
        }
        
        .dropdown-item:hover {
            background-color: rgba(var(--bs-primary-rgb), 0.08);
        }
        
        .dropdown-item i.fa-fw {
            width: 20px;
            text-align: center;
        }
        
        .dropdown-divider {
            margin: 4px 0;
        }
        
        @media (max-width: 991px) {
            .navbar-collapse {
                background-color: white;
                border-radius: 10px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                padding: 20px;
                margin-top: 15px;
            }
        }
        
        /* 调整导航栏在大屏幕上的布局 */
        @media (min-width: 992px) {
            .navbar-nav {
                margin-left: 0 !important;
                margin-right: 0 !important;
                justify-content: space-between;
                width: auto;
                min-width: 580px;
            }
            
            .navbar > .container {
                display: flex;
                justify-content: space-between;
            }
            
            /* 用户登录区域 */
            .d-flex.align-items-center {
                margin-left: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- 现代化导航栏 -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo url('index.php'); ?>">
                <img src="<?php echo asset('images/ppa-long-blue.png'); ?>" alt="PPA Logo" class="img-fluid">
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link<?php echo $current_page === 'home' ? ' active' : ''; ?>" href="<?php echo url('index.php'); ?>"><?php echo __('nav_home'); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php echo $current_page === 'plans' ? ' active' : ''; ?>" href="<?php echo url('pages/plans.php'); ?>"><?php echo __('nav_plans'); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php echo $current_page === 'how_it_works' ? ' active' : ''; ?>" href="<?php echo url('pages/how-it-works.php'); ?>"><?php echo __('nav_how_it_works'); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php echo $current_page === 'claims' ? ' active' : ''; ?>" href="<?php echo url('pages/claims.php'); ?>"><?php echo __('nav_claims'); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php echo $current_page === 'about' ? ' active' : ''; ?>" href="<?php echo url('pages/about.php'); ?>"><?php echo __('nav_about'); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php echo $current_page === 'faq' ? ' active' : ''; ?>" href="<?php echo url('pages/faq.php'); ?>"><?php echo __('nav_faq'); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php echo $current_page === 'contact' ? ' active' : ''; ?>" href="<?php echo url('pages/contact.php'); ?>"><?php echo __('nav_contact'); ?></a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center">
                    <!-- 语言切换下拉菜单 -->
                    <div class="dropdown me-2">
                        <button class="btn language-selector dropdown-toggle" type="button" id="langDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-globe-asia"></i> <span class="d-none d-lg-inline"><?php echo __('lang_' . getCurrentLanguage()); ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="langDropdown">
                            <?php foreach (Language::getInstance()->getAvailableLanguages() as $code => $name): ?>
                            <li><a class="dropdown-item<?php echo getCurrentLanguage() === $code ? ' active' : ''; ?>" href="<?php echo getLangSwitchUrl($code); ?>"><?php echo $name; ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <!-- 用户登录/菜单 - 始终只显示登录链接，不显示用户信息 -->
                    <?php 
                    // 简化逻辑，不再需要检查当前页面或用户登录状态
                    ?>
                    <a href="<?php echo url('login.php'); ?>" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-1"></i> <?php echo __('btn_login'); ?>
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- 主内容容器 -->
    <main id="main-content"> 