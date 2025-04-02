<?php
// Claims Management Page
$page_title = "Claims Management";
$page_description = "Process and manage insurance claims";
$current_page = 'claims'; // 设置当前页面标识符

require_once '../init.php';
require_once 'classes/Admin.php';

// Get User instance
$user = User::getInstance();
$admin = Admin\Admin::getInstance();

// Check if user is logged in and has appropriate role
if (!$user->isLoggedIn() || !($user->hasRole('super_admin') || $user->hasRole('admin') || $user->hasRole('agent'))) {
    header('Location: dashboard.php');
    exit;
}

// Handle form submissions
$message = "";
$message_type = "";

// Update claim status
if (isset($_POST['update_claim_status'])) {
    $claim_id = $_POST['claim_id'] ?? 0;
    $status = $_POST['status'] ?? '';
    $admin_notes = $_POST['admin_notes'] ?? '';
    
    if (empty($claim_id) || empty($status)) {
        $message = "Claim ID and status are required";
        $message_type = "danger";
    } else {
        if ($admin->updateClaimStatus($claim_id, $status, $admin_notes)) {
            $message = "Claim status updated successfully";
            $message_type = "success";
            
            // Log the activity
            $user->logAdminActivity('updated', 'claim status', $claim_id, "Updated claim #$claim_id status to $status");
        } else {
            $message = "Failed to update claim status";
            $message_type = "danger";
        }
    }
}

// Fetch all claims
$claims = $admin->getAllClaims();

// Get claims statistics
$claimStats = [
    'total' => count($claims),
    'pending' => 0,
    'approved' => 0,
    'rejected' => 0,
    'processing' => 0,
    'in_progress' => 0  // 添加另一个可能的状态
];

