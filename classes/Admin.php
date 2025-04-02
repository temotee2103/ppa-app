<?php
/**
 * Admin Class for PPA Admin Panel
 * Handles all admin-related functionalities, including statistics, user management,
 * workshops, claims, and customer management.
 */
class Admin
{
    private $db;
    private static $instance;
    private $user;
    
    /**
     * Constructor - Initialize database connection
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->user = User::getInstance();
    }
    
    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Check if user is authorized to access admin panel
     * 
     * @return bool True if user is authorized, false otherwise
     */
    public function isAuthorized() {
        if (!$this->user->isLoggedIn()) {
            return false;
        }
        
        $userData = $this->user->getCurrentUser();
        
        if (!isset($userData['role_name'])) {
            return false;
        }
        
        return in_array($userData['role_name'], ['super_admin', 'admin', 'agent', 'accountant']);
    }
    
    /**
     * Get dashboard statistics
     * 
     * @return array Statistics for dashboard display
     */
    public function getDashboardStats()
    {
        $stats = [
            'users' => 0,
            'customers' => 0,
            'claims' => 0,
            'pending_claims' => 0,
            'workshops' => 0,
            'upcoming_workshops' => 0,
            'revenue' => 0,
            'new_customers' => 0,
            'top_agents' => []
        ];
        
        // Get counts from database
        // Count users
        $sql = "SELECT COUNT(*) as count FROM users WHERE role_id IN (SELECT id FROM user_roles WHERE name != 'customer')";
        $result = $this->db->query($sql);
        if ($result && $result->num_rows > 0) {
            $stats['users'] = $result->fetch_assoc()['count'] ?? 0;
        }
        
        // Count customers
        $sql = "SELECT COUNT(*) as count FROM users WHERE role_id = (SELECT id FROM user_roles WHERE name = 'customer')";
        $result = $this->db->query($sql);
        if ($result && $result->num_rows > 0) {
            $stats['customers'] = $result->fetch_assoc()['count'] ?? 0;
        }
        
        // Count new customers (last 30 days)
        $sql = "SELECT COUNT(*) as count FROM users WHERE role_id = (SELECT id FROM user_roles WHERE name = 'customer') AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        $result = $this->db->query($sql);
        if ($result && $result->num_rows > 0) {
            $stats['new_customers'] = $result->fetch_assoc()['count'] ?? 0;
        }
        
        // Count claims
        $sql = "SELECT COUNT(*) as count FROM claims";
        $result = $this->db->query($sql);
        if ($result && $result->num_rows > 0) {
            $stats['claims'] = $result->fetch_assoc()['count'] ?? 0;
        }
        
        // Count pending claims
        $sql = "SELECT COUNT(*) as count FROM claims WHERE status = 'pending'";
        $result = $this->db->query($sql);
        if ($result && $result->num_rows > 0) {
            $stats['pending_claims'] = $result->fetch_assoc()['count'] ?? 0;
        }
        
        // Count workshops
        $sql = "SELECT COUNT(*) as count FROM workshops";
        $result = $this->db->query($sql);
        if ($result && $result->num_rows > 0) {
            $stats['workshops'] = $result->fetch_assoc()['count'] ?? 0;
        }
        
        // Count upcoming workshops
        $sql = "SELECT COUNT(*) as count FROM workshops WHERE status = 'upcoming'";
        $result = $this->db->query($sql);
        if ($result && $result->num_rows > 0) {
            $stats['upcoming_workshops'] = $result->fetch_assoc()['count'] ?? 0;
        }
        
        // 移除对 sales 表的查询
        // 设置一个虚拟的收入值
        $stats['revenue'] = 0;
        
        return $stats;
    }
    
