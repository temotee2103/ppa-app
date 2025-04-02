<?php
require_once '../init.php';

$pageTitle = "Policy Renewals | Customer Portal";
$current_page = 'my-renewals';

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

// Initialize Plan and Vehicle classes
$planObj = new Plan($db);
$vehicleObj = new Vehicle($db);

// Fetch user's plans
$userPlans = $planObj->getUserPlans($currentUser['id']) ?: [];

// Filter plans by status
$activePlans = array_filter($userPlans, function($plan) {
    return $plan['status'] === 'active';
});

$expiredPlans = array_filter($userPlans, function($plan) {
    return $plan['status'] === 'expired';
});

$renewablePlans = array_filter($activePlans, function($plan) {
    // Plans within 30 days of expiry are considered renewable
    $expiryDate = new DateTime($plan['expiry_date']);
    $now = new DateTime();
    $diff = $now->diff($expiryDate);
    return $diff->days <= 30 && $expiryDate > $now;
});

// Include header
include_once("includes/header.php");
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Policy Renewals</h1>
</div>

<!-- Renewable Plans Section -->
<div class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">Plans Due for Renewal</h5>
        </div>
        <div class="card-body">
            <?php if (empty($renewablePlans)): ?>
                <div class="alert alert-info" role="alert">
                    <h5><i class="fas fa-info-circle me-2"></i> No Upcoming Renewals</h5>
                    <p class="mb-0">You don't have any plans due for renewal in the next 30 days.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($renewablePlans as $plan): ?>
                        <?php 
                        $expiryDate = new DateTime($plan['expiry_date']);
                        $now = new DateTime();
                        $daysLeft = $now->diff($expiryDate)->days;
                        $urgencyClass = $daysLeft <= 7 ? 'text-danger' : ($daysLeft <= 14 ? 'text-warning' : 'text-primary');
                        ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 position-relative">
                                <div class="badge bg-primary position-absolute top-0 end-0 m-3">
                                    <i class="fas fa-sync-alt me-1"></i> Renewable
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <div class="p-3 rounded-circle bg-light d-inline-block mb-2">
                                            <i class="fas fa-shield-alt fa-2x text-primary"></i>
                                        </div>
                                        <h5 class="card-title mb-1"><?php echo htmlspecialchars($plan['plan_name']); ?></h5>
                                        <p class="text-muted small mb-0"><?php echo htmlspecialchars($plan['vehicle_make'] . ' ' . $plan['vehicle_model']); ?></p>
                                        <p class="text-muted small mb-0"><?php echo htmlspecialchars($plan['vehicle_reg_number']); ?></p>
                                    </div>
                                    
                                    <div class="mb-3 text-center">
                                        <span class="fw-bold <?php echo $urgencyClass; ?>">
                                            <?php echo $daysLeft; ?> days left
                                        </span>
                                        <div class="progress mt-2" style="height: 6px;">
                                            <?php
                                            $percentage = min(100, max(0, 100 - ($daysLeft / 30 * 100)));
                                            $progressClass = $daysLeft <= 7 ? 'bg-danger' : ($daysLeft <= 14 ? 'bg-warning' : 'bg-primary');
                                            ?>
                                            <div class="progress-bar <?php echo $progressClass; ?>" role="progressbar" style="width: <?php echo $percentage; ?>%" aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="small mt-1 text-muted">
                                            Expires on <?php echo $expiryDate->format('d M Y'); ?>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <div class="text-muted small">Monthly Premium</div>
                                            <div class="fw-bold">RM <?php echo number_format($plan['monthly_premium'], 2); ?></div>
                                        </div>
                                        <div>
                                            <div class="text-muted small">Coverage</div>
                                            <div class="fw-bold"><?php echo htmlspecialchars($plan['coverage_type']); ?></div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#renewPlanModal" 
                                            data-plan-id="<?php echo $plan['id']; ?>"
                                            data-plan-name="<?php echo htmlspecialchars($plan['plan_name']); ?>"
                                            data-vehicle-info="<?php echo htmlspecialchars($plan['vehicle_make'] . ' ' . $plan['vehicle_model'] . ' (' . $plan['vehicle_reg_number'] . ')'); ?>"
                                            data-expiry-date="<?php echo $expiryDate->format('d M Y'); ?>"
                                            data-premium="<?php echo $plan['monthly_premium']; ?>">
                                            <i class="fas fa-sync-alt me-2"></i> Renew Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Active Plans Section -->
