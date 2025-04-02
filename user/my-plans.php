<?php
require_once '../init.php';

$pageTitle = "My Plans | Customer Portal";
$current_page = 'my-plans';

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

// Get selected vehicle if any
$selectedVehicleId = isset($_GET['vehicle_id']) ? intval($_GET['vehicle_id']) : 0;

// Initialize Plan class
$planObj = new Plan($db);

// Fetch user's active plans
$userPlans = $planObj->getUserActivePlans($currentUser['id']) ?: [];

// Fetch available plans
$availablePlans = $planObj->getAvailablePlanTypes() ?: [];

// Fetch user's vehicles without plans
$vehicleObj = new Vehicle($db);
$userVehicles = $vehicleObj->getUserVehiclesWithoutPlans($currentUser['id']) ?: [];

// Include header
include_once("includes/header.php");
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">My Protection Plans</h1>
    <button class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#addVehicleModal" <?php echo empty($userVehicles) ? 'disabled' : ''; ?>>
        <i class="fas fa-plus me-2"></i>Get New Plan
    </button>
</div>

<?php if (empty($userPlans) && empty($userVehicles)): ?>
    <div class="alert alert-info p-4 rounded-3 shadow-sm" role="alert">
        <h4 class="alert-heading"><i class="fas fa-info-circle me-2"></i>No Vehicles Yet</h4>
        <p class="mb-0">You need to add a vehicle before you can purchase a protection plan. <a href="my-vehicles.php" class="alert-link">Click here</a> to add a vehicle.</p>
    </div>
