<?php
// Get customer policies Ajax handler
require_once '../../init.php';
require_once '../classes/Admin.php';

// 设置适当的内容类型头
header('Content-Type: application/json');

try {
    // Check if user is logged in
    $user = User::getInstance();
    if (!$user->isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized access', 'error_code' => 'auth_required']);
        exit;
    }

    // Check for appropriate role - 使用更宽松的角色检查以防止错误
    $currentUser = $user->getCurrentUser();
    $role = $currentUser['role_name'] ?? '';
    
    if (!in_array($role, ['super_admin', 'admin', 'agent', 'accountant'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Insufficient permissions', 'error_code' => 'permission_denied']);
        exit;
    }

    // Get customer ID from request
    $customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;

    if ($customer_id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid customer ID', 'error_code' => 'invalid_id']);
        exit;
    }

    // Get admin instance
    $admin = Admin\Admin::getInstance();

    // Get customer information
    $customer = $admin->getCustomerById($customer_id);

    if (!$customer) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Customer not found', 'error_code' => 'not_found']);
        exit;
    }

    // Get policies for this customer
    $policies = $admin->getCustomerPolicies($customer_id);

    // Return success response with customer and policies data
    echo json_encode([
        'success' => true, 
        'customer' => $customer,
        'policies' => $policies
    ]);
} catch (Exception $e) {
    // 捕获任何可能的异常并返回适当的错误响应
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Server error: ' . $e->getMessage(),
        'error_code' => 'server_error'
    ]);
}
exit;
?> 