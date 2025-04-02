<?php
/**
 * Workshop class for managing workshops
 */
class Workshop {
    private $db;
    
    /**
     * Constructor - initialize database connection
     */
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Get all active workshops
     * 
     * @return array|null Array of active workshops or null if none found
     */
    public function getAllWorkshops() {
        $sql = "SELECT * FROM workshops WHERE status = 'active' ORDER BY name";
                
        $result = $this->db->prepareAndExecute($sql, "", []);
        
        if ($result && $result->num_rows > 0) {
            $workshops = [];
            while ($row = $result->fetch_assoc()) {
                $workshops[] = $row;
            }
            return $workshops;
        }
        
        return null;
    }
    
    /**
     * Get a workshop by ID
     * 
     * @param int $workshop_id Workshop ID
     * @return array|null Workshop details or null if not found
     */
    public function getWorkshopById($workshop_id) {
        $sql = "SELECT * FROM workshops WHERE id = ?";
                
        $result = $this->db->prepareAndExecute($sql, "i", [$workshop_id]);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Add a new workshop
     * 
     * @param array $workshop_data Workshop data including name, location, contact, etc.
     * @return bool|int False on failure, workshop ID on success
     */
    public function addWorkshop($workshop_data) {
        // Check if workshop with same name already exists
        $sql = "SELECT id FROM workshops WHERE name = ?";
        $result = $this->db->prepareAndExecute($sql, "s", [$workshop_data['name']]);
        
        if ($result && $result->num_rows > 0) {
            return false; // Workshop already exists
        }
        
        // Insert workshop
        $sql = "INSERT INTO workshops (name, location, contact_name, phone, email, address, status, created_at) 
               VALUES (?, ?, ?, ?, ?, ?, 'active', NOW())";
               
        $params = [
            $workshop_data['name'],
            $workshop_data['location'],
            $workshop_data['contact_name'] ?? '',
            $workshop_data['phone'] ?? '',
            $workshop_data['email'] ?? '',
            $workshop_data['address'] ?? ''
        ];
        
        $result = $this->db->prepareAndExecute($sql, "ssssss", $params);
        
        if ($result) {
            return $this->db->getLastInsertId();
        }
        
        return false;
    }
    
    /**
     * Update a workshop
     * 
     * @param int $workshop_id Workshop ID
     * @param array $workshop_data Updated workshop data
     * @return bool True on success, false on failure
     */
    public function updateWorkshop($workshop_id, $workshop_data) {
        // Check if workshop exists
        $sql = "SELECT id FROM workshops WHERE id = ?";
        $result = $this->db->prepareAndExecute($sql, "i", [$workshop_id]);
        
        if (!$result || $result->num_rows === 0) {
            return false;
        }
        
        // Update workshop
        $sql = "UPDATE workshops SET 
                name = ?, 
                location = ?, 
                contact_name = ?, 
                phone = ?, 
                email = ?, 
                address = ?, 
                updated_at = NOW() 
                WHERE id = ?";
                
        $params = [
            $workshop_data['name'],
            $workshop_data['location'],
            $workshop_data['contact_name'] ?? '',
            $workshop_data['phone'] ?? '',
            $workshop_data['email'] ?? '',
            $workshop_data['address'] ?? '',
            $workshop_id
        ];
        
        $result = $this->db->prepareAndExecute($sql, "ssssssi", $params);
        
        return $result ? true : false;
    }
    
    /**
     * Delete a workshop
     * 
     * @param int $workshop_id Workshop ID
     * @return bool True on success, false on failure
     */
    public function deleteWorkshop($workshop_id) {
        // Check if workshop is being used in any claims
        $sql = "SELECT COUNT(*) as claim_count FROM claims WHERE workshop_id = ?";
        $result = $this->db->prepareAndExecute($sql, "i", [$workshop_id]);
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['claim_count'] > 0) {
                return false; // Workshop is being used in claims and cannot be deleted
            }
        }
        
        // Delete workshop
        $sql = "DELETE FROM workshops WHERE id = ?";
        $result = $this->db->prepareAndExecute($sql, "i", [$workshop_id]);
        
        return $result ? true : false;
    }
    
    /**
     * Deactivate a workshop (set status to inactive)
     * 
     * @param int $workshop_id Workshop ID
     * @return bool True on success, false on failure
     */
    public function deactivateWorkshop($workshop_id) {
        $sql = "UPDATE workshops SET status = 'inactive', updated_at = NOW() WHERE id = ?";
        $result = $this->db->prepareAndExecute($sql, "i", [$workshop_id]);
        
        return $result ? true : false;
    }
} 