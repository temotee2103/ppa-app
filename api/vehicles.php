<?php
/**
 * Vehicles API
 * Handles vehicle addition, updating, and retrieval
 */

// Include initialization file
require_once '../init.php';

// Set content type to JSON
header('Content-Type: application/json');

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Get action from request
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Initialize User and Vehicle classes
$user = User::getInstance();
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
    case 'add':
        handleAddVehicle();
        break;
        
    case 'update':
        handleUpdateVehicle();
        break;
        
    case 'delete':
        handleDeleteVehicle();
        break;
        
    case 'get_user_vehicles':
        getUserVehicles();
        break;
        
    case 'get_vehicle_details':
        getVehicleDetails();
        break;
        
    case 'update_mileage':
        updateVehicleMileage();
        break;
        
    default:
        // Invalid action
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

/**
 * Handle adding a new vehicle
 */
function handleAddVehicle() {
    global $vehicle, $currentUser;
    
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $required_fields = ['make', 'model', 'year', 'reg_number'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
            return;
        }
    }
    
    // Prepare vehicle data
    $vehicle_data = [
        'user_id' => $currentUser['id'],
        'make' => $data['make'],
        'model' => $data['model'],
        'year' => $data['year'],
        'reg_number' => $data['reg_number'],
        'engine_no' => $data['engine_no'] ?? '',
        'chassis_no' => $data['chassis_no'] ?? '',
        'color' => $data['color'] ?? '',
        'mileage' => $data['mileage'] ?? 0
    ];
    
    // Add vehicle
    $vehicle_id = $vehicle->addVehicle($vehicle_data);
    
    if ($vehicle_id) {
        echo json_encode([
            'success' => true, 
            'message' => 'Vehicle added successfully',
            'vehicle_id' => $vehicle_id
        ]);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Failed to add vehicle. The registration number may already be in use.']);
    }
}

/**
 * Handle updating a vehicle
 */
function handleUpdateVehicle() {
    global $vehicle, $currentUser;
    
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    if (!isset($data['vehicle_id']) || empty($data['vehicle_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Vehicle ID is required']);
        return;
    }
    
    $required_fields = ['make', 'model', 'year', 'reg_number'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
            return;
        }
    }
    
    // Prepare vehicle data
    $vehicle_data = [
        'make' => $data['make'],
        'model' => $data['model'],
        'year' => $data['year'],
        'reg_number' => $data['reg_number'],
        'engine_no' => $data['engine_no'] ?? '',
        'chassis_no' => $data['chassis_no'] ?? '',
        'color' => $data['color'] ?? '',
        'mileage' => $data['mileage'] ?? 0
    ];
    
    // Update vehicle
    $result = $vehicle->updateVehicle($data['vehicle_id'], $vehicle_data, $currentUser['id']);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Vehicle updated successfully']);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Failed to update vehicle']);
    }
}

/**
 * Handle deleting a vehicle
 */
function handleDeleteVehicle() {
    global $vehicle, $currentUser;
    
    // Get vehicle ID from request
    $vehicle_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if (!$vehicle_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Vehicle ID is required']);
        return;
    }
    
    // Delete vehicle
    $result = $vehicle->deleteVehicle($vehicle_id, $currentUser['id']);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Vehicle deleted successfully']);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Failed to delete vehicle. It may be associated with claims or protection plans.']);
    }
}

/**
 * Get vehicles for current user
 */
function getUserVehicles() {
    global $vehicle, $currentUser;
    
    // Get user vehicles
    $user_vehicles = $vehicle->getUserVehicles($currentUser['id']);
    
    echo json_encode([
        'success' => true,
        'vehicles' => $user_vehicles ?: []
    ]);
}

/**
 * Get details for a specific vehicle
 */
function getVehicleDetails() {
    global $vehicle, $currentUser;
    
    // Get vehicle ID from request
    $vehicle_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if (!$vehicle_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Vehicle ID is required']);
        return;
    }
    
    // Get vehicle details
    $vehicle_details = $vehicle->getVehicleById($vehicle_id, $currentUser['id']);
    
    if (!$vehicle_details) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Vehicle not found']);
        return;
    }
    
    echo json_encode([
        'success' => true,
        'vehicle' => $vehicle_details
    ]);
}

/**
 * Update vehicle mileage
 */
function updateVehicleMileage() {
    global $vehicle, $currentUser;
    
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    if (!isset($data['vehicle_id']) || empty($data['vehicle_id']) || !isset($data['mileage']) || $data['mileage'] < 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Vehicle ID and valid mileage are required']);
        return;
    }
    
    // Update mileage
    $result = $vehicle->updateMileage($data['vehicle_id'], $data['mileage'], $currentUser['id']);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Vehicle mileage updated successfully']);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Failed to update vehicle mileage']);
    }
} 