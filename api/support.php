<?php
/**
 * Support API
 * Handles support ticket actions
 */

require_once '../init.php';

// Check if user is logged in
$user = User::getInstance();
if (!$user->isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$currentUser = $user->getCurrentUser();
$supportObj = new Support($db);

// Process API requests
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'get_ticket':
        getTicketDetails();
        break;
    
    case 'update_status':
        updateTicketStatus();
        break;
    
    case 'get_categories':
        getCategories();
        break;
    
    default:
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
}

/**
 * Get ticket details
 */
function getTicketDetails() {
    global $supportObj, $currentUser;
    
    // Check if ticket ID is provided
    if (!isset($_GET['ticket_id']) || !is_numeric($_GET['ticket_id'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid ticket ID']);
        exit;
    }
    
    $ticketId = (int)$_GET['ticket_id'];
    
    // Get ticket details
    $ticket = $supportObj->getTicketDetails($ticketId);
    
    if (!$ticket) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Ticket not found']);
        exit;
    }
    
    // Check if user is allowed to view this ticket
    if ($currentUser['role_name'] !== 'admin' && $currentUser['role_name'] !== 'staff' && $ticket['user_id'] != $currentUser['id']) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'You do not have permission to view this ticket']);
        exit;
    }
    
    // Return ticket details
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'ticket' => $ticket]);
    exit;
}

/**
 * Update ticket status
 */
function updateTicketStatus() {
    global $supportObj, $currentUser;
    
    // Only admins and staff can update ticket status
    if ($currentUser['role_name'] !== 'admin' && $currentUser['role_name'] !== 'staff') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'You do not have permission to update ticket status']);
        exit;
    }
    
    // Check if ticket ID and status are provided
    if (!isset($_POST['ticket_id']) || !is_numeric($_POST['ticket_id']) || !isset($_POST['status'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
        exit;
    }
    
    $ticketId = (int)$_POST['ticket_id'];
    $status = $_POST['status'];
    
    // Validate status
    $validStatuses = ['open', 'in_progress', 'closed'];
    if (!in_array($status, $validStatuses)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        exit;
    }
    
    // Update ticket status
    $updated = $supportObj->updateTicketStatus($ticketId, $status);
    
    if (!$updated) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Failed to update ticket status']);
        exit;
    }
    
    // Return success
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Ticket status updated successfully']);
    exit;
}

/**
 * Get support categories
 */
function getCategories() {
    global $supportObj;
    
    // Get categories
    $categories = $supportObj->getCategories();
    
    if (!$categories) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Failed to get categories']);
        exit;
    }
    
    // Return categories
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'categories' => $categories]);
    exit;
}
?> 