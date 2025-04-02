<?php
// Admin Dashboard Page
require_once '../init.php';
require_once 'classes/Admin.php';
$pageTitle = "Dashboard | PPA Admin";
$current_page = 'dashboard';
$page_title = 'Dashboard';
$page_description = 'Overview of statistics, activities, and key metrics.';

// Debug session info
error_log("Dashboard - Session user_id: " . ($_SESSION['user_id'] ?? 'not set'));
error_log("Dashboard - Session user_role: " . ($_SESSION['user_role'] ?? 'not set'));

// Ensure user is logged in and has admin privileges
$user = User::getInstance();
$admin = Admin\Admin::getInstance();

// Debug user info
$current_user = $user->getCurrentUser();
error_log("Dashboard - User role from getCurrentUser: " . ($current_user['role_name'] ?? 'undefined'));
error_log("Dashboard - User has admin access: " . ($user->hasAdminAccess() ? 'Yes' : 'No'));
error_log("Dashboard - User is super_admin: " . ($user->hasRole('super_admin') ? 'Yes' : 'No'));
error_log("Dashboard - User is admin: " . ($user->hasRole('admin') ? 'Yes' : 'No'));
error_log("Dashboard - User is agent: " . ($user->hasRole('agent') ? 'Yes' : 'No'));

if (!$user->isLoggedIn() || !$user->hasAdminAccess()) {
    error_log("Dashboard - Access denied, redirecting to login page");
    header('Location: login.php');
    exit;
}

// Get dashboard statistics
$stats = $admin->getDashboardStats();

// Get recent admin activities
$adminActivities = $admin->getRecentActivities(5);

// Include header
include_once("includes/header.php");
?>

<!-- Welcome Section -->
<div class="card-elevated mb-4 fade-in">
    <div class="card-body position-relative overflow-hidden">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h4 class="text-primary fw-bold mb-3">Welcome back, <?php echo htmlspecialchars($current_user['first_name']); ?>!</h4>
                <p class="text-secondary mb-4">Manage customers, claims, workshops, and view comprehensive reports all in one place. Your role: <span class="badge bg-primary"><?php echo ucfirst(str_replace('_', ' ', $current_user['role_name'])); ?></span></p>
                <div class="d-flex flex-wrap gap-2">
                    <?php if ($user->hasRole('super_admin') || $user->hasRole('admin')): ?>
                    <a href="claims.php?filter=pending" class="btn btn-primary">
                        <i class="fas fa-clipboard-list me-2"></i>Review Claims
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($user->hasRole('super_admin') || $user->hasRole('admin')): ?>
                    <a href="workshops.php" class="btn btn-light">
                        <i class="fas fa-calendar-alt me-2"></i>Manage Workshops
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-4 text-center mt-4 mt-lg-0">
                <div class="welcome-illustration">
                    <i class="fas fa-user-shield fa-5x text-primary mb-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-4 mb-4">
    <!-- Customers Card -->
    <div class="col-xl-3 col-md-6">
        <div class="card-elevated h-100 fade-in" style="animation-delay: 0.1s">
            <div class="stats-card">
                <div class="stats-info">
                    <p class="text-secondary mb-2">TOTAL CUSTOMERS</p>
                    <h3 class="counter"><?php echo number_format($stats['customers']); ?></h3>
                    <small class="text-success">
                        <i class="fas fa-arrow-up fa-sm"></i> <?php echo $stats['new_customers']; ?> new this month
                    </small>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Claims Card -->
    <div class="col-xl-3 col-md-6">
        <div class="card-elevated h-100 fade-in" style="animation-delay: 0.2s">
            <div class="stats-card">
                <div class="stats-info">
                    <p class="text-secondary mb-2">TOTAL CLAIMS</p>
                    <h3 class="counter"><?php echo number_format($stats['claims']); ?></h3>
                    <small class="text-warning">
                        <i class="fas fa-exclamation-circle fa-sm"></i> <?php echo $stats['pending_claims']; ?> pending
                    </small>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-file-invoice"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Workshops Card -->
    <div class="col-xl-3 col-md-6">
        <div class="card-elevated h-100 fade-in" style="animation-delay: 0.3s">
            <div class="stats-card">
                <div class="stats-info">
                    <p class="text-secondary mb-2">WORKSHOPS</p>
                    <h3 class="counter"><?php echo number_format($stats['workshops']); ?></h3>
                    <small class="text-info">
                        <i class="fas fa-calendar fa-sm"></i> <?php echo $stats['upcoming_workshops']; ?> upcoming
                    </small>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Card -->
    <div class="col-xl-3 col-md-6">
        <div class="card-elevated h-100 fade-in" style="animation-delay: 0.4s">
            <div class="stats-card">
                <div class="stats-info">
                    <p class="text-secondary mb-2">TOTAL REVENUE</p>
                    <h3 class="counter">RM <?php echo number_format($stats['revenue'], 2); ?></h3>
                    <small class="text-success">
                        <i class="fas fa-file-invoice-dollar fa-sm"></i> From policies & workshops
                    </small>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities & Quick Actions -->
<div class="row">
    <!-- Recent Activities -->
    <div class="col-xl-8 mb-4">
        <div class="card-elevated h-100 fade-in" style="animation-delay: 0.5s">
            <div class="card-header d-flex justify-content-between align-items-center p-4">
                <h5 class="card-title mb-0">Recent Activities</h5>
                <a href="activity-log.php" class="btn btn-sm btn-light">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Activity</th>
                                <th>User</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($adminActivities as $activity): ?>
                            <tr>
                                <td>
                                    <i class="fas fa-<?php echo $activity['icon']; ?> text-<?php echo $activity['color']; ?> me-2"></i>
                                    <?php echo htmlspecialchars($activity['description']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($activity['user_name']); ?></td>
                                <td><?php echo $activity['time_ago']; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $activity['status_color']; ?>">
                                        <?php echo htmlspecialchars($activity['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-xl-4 mb-4">
        <div class="card-elevated h-100 fade-in" style="animation-delay: 0.6s">
            <div class="card-header p-4">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    <?php if ($user->hasRole('super_admin') || $user->hasRole('admin')): ?>
                    <a href="customers.php?action=new" class="btn btn-light text-start">
                        <i class="fas fa-user-plus text-primary me-2"></i>Add New Customer
                    </a>
                    <a href="claims.php?action=new" class="btn btn-light text-start">
                        <i class="fas fa-file-medical text-warning me-2"></i>Create New Claim
                    </a>
                    <a href="workshops.php?action=new" class="btn btn-light text-start">
                        <i class="fas fa-calendar-plus text-info me-2"></i>Schedule Workshop
                    </a>
                    <?php endif; ?>
                    <?php if ($user->hasRole('super_admin') || $user->hasRole('accountant')): ?>
                    <a href="reports/generate.php" class="btn btn-light text-start">
                        <i class="fas fa-chart-bar text-success me-2"></i>Generate Report
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once("includes/footer.php");
?> 