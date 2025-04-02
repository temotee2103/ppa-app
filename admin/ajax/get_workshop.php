<?php
// Get workshop details Ajax handler
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
    $role = $currentUser['role'] ?? '';
    
    if (!in_array($role, ['super_admin', 'admin', 'agent', 'manager'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Insufficient permissions', 'error_code' => 'permission_denied']);
        exit;
    }

    // Get workshop ID from request
    $workshop_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($workshop_id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid workshop ID', 'error_code' => 'invalid_id']);
        exit;
    }

    // Get admin instance
    $admin = Admin\Admin::getInstance();

    // Get workshop details
    $workshop = $admin->getWorkshopById($workshop_id);

    if (!$workshop) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Workshop not found', 'error_code' => 'not_found']);
        exit;
    }

    // Get participants for this workshop
    $participants = $admin->getWorkshopParticipants($workshop_id);

    // Add participants to the workshop data
    $workshop['participants'] = $participants;

    // Return success response with workshop data
    echo json_encode(['success' => true, 'data' => $workshop]);
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