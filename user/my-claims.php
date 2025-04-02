<?php
require_once '../init.php';

$pageTitle = "My Claims | Customer Portal";
$current_page = 'my-claims';

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

// Initialize Claim class
$claimObj = new Claim($db);

// Fetch user's vehicles
$vehicleObj = new Vehicle($db);
$userVehicles = $vehicleObj->getUserVehicles($currentUser['id']) ?: [];

// Fetch user's claims
$userClaims = $claimObj->getUserClaims($currentUser['id']) ?: [];

// Fetch workshops
$workshopObj = new Workshop($db);
$workshops = $workshopObj->getAllWorkshops() ?: [];

// Include header
include_once("includes/header.php");
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">My Claims</h1>
    <button class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#newClaimModal" <?php echo empty($userVehicles) ? 'disabled' : ''; ?>>
        <i class="fas fa-plus me-2"></i>Submit New Claim
    </button>
</div>

<?php if (empty($userVehicles)): ?>
    <div class="alert alert-info p-4 rounded-3 shadow-sm" role="alert">
        <h4 class="alert-heading"><i class="fas fa-info-circle me-2"></i>No Vehicles Yet</h4>
        <p class="mb-0">You need to add a vehicle before you can submit a claim. <a href="my-vehicles.php" class="alert-link">Click here</a> to add a vehicle.</p>
    </div>
<?php elseif (empty($userClaims)): ?>
    <div class="alert alert-info p-4 rounded-3 shadow-sm" role="alert">
        <h4 class="alert-heading"><i class="fas fa-info-circle me-2"></i>No Claims Yet</h4>
        <p class="mb-0">You haven't submitted any claims yet. Click the "Submit New Claim" button to get started.</p>
    </div>
