<?php
// Admin Analytics Page
require_once '../init.php';
require_once 'classes/Admin.php';
$pageTitle = "Analytics | PPA Admin";
$current_page = 'analytics';
$page_title = 'Analytics Dashboard';
$page_description = 'View comprehensive analytics and performance metrics.';

// Additional CSS & JS
$additional_js = ['chart.min.js'];

// Ensure user has appropriate role
$user = User::getInstance();
$admin = Admin\Admin::getInstance();

if (!$user->hasRole(['super_admin', 'accountant'])) {
    header('Location: dashboard.php');
    exit;
}

// Get analytics data
try {
    $analyticsData = $admin->getAnalyticsData();
} catch (Exception $e) {
    // 如果发生错误，记录日志并创建一个默认的空数据结构
    error_log("Error loading analytics data: " . $e->getMessage());
    $analyticsData = [
        'sales_trend' => ['labels' => [], 'data' => []],
        'traffic_sources' => ['labels' => [], 'data' => []],
        'claims_by_type' => ['labels' => [], 'data' => []],
        'demographics' => ['labels' => [], 'data' => []],
        'performance_metrics' => []
    ];
}

// 提取性能指标，确保有默认值
$metrics = $analyticsData['performance_metrics'] ?? [];
$newCustomers = $metrics['new_customers'] ?? ['value' => 0, 'change' => 0, 'trend' => 'up'];
$conversionRate = $metrics['conversion_rate'] ?? ['value' => 0, 'change' => 0, 'trend' => 'up'];
$retentionRate = $metrics['retention_rate'] ?? ['value' => 0, 'change' => 0, 'trend' => 'up'];
$avgPolicyValue = $metrics['avg_policy_value'] ?? ['value' => 0, 'change' => 0, 'trend' => 'up'];

// Include header
include_once("includes/header.php");
?>

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card-elevated">
            <div class="card-body">
                <h4 class="mb-3">Analytics Overview</h4>
                <p class="text-muted">View detailed analytics and performance metrics for the PPA system.</p>
            </div>
        </div>
    </div>
</div>

