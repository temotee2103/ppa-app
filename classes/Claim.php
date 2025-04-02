<?php
/**
 * Claim class for handling vehicle claims
 */
class Claim {
    private $db;
    
    /**
     * Constructor - initialize database connection
     */
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Get claims for a specific user
     * 
     * @param int $user_id User ID
     * @return array|null Array of claims or null if no claims found
     */
    public function getUserClaims($user_id) {
        // 检查claims表中是否存在vehicle_id列
        $checkVehicleIdColumn = $this->db->prepareAndExecute("SHOW COLUMNS FROM claims LIKE 'vehicle_id'", "", []);
        $hasVehicleIdColumn = $checkVehicleIdColumn && $checkVehicleIdColumn->num_rows > 0;
        
        // 检查claims表中是否存在user_id列
        $checkUserIdColumn = $this->db->prepareAndExecute("SHOW COLUMNS FROM claims LIKE 'user_id'", "", []);
        $hasUserIdColumn = $checkUserIdColumn && $checkUserIdColumn->num_rows > 0;
        
        if ($hasVehicleIdColumn && $hasUserIdColumn) {
            // 如果两个列都存在，使用正常的JOIN
            $sql = "SELECT c.*, v.make, v.model, v.year, v.reg_number, w.name as workshop_name,
                    CONCAT(v.make, ' ', v.model, ' (', v.reg_number, ')') as vehicle_name
                    FROM claims c
                    LEFT JOIN vehicles v ON c.vehicle_id = v.id
                    LEFT JOIN workshops w ON c.workshop_id = w.id
                    WHERE c.user_id = ?
                    ORDER BY c.created_at DESC";
        } else if ($hasUserIdColumn) {
            // 如果只有user_id列存在
            $sql = "SELECT c.*, w.name as workshop_name
                    FROM claims c
                    LEFT JOIN workshops w ON c.workshop_id = w.id
                    WHERE c.user_id = ?
                    ORDER BY c.created_at DESC";
        } else {
            // 如果都不存在，返回空结果
            return null;
        }
                
        $result = $this->db->prepareAndExecute($sql, "i", [$user_id]);
        
        if ($result && $result->num_rows > 0) {
            $claims = [];
            while ($row = $result->fetch_assoc()) {
                $claims[] = $row;
            }
            return $claims;
        }
        
        return null;
    }
    
