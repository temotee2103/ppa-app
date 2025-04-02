<?php
require_once '../init.php';
require_once '../classes/Support.php';
require_once '../classes/Vehicle.php';

$pageTitle = "Support | Customer Portal";
$current_page = 'support';

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

// Initialize Support and Vehicle classes
$supportObj = new Support($db);
$vehicleObj = new Vehicle($db);

// Fetch user's support requests
$userSupportRequests = $supportObj->getUserSupportRequests($currentUser['id']) ?: [];

// Fetch user's vehicles for the form
$userVehicles = $vehicleObj->getUserVehicles($currentUser['id']) ?: [];

// Process form submission for new support ticket
$ticketError = '';
$ticketSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create_ticket') {
        $request_data = [
            'user_id' => $currentUser['id'],
            'vehicle_id' => $_POST['vehicle_id'],
            'subject' => $_POST['subject'],
            'description' => $_POST['description'],
            'priority' => $_POST['priority'],
            'status' => 'open'
        ];
        
        if ($supportObj->createSupportRequest($request_data)) {
            $ticketSuccess = 'Your support request has been created successfully.';
            // Refresh ticket list
            $userSupportRequests = $supportObj->getUserSupportRequests($currentUser['id']) ?: [];
        } else {
            $ticketError = 'An error occurred while creating your ticket. Please try again.';
        }
    } elseif ($_POST['action'] === 'add_reply' && isset($_POST['request_id'])) {
        $reply_data = [
            'request_id' => $_POST['request_id'],
            'user_id' => $currentUser['id'],
            'message' => $_POST['message']
        ];
        
        if ($supportObj->addSupportReply($reply_data)) {
            $ticketSuccess = 'Your reply has been added successfully.';
            // Refresh ticket list
            $userSupportRequests = $supportObj->getUserSupportRequests($currentUser['id']) ?: [];
        } else {
            $ticketError = 'An error occurred while adding your reply. Please try again.';
        }
    }
}

// Include header
include_once("includes/header.php");
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Support Requests</h5>
</div>
                <div class="card-body">
                    <?php if ($ticketSuccess): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $ticketSuccess; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

                    <?php if ($ticketError): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $ticketError; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

                    <!-- New Support Request Form -->
<div class="mb-4">
                        <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#newTicketForm">
                            <i class="fas fa-plus me-2"></i>New Support Request
                                    </button>
                        <div class="collapse mt-3" id="newTicketForm">
                            <div class="card">
    <div class="card-body">
                                    <form method="POST" action="">
                    <input type="hidden" name="action" value="create_ticket">
                    
                    <div class="mb-3">
                                            <label for="vehicle_id" class="form-label">Select Vehicle</label>
                                            <select class="form-select" id="vehicle_id" name="vehicle_id" required>
                                                <option value="">Choose a vehicle...</option>
                                                <?php foreach ($userVehicles as $vehicle): ?>
                                                    <option value="<?php echo $vehicle['id']; ?>">
                                                        <?php echo htmlspecialchars($vehicle['make'] . ' ' . $vehicle['model'] . ' (' . $vehicle['reg_number'] . ')'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                                            <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    
                    <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority</label>
                                            <select class="form-select" id="priority" name="priority" required>
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    
                                        <button type="submit" class="btn btn-primary">Submit Request</button>
                </form>
            </div>
        </div>
    </div>
</div>

                    <!-- Support Requests List -->
                    <?php if (empty($userSupportRequests)): ?>
                        <div class="text-center py-4">
                            <img src="../assets/images/empty-tickets.svg" alt="No Tickets" class="img-fluid mb-3" style="max-width: 150px;">
                            <h5>No Support Requests Yet</h5>
                            <p class="text-muted">Create your first support request by clicking the button above.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Subject</th>
                                        <th>Vehicle</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($userSupportRequests as $ticket): ?>
                                        <tr>
                                            <td><span class="fw-medium">#<?php echo $ticket['id']; ?></span></td>
                                            <td><?php echo htmlspecialchars($ticket['subject']); ?></td>
                                            <td><?php echo htmlspecialchars($ticket['vehicle_make'] . ' ' . $ticket['vehicle_model'] . ' (' . $ticket['vehicle_reg'] . ')'); ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $ticket['priority'] === 'high' ? 'danger' : 
                                                        ($ticket['priority'] === 'medium' ? 'warning' : 'info'); 
                                                ?>">
                                                    <?php echo ucfirst($ticket['priority']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $ticket['status'] === 'closed' ? 'success' : 
                                                        ($ticket['status'] === 'in_progress' ? 'primary' : 'secondary'); 
                                                ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $ticket['status'])); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($ticket['created_at'])); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#ticketModal<?php echo $ticket['id']; ?>">
                                                    View Details
                                                </button>
                                            </td>
                                        </tr>
                                        
                                        <!-- Ticket Details Modal -->
                                        <div class="modal fade" id="ticketModal<?php echo $ticket['id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                                                        <h5 class="modal-title">Support Request #<?php echo $ticket['id']; ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                                                        <div class="mb-4">
                                                            <h6>Request Details</h6>
                                                            <p><strong>Subject:</strong> <?php echo htmlspecialchars($ticket['subject']); ?></p>
                                                            <p><strong>Vehicle:</strong> <?php echo htmlspecialchars($ticket['vehicle_make'] . ' ' . $ticket['vehicle_model'] . ' (' . $ticket['vehicle_reg'] . ')'); ?></p>
                                                            <p><strong>Priority:</strong> <?php echo ucfirst($ticket['priority']); ?></p>
                                                            <p><strong>Status:</strong> <?php echo ucfirst(str_replace('_', ' ', $ticket['status'])); ?></p>
                                                            <p><strong>Created:</strong> <?php echo date('M d, Y H:i', strtotime($ticket['created_at'])); ?></p>
                                                            <p><strong>Description:</strong></p>
                                                            <p><?php echo nl2br(htmlspecialchars($ticket['description'])); ?></p>
                                                        </div>
                                                        
                                                        <!-- Replies Section -->
                                                        <div class="mb-4">
                                                            <h6>Replies</h6>
                                                            <?php 
                                                            $replies = $supportObj->getSupportReplies($ticket['id']);
                                                            if ($replies): 
                                                                foreach ($replies as $reply): 
                                                            ?>
                                                                <div class="card mb-2">
                                                                    <div class="card-body">
                                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                                            <strong><?php echo htmlspecialchars($reply['first_name'] . ' ' . $reply['last_name']); ?></strong>
                                                                            <small class="text-muted"><?php echo date('M d, Y H:i', strtotime($reply['created_at'])); ?></small>
                                                                        </div>
                                                                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($reply['message'])); ?></p>
                        </div>
                    </div>
                                                            <?php 
                                                                endforeach;
                                                            else: 
                                                            ?>
                                                                <p class="text-muted">No replies yet.</p>
                                                            <?php endif; ?>
                </div>
                
                <!-- Reply Form -->
                                                        <form method="POST" action="">
                        <input type="hidden" name="action" value="add_reply">
                                                            <input type="hidden" name="request_id" value="<?php echo $ticket['id']; ?>">
                        
                        <div class="mb-3">
                                                                <label for="message<?php echo $ticket['id']; ?>" class="form-label">Add Reply</label>
                                                                <textarea class="form-control" id="message<?php echo $ticket['id']; ?>" name="message" rows="3" required></textarea>
                        </div>
                        
                                                            <button type="submit" class="btn btn-primary">Send Reply</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once("includes/footer.php"); ?>
