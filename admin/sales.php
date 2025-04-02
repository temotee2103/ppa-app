<?php
// Admin Sales and Reports Page
require_once '../init.php';
require_once 'classes/Admin.php';
$pageTitle = "Sales & Reports | PPA Admin";
$current_page = 'sales';
$page_title = 'Sales & Reports';
$page_description = 'Generate financial reports and export sales data.';

// Additional CSS & JS
$additional_css = ['datepicker.css'];
$additional_js = ['datepicker.js'];

// Ensure user has appropriate role
$user = User::getInstance();
$admin = Admin\Admin::getInstance();

if (!$user->hasRole(['super_admin', 'accountant'])) {
    header('Location: dashboard.php');
    exit;
}

// Process export request
$exportSuccess = false;
$exportError = '';
$exportedFile = '';

if (isset($_GET['action']) && $_GET['action'] === 'export') {
    $startDate = $_POST['start_date'] ?? date('Y-m-01'); // First day of current month
    $endDate = $_POST['end_date'] ?? date('Y-m-t'); // Last day of current month
    $format = $_POST['format'] ?? 'pdf';
    
    // Get sales records
    $salesRecords = $admin->getSalesData($startDate, $endDate);
    
    if (!empty($salesRecords)) {
        $exportedFile = $admin->exportSalesData($format, $startDate, $endDate);
        if ($exportedFile) {
            $exportSuccess = true;
            // Log activity
            $user->logAdminActivity('exported', 'sales report', null, "Exported $format report for period $startDate to $endDate");
        } else {
            $exportError = 'Failed to generate export file. Please try again.';
        }
    } else {
        $exportError = 'No sales records found for the selected period.';
    }
}

// Get sales stats
$salesRecords = $admin->getSalesData();

// Calculate totals
$totalSales = 0;
$totalCommissions = 0;
$salesByMonth = [];
$salesByAgent = [];

foreach ($salesRecords as $record) {
    $totalSales += $record['premium_amount'];
    
    // Group by month
    $month = date('M Y', strtotime($record['payment_date'] ?? $record['created_at']));
    if (!isset($salesByMonth[$month])) {
        $salesByMonth[$month] = 0;
    }
    $salesByMonth[$month] += $record['premium_amount'];
    
    // Group by agent
    $agentName = $record['agent_first_name'] ? $record['agent_first_name'] . ' ' . $record['agent_last_name'] : 'Direct';
    if (!isset($salesByAgent[$agentName])) {
        $salesByAgent[$agentName] = 0;
    }
    $salesByAgent[$agentName] += $record['premium_amount'];
}

// Set breadcrumbs
$breadcrumbs = [
    'Sales & Reports' => null
];

// Include header
include_once("includes/header.php");
?>