<div class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">Other Active Plans</h5>
        </div>
        <div class="card-body">
            <?php 
            $otherActivePlans = array_filter($activePlans, function($plan) {
                $expiryDate = new DateTime($plan['expiry_date']);
                $now = new DateTime();
                $diff = $now->diff($expiryDate);
                return $diff->days > 30;
            });
            ?>
            
            <?php if (empty($otherActivePlans)): ?>
                <div class="alert alert-info" role="alert">
                    <p class="mb-0">You don't have any other active plans at the moment.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Plan</th>
                                <th>Vehicle</th>
                                <th>Start Date</th>
                                <th>Expiry Date</th>
                                <th>Status</th>
                                <th>Premium</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($otherActivePlans as $plan): ?>
                            <tr>
                                <td>
                                    <span class="fw-medium"><?php echo htmlspecialchars($plan['plan_name']); ?></span>
                                </td>
                                <td>
                                    <div><?php echo htmlspecialchars($plan['vehicle_make'] . ' ' . $plan['vehicle_model']); ?></div>
                                    <div class="small text-muted"><?php echo htmlspecialchars($plan['vehicle_reg_number']); ?></div>
                                </td>
                                <td><?php echo date('d M Y', strtotime($plan['start_date'])); ?></td>
                                <td><?php echo date('d M Y', strtotime($plan['expiry_date'])); ?></td>
                                <td><span class="badge bg-success text-white">Active</span></td>
                                <td>RM <?php echo number_format($plan['monthly_premium'], 2); ?>/month</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Expired Plans Section -->
<div class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">Expired Plans</h5>
        </div>
        <div class="card-body">
            <?php if (empty($expiredPlans)): ?>
                <div class="alert alert-info" role="alert">
                    <p class="mb-0">You don't have any expired plans.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Plan</th>
                                <th>Vehicle</th>
                                <th>Expired On</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($expiredPlans as $plan): ?>
                            <tr>
                                <td>
                                    <span class="fw-medium"><?php echo htmlspecialchars($plan['plan_name']); ?></span>
                                </td>
                                <td>
                                    <div><?php echo htmlspecialchars($plan['vehicle_make'] . ' ' . $plan['vehicle_model']); ?></div>
                                    <div class="small text-muted"><?php echo htmlspecialchars($plan['vehicle_reg_number']); ?></div>
                                </td>
                                <td><?php echo date('d M Y', strtotime($plan['expiry_date'])); ?></td>
                                <td><span class="badge bg-secondary text-white">Expired</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#reactivatePlanModal" 
                                        data-plan-id="<?php echo $plan['id']; ?>"
                                        data-plan-name="<?php echo htmlspecialchars($plan['plan_name']); ?>"
                                        data-vehicle-info="<?php echo htmlspecialchars($plan['vehicle_make'] . ' ' . $plan['vehicle_model'] . ' (' . $plan['vehicle_reg_number'] . ')'); ?>"
                                        data-premium="<?php echo $plan['monthly_premium']; ?>">
                                        <i class="fas fa-redo-alt me-1"></i> Reactivate
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Renew Plan Modal -->
<div class="modal fade" id="renewPlanModal" tabindex="-1" aria-labelledby="renewPlanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="renewPlanModalLabel">Renew Protection Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="renewPlanForm">
                    <input type="hidden" id="plan_id" name="plan_id">
                    
                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>About Plan Renewal</h6>
                        <p class="mb-0">Renewing your plan ensures continuous protection for your vehicle without any gaps in coverage. The new term will begin immediately after your current plan expires.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Plan Details</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Plan</label>
                                <div class="form-control-plaintext fw-medium" id="renewal_plan_name"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Vehicle</label>
                                <div class="form-control-plaintext fw-medium" id="renewal_vehicle_info"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Current Expiry Date</label>
                                <div class="form-control-plaintext fw-medium" id="renewal_expiry_date"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Monthly Premium</label>
                                <div class="form-control-plaintext fw-medium" id="renewal_premium"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Renewal Options</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="renewal_term" class="form-label">Renewal Term*</label>
                                <select class="form-select" id="renewal_term" name="renewal_term" required>
                                    <option value="12">12 Months</option>
                                    <option value="6">6 Months</option>
                                </select>
                                <div class="form-text">Choose your preferred renewal period.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="renewal_payment_method" class="form-label">Payment Method*</label>
                                <select class="form-select" id="renewal_payment_method" name="renewal_payment_method" required>
                                    <option value="credit_card">Credit/Debit Card</option>
                                    <option value="bank_transfer">Online Banking</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="renewal_auto_renew" name="renewal_auto_renew">
                            <label class="form-check-label" for="renewal_auto_renew">
                                Enable auto-renewal for this plan
                            </label>
                        </div>
                        <div class="form-text ms-4">
                            We'll automatically renew your plan before expiry to ensure continuous coverage. You can disable this anytime.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="renewal_terms" name="renewal_terms" required>
                            <label class="form-check-label" for="renewal_terms">
                                I agree to the <a href="#" class="text-decoration-underline">Terms and Conditions</a>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmRenewalBtn">
                    <i class="fas fa-sync-alt me-2"></i> Confirm Renewal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reactivate Plan Modal -->