    /**
     * Get a single claim by ID
     * 
     * @param int $claim_id Claim ID
     * @param int $user_id User ID for verification
     * @return array|null Claim details or null if not found
     */
    public function getClaimById($claim_id, $user_id) {
        $sql = "SELECT c.*, v.make, v.model, v.year, v.reg_number, w.name as workshop_name, w.address as workshop_address
                FROM claims c
                LEFT JOIN vehicles v ON c.vehicle_id = v.id
                LEFT JOIN workshops w ON c.workshop_id = w.id
                WHERE c.id = ? AND c.user_id = ?";
                
        $result = $this->db->prepareAndExecute($sql, "ii", [$claim_id, $user_id]);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Get claim timeline events
     * 
     * @param int $claim_id Claim ID
     * @return array|null Timeline events or null if none found
     */
    public function getClaimTimeline($claim_id) {
        $sql = "SELECT * FROM claim_timeline 
                WHERE claim_id = ? 
                ORDER BY event_date ASC";
                
        $result = $this->db->prepareAndExecute($sql, "i", [$claim_id]);
        
        if ($result && $result->num_rows > 0) {
            $timeline = [];
            while ($row = $result->fetch_assoc()) {
                $timeline[] = $row;
            }
            return $timeline;
        }
        
        return null;
    }
    
    /**
     * Submit a new claim
     * 
     * @param array $claim_data Claim data including user_id, vehicle_id, workshop_id, issue_type, description, etc.
     * @return bool|int False on failure, claim ID on success
     */
    public function submitClaim($claim_data) {
        // Validate required fields
        $required_fields = ['user_id', 'vehicle_id', 'workshop_id', 'issue_type', 'issue_description', 'mileage', 'issue_date'];
        foreach ($required_fields as $field) {
            if (!isset($claim_data[$field]) || empty($claim_data[$field])) {
                return false;
            }
        }
        
        // Begin transaction
        $this->db->beginTransaction();
        
        try {
            // Insert claim
            $sql = "INSERT INTO claims (user_id, vehicle_id, workshop_id, issue_type, issue_description, 
                   mileage, issue_date, status, created_at) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
                   
            $params = [
                $claim_data['user_id'],
                $claim_data['vehicle_id'],
                $claim_data['workshop_id'],
                $claim_data['issue_type'],
                $claim_data['issue_description'],
                $claim_data['mileage'],
                $claim_data['issue_date']
            ];
            
            $result = $this->db->prepareAndExecute($sql, "iiissss", $params);
            
            if (!$result) {
                $this->db->rollback();
                return false;
            }
            
            $claim_id = $this->db->getLastInsertId();
            
            // Add initial timeline event
            $sql = "INSERT INTO claim_timeline (claim_id, status, event_date, description) 
                   VALUES (?, 'submitted', NOW(), 'Claim submitted successfully')";
                   
            $result = $this->db->prepareAndExecute($sql, "i", [$claim_id]);
            
            if (!$result) {
                $this->db->rollback();
                return false;
            }
            
            // Commit transaction
            $this->db->commit();
            
            return $claim_id;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    
    /**
     * Update claim status
     * 
     * @param int $claim_id Claim ID
     * @param string $status New status
     * @param string $description Status change description
     * @param int $user_id User ID for verification (admin or owner)
     * @return bool True on success, false on failure
     */
    public function updateClaimStatus($claim_id, $status, $description, $user_id) {
        // Verify user owns the claim or is admin
        $sql = "SELECT user_id FROM claims WHERE id = ?";
        $result = $this->db->prepareAndExecute($sql, "i", [$claim_id]);
        
        if (!$result || $result->num_rows === 0) {
            return false;
        }
        
        $claim = $result->fetch_assoc();
        
        // Check if user is claim owner or admin (implementation may vary)
        if ($claim['user_id'] !== $user_id && !$this->isAdmin($user_id)) {
            return false;
        }
        
        // Begin transaction
        $this->db->beginTransaction();
        
        try {
            // Update claim status
            $sql = "UPDATE claims SET status = ?, updated_at = NOW() WHERE id = ?";
            $result = $this->db->prepareAndExecute($sql, "si", [$status, $claim_id]);
            
            if (!$result) {
                $this->db->rollback();
                return false;
            }
            
            // Add timeline event
            $sql = "INSERT INTO claim_timeline (claim_id, status, event_date, description) 
                   VALUES (?, ?, NOW(), ?)";
                   
            $result = $this->db->prepareAndExecute($sql, "iss", [$claim_id, $status, $description]);
            
            if (!$result) {
                $this->db->rollback();
                return false;
            }
            
            // Commit transaction
            $this->db->commit();
            
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    
    /**
     * Check if user is admin (placeholder - implement according to your auth system)
     * 
     * @param int $user_id User ID
     * @return bool True if admin, false otherwise
     */
    private function isAdmin($user_id) {
        // This is a placeholder. Implement according to your authentication system
        return false;
    }
    
    /**
     * Add a note to a claim
     * 
     * @param int $claim_id Claim ID
     * @param string $note Note content
     * @param int $user_id User adding the note
     * @return bool True on success, false on failure
     */
    public function addClaimNote($claim_id, $note, $user_id) {
        $sql = "INSERT INTO claim_notes (claim_id, user_id, note, created_at) 
               VALUES (?, ?, ?, NOW())";
               
        $result = $this->db->prepareAndExecute($sql, "iis", [$claim_id, $user_id, $note]);
        
        return $result ? true : false;
    }
    
    /**
     * Get notes for a claim
     * 
     * @param int $claim_id Claim ID
     * @return array|null Notes or null if none found
     */
    public function getClaimNotes($claim_id) {
        $sql = "SELECT n.*, u.first_name, u.last_name 
                FROM claim_notes n
                JOIN users u ON n.user_id = u.id
                WHERE n.claim_id = ?
                ORDER BY n.created_at DESC";
                
        $result = $this->db->prepareAndExecute($sql, "i", [$claim_id]);
        
        if ($result && $result->num_rows > 0) {
            $notes = [];
            while ($row = $result->fetch_assoc()) {
                $notes[] = $row;
            }
            return $notes;
        }
        
        return null;
    }
} 