<?php else: ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Claims History</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="claimsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Claim ID</th>
                            <th>Date</th>
                            <th>Vehicle</th>
                            <th>Issue</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($userClaims as $claim): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($claim['claim_number']); ?></td>
                                <td><?php echo date('d M Y', strtotime($claim['claim_date'])); ?></td>
                                <td><?php echo htmlspecialchars($claim['vehicle_name']); ?></td>
                                <td><?php echo htmlspecialchars($claim['issue_type']); ?></td>
                                <td>
                                    <?php
                                    $statusClass = '';
                                    switch ($claim['status']) {
                                        case 'Pending':
                                            $statusClass = 'bg-warning';
                                            break;
                                        case 'Approved':
                                            $statusClass = 'bg-success';
                                            break;
                                        case 'In Progress':
                                            $statusClass = 'bg-info';
                                            break;
                                        case 'Completed':
                                            $statusClass = 'bg-primary';
                                            break;
                                        case 'Rejected':
                                            $statusClass = 'bg-danger';
                                            break;
                                        default:
                                            $statusClass = 'bg-secondary';
                                    }
                                    ?>
                                    <span class="badge <?php echo $statusClass; ?> text-white py-1 px-2">
                                        <?php echo htmlspecialchars($claim['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo isset($claim['approved_amount']) && $claim['approved_amount'] > 0 
                                        ? '$' . number_format($claim['approved_amount'], 2) 
                                        : 'Pending'; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary view-claim-details" data-claim-id="<?php echo $claim['id']; ?>">
                                        <i class="fas fa-eye me-1"></i> View
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

<!-- Claim Submission Modal -->
<div class="modal fade" id="newClaimModal" tabindex="-1" aria-labelledby="newClaimModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newClaimModalLabel">Submit New Claim</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newClaimForm">
                    <div class="mb-3">
                        <label for="vehicle_id" class="form-label">Select Vehicle*</label>
                        <select class="form-select" id="vehicle_id" name="vehicle_id" required>
                            <option value="">-- Select Vehicle --</option>
                            <?php foreach ($userVehicles as $vehicle): ?>
                                <?php if (!empty($vehicle['plan_name'])): ?>
                                    <option value="<?php echo $vehicle['id']; ?>">
                                        <?php echo htmlspecialchars($vehicle['make'] . ' ' . $vehicle['model'] . ' (' . $vehicle['reg_number'] . ')'); ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Only vehicles with active protection plans are displayed.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="workshop_id" class="form-label">Select Workshop*</label>
                        <select class="form-select" id="workshop_id" name="workshop_id" required>
                            <option value="">-- Select Workshop --</option>
                            <?php foreach ($workshops as $workshop): ?>
                                <option value="<?php echo $workshop['id']; ?>">
                                    <?php echo htmlspecialchars($workshop['name'] . ' (' . $workshop['location'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="issue_type" class="form-label">Issue Type*</label>
                        <select class="form-select" id="issue_type" name="issue_type" required>
                            <option value="">-- Select Issue Type --</option>
                            <option value="Engine Problem">Engine Problem</option>
                            <option value="Transmission Issue">Transmission Issue</option>
                            <option value="Electrical Fault">Electrical Fault</option>
                            <option value="Brake System">Brake System</option>
                            <option value="Suspension">Suspension</option>
                            <option value="Air Conditioning">Air Conditioning</option>
                            <option value="Cooling System">Cooling System</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description*</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        <div class="form-text">Please provide detailed information about the issue you're experiencing.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="incident_date" class="form-label">Incident Date*</label>
                        <input type="date" class="form-control" id="incident_date" name="incident_date" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="current_mileage" class="form-label">Current Mileage (km)*</label>
                        <input type="number" class="form-control" id="current_mileage" name="current_mileage" min="0" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Supporting Documents</label>
                        <input type="file" class="form-control" id="claim_documents" name="claim_documents[]" multiple>
                        <div class="form-text">You can upload photos of the damage, repair quotes, or other relevant documents (Max 5MB each).</div>
                    </div>
                    
                    <div class="form-text mb-3">Fields marked with * are required.</div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitClaimBtn">Submit Claim</button>
            </div>
        </div>
    </div>
</div>

<!-- Claim Details Modal -->
<div class="modal fade" id="claimDetailsModal" tabindex="-1" aria-labelledby="claimDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="claimDetailsModalLabel">Claim Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="claimDetailsContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading claim details...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set today's date as the default for the incident date field
    const today = new Date().toISOString().split('T')[0];
    const incidentDateField = document.getElementById('incident_date');
    if (incidentDateField) {
        incidentDateField.value = today;
        incidentDateField.max = today; // Can't select future dates
    }
    
    // New claim form submission
    const submitClaimBtn = document.getElementById('submitClaimBtn');
    if (submitClaimBtn) {
        submitClaimBtn.addEventListener('click', function() {
            const form = document.getElementById('newClaimForm');
            if (form.checkValidity()) {
                // In production, submit form data via AJAX
                // For demo purposes, just close the modal and show alert
                const modal = bootstrap.Modal.getInstance(document.getElementById('newClaimModal'));
                modal.hide();
                
                // Show success message
                alert('Your claim has been submitted successfully! Our team will review it and contact you shortly.');
                
                // Reload page to show new claim (in production, would add dynamically)
                window.location.reload();
            } else {
                form.reportValidity();
            }
        });
    }
    
    // View claim details
    const viewClaimButtons = document.querySelectorAll('.view-claim-details');
    if (viewClaimButtons.length > 0) {
        viewClaimButtons.forEach(button => {
            button.addEventListener('click', function() {
                const claimId = this.getAttribute('data-claim-id');
                
                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('claimDetailsModal'));
                modal.show();
                
                // In production, fetch claim details via AJAX
                // For demo purposes, show dummy data after a short delay
                setTimeout(() => {
                    const claimDetailsContent = document.getElementById('claimDetailsContent');
                    
                    // Sample data for demonstration
                    claimDetailsContent.innerHTML = `
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold">Claim Information</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td class="fw-medium">Claim Number:</td>
                                        <td>CLM-2023-001</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium">Status:</td>
                                        <td><span class="badge bg-warning text-white">Pending</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium">Submitted Date:</td>
                                        <td>15 Jun 2023</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium">Incident Date:</td>
                                        <td>10 Jun 2023</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold">Vehicle Information</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td class="fw-medium">Vehicle:</td>
                                        <td>Toyota Camry (ABC1234)</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium">Plan:</td>
                                        <td>Premium Protection</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium">Mileage:</td>
                                        <td>25,000 km</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium">Workshop:</td>
                                        <td>AutoCare Service Center</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="fw-bold">Issue Details</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td class="fw-medium" style="width: 20%;">Issue Type:</td>
                                        <td>Engine Problem</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium">Description:</td>
                                        <td>Engine making unusual noises and vehicle struggling to start. Check engine light is on.</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="fw-bold">Claim Processing</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td class="fw-medium" style="width: 20%;">Estimated Amount:</td>
                                        <td>Pending Assessment</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium">Approved Amount:</td>
                                        <td>Pending Approval</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium">Notes:</td>
                                        <td>Your claim is currently under review. Our claims team will contact you within 2 business days.</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <h6 class="fw-bold">Claim Timeline</h6>
                                <ul class="timeline">
                                    <li class="timeline-item">
                                        <div class="timeline-marker"></div>
                                        <div class="timeline-content">
                                            <h6 class="mb-0">Claim Submitted</h6>
                                            <p class="text-muted small mb-0">15 Jun 2023, 10:30 AM</p>
                                        </div>
                                    </li>
                                    <li class="timeline-item">
                                        <div class="timeline-marker"></div>
                                        <div class="timeline-content">
                                            <h6 class="mb-0">Claim Received</h6>
                                            <p class="text-muted small mb-0">15 Jun 2023, 11:45 AM</p>
                                            <p class="mb-0">Your claim has been received and is pending review.</p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    `;
                }, 1000);
            });
        });
    }
});
</script>

<style>
.timeline {
    list-style-type: none;
    margin: 0;
    padding: 0;
    position: relative;
}

.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
    left: 7px;
    margin-left: 0;
}

.timeline-item {
    position: relative;
    padding-left: 30px;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #4e73df;
    background: white;
    left: 0;
    top: 3px;
}

.timeline-content {
    padding-bottom: 10px;
}

.fw-medium {
    font-weight: 500;
}
</style>

<?php include_once("includes/footer.php"); ?> 