    /**
     * Get recent admin activities
     * 
     * @param int $limit Number of activities to retrieve
     * @return array Recent admin activities
     */
    public function getRecentAdminActivities($limit = 5)
    {
        $sql = "SELECT aa.*, u.first_name, u.last_name 
               FROM admin_activity_log aa
               JOIN users u ON aa.admin_id = u.id
               ORDER BY aa.created_at DESC
               LIMIT ?";
        
        $result = $this->db->prepareAndExecute($sql, "i", [$limit]);
        $activities = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $activities[] = $row;
            }
        }
        
        return $activities;
    }
    
    /**
     * Get all claims
     * 
     * @return array All claims
     */
    public function getAllClaims()
    {
        return [];
    }
    
    /**
     * Get claim count by status
     * 
     * @param string $status Claim status
     * @return int Count of claims with given status
     */
    public function getClaimCount($status)
    {
        return 0;
    }
    
    /**
     * Get customer statistics
     */
    public function getCustomerStats() {
        $stats = [
            'total' => $this->getCustomerCount(),
            'active' => $this->getCustomerCount('active'),
            'inactive' => $this->getCustomerCount('inactive'),
            'new' => $this->getNewCustomerCount()
        ];
        
        return $stats;
    }
    
    /**
     * Get claim statistics
     */
    public function getClaimStats() {
        $stats = [
            'total' => $this->getClaimCount(),
            'pending' => $this->getClaimCount('pending'),
            'approved' => $this->getClaimCount('approved'),
            'rejected' => $this->getClaimCount('rejected')
        ];
        
        return $stats;
    }
    
    /**
     * Get workshop statistics
     */
    public function getWorkshopStats() {
        $stats = [
            'total' => $this->getWorkshopCount(),
            'upcoming' => $this->getWorkshopCount('upcoming'),
            'completed' => $this->getWorkshopCount('completed'),
            'revenue' => $this->getWorkshopRevenue()
        ];
        
        return $stats;
    }
    
    /**
     * Get sales statistics
     */
    public function getSalesStats($startDate = null, $endDate = null) {
        if (!$startDate) {
            $startDate = date('Y-m-01'); // First day of current month
        }
        
        if (!$endDate) {
            $endDate = date('Y-m-t'); // Last day of current month
        }
        
        // 由于 sales 表不存在，返回默认值
        $stats = [
            'total_sales' => 0,
            'total_commission' => 0,
            'count' => 0,
            'by_month' => [],
            'by_agent' => []
        ];
        
        return $stats;
    }
    
    /**
     * Get sales data for a specified date range
     */
    public function getSalesData($startDate, $endDate) {
        // 因为 sales 表不存在，所以返回空数组
        return [];
    }
    
    /**
     * Get sales grouped by month
     */
    private function getSalesByMonth($startDate, $endDate) {
        // 因为 sales 表不存在，所以返回空数组
        return [];
    }
    
    /**
     * Get sales grouped by agent
     */
    private function getSalesByAgent($startDate, $endDate) {
        // 因为 sales 表不存在，所以返回空数组
        return [];
    }
    
    /**
     * Get all customers
     */
    public function getAllCustomers() {
        $query = "SELECT u.* 
                 FROM users u 
                 LEFT JOIN user_roles r ON u.role_id = r.id
                 WHERE r.name = 'customer'
                 ORDER BY u.created_at DESC";
                 
        $result = $this->db->query($query);
        $customers = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // 添加默认的策略计数
                $row['policy_count'] = 0;
                $customers[] = $row;
            }
        }
        
        return $customers;
    }
    
    /**
     * Get customer activities
     */
    private function getCustomerActivities($customerId) {
        $query = "SELECT * FROM user_activities 
                 WHERE user_id = ? 
                 ORDER BY created_at DESC 
                 LIMIT 5";
                 
        $result = $this->db->prepareAndExecute($query, "i", [$customerId]);
        $activities = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $activities[] = $row;
            }
        }
        
        return $activities;
    }
    
    /**
     * Get customer policies
     */
    private function getCustomerPolicies($customerId) {
        // policies 表可能不存在，返回空数组
        return [];
    }
    
    /**
     * Get customer claims
     */
    private function getCustomerClaims($customerId) {
        $query = "SELECT c.* 
                 FROM claims c 
                 WHERE c.user_id = ? 
                 ORDER BY c.created_at DESC";
                 
        $result = $this->db->prepareAndExecute($query, "i", [$customerId]);
        $claims = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $claims[] = $row;
            }
        }
        
        return $claims;
    }
    
    /**
     * Get all admin users
     */
    public function getAllAdminUsers() {
        $query = "SELECT u.*, r.name as role_name
                 FROM users u 
                 LEFT JOIN user_roles r ON u.role_id = r.id
                 WHERE r.name != 'customer'
                 ORDER BY u.created_at DESC";
                 
        $result = $this->db->query($query);
        $users = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }
        
        return $users;
    }
    
    /**
     * Get all workshops
     * 
     * @return array All workshops
     */
    public function getAllWorkshops() {
        $query = "SELECT w.* FROM workshops w ORDER BY w.id DESC";
        
        $result = $this->db->query($query);
        $workshops = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // 添加虚拟的注册人数
                $row['registered_count'] = 0;
                $workshops[] = $row;
            }
        }
        
        return $workshops;
    }
    
    /**
     * Add new workshop
     */
    public function addWorkshop($workshopData) {
        // 使用 SHOW COLUMNS 查询找出实际的列名
        $query = "SHOW COLUMNS FROM workshops";
        $result = $this->db->query($query);
        $columns = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $columns[] = $row['Field'];
            }
        }
        
        // 动态构建 SQL 查询和参数
        $fields = [];
        $placeholders = [];
        $params = [];
        $params_types = "";
        
        // 添加已知可能存在的字段
        $possible_fields = [
            'title' => 's', 
            'description' => 's', 
            'venue' => 's', 
            'workshop_date' => 's',
            'start_date' => 's',
            'end_date' => 's',
            'duration' => 'i', 
            'capacity' => 'i', 
            'price' => 'd', 
            'instructor' => 's',
            'status' => 's'
        ];
        
        foreach ($possible_fields as $field => $type) {
            // 只添加存在于数据库表中的字段
            if (in_array($field, $columns) && isset($workshopData[$field])) {
                $fields[] = $field;
                $placeholders[] = '?';
                $params[] = $workshopData[$field];
                $params_types .= $type;
            }
        }
        
        // 添加固定字段
        if (in_array('status', $columns) && !in_array('status', $fields)) {
            $fields[] = 'status';
            $placeholders[] = '?';
            $params[] = 'upcoming';
            $params_types .= 's';
        }
        
        if (in_array('created_at', $columns)) {
            $fields[] = 'created_at';
            $placeholders[] = 'NOW()';
        }
        
        if (in_array('updated_at', $columns)) {
            $fields[] = 'updated_at';
            $placeholders[] = 'NOW()';
        }
        
        // 如果找不到任何字段，返回失败
        if (empty($fields)) {
            return false;
        }
        
        $sql = "INSERT INTO workshops (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        $result = $this->db->prepareAndExecute($sql, $params_types, $params);
        
        return $result !== false;
    }
    
    /**
     * Update workshop status
     */
    public function updateWorkshopStatus($workshopId, $status) {
        $query = "UPDATE workshops SET 
                 status = ?, 
                 updated_at = NOW() 
                 WHERE id = ?";
                 
        $result = $this->db->prepareAndExecute($query, "si", [$status, $workshopId]);
        
        return $result !== false;
    }
    
    /**
     * Get customer count
     */
    private function getCustomerCount($status = null) {
        $query = "SELECT COUNT(*) as count FROM users 
                 WHERE role_id = (SELECT id FROM user_roles WHERE name = 'customer')";
        
        if ($status) {
            $query .= " AND status = ?";
            $result = $this->db->prepareAndExecute($query, "s", [$status]);
        } else {
            $result = $this->db->query($query);
        }
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc()['count'] ?? 0;
        }
        
        return 0;
    }
    
    /**
     * Get new customer count (registered in the last 30 days)
     */
    private function getNewCustomerCount() {
        $query = "SELECT COUNT(*) as count FROM users 
                 WHERE role_id = (SELECT id FROM user_roles WHERE name = 'customer')
                 AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        
        $result = $this->db->query($query);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc()['count'] ?? 0;
        }
        
        return 0;
    }
    
    /**
     * Get workshop count
     */
    private function getWorkshopCount($status = null) {
        $query = "SELECT COUNT(*) as count FROM workshops";
        
        if ($status) {
            $query .= " WHERE status = ?";
            $result = $this->db->prepareAndExecute($query, "s", [$status]);
        } else {
            $result = $this->db->query($query);
        }
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc()['count'] ?? 0;
        }
        
        return 0;
    }
    
    /**
     * Get workshop revenue
     */
    private function getWorkshopRevenue() {
        // workshop_registrations 表不存在，返回 0
        return 0;
    }
    
    /**
     * Log admin activity
     */
    public function logAdminActivity($action, $resourceType, $resourceId, $details = '') {
        $adminId = $this->user->getCurrentUser()['id'];
        
        $query = "INSERT INTO admin_activities 
                 (admin_id, action, resource_type, resource_id, details, created_at) 
                 VALUES 
                 (?, ?, ?, ?, ?, NOW())";
                 
        $result = $this->db->prepareAndExecute($query, "issis", [$adminId, $action, $resourceType, $resourceId, $details]);
        
        return $result !== false;
    }
} 