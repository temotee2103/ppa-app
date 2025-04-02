<?php
// Get claim status Ajax handler
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

// Get claim status
$status = $admin->getClaimStatus($claim_id);

if ($status === false) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Claim not found']);
    exit;
}

// Return success response with status
echo json_encode(['success' => true, 'status' => $status]);
exit;
?> 