foreach ($claims as $claim) {
    if (isset($claim['status'])) {
        $status = strtolower($claim['status']);
        
        // 处理状态映射，统一不同表示方式的状态
        if ($status === 'in_progress' || $status === 'in-progress' || $status === 'reviewing') {
            $status = 'processing';
        } elseif ($status === 'accept' || $status === 'accepted') {
            $status = 'approved';
        } elseif ($status === 'deny' || $status === 'denied') {
            $status = 'rejected';
        } elseif ($status === 'new' || $status === 'submitted') {
            $status = 'pending';
        }
        
        if (isset($claimStats[$status])) {
            $claimStats[$status]++;
        }
    }
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Claims Management</h1>
    <p class="mb-4">Review, process, and manage insurance claims from customers.</p>

    <?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <!-- Claims Overview Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Claims</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo isset($claimStats['total']) ? $claimStats['total'] : 0; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Claims</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo isset($claimStats['pending']) ? $claimStats['pending'] : 0; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Processing</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo isset($claimStats['processing']) ? $claimStats['processing'] : 0; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-spinner fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Approved Claims</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo isset($claimStats['approved']) ? $claimStats['approved'] : 0; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Claims List Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">All Claims</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                    aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Filter Options:</div>
                    <a class="dropdown-item" href="?status=pending">Pending Claims</a>
                    <a class="dropdown-item" href="?status=processing">Processing Claims</a>
                    <a class="dropdown-item" href="?status=approved">Approved Claims</a>
                    <a class="dropdown-item" href="?status=rejected">Rejected Claims</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="claims.php">View All Claims</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered admin-datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Policy #</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Submitted</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($claims as $claim): ?>
                        <tr>
                            <td><?php echo $claim['id']; ?></td>
                            <td>
                                <?php echo htmlspecialchars($claim['customer_name'] ?? 'Unknown'); ?>
                            </td>
                            <td><?php echo htmlspecialchars($claim['policy_number'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($claim['claim_type'] ?? 'N/A'); ?></td>
                            <td>RM <?php echo number_format($claim['amount'] ?? 0, 2); ?></td>
                            <td><?php echo date('M d, Y', strtotime($claim['created_at'])); ?></td>
                            <td>
                                <?php 
                                $statusClass = 'bg-secondary';
                                $status = isset($claim['status']) ? strtolower($claim['status']) : '';
                                switch ($status) {
                                    case 'pending':
                                        $statusClass = 'bg-warning';
                                        break;
                                    case 'processing':
                                        $statusClass = 'bg-info';
                                        break;
                                    case 'approved':
                                        $statusClass = 'bg-success';
                                        break;
                                    case 'rejected':
                                        $statusClass = 'bg-danger';
                                        break;
                                }
                                ?>
                                <span class="badge <?php echo $statusClass; ?>">
                                    <?php echo isset($claim['status']) ? ucfirst($claim['status']) : 'Unknown'; ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-info btn-sm view-claim" data-id="<?php echo $claim['id']; ?>"
                                        data-bs-toggle="modal" data-bs-target="#viewClaimModal">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-primary btn-sm update-status" data-id="<?php echo $claim['id']; ?>"
                                        data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="claim_details.php?id=<?php echo $claim['id']; ?>" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-file-alt"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- View Claim Modal -->
<div class="modal fade" id="viewClaimModal" tabindex="-1" aria-labelledby="viewClaimModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewClaimModalLabel">Claim Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Claim Information</h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <p><strong>Claim ID:</strong> <span id="claim_id"></span></p>
                                <p><strong>Claim Type:</strong> <span id="claim_type"></span></p>
                                <p><strong>Status:</strong> <span id="claim_status"></span></p>
                                <p><strong>Submitted:</strong> <span id="claim_date"></span></p>
                                <p><strong>Amount:</strong> RM <span id="claim_amount"></span></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Customer & Policy Information</h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <p><strong>Customer:</strong> <span id="customer_name"></span></p>
                                <p><strong>Email:</strong> <span id="customer_email"></span></p>
                                <p><strong>Phone:</strong> <span id="customer_phone"></span></p>
                                <p><strong>Policy #:</strong> <span id="policy_number"></span></p>
                                <p><strong>Policy Type:</strong> <span id="policy_type"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h6 class="font-weight-bold">Claim Description</h6>
                    <div class="card bg-light">
                        <div class="card-body">
                            <p id="claim_description"></p>
                        </div>
                    </div>
                </div>
                
                <div id="documents_section" class="mb-4">
                    <h6 class="font-weight-bold">Supporting Documents</h6>
                    <div class="card bg-light">
                        <div class="card-body" id="documents_list">
                            <!-- Documents will be loaded here -->
                        </div>
                    </div>
                </div>
                
                <div id="notes_section">
                    <h6 class="font-weight-bold">Administrative Notes</h6>
                    <div class="card bg-light">
                        <div class="card-body">
                            <p id="admin_notes"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary update-status-btn">Update Status</button>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusModalLabel">Update Claim Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="claims.php" class="needs-validation" novalidate>
                <input type="hidden" name="claim_id" id="status_claim_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_status" class="form-label">Current Status</label>
                        <input type="text" class="form-control" id="current_status" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">New Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a status.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="admin_notes" class="form-label">Administrative Notes</label>
                        <textarea class="form-control" id="status_admin_notes" name="admin_notes" rows="3"></textarea>
                        <small class="text-muted">
                            Add any relevant notes about this status change. These notes are for internal use only.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_claim_status" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // View claim details
    $('.view-claim').click(function() {
        var claimId = $(this).data('id');
        
        // Store claim ID for update status button
        $('.update-status-btn').data('id', claimId);
        
        // AJAX request to get claim details
        $.ajax({
            url: 'ajax/get_claim.php',
            type: 'GET',
            data: {id: claimId},
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var claim = response.data;
                    
                    // Populate modal fields - Claim Information
                    $('#claim_id').text(claim.id);
                    $('#claim_type').text(claim.claim_type);
                    $('#claim_date').text(new Date(claim.created_at).toLocaleString());
                    $('#claim_amount').text(parseFloat(claim.amount).toFixed(2));
                    
                    // Status with badge
                    var statusClass = 'badge bg-secondary';
                    switch (claim.status.toLowerCase()) {
                        case 'pending':
                            statusClass = 'badge bg-warning';
                            break;
                        case 'processing':
                            statusClass = 'badge bg-info';
                            break;
                        case 'approved':
                            statusClass = 'badge bg-success';
                            break;
                        case 'rejected':
                            statusClass = 'badge bg-danger';
                            break;
                    }
                    $('#claim_status').html('<span class="' + statusClass + '">' + claim.status + '</span>');
                    
                    // Customer & Policy Information
                    $('#customer_name').text(claim.customer_name);
                    $('#customer_email').text(claim.customer_email);
                    $('#customer_phone').text(claim.customer_phone || 'N/A');
                    $('#policy_number').text(claim.policy_number);
                    $('#policy_type').text(claim.policy_type);
                    
                    // Claim Description
                    $('#claim_description').text(claim.description || 'No description provided.');
                    
                    // Supporting Documents
                    var documentsList = $('#documents_list');
                    documentsList.empty();
                    
                    if (claim.documents && claim.documents.length > 0) {
                        var documentItems = '<ul class="list-group list-group-flush">';
                        claim.documents.forEach(function(doc) {
                            documentItems += '<li class="list-group-item bg-light">' +
                                '<i class="fas fa-file-alt me-2"></i> ' +
                                '<a href="../uploads/claims/' + doc.filename + '" target="_blank">' +
                                doc.original_name + '</a></li>';
                        });
                        documentItems += '</ul>';
                        documentsList.html(documentItems);
                    } else {
                        documentsList.html('<p class="text-muted">No supporting documents uploaded.</p>');
                    }
                    
                    // Admin Notes
                    $('#admin_notes').text(claim.admin_notes || 'No administrative notes available.');
                } else {
                    alert('Failed to load claim details: ' + response.message);
                }
            },
            error: function() {
                alert('Error loading claim details. Please try again.');
            }
        });
    });
    
    // Handle update status button in view claim modal
    $('.update-status-btn').click(function() {
        var claimId = $(this).data('id');
        $('#viewClaimModal').modal('hide');
        
        // Trigger the update status modal
        setTimeout(function() {
            $('.update-status[data-id="' + claimId + '"]').trigger('click');
        }, 500);
    });
    
    // Update status modal
    $('.update-status').click(function() {
        var claimId = $(this).data('id');
        
        // AJAX request to get claim status
        $.ajax({
            url: 'ajax/get_claim_status.php',
            type: 'GET',
            data: {id: claimId},
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#status_claim_id').val(claimId);
                    $('#current_status').val(response.status);
                    $('#status').val('');  // Reset the status dropdown
                    $('#status_admin_notes').val('');  // Clear the notes field
                } else {
                    alert('Failed to load claim status: ' + response.message);
                }
            },
            error: function() {
                alert('Error loading claim status. Please try again.');
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?> 