<?php else: ?>
    <div class="row">
        <div class="col-lg-6">
            <?php if (empty($userPlans)): ?>
                <div class="alert alert-info p-4 rounded-3 shadow-sm mb-4" role="alert">
                    <h4 class="alert-heading"><i class="fas fa-info-circle me-2"></i>No Active Plans</h4>
                    <p class="mb-0">You don't have any active protection plans. Select one of our plans to protect your vehicle.</p>
                </div>
            <?php else: ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Your Active Plans</h6>
                    </div>
                    <div class="card-body">
                        <?php foreach ($userPlans as $plan): ?>
                            <div class="plan-card mb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($plan['plan_name']); ?></h5>
                                        <p class="text-muted mb-2">Vehicle: <?php echo htmlspecialchars($plan['vehicle_name']); ?></p>
                                    </div>
                                    <span class="badge bg-success px-3 py-2">Active</span>
                                </div>
                                <div class="plan-details mt-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Plan ID:</span>
                                        <span class="fw-medium"><?php echo htmlspecialchars($plan['reference_number']); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Start Date:</span>
                                        <span class="fw-medium"><?php echo date('d M Y', strtotime($plan['start_date'])); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Expiry Date:</span>
                                        <span class="fw-medium"><?php echo date('d M Y', strtotime($plan['end_date'])); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Coverage:</span>
                                        <span class="fw-medium">$<?php echo number_format($plan['coverage_amount'], 2); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Status:</span>
                                        <span class="fw-medium text-success">Active</span>
                                    </div>
                                </div>
                                <div class="mt-3 pt-3 border-top">
                                    <button class="btn btn-sm btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#planDetailsModal" data-plan-id="<?php echo $plan['id']; ?>">
                                        <i class="fas fa-file-alt me-2"></i>View Plan Details
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($userVehicles)): ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Vehicles Without Protection</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">The following vehicles do not have an active protection plan:</p>
                        <div class="list-group">
                            <?php foreach ($userVehicles as $vehicle): ?>
                                <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?php echo htmlspecialchars($vehicle['make'] . ' ' . $vehicle['model']); ?></h6>
                                        <small class="text-muted"><?php echo htmlspecialchars($vehicle['reg_number'] . ' (' . $vehicle['year'] . ')'); ?></small>
                                    </div>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addVehicleModal" data-vehicle-id="<?php echo $vehicle['id']; ?>">
                                        <i class="fas fa-shield-alt me-2"></i>Get Plan
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Available Protection Plans</h6>
                </div>
                <div class="card-body">
                    <?php foreach ($availablePlans as $index => $plan): ?>
                        <div class="plan-option mb-4 <?php echo ($index == 0) ? 'recommended' : ''; ?>">
                            <?php if ($index == 0): ?>
                                <div class="recommended-badge">Most Popular</div>
                            <?php endif; ?>
                            <div class="plan-option-header">
                                <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($plan['name']); ?></h5>
                                <div class="price-tag">
                                    <span class="currency">$</span>
                                    <span class="price"><?php echo number_format($plan['price'], 0); ?></span>
                                    <span class="period">/year</span>
                                </div>
                            </div>
                            <div class="plan-option-body">
                                <p class="mb-3"><?php echo htmlspecialchars($plan['description']); ?></p>
                                <ul class="plan-features">
                                    <?php 
                                    $features = explode(',', $plan['features']);
                                    foreach ($features as $feature): 
                                    ?>
                                        <li><i class="fas fa-check-circle text-success me-2"></i><?php echo htmlspecialchars(trim($feature)); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <div class="coverage-info mt-3">
                                    <p class="text-muted mb-1">Coverage up to <strong>$<?php echo number_format($plan['coverage_limit'], 0); ?></strong></p>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-top">
                                <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#addVehicleModal" data-plan-id="<?php echo $plan['id']; ?>" <?php echo empty($userVehicles) ? 'disabled' : ''; ?>>
                                    <i class="fas fa-shield-alt me-2"></i>Select This Plan
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Add Vehicle Modal -->
<div class="modal fade" id="addVehicleModal" tabindex="-1" aria-labelledby="addVehicleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVehicleModalLabel">Select Vehicle for Protection Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (empty($userVehicles)): ?>
                    <div class="alert alert-info" role="alert">
                        <p class="mb-0">You don't have any vehicles without protection plans. <a href="my-vehicles.php" class="alert-link">Add a new vehicle</a> or wait for your current plans to expire.</p>
                    </div>
                <?php else: ?>
                    <form id="selectVehicleForm">
                        <input type="hidden" id="selected_plan_id" name="plan_id" value="">
                        
                        <div class="mb-4">
                            <label for="vehicle_id" class="form-label">Select Vehicle*</label>
                            <select class="form-select" id="vehicle_id" name="vehicle_id" required>
                                <option value="">-- Select Vehicle --</option>
                                <?php foreach ($userVehicles as $vehicle): ?>
                                    <option value="<?php echo $vehicle['id']; ?>" <?php echo ($selectedVehicleId == $vehicle['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($vehicle['make'] . ' ' . $vehicle['model'] . ' (' . $vehicle['reg_number'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label for="modal_plan_id" class="form-label">Select Protection Plan*</label>
                            <select class="form-select" id="modal_plan_id" name="modal_plan_id" required>
                                <option value="">-- Select Plan --</option>
                                <?php foreach ($availablePlans as $plan): ?>
                                    <option value="<?php echo $plan['id']; ?>">
                                        <?php echo htmlspecialchars($plan['name'] . ' - $' . number_format($plan['price'], 2) . '/year'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label for="start_date" class="form-label">Start Date*</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms_check" name="terms_check" required>
                                <label class="form-check-label" for="terms_check">
                                    I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</a>
                                </label>
                            </div>
                        </div>
                        
                        <div id="plan_details_preview" class="card border p-3 mb-3 d-none">
                            <h6 class="fw-bold">Plan Summary</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Plan:</strong> <span id="preview_plan_name">--</span></p>
                                    <p class="mb-1"><strong>Coverage:</strong> <span id="preview_coverage">--</span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Price:</strong> <span id="preview_price">--</span></p>
                                    <p class="mb-1"><strong>Duration:</strong> 1 Year</p>
                                </div>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <?php if (!empty($userVehicles)): ?>
                    <button type="button" class="btn btn-primary" id="proceedToPaymentBtn">Proceed to Payment</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Plan Details Modal -->
<div class="modal fade" id="planDetailsModal" tabindex="-1" aria-labelledby="planDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="planDetailsModalLabel">Plan Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="planDetailsContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading plan details...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Terms and Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>1. Coverage</h6>
                <p>Protection plans cover mechanical breakdowns as defined in the plan documentation. Coverage is limited to the stated maximum amount per claim and annual aggregate.</p>
                
                <h6>2. Term</h6>
                <p>Protection plans are valid for one year from the start date, unless otherwise specified.</p>
                
                <h6>3. Claims</h6>
                <p>All claims must be reported within 7 days of the incident. Repairs must be performed by authorized service providers.</p>
                
                <h6>4. Exclusions</h6>
                <p>Protection plans do not cover:</p>
                <ul>
                    <li>Pre-existing conditions</li>
                    <li>Regular maintenance items</li>
                    <li>Cosmetic damage</li>
                    <li>Damage from accidents</li>
                    <li>Modifications not approved by the manufacturer</li>
                    <li>Damage from misuse or neglect</li>
                </ul>
                
                <h6>5. Cancellation</h6>
                <p>Cancellation within 30 days of purchase may qualify for a full refund if no claims have been made. After 30 days, refunds are prorated.</p>
                
                <h6>6. Transferability</h6>
                <p>Plans are linked to specific vehicles and cannot be transferred to other vehicles.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.plan-card {
    border: 1px solid #e3e6f0;
    border-radius: 0.5rem;
    padding: 1.25rem;
    transition: all 0.3s ease;
    background-color: #fff;
}

.plan-card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.fw-medium {
    font-weight: 500;
}

.plan-option {
    border: 1px solid #e3e6f0;
    border-radius: 0.5rem;
    padding: 1.5rem;
    position: relative;
    transition: all 0.3s ease;
    background-color: #fff;
}

.plan-option:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.plan-option.recommended {
    border-color: #4e73df;
    box-shadow: 0 0.5rem 1rem rgba(78, 115, 223, 0.15);
}

.recommended-badge {
    position: absolute;
    top: -12px;
    right: 20px;
    background-color: #4e73df;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
}

.plan-option-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.price-tag {
    text-align: right;
}

.currency {
    font-size: 16px;
    vertical-align: super;
}

.price {
    font-size: 32px;
    font-weight: bold;
    color: #4e73df;
}

.period {
    font-size: 14px;
    color: #6c757d;
}

.plan-features {
    list-style-type: none;
    padding-left: 0;
    margin-bottom: 1.5rem;
}

.plan-features li {
    margin-bottom: 0.5rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set today's date as the default for the start date field
    const today = new Date();
    const startDateField = document.getElementById('start_date');
    if (startDateField) {
        const formattedDate = today.toISOString().split('T')[0];
        startDateField.value = formattedDate;
        startDateField.min = formattedDate; // Can't select past dates
    }
    
    // Handle pre-selection of vehicle and plan from URL or buttons
    const addVehicleModal = document.getElementById('addVehicleModal');
    if (addVehicleModal) {
        addVehicleModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            
            // Check if a vehicle ID was provided
            const vehicleId = button.getAttribute('data-vehicle-id');
            if (vehicleId) {
                const vehicleSelect = document.getElementById('vehicle_id');
                if (vehicleSelect) {
                    vehicleSelect.value = vehicleId;
                }
            }
            
            // Check if a plan ID was provided
            const planId = button.getAttribute('data-plan-id');
            if (planId) {
                const planSelect = document.getElementById('modal_plan_id');
                if (planSelect) {
                    planSelect.value = planId;
                    updatePlanPreview(planId);
                }
            }
        });
    }
    
    // Update plan preview when plan is selected
    const planSelect = document.getElementById('modal_plan_id');
    if (planSelect) {
        planSelect.addEventListener('change', function() {
            updatePlanPreview(this.value);
        });
    }
    
    // Function to update plan preview
    function updatePlanPreview(planId) {
        const previewDiv = document.getElementById('plan_details_preview');
        
        if (!planId) {
            previewDiv.classList.add('d-none');
            return;
        }
        
        // In production, you'd fetch plan details via AJAX
        // For demo purposes, use the data from the available plans
        <?php if (!empty($availablePlans)): ?>
        const plans = <?php echo json_encode($availablePlans); ?>;
        const selectedPlan = plans.find(plan => plan.id == planId);
        
        if (selectedPlan) {
            document.getElementById('preview_plan_name').textContent = selectedPlan.name;
            document.getElementById('preview_coverage').textContent = `$${selectedPlan.coverage_limit.toLocaleString()}`;
            document.getElementById('preview_price').textContent = `$${selectedPlan.price.toLocaleString()}/year`;
            previewDiv.classList.remove('d-none');
        } else {
            previewDiv.classList.add('d-none');
        }
        <?php endif; ?>
    }
    
    // Handle payment process
    const proceedToPaymentBtn = document.getElementById('proceedToPaymentBtn');
    if (proceedToPaymentBtn) {
        proceedToPaymentBtn.addEventListener('click', function() {
            const form = document.getElementById('selectVehicleForm');
            if (form.checkValidity()) {
                // In production, submit form data via AJAX
                // For demo purposes, just close the modal and show alert
                const modal = bootstrap.Modal.getInstance(document.getElementById('addVehicleModal'));
                modal.hide();
                
                // Show success message
                alert('Your plan purchase has been processed successfully! The plan is now active for your vehicle.');
                
                // Reload page to show new plan (in production, would add dynamically)
                window.location.reload();
            } else {
                form.reportValidity();
            }
        });
    }
    
    // View plan details
    const planDetailsModal = document.getElementById('planDetailsModal');
    if (planDetailsModal) {
        planDetailsModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const planId = button.getAttribute('data-plan-id');
            
            // In production, fetch plan details via AJAX
            // For demo purposes, show dummy data after a short delay
            setTimeout(() => {
                const planDetailsContent = document.getElementById('planDetailsContent');
                
                // Sample data for demonstration
                planDetailsContent.innerHTML = `
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold">Plan Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td class="fw-medium">Plan Name:</td>
                                    <td>Premium Protection</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Plan ID:</td>
                                    <td>PLN-2023-001</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Status:</td>
                                    <td><span class="badge bg-success text-white">Active</span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">Coverage Details</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td class="fw-medium">Coverage Amount:</td>
                                    <td>$20,000.00</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Start Date:</td>
                                    <td>01 Jan 2023</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">End Date:</td>
                                    <td>31 Dec 2023</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="fw-bold">Vehicle Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td class="fw-medium" style="width: 20%;">Vehicle:</td>
                                    <td>Toyota Camry (ABC1234)</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Year:</td>
                                    <td>2020</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Current Mileage:</td>
                                    <td>25,000 km</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <h6 class="fw-bold">Covered Components</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success me-2"></i> Engine</li>
                                        <li><i class="fas fa-check text-success me-2"></i> Transmission</li>
                                        <li><i class="fas fa-check text-success me-2"></i> Drive Axle</li>
                                        <li><i class="fas fa-check text-success me-2"></i> Brakes</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success me-2"></i> Electrical</li>
                                        <li><i class="fas fa-check text-success me-2"></i> Air Conditioning</li>
                                        <li><i class="fas fa-check text-success me-2"></i> Cooling System</li>
                                        <li><i class="fas fa-check text-success me-2"></i> Fuel System</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-4" role="alert">
                        <p class="mb-0"><i class="fas fa-info-circle me-2"></i> For claims or service, please call our 24/7 helpline at (555) 123-4567 or submit a claim through the "My Claims" section.</p>
                    </div>
                `;
            }, 1000);
        });
    }
});
</script>

<?php include_once("includes/footer.php"); ?> 