<!-- Action Buttons -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="admin-card">
            <div class="admin-card-body">
                <h4 class="mb-3">Export Sales Data</h4>
                <p class="text-muted mb-4">Generate downloadable sales reports in Excel or PDF format.</p>
                
                <?php if ($exportSuccess): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Report generated successfully! <a href="<?php echo $exportedFile; ?>" class="alert-link" target="_blank">Download File</a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <?php if ($exportError): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $exportError; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <form action="sales.php?action=export" method="POST" class="admin-form">
                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control datepicker" id="start_date" name="start_date" value="<?php echo date('Y-m-01'); ?>">
                        </div>
                        <div class="col-md-5">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control datepicker" id="end_date" name="end_date" value="<?php echo date('Y-m-t'); ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="format" class="form-label">Format</label>
                            <select class="form-select" id="format" name="format">
                                <option value="pdf">PDF</option>
                                <option value="excel">Excel</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-file-export me-2"></i>Generate Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="admin-card">
            <div class="admin-card-body">
                <h4 class="mb-3">Sales Overview</h4>
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1">Total Sales</p>
                        <h3 class="mb-0">RM <?php echo number_format($totalSales, 2); ?></h3>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Records</p>
                        <h3 class="mb-0"><?php echo count($salesRecords); ?></h3>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Average</p>
                        <h3 class="mb-0">RM <?php echo count($salesRecords) ? number_format($totalSales / count($salesRecords), 2) : '0.00'; ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sales by Month & Agent -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Sales by Month</h5>
            </div>
            <div class="admin-card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th class="text-end">Sales Amount</th>
                                <th class="text-end">% of Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($salesByMonth as $month => $amount): ?>
                            <tr>
                                <td><?php echo $month; ?></td>
                                <td class="text-end">RM <?php echo number_format($amount, 2); ?></td>
                                <td class="text-end">
                                    <?php echo $totalSales ? round(($amount / $totalSales) * 100, 1) : 0; ?>%
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($salesByMonth)): ?>
                            <tr>
                                <td colspan="3" class="text-center">No sales data available</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Sales by Agent</h5>
            </div>
            <div class="admin-card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Agent</th>
                                <th class="text-end">Sales Amount</th>
                                <th class="text-end">Commission</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($salesByAgent as $agent => $amount): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($agent); ?></td>
                                <td class="text-end">RM <?php echo number_format($amount, 2); ?></td>
                                <td class="text-end">
                                    <?php 
                                    $commission = $agent !== 'Direct' ? $amount * 0.05 : 0;
                                    echo 'RM ' . number_format($commission, 2);
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($salesByAgent)): ?>
                            <tr>
                                <td colspan="3" class="text-center">No sales data available</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sales Table -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Sales Records</h5>
                <div class="dropdown no-arrow">
                    <button class="btn btn-link btn-sm dropdown-toggle" type="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink">
                        <li><a class="dropdown-item" href="#">Export as Excel</a></li>
                        <li><a class="dropdown-item" href="#">Export as PDF</a></li>
                    </ul>
                </div>
            </div>
            <div class="admin-card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Policy #</th>
                                <th>Customer</th>
                                <th>Agent</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($salesRecords as $record): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($record['policy_number']); ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle avatar-small me-2">
                                            <?php echo strtoupper(substr($record['first_name'], 0, 1) . substr($record['last_name'], 0, 1)); ?>
                                        </div>
                                        <span><?php echo htmlspecialchars($record['customer_name']); ?></span>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($record['agent_name'] ?: 'Direct'); ?></td>
                                <td><?php echo date('d M Y', strtotime($record['created_at'])); ?></td>
                                <td><?php echo htmlspecialchars($record['type']); ?></td>
                                <td>RM <?php echo number_format($record['premium_amount'], 2); ?></td>
                                <td>
                                    <span class="badge bg-success">Active</span>
                                </td>
                                <td>
                                    <a href="sales.php?receipt=<?php echo $record['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-receipt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($salesRecords)): ?>
                            <tr>
                                <td colspan="8" class="text-center">No sales records found</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (isset($_GET['receipt'])): ?>
<!-- Receipt Modal -->
<div class="modal fade show" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-modal="true" role="dialog" style="display: block; background: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="receiptModalLabel">Receipt #INV-<?php echo rand(1000, 9999); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="window.location = 'sales.php';"></button>
            </div>
            <div class="modal-body">
                <?php
                // Find the receipt in salesRecords
                $receipt = null;
                foreach ($salesRecords as $record) {
                    if ($record['id'] == $_GET['receipt']) {
                        $receipt = $record;
                        break;
                    }
                }
                
                if ($receipt):
                ?>
                <div class="receipt-container">
                    <div class="receipt-header text-center mb-4">
                        <h3>PPA Insurance</h3>
                        <p>Insurance Receipt</p>
                        <p class="text-muted"><?php echo date('F j, Y', strtotime($receipt['created_at'])); ?></p>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Customer</h6>
                            <p><?php echo htmlspecialchars($receipt['customer_name']); ?><br>
                            <?php echo htmlspecialchars($receipt['email'] ?? 'N/A'); ?><br>
                            <?php echo htmlspecialchars($receipt['phone'] ?? 'N/A'); ?></p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h6>Policy Details</h6>
                            <p>Policy #: <?php echo htmlspecialchars($receipt['policy_number']); ?><br>
                            Type: <?php echo htmlspecialchars($receipt['type']); ?><br>
                            Agent: <?php echo htmlspecialchars($receipt['agent_name'] ?: 'Direct'); ?></p>
                        </div>
                    </div>
                    
                    <div class="table-responsive mb-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Policy Period</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo htmlspecialchars($receipt['type']); ?> Insurance</td>
                                    <td><?php echo date('M d, Y', strtotime($receipt['start_date'])); ?> - <?php echo date('M d, Y', strtotime($receipt['end_date'])); ?></td>
                                    <td class="text-end">RM <?php echo number_format($receipt['premium_amount'], 2); ?></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-end">Total:</th>
                                    <th class="text-end">RM <?php echo number_format($receipt['premium_amount'], 2); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <div class="receipt-footer text-center mt-5">
                        <p class="mb-0">Thank you for choosing PPA Insurance!</p>
                        <p class="text-muted">This is an electronically generated receipt. No signature required.</p>
                    </div>
                </div>
                <?php else: ?>
                <div class="text-center py-4">
                    <p>Receipt not found.</p>
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="window.location = 'sales.php';">Close</button>
                <button type="button" class="btn btn-primary" onclick="window.print();">Print Receipt</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
include_once("includes/footer.php");
?> 