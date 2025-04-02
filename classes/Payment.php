<?php
/**
 * Payment class for handling payment operations
 */
class Payment {
    private $db;
    
    /**
     * Constructor - initialize database connection
     */
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Get all payments for a specific user
     * 
     * @param int $user_id User ID
     * @return array|null Array of payments or null if none found
     */
    public function getUserPayments($user_id) {
        $sql = "SELECT p.*, 
                v.make as vehicle_make,
                v.model as vehicle_model,
                v.reg_number as vehicle_reg
                FROM payments p
                LEFT JOIN protection_plans pp ON p.id = pp.id
                LEFT JOIN vehicles v ON v.id = p.id
                WHERE v.user_id = ?
                ORDER BY p.payment_date DESC";
                
        $result = $this->db->prepareAndExecute($sql, "i", [$user_id]);
        
        if ($result && $result->num_rows > 0) {
            $payments = [];
            while ($row = $result->fetch_assoc()) {
                $payments[] = $row;
            }
            return $payments;
        }
        
        return null;
    }
    
    /**
     * Get a single payment by ID
     * 
     * @param int $payment_id Payment ID
     * @param int $user_id User ID for verification
     * @return array|null Payment details or null if not found
     */
    public function getPaymentById($payment_id, $user_id) {
        $sql = "SELECT p.*, 
                pp.plan_name,
                v.reg_number as vehicle_reg
                FROM payments p
                LEFT JOIN protection_plans pp ON p.plan_id = pp.id
                LEFT JOIN vehicles v ON pp.vehicle_id = v.id
                WHERE p.id = ? AND p.user_id = ?";
                
        $result = $this->db->prepareAndExecute($sql, "ii", [$payment_id, $user_id]);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Create a new payment record
     * 
     * @param array $payment_data Payment data including user_id, plan_id, amount, etc.
     * @return bool|int False on failure, payment ID on success
     */
    public function createPayment($payment_data) {
        $sql = "INSERT INTO payments (
                    user_id, plan_id, amount, payment_date, 
                    reference_no, status, description, 
                    created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
                
        $params = [
            $payment_data['user_id'],
            $payment_data['plan_id'],
            $payment_data['amount'],
            $payment_data['payment_date'],
            $payment_data['reference_no'],
            $payment_data['status'],
            $payment_data['description']
        ];
        
        $result = $this->db->prepareAndExecute($sql, "iidssss", $params);
        
        if ($result) {
            return $this->db->getLastInsertId();
        }
        
        return false;
    }
    
    /**
     * Update payment status
     * 
     * @param int $payment_id Payment ID
     * @param string $status New status
     * @param int $user_id User ID for verification
     * @return bool True on success, false on failure
     */
    public function updatePaymentStatus($payment_id, $status, $user_id) {
        $sql = "UPDATE payments 
                SET status = ?, updated_at = NOW() 
                WHERE id = ? AND user_id = ?";
                
        $result = $this->db->prepareAndExecute($sql, "sii", [$status, $payment_id, $user_id]);
        
        return $result ? true : false;
    }
} 