<!-- Performance Metrics -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card-elevated h-100 fade-in" style="animation-delay: 0.1s">
            <div class="stats-card">
                <div class="stats-info">
                    <p class="text-secondary mb-2">NEW CUSTOMERS</p>
                    <h3 class="counter"><?php echo $newCustomers['value']; ?></h3>
                    <small class="text-<?php echo $newCustomers['trend'] === 'up' ? 'success' : 'danger'; ?>">
                        <i class="fas fa-arrow-<?php echo $newCustomers['trend']; ?> fa-sm"></i> <?php echo $newCustomers['change']; ?>% <?php echo $newCustomers['trend'] === 'up' ? 'increase' : 'decrease'; ?>
                    </small>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card-elevated h-100 fade-in" style="animation-delay: 0.2s">
            <div class="stats-card">
                <div class="stats-info">
                    <p class="text-secondary mb-2">CONVERSION RATE</p>
                    <h3 class="counter"><?php echo $conversionRate['value']; ?>%</h3>
                    <small class="text-<?php echo $conversionRate['trend'] === 'up' ? 'success' : 'danger'; ?>">
                        <i class="fas fa-arrow-<?php echo $conversionRate['trend']; ?> fa-sm"></i> <?php echo $conversionRate['change']; ?>% <?php echo $conversionRate['trend'] === 'up' ? 'increase' : 'decrease'; ?>
                    </small>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-percentage"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card-elevated h-100 fade-in" style="animation-delay: 0.3s">
            <div class="stats-card">
                <div class="stats-info">
                    <p class="text-secondary mb-2">RETENTION RATE</p>
                    <h3 class="counter"><?php echo $retentionRate['value']; ?>%</h3>
                    <small class="text-<?php echo $retentionRate['trend'] === 'up' ? 'success' : 'danger'; ?>">
                        <i class="fas fa-arrow-<?php echo $retentionRate['trend']; ?> fa-sm"></i> <?php echo $retentionRate['change']; ?>% <?php echo $retentionRate['trend'] === 'up' ? 'increase' : 'decrease'; ?>
                    </small>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-sync-alt"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card-elevated h-100 fade-in" style="animation-delay: 0.4s">
            <div class="stats-card">
                <div class="stats-info">
                    <p class="text-secondary mb-2">AVG. POLICY VALUE</p>
                    <h3 class="counter">RM <?php echo number_format($avgPolicyValue['value']); ?></h3>
                    <small class="text-<?php echo $avgPolicyValue['trend'] === 'up' ? 'success' : 'danger'; ?>">
                        <i class="fas fa-arrow-<?php echo $avgPolicyValue['trend']; ?> fa-sm"></i> <?php echo $avgPolicyValue['change']; ?>% <?php echo $avgPolicyValue['trend'] === 'up' ? 'increase' : 'decrease'; ?>
                    </small>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row">
    <!-- Sales Trend Chart -->
    <div class="col-md-8 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title">Sales Trend</h5>
            </div>
            <div class="card-body">
                <canvas id="salesTrendChart" width="100%" height="50"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Traffic Sources Chart -->
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title">Traffic Sources</h5>
            </div>
            <div class="card-body">
                <canvas id="trafficSourcesChart" width="100%" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Claims by Type -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title">Claims by Type</h5>
            </div>
            <div class="card-body">
                <canvas id="claimsTypeChart" width="100%" height="80"></canvas>
            </div>
        </div>
    </div>
    
    <!-- User Demographics -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title">Customer Demographics</h5>
            </div>
            <div class="card-body">
                <canvas id="demographicsChart" width="100%" height="80"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Charts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 准备从PHP传递的图表数据
    var salesTrendData = <?php echo json_encode($analyticsData['sales_trend'] ?? ['labels' => [], 'data' => []]); ?>;
    var trafficSourcesData = <?php echo json_encode($analyticsData['traffic_sources'] ?? ['labels' => [], 'data' => []]); ?>;
    var claimsTypeData = <?php echo json_encode($analyticsData['claims_by_type'] ?? ['labels' => [], 'data' => []]); ?>;
    var demographicsData = <?php echo json_encode($analyticsData['demographics'] ?? ['labels' => [], 'data' => []]); ?>;
    
    // Sales Trend Chart
    var salesTrendCtx = document.getElementById('salesTrendChart').getContext('2d');
    var salesTrendChart = new Chart(salesTrendCtx, {
        type: 'line',
        data: {
            labels: salesTrendData.labels,
            datasets: [{
                label: 'Sales (RM)',
                data: salesTrendData.data,
                borderColor: '#4361ee',
                backgroundColor: 'rgba(67, 97, 238, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
    
    // Traffic Sources Chart
    var trafficSourcesCtx = document.getElementById('trafficSourcesChart').getContext('2d');
    var trafficSourcesChart = new Chart(trafficSourcesCtx, {
        type: 'doughnut',
        data: {
            labels: trafficSourcesData.labels,
            datasets: [{
                data: trafficSourcesData.data,
                backgroundColor: ['#4361ee', '#3a0ca3', '#7209b7', '#f72585', '#480ca8']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    
    // Claims by Type Chart
    var claimsTypeCtx = document.getElementById('claimsTypeChart').getContext('2d');
    var claimsTypeChart = new Chart(claimsTypeCtx, {
        type: 'bar',
        data: {
            labels: claimsTypeData.labels,
            datasets: [{
                label: 'Claims',
                data: claimsTypeData.data,
                backgroundColor: ['#4cc9f0', '#4895ef', '#4361ee', '#3f37c9', '#3a0ca3']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
    
    // Demographics Chart
    var demographicsCtx = document.getElementById('demographicsChart').getContext('2d');
    var demographicsChart = new Chart(demographicsCtx, {
        type: 'bar',
        data: {
            labels: demographicsData.labels,
            datasets: [{
                label: 'Customers (%)',
                data: demographicsData.data,
                backgroundColor: '#4cc9f0'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>

<?php
// Include footer
include_once("includes/footer.php");
?> 