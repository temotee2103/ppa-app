<?php
// 包含必要的类文件
require_once '../classes/Database.php';
require_once '../classes/User.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get current user info
$user = User::getInstance();
$current_user = $user->getCurrentUser();

// Update last login time if it hasn't been updated in the current session
if (!isset($_SESSION['last_login_updated'])) {
    $db = Database::getInstance();
    $db->prepareAndExecute(
        "UPDATE users SET last_login = NOW() WHERE id = ?",
        "i",
        [$_SESSION['user_id']]
    );
    $_SESSION['last_login_updated'] = true;
}

// Check if user has admin privileges
if (!in_array($current_user['role_name'], ['super_admin', 'admin', 'agent', 'accountant'])) {
    header('Location: ../index.php');
    exit();
}

// Include admin class
require_once 'classes/Admin.php';
$admin = Admin\Admin::getInstance();

// Check for notifications - only admins can see pending claims
$notifications = [];
if (in_array($current_user['role_name'], ['super_admin', 'admin'])) {
    // Get pending claims count
    $pendingClaimsCount = 0;
    $claimStats = $admin->getClaimStats();
    if (isset($claimStats['pending'])) {
        $pendingClaimsCount = $claimStats['pending'];
    }
    
    if ($pendingClaimsCount > 0) {
        $notifications[] = [
            'type' => 'claims',
            'count' => $pendingClaimsCount,
            'message' => $pendingClaimsCount . ' pending claims require attention'
        ];
    }
}

