<?php
require_once '../init.php';
require_once '../classes/Payment.php';

$pageTitle = "My Payments | Customer Portal";
$current_page = 'my-payments';

// Ensure user is logged in
$user = User::getInstance();

if (!$user->isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

// Get current user
$currentUser = $user->getCurrentUser();

// Check if user should be here (only customer role)
if (isset($currentUser['role_name']) && !in_array($currentUser['role_name'], ['customer', ''])) {
    header('Location: ../admin/dashboard.php');
    exit;
}

// Initialize Payment class
$paymentObj = new Payment($db);

// Fetch user's payments
$userPayments = $paymentObj->getUserPayments($currentUser['id']) ?: [];

// Include header
include_once("includes/header.php");
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">My Payments</h1>
    <button class="btn btn-outline-primary px-4" data-bs-toggle="modal" data-bs-target="#filterPaymentsModal">
        <i class="fas fa-filter me-2"></i>Filter
    </button>
</div>

<div class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">Payment Summary</h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="d-flex align-items-center mb-2">
                            <div class="icon-circle bg-primary text-white me-3">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <h6 class="mb-0">Total Payments</h6>
                        </div>
                        <h3 class="fw-bold mt-2">RM <?php echo number_format(array_sum(array_column($userPayments, 'amount')), 2); ?></h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="d-flex align-items-center mb-2">
                            <div class="icon-circle bg-success text-white me-3">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h6 class="mb-0">Active Subscriptions</h6>
                        </div>
                        <h3 class="fw-bold mt-2"><?php echo count(array_filter($userPayments, function($p) { return $p['status'] === 'active'; })); ?></h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="d-flex align-items-center mb-2">
                            <div class="icon-circle bg-info text-white me-3">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <h6 class="mb-0">Next Payment</h6>
                        </div>
                        <?php 
                        $nextPayment = null;
                        $today = new DateTime();
                        
                        foreach ($userPayments as $payment) {
                            if ($payment['status'] === 'active') {
                                $nextDate = new DateTime($payment['next_payment_date']);
                                if ($nextDate > $today && (!$nextPayment || $nextDate < new DateTime($nextPayment['next_payment_date']))) {
                                    $nextPayment = $payment;
                                }
                            }
                        }
                        
                        if ($nextPayment): 
                        ?>
                        <h3 class="fw-bold mt-2">
                            <?php echo date('d M Y', strtotime($nextPayment['next_payment_date'])); ?>
                        </h3>
                        <p class="small text-muted mb-0">RM <?php echo number_format($nextPayment['amount'], 2); ?></p>
                        <?php else: ?>
                        <h3 class="fw-bold mt-2">No upcoming payments</h3>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (empty($userPayments)): ?>
    <div class="alert alert-info p-4 rounded-3 shadow-sm" role="alert">
        <h4 class="alert-heading"><i class="fas fa-info-circle me-2"></i>No Payment History Yet</h4>
        <p class="mb-0">You don't have any payment records yet. Once you subscribe to a protection plan, your payment history will appear here.</p>
    </div>
<?php else: ?>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">Payment History</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col">Reference No.</th>
                            <th scope="col">Date</th>
                            <th scope="col">Description</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Status</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($userPayments as $payment): ?>
                        <tr>
                            <td>
                                <span class="fw-medium"><?php echo htmlspecialchars($payment['reference_no']); ?></span>
                            </td>
                            <td><?php echo date('d M Y', strtotime($payment['payment_date'])); ?></td>
                            <td>
                                <div class="fw-medium"><?php echo htmlspecialchars($payment['description']); ?></div>
                                <div class="small text-muted"><?php echo htmlspecialchars($payment['plan_name'] . ' - ' . $payment['vehicle_reg']); ?></div>
                            </td>
                            <td>
                                <span class="fw-bold">RM <?php echo number_format($payment['amount'], 2); ?></span>
                            </td>
                            <td>
                                <?php 
                                $statusClass = '';
                                switch ($payment['status']) {
                                    case 'completed':
                                        $statusClass = 'bg-success';
                                        break;
                                    case 'pending':
                                        $statusClass = 'bg-warning';
                                        break;
                                    case 'active':
                                        $statusClass = 'bg-primary';
                                        break;
                                    case 'failed':
                                        $statusClass = 'bg-danger';
                                        break;
                                    default:
                                        $statusClass = 'bg-secondary';
                                }
                                ?>
                                <span class="badge <?php echo $statusClass; ?> text-white"><?php echo ucfirst(htmlspecialchars($payment['status'])); ?></span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary view-receipt-btn" data-payment-id="<?php echo $payment['id']; ?>">
                                    <i class="fas fa-file-alt me-1"></i> Receipt
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Filter Payments Modal -->
<div class="modal fade" id="filterPaymentsModal" tabindex="-1" aria-labelledby="filterPaymentsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterPaymentsModalLabel">Filter Payments</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="filterPaymentsForm">
                    <div class="mb-3">
                        <label class="form-label">Date Range</label>
                        <div class="row g-2">
                            <div class="col">
                                <input type="date" class="form-control" id="from_date" name="from_date">
                                <label class="form-text" for="from_date">From</label>
                            </div>
                            <div class="col">
                                <input type="date" class="form-control" id="to_date" name="to_date">
                                <label class="form-text" for="to_date">To</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_status" class="form-label">Payment Status</label>
                        <select class="form-select" id="payment_status" name="payment_status">
                            <option value="">All Statuses</option>
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                            <option value="active">Active</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="plan_filter" class="form-label">Protection Plan</label>
                        <select class="form-select" id="plan_filter" name="plan_filter">
                            <option value="">All Plans</option>
                            <?php 
                            $plans = array_unique(array_column($userPayments, 'plan_name'));
                            foreach ($plans as $plan) {
                                echo '<option value="' . htmlspecialchars($plan) . '">' . htmlspecialchars($plan) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="vehicle_filter" class="form-label">Vehicle</label>
                        <select class="form-select" id="vehicle_filter" name="vehicle_filter">
                            <option value="">All Vehicles</option>
                            <?php 
                            $vehicles = array_unique(array_column($userPayments, 'vehicle_reg'));
                            foreach ($vehicles as $vehicle) {
                                echo '<option value="' . htmlspecialchars($vehicle) . '">' . htmlspecialchars($vehicle) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="applyFiltersBtn">Apply Filters</button>
            </div>
        </div>
    </div>
</div>

<!-- View Receipt Modal -->
<div class="modal fade" id="viewReceiptModal" tabindex="-1" aria-labelledby="viewReceiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewReceiptModalLabel">Payment Receipt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="receiptContent">
                <div class="d-flex justify-content-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="downloadReceiptBtn">
                    <i class="fas fa-download me-1"></i> Download PDF
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.icon-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // View Receipt Button Click
    const viewReceiptBtns = document.querySelectorAll('.view-receipt-btn');
    const receiptContent = document.getElementById('receiptContent');
    const downloadReceiptBtn = document.getElementById('downloadReceiptBtn');
    
    viewReceiptBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const paymentId = this.getAttribute('data-payment-id');
            
            // Show loading spinner
            receiptContent.innerHTML = `
                <div class="d-flex justify-content-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>`;
            
            // Show modal
            const receiptModal = new bootstrap.Modal(document.getElementById('viewReceiptModal'));
            receiptModal.show();
            
            // Fetch receipt data
            fetch(`../api/payments.php?action=get_receipt&payment_id=${paymentId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const receipt = data.receipt;
                        
                        // Format receipt HTML
                        receiptContent.innerHTML = `
                            <div class="receipt-container p-4">
                                <div class="text-center mb-4">
                                    <img src="../assets/images/ppa-logo-white.png" alt="PPA Logo" style="max-height: 60px;">
                                    <h4 class="mt-3">Payment Receipt</h4>
                                    <p class="text-muted">Reference: ${receipt.reference_no}</p>
                                </div>
                                
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold">Payment Details</h6>
                                        <p class="mb-1"><strong>Date:</strong> ${new Date(receipt.payment_date).toLocaleDateString()}</p>
                                        <p class="mb-1"><strong>Amount:</strong> RM ${parseFloat(receipt.amount).toFixed(2)}</p>
                                        <p class="mb-1"><strong>Status:</strong> ${receipt.status}</p>
                                        <p class="mb-1"><strong>Payment Method:</strong> ${receipt.payment_method}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold">Billing Details</h6>
                                        <p class="mb-1"><strong>Name:</strong> ${receipt.customer_name}</p>
                                        <p class="mb-1"><strong>Email:</strong> ${receipt.customer_email}</p>
                                        <p class="mb-1"><strong>Phone:</strong> ${receipt.customer_phone}</p>
                                    </div>
                                </div>
                                
                                <div class="table-responsive mb-4">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Description</th>
                                                <th>Details</th>
                                                <th class="text-end">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>${receipt.description}</td>
                                                <td>${receipt.plan_name} - ${receipt.vehicle_reg}<br>
                                                <small class="text-muted">Period: ${new Date(receipt.period_start).toLocaleDateString()} to ${new Date(receipt.period_end).toLocaleDateString()}</small></td>
                                                <td class="text-end">RM ${parseFloat(receipt.amount).toFixed(2)}</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="2" class="text-end">Total:</th>
                                                <th class="text-end">RM ${parseFloat(receipt.amount).toFixed(2)}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                
                                <div class="border-top pt-3 text-center">
                                    <p class="small text-muted mb-0">Thank you for your business!</p>
                                    <p class="small text-muted mb-0">For any inquiries, please contact support@ppa.com</p>
                                </div>
                            </div>
                        `;
                        
                        // Enable download button
                        downloadReceiptBtn.disabled = false;
                        downloadReceiptBtn.setAttribute('data-payment-id', paymentId);
                    } else {
                        receiptContent.innerHTML = `
                            <div class="alert alert-danger" role="alert">
                                Failed to load receipt. Please try again later.
                            </div>
                        `;
                        
                        // Disable download button
                        downloadReceiptBtn.disabled = true;
                    }
                })
                .catch(error => {
                    console.error('Error fetching receipt:', error);
                    receiptContent.innerHTML = `
                        <div class="alert alert-danger" role="alert">
                            An error occurred while loading the receipt. Please try again later.
                        </div>
                    `;
                    
                    // Disable download button
                    downloadReceiptBtn.disabled = true;
                });
        });
    });
    
    // Download Receipt Button Click
    downloadReceiptBtn.addEventListener('click', function() {
        const paymentId = this.getAttribute('data-payment-id');
        window.location.href = `../api/payments.php?action=download_receipt&payment_id=${paymentId}`;
    });
    
    // Apply Filters Button Click
    const applyFiltersBtn = document.getElementById('applyFiltersBtn');
    applyFiltersBtn.addEventListener('click', function() {
        const filterForm = document.getElementById('filterPaymentsForm');
        const formData = new FormData(filterForm);
        
        // Create query string from form data
        const params = new URLSearchParams();
        for (const [key, value] of formData.entries()) {
            if (value) {
                params.append(key, value);
            }
        }
        
        // Redirect to same page with filters
        window.location.href = `my-payments.php?${params.toString()}`;
    });
});
</script>

<?php
// Include footer
include_once("includes/footer.php");
?> 