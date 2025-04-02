<?php
// Get claim details Ajax handler
require_once '../../init.php';
require_once '../classes/Admin.php';

// Check if user is logged in
$user = User::getInstance();
if (!$user->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check for appropriate role
if (!in_array($user->getCurrentUser()['role'], ['super_admin', 'admin', 'agent'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Insufficient permissions']);
    exit;
}

// Get claim ID from request
$claim_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($claim_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid claim ID']);
    exit;
}

// Get admin instance
$admin = Admin\Admin::getInstance();

// Get claim details
$claim = $admin->getClaimById($claim_id);

if (!$claim) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Claim not found']);
    exit;
}

// Get documents for this claim
$documents = $admin->getClaimDocuments($claim_id);

// Add documents to the claim data
$claim['documents'] = $documents;

// Return success response with claim data
echo json_encode(['success' => true, 'data' => $claim]);
exit;
?> 