<div class="modal fade" id="reactivatePlanModal" tabindex="-1" aria-labelledby="reactivatePlanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reactivatePlanModalLabel">Reactivate Expired Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reactivatePlanForm">
                    <input type="hidden" id="reactivate_plan_id" name="plan_id">
                    
                    <div class="alert alert-warning mb-4">
                        <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Important Notice</h6>
                        <p class="mb-0">This plan has expired. Reactivating it will create a new coverage period starting from today.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Plan Details</h6>
                        <div class="mb-3">
                            <label class="form-label text-muted">Plan</label>
                            <div class="form-control-plaintext fw-medium" id="reactivate_plan_name"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Vehicle</label>
                            <div class="form-control-plaintext fw-medium" id="reactivate_vehicle_info"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Monthly Premium</label>
                            <div class="form-control-plaintext fw-medium" id="reactivate_premium"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reactivate_term" class="form-label">New Term*</label>
                        <select class="form-select" id="reactivate_term" name="term" required>
                            <option value="12">12 Months</option>
                            <option value="6">6 Months</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="reactivate_terms" name="terms" required>
                            <label class="form-check-label" for="reactivate_terms">
                                I agree to the <a href="#" class="text-decoration-underline">Terms and Conditions</a>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmReactivateBtn">
                    <i class="fas fa-redo-alt me-2"></i> Reactivate Plan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Renew Plan Modal
    const renewPlanModal = document.getElementById('renewPlanModal');
    if (renewPlanModal) {
        renewPlanModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const planId = button.getAttribute('data-plan-id');
            const planName = button.getAttribute('data-plan-name');
            const vehicleInfo = button.getAttribute('data-vehicle-info');
            const expiryDate = button.getAttribute('data-expiry-date');
            const premium = button.getAttribute('data-premium');
            
            document.getElementById('plan_id').value = planId;
            document.getElementById('renewal_plan_name').textContent = planName;
            document.getElementById('renewal_vehicle_info').textContent = vehicleInfo;
            document.getElementById('renewal_expiry_date').textContent = expiryDate;
            document.getElementById('renewal_premium').textContent = `RM ${parseFloat(premium).toFixed(2)}/month`;
        });
    }
    
    // Reactivate Plan Modal
    const reactivatePlanModal = document.getElementById('reactivatePlanModal');
    if (reactivatePlanModal) {
        reactivatePlanModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const planId = button.getAttribute('data-plan-id');
            const planName = button.getAttribute('data-plan-name');
            const vehicleInfo = button.getAttribute('data-vehicle-info');
            const premium = button.getAttribute('data-premium');
            
            document.getElementById('reactivate_plan_id').value = planId;
            document.getElementById('reactivate_plan_name').textContent = planName;
            document.getElementById('reactivate_vehicle_info').textContent = vehicleInfo;
            document.getElementById('reactivate_premium').textContent = `RM ${parseFloat(premium).toFixed(2)}/month`;
        });
    }
    
    // Confirm Renewal Button
    const confirmRenewalBtn = document.getElementById('confirmRenewalBtn');
    if (confirmRenewalBtn) {
        confirmRenewalBtn.addEventListener('click', function() {
            const form = document.getElementById('renewPlanForm');
            
            // Basic form validation
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            // Submit form
            const formData = new FormData(form);
            
            // Show loading state
            confirmRenewalBtn.disabled = true;
            confirmRenewalBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
            
            // Send AJAX request
            fetch('../api/plans.php?action=renew_plan', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message and reload page
                    alert('Your plan has been successfully renewed!');
                    window.location.reload();
                } else {
                    // Show error message
                    alert('Error: ' + (data.message || 'Failed to renew plan. Please try again later.'));
                    
                    // Reset button
                    confirmRenewalBtn.disabled = false;
                    confirmRenewalBtn.innerHTML = '<i class="fas fa-sync-alt me-2"></i> Confirm Renewal';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again later.');
                
                // Reset button
                confirmRenewalBtn.disabled = false;
                confirmRenewalBtn.innerHTML = '<i class="fas fa-sync-alt me-2"></i> Confirm Renewal';
            });
        });
    }
    
    // Confirm Reactivate Button
    const confirmReactivateBtn = document.getElementById('confirmReactivateBtn');
    if (confirmReactivateBtn) {
        confirmReactivateBtn.addEventListener('click', function() {
            const form = document.getElementById('reactivatePlanForm');
            
            // Basic form validation
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            // Submit form
            const formData = new FormData(form);
            
            // Show loading state
            confirmReactivateBtn.disabled = true;
            confirmReactivateBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
            
            // Send AJAX request
            fetch('../api/plans.php?action=reactivate_plan', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message and reload page
                    alert('Your plan has been successfully reactivated!');
                    window.location.reload();
                } else {
                    // Show error message
                    alert('Error: ' + (data.message || 'Failed to reactivate plan. Please try again later.'));
                    
                    // Reset button
                    confirmReactivateBtn.disabled = false;
                    confirmReactivateBtn.innerHTML = '<i class="fas fa-redo-alt me-2"></i> Reactivate Plan';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again later.');
                
                // Reset button
                confirmReactivateBtn.disabled = false;
                confirmReactivateBtn.innerHTML = '<i class="fas fa-redo-alt me-2"></i> Reactivate Plan';
            });
        });
    }
});
</script>

<?php
// Include footer
include_once("includes/footer.php");
?> 