// Determine active page
if (!isset($current_page)) {
    $current_page = '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Admin Portal'; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../assets/images/ppa-app-logo.png">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-dark: #3a0ca3;
            --accent-color: #ffd166;
            --sidebar-bg: #14203e;
            --sidebar-text: rgba(255, 255, 255, 0.8);
            --sidebar-icon: rgba(255, 255, 255, 0.6);
            --sidebar-icon-active: #ffffff;
            --text-primary: #232946;
            --text-secondary: #4f5d75;
            --card-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            --card-hover-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        /* Base Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9ff;
            min-height: 100vh;
            color: var(--text-primary);
            margin: 0;
            padding: 0;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Layout */
        .admin-wrapper {
            min-height: 100vh;
            display: flex;
            position: relative;
        }
        
        /* Sidebar */
        .admin-sidebar {
            width: 280px;
            background: var(--sidebar-bg);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1030;
            box-shadow: 5px 0 25px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease-in-out;
        }
        
        /* Content Area */
        .admin-content {
            flex: 1;
            margin-left: 280px;
            transition: all 0.3s ease;
        }
        
        /* Top Navigation Bar */
        .admin-topbar {
            background-color: #ffffff;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            border-radius: 0;
            margin: 0;
            position: sticky;
            top: 0;
            z-index: 1020;
        }
        
        .main-content {
            padding: 25px;
            margin: 20px 25px;
            background: #ffffff;
            border-radius: 16px;
            min-height: calc(100vh - 140px);
            box-shadow: var(--card-shadow);
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .card-elevated {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            border: none;
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .card-elevated:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-hover-shadow);
        }
        
        /* Logo Area */
        .sidebar-logo {
            position: relative;
            padding: 25px 15px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        }
        
        .sidebar-logo img {
            height: 100px;
            margin-bottom: 10px;
            filter: drop-shadow(0 0 8px rgba(255, 255, 255, 0.3));
            transition: all 0.3s ease;
        }
        
        .sidebar-logo img:hover {
            transform: scale(1.05);
        }
        
        .sidebar-logo .logo-text {
            font-weight: 600;
            letter-spacing: 1px;
            margin-top: 5px;
            color: white;
        }
        
        /* Navigation */
        .nav-link {
            color: var(--sidebar-text);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            margin: 8px 15px;
            border-radius: 10px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 3px;
            height: 100%;
            background: var(--accent-color);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }
        
        .nav-link i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            color: var(--sidebar-icon);
            transition: all 0.3s ease;
            font-size: 1.1rem;
        }
        
        .nav-link span {
            position: relative;
            z-index: 1;
        }
        
        .nav-link.active, .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            transform: translateX(5px);
        }
        
        .nav-link.active::before, .nav-link:hover::before {
            transform: scaleY(1);
        }
        
        .nav-link.active i, .nav-link:hover i {
            color: var(--sidebar-icon-active);
        }
        
        .menu-header {
            color: var(--sidebar-text);
            padding: 20px 15px 10px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 600;
        }

        /* Card Styles */
        .card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(67, 97, 238, 0.05);
            transition: all 0.3s ease;
            margin-bottom: 25px;
            overflow: hidden;
            color: var(--text-primary);
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(67, 97, 238, 0.15);
        }
        
        .card-header {
            background: rgba(67, 97, 238, 0.08);
            border-bottom: 1px solid rgba(67, 97, 238, 0.1);
            padding: 15px 20px;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .card-body {
            padding: 20px;
        }
        
        /* Stats Cards */
        .stats-card {
            padding: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stats-info h3 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        .stats-info p {
            color: var(--text-secondary);
            margin: 0;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
        }
        
        /* Border Styles */
        .border-left-primary { 
            border-left: 4px solid var(--primary-color); 
            position: relative;
        }
        
        .border-left-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--primary-color);
        }
        
        .border-left-success { 
            border-left: 4px solid #28a745; 
            position: relative;
        }
        
        .border-left-success::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: #28a745;
        }
        
        .border-left-info { 
            border-left: 4px solid #36b9cc; 
            position: relative;
        }
        
        .border-left-info::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: #36b9cc;
        }
        
        .border-left-warning { 
            border-left: 4px solid #ffc107; 
            position: relative;
        }
        
        .border-left-warning::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: #ffc107;
        }
        
        .text-primary { color: var(--primary-color) !important; }
        .text-accent { color: var(--accent-color) !important; }
        .bg-primary { background-color: var(--primary-color) !important; }
        
        /* Button Styles */
        .btn {
            border-radius: 10px;
            padding: 8px 16px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            font-weight: 500;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            z-index: 0;
        }
        
        .btn:hover::before {
            left: 0;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        .btn-light {
            background: #ffffff;
            color: var(--primary-color);
            border: 1px solid rgba(67, 97, 238, 0.2);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }
        
        .btn-light:hover {
            background: #f8f9ff;
            color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.2);
        }
        
        /* User dropdown styling */
        .dropdown-menu {
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: none;
            padding: 10px;
            min-width: 200px;
        }
        
        .dropdown-item {
            padding: 10px 15px;
            border-radius: 8px;
            transition: all 0.2s ease;
            color: var(--text-primary);
        }
        
        .dropdown-item:hover, .dropdown-item:focus {
            background-color: rgba(67, 97, 238, 0.05);
            color: var(--primary-color);
        }
        
        .dropdown-divider {
            margin: 5px 0;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        /* Notification badge */
        .notification-badge {
            position: absolute;
            top: 0;
            right: 3px;
            padding: 0.25em 0.6em;
            font-size: 0.7rem;
            font-weight: 700;
            border-radius: 50px;
            background: linear-gradient(135deg, #FF6B6B, #FF5E62);
            color: white;
            box-shadow: 0 3px 10px rgba(255, 107, 107, 0.3);
            animation: pulse-notification 2s infinite;
        }
        
        @keyframes pulse-notification {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        /* Table styling */
        .table {
            color: var(--text-primary);
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .table thead th {
            background-color: rgba(67, 97, 238, 0.05);
            border-bottom: none;
            color: var(--primary-color);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 15px;
        }
        
        .table td {
            padding: 15px;
            vertical-align: middle;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.02);
        }
        
        /* Mobile Responsiveness */
        @media (max-width: 992px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }
            
            .admin-sidebar.show {
                transform: translateX(0);
            }
            
            .admin-content {
                margin-left: 0;
            }
            
            .main-content {
                margin: 15px 10px;
                padding: 20px;
            }
        }
        
        /* Animation Classes */
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        
        .slide-in-left {
            animation: slideInLeft 0.5s ease-in-out;
        }
        
        .slide-in-right {
            animation: slideInRight 0.5s ease-in-out;
        }
        
        @keyframes slideInLeft {
            from { transform: translateX(-50px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes slideInRight {
            from { transform: translateX(50px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        /* User avatar */
        .user-avatar {
            position: relative;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            margin-left: 10px;
            background: #f0f0f0;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            border: 2px solid #ffffff;
        }
        
        .status-indicator {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #28a745;
            border: 2px solid #ffffff;
        }
        
        .border-primary { border-color: var(--primary-color) !important; }
        .border-success { border-color: #28a745 !important; }
        .border-info { border-color: #36b9cc !important; }
        .border-warning { border-color: #ffc107 !important; }
        
        .border-start { border-width: 4px !important; }
        
        .shadow-sm {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1) !important;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <div class="admin-sidebar slide-in-left">
            <div class="sidebar-logo">
                <a href="dashboard.php" class="text-white text-decoration-none">
                    <img src="../assets/images/ppa-logo-white.png" alt="PPA Logo" class="img-fluid">
                </a>
            </div>
            
            <ul class="nav flex-column px-3 mt-4">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>" href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <?php if ($user->hasRole('super_admin') || $user->hasRole('admin')): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'customers') ? 'active' : ''; ?>" href="customers.php">
                        <i class="fas fa-users"></i>
                        <span>Customers</span>
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'claims') ? 'active' : ''; ?>" href="claims.php">
                        <i class="fas fa-file-invoice"></i>
                        <span>Claims</span>
                    </a>
                </li>
                <?php if ($user->hasRole('super_admin') || $user->hasRole('admin')): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'workshops') ? 'active' : ''; ?>" href="workshops.php">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Workshops</span>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if ($user->hasRole('super_admin') || $user->hasRole('accountant')): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'sales') ? 'active' : ''; ?>" href="sales.php">
                        <i class="fas fa-chart-line"></i>
                        <span>Sales Report</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'analytics') ? 'active' : ''; ?>" href="analytics.php">
                        <i class="fas fa-chart-pie"></i>
                        <span>Analytics</span>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if ($user->hasRole('super_admin')): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'users') ? 'active' : ''; ?>" href="admin-users.php">
                        <i class="fas fa-user-shield"></i>
                        <span>Admin Users</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            
            <!-- Decorative Elements -->
            <div class="position-absolute bottom-0 start-0 w-100 p-3 text-center text-white-50 small">
                <p class="mb-0">PPA Protection &copy; <?php echo date('Y'); ?></p>
            </div>
        </div>
        
        <!-- Content Wrapper -->
        <div class="admin-content">
            <!-- Top Navigation -->
            <nav class="admin-topbar mb-4 fade-in">
                <button id="sidebarToggleTop" class="btn btn-light d-md-none">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="d-flex align-items-center ms-auto">
                    <?php if (!empty($notifications)): ?>
                    <div class="me-4 position-relative">
                        <a href="#" role="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false" class="text-secondary position-relative">
                            <i class="fas fa-bell fa-fw fs-5"></i>
                            <span class="notification-badge"><?php echo count($notifications); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
                            <?php foreach ($notifications as $notification): ?>
                            <li><a class="dropdown-item" href="claims.php?filter=pending"><i class="fas fa-exclamation-circle me-2 text-warning"></i> <?php echo htmlspecialchars($notification['message']); ?></a></li>
                            <?php endforeach; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-center small" href="claims.php?filter=pending">View All Notifications</a></li>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <div class="dropdown">
                        <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <div>
                                <span class="d-none d-lg-inline me-2 fw-medium"><?php echo htmlspecialchars($current_user['first_name'] . ' ' . $current_user['last_name']); ?></span>
                                <div class="d-lg-none d-inline-block small text-muted"><?php echo ucfirst(str_replace('_', ' ', $current_user['role_name'])); ?></div>
                            </div>
                            <div class="user-avatar">
                                <?php if (isset($current_user['avatar']) && !empty($current_user['avatar'])): ?>
                                <img src="<?php echo file_exists('../' . $current_user['avatar']) ? '../' . $current_user['avatar'] : 'assets/images/default-avatar.png'; ?>?v=<?php echo time(); ?>" width="100%" height="100%" alt="User Avatar">
                                <?php else: ?>
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 100%; height: 100%;">
                                    <?php echo strtoupper(substr($current_user['first_name'], 0, 1)); ?>
                                </div>
                                <?php endif; ?>
                                <span class="status-indicator"></span>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-circle fa-sm fa-fw me-2 text-primary"></i> Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-primary"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            
            <!-- Page Content -->
            <div class="main-content">
            <!-- Content inserted from each page begins here -->