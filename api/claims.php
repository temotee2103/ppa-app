<?php
/**
 * Claims API
 * Handles claim submission, retrieval, and status updates
 */

// Include initialization file
require_once '../init.php';

// Set content type to JSON
header('Content-Type: application/json');

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Get action from request
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Initialize User and Claim classes
$user = User::getInstance();
$claim = new Claim($db);
$vehicle = new Vehicle($db);

// Check if user is logged in
if (!$user->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

// Get current user
$currentUser = $user->getCurrentUser();

// Handle different actions
switch ($action) {
    case 'submit':
        handleSubmitClaim();
        break;
        
    case 'get_user_claims':
        getUserClaims();
        break;
        
    case 'get_claim_details':
        getClaimDetails();
        break;
        
    case 'add_note':
        addClaimNote();
        break;
        
    case 'get_eligible_vehicles':
        getEligibleVehicles();
        break;
        
    default:
        // Invalid action
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

/**
 * Handle claim submission
 */
function handleSubmitClaim() {
    global $claim, $currentUser;
    
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $required_fields = ['vehicle_id', 'workshop_id', 'issue_type', 'issue_description', 'mileage', 'issue_date'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
            return;
        }
    }
    
    // Prepare claim data
    $claim_data = [
        'user_id' => $currentUser['id'],
        'vehicle_id' => $data['vehicle_id'],
        'workshop_id' => $data['workshop_id'],
        'issue_type' => $data['issue_type'],
        'issue_description' => $data['issue_description'],
        'mileage' => $data['mileage'],
        'issue_date' => $data['issue_date']
    ];
    
    // Submit claim
    $claim_id = $claim->submitClaim($claim_data);
    
    if ($claim_id) {
        echo json_encode([
            'success' => true, 
            'message' => 'Claim submitted successfully',
            'claim_id' => $claim_id
        ]);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Failed to submit claim']);
    }
}

/**
 * Get claims for current user
 */
function getUserClaims() {
    global $claim, $currentUser;
    
    // Get user claims
    $user_claims = $claim->getUserClaims($currentUser['id']);
    
    echo json_encode([
        'success' => true,
        'claims' => $user_claims ?: []
    ]);
}

/**
 * Get details for a specific claim
 */
function getClaimDetails() {
    global $claim, $currentUser;
    
    // Get claim ID from request
    $claim_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if (!$claim_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Claim ID is required']);
        return;
    }
    
    // Get claim details
    $claim_details = $claim->getClaimById($claim_id, $currentUser['id']);
    
    if (!$claim_details) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Claim not found']);
        return;
    }
    
    // Get claim timeline
    $timeline = $claim->getClaimTimeline($claim_id);
    
    // Get claim notes
    $notes = $claim->getClaimNotes($claim_id);
    
    echo json_encode([
        'success' => true,
        'claim' => $claim_details,
        'timeline' => $timeline ?: [],
        'notes' => $notes ?: []
    ]);
}

/**
 * Add a note to a claim
 */
function addClaimNote() {
    global $claim, $currentUser;
    
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    if (!isset($data['claim_id']) || !isset($data['note']) || empty($data['note'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Claim ID and note are required']);
        return;
    }
    
    // Add note
    $result = $claim->addClaimNote($data['claim_id'], $data['note'], $currentUser['id']);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Note added successfully']);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Failed to add note']);
    }
}

/**
 * Get vehicles eligible for claims
 */
function getEligibleVehicles() {
    global $vehicle, $currentUser;
    
    // Get eligible vehicles
    $eligible_vehicles = $vehicle->getEligibleVehicles($currentUser['id']);
    
    echo json_encode([
        'success' => true,
        'vehicles' => $eligible_vehicles ?: []
    ]);
} 