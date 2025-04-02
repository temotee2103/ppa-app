<?php
/**
 * Vehicle class for managing user vehicles
 */
class Vehicle {
    private $db;
    
    /**
     * Constructor - initialize database connection
     */
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Get vehicles for a specific user
     * 
     * @param int $user_id User ID
     * @return array|null Array of vehicles or null if no vehicles found
     */
    public function getUserVehicles($user_id) {
        // Using a simpler query without joining protection_plans table
        // to avoid issues with column name mismatches
        $sql = "SELECT v.* 
                FROM vehicles v
                WHERE v.user_id = ?
                ORDER BY v.created_at DESC";
                
        $result = $this->db->prepareAndExecute($sql, "i", [$user_id]);
        
        if ($result && $result->num_rows > 0) {
            $vehicles = [];
            while ($row = $result->fetch_assoc()) {
                // Check for active plan separately
                $row['plan_name'] = null;
                $row['plan_expiry'] = null;
                
                $planSql = "SELECT plan_name, end_date 
                           FROM protection_plans 
                           WHERE vehicle_id = ? AND status = 'active' 
                           LIMIT 1";
                $planResult = $this->db->prepareAndExecute($planSql, "i", [$row['id']]);
                
                if ($planResult && $planResult->num_rows > 0) {
                    $planData = $planResult->fetch_assoc();
                    $row['plan_name'] = $planData['plan_name'];
                    $row['plan_expiry'] = $planData['end_date'];
                }
                
                $vehicles[] = $row;
            }
            return $vehicles;
        }
        
        return null;
    }
    
    /**
     * Get a single vehicle by ID
     * 
     * @param int $vehicle_id Vehicle ID
     * @param int $user_id User ID for verification
     * @return array|null Vehicle details or null if not found
     */
    public function getVehicleById($vehicle_id, $user_id) {
        // Using a simpler query without joining protection_plans table
        $sql = "SELECT v.*  
                FROM vehicles v
                WHERE v.id = ? AND v.user_id = ?";
                
        $result = $this->db->prepareAndExecute($sql, "ii", [$vehicle_id, $user_id]);
        
        if ($result && $result->num_rows > 0) {
            $vehicle = $result->fetch_assoc();
            
            // Check for active plan separately
            $planSql = "SELECT plan_name, start_date, end_date 
                       FROM protection_plans 
                       WHERE vehicle_id = ? AND status = 'active' 
                       LIMIT 1";
            $planResult = $this->db->prepareAndExecute($planSql, "i", [$vehicle_id]);
            
            if ($planResult && $planResult->num_rows > 0) {
                $planData = $planResult->fetch_assoc();
                $vehicle['plan_name'] = $planData['plan_name'];
                $vehicle['plan_start'] = $planData['start_date'];
                $vehicle['plan_expiry'] = $planData['end_date'];
            } else {
                $vehicle['plan_name'] = null;
                $vehicle['plan_start'] = null;
                $vehicle['plan_expiry'] = null;
            }
            
            return $vehicle;
        }
        
        return null;
    }
    
    /**
     * Check if a vehicle has an active plan
     * 
     * @param int $vehicle_id Vehicle ID
     * @return bool True if active plan exists, false otherwise
     */
    public function hasActivePlan($vehicle_id) {
        $sql = "SELECT COUNT(*) as count FROM protection_plans 
                WHERE vehicle_id = ? AND status = 'active' AND end_date >= CURDATE()";
                
        $result = $this->db->prepareAndExecute($sql, "i", [$vehicle_id]);
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['count'] > 0;
        }
        
        return false;
    }
    
    /**
     * Add a new vehicle
     * 
     * @param array $vehicle_data Vehicle data including user_id, make, model, year, reg_number, etc.
     * @return bool|int False on failure, vehicle ID on success
     */
    public function addVehicle($vehicle_data) {
        // Validate required fields
        $required_fields = ['user_id', 'make', 'model', 'year', 'reg_number'];
        foreach ($required_fields as $field) {
            if (!isset($vehicle_data[$field]) || empty($vehicle_data[$field])) {
                return false;
            }
        }
        
        // Check if vehicle with same registration number already exists for this user
        $sql = "SELECT id FROM vehicles WHERE reg_number = ? AND user_id = ?";
        $result = $this->db->prepareAndExecute($sql, "si", [$vehicle_data['reg_number'], $vehicle_data['user_id']]);
        
        if ($result && $result->num_rows > 0) {
            return false; // Vehicle already exists
        }
        
        // Insert vehicle
        $sql = "INSERT INTO vehicles (user_id, make, model, year, reg_number, engine_no, chassis_no, color, mileage, created_at) 
               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
               
        $params = [
            $vehicle_data['user_id'],
            $vehicle_data['make'],
            $vehicle_data['model'],
            $vehicle_data['year'],
            $vehicle_data['reg_number'],
            $vehicle_data['engine_no'] ?? '',
            $vehicle_data['chassis_no'] ?? '',
            $vehicle_data['color'] ?? '',
            $vehicle_data['mileage'] ?? 0
        ];
        
        $result = $this->db->prepareAndExecute($sql, "issssssi", $params);
        
        if ($result) {
            return $this->db->getLastInsertId();
        }
        
        return false;
    }
    
    /**
     * Update a vehicle
     * 
     * @param int $vehicle_id Vehicle ID
     * @param array $vehicle_data Updated vehicle data
     * @param int $user_id User ID for verification
     * @return bool True on success, false on failure
     */
    public function updateVehicle($vehicle_id, $vehicle_data, $user_id) {
        // Verify vehicle belongs to user
        $sql = "SELECT id FROM vehicles WHERE id = ? AND user_id = ?";
        $result = $this->db->prepareAndExecute($sql, "ii", [$vehicle_id, $user_id]);
        
        if (!$result || $result->num_rows === 0) {
            return false;
        }
        
        // Update vehicle
        $sql = "UPDATE vehicles SET 
                make = ?, 
                model = ?, 
                year = ?, 
                reg_number = ?, 
                engine_no = ?, 
                chassis_no = ?, 
                color = ?, 
                mileage = ?, 
                updated_at = NOW() 
                WHERE id = ? AND user_id = ?";
                
        $params = [
            $vehicle_data['make'],
            $vehicle_data['model'],
            $vehicle_data['year'],
            $vehicle_data['reg_number'],
            $vehicle_data['engine_no'] ?? '',
            $vehicle_data['chassis_no'] ?? '',
            $vehicle_data['color'] ?? '',
            $vehicle_data['mileage'] ?? 0,
            $vehicle_id,
            $user_id
        ];
        
        $result = $this->db->prepareAndExecute($sql, "sssssssiii", $params);
        
        return $result ? true : false;
    }
    
    /**
     * Delete a vehicle
     * 
     * @param int $vehicle_id Vehicle ID
     * @param int $user_id User ID for verification
     * @return bool True on success, false on failure
     */
    public function deleteVehicle($vehicle_id, $user_id) {
        // Check if vehicle has any claims or plans before deleting
        $sql = "SELECT 
                (SELECT COUNT(*) FROM claims WHERE vehicle_id = ?) as claim_count,
                (SELECT COUNT(*) FROM protection_plans WHERE vehicle_id = ?) as plan_count";
                
        $result = $this->db->prepareAndExecute($sql, "ii", [$vehicle_id, $vehicle_id]);
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            // Don't delete if there are related records
            if ($row['claim_count'] > 0 || $row['plan_count'] > 0) {
                return false;
            }
        }
        
        // Delete vehicle
        $sql = "DELETE FROM vehicles WHERE id = ? AND user_id = ?";
        $result = $this->db->prepareAndExecute($sql, "ii", [$vehicle_id, $user_id]);
        
        return $result ? true : false;
    }
    
    /**
     * Update vehicle mileage
     * 
     * @param int $vehicle_id Vehicle ID
     * @param int $mileage New mileage
     * @param int $user_id User ID for verification
     * @return bool True on success, false on failure
     */
    public function updateMileage($vehicle_id, $mileage, $user_id) {
        $sql = "UPDATE vehicles SET mileage = ?, updated_at = NOW() 
                WHERE id = ? AND user_id = ?";
                
        $result = $this->db->prepareAndExecute($sql, "iii", [$mileage, $vehicle_id, $user_id]);
        
        return $result ? true : false;
    }
    
    /**
     * Get vehicles eligible for claims (has active protection plan)
     * 
     * @param int $user_id User ID
     * @return array|null Array of eligible vehicles or null if none found
     */
    public function getEligibleVehicles($user_id) {
        // 检查protection_plans表中vehicle_id列是否存在
        $checkVehicleIdColumn = $this->db->prepareAndExecute("SHOW COLUMNS FROM protection_plans LIKE 'vehicle_id'", "", []);
        $hasVehicleIdColumn = $checkVehicleIdColumn && $checkVehicleIdColumn->num_rows > 0;
        
        if (!$hasVehicleIdColumn) {
            // 如果vehicle_id列不存在，返回所有车辆
            $sql = "SELECT v.*, 'NA' as plan_name, NULL as plan_expiry
                    FROM vehicles v
                    WHERE v.user_id = ?
                    ORDER BY v.make, v.model";
        } else {
            $sql = "SELECT v.*, p.plan_name, p.end_date as plan_expiry 
                    FROM vehicles v
                    JOIN protection_plans p ON v.id = p.vehicle_id 
                    WHERE v.user_id = ? AND p.status = 'active' AND p.end_date >= CURDATE()
                    ORDER BY v.make, v.model";
        }
                
        $result = $this->db->prepareAndExecute($sql, "i", [$user_id]);
        
        if ($result && $result->num_rows > 0) {
            $vehicles = [];
            while ($row = $result->fetch_assoc()) {
                $vehicles[] = $row;
            }
            return $vehicles;
        }
        
        return null;
    }
    
    /**
     * Get vehicles without active plans for a user
     * 
     * @param int $user_id User ID
     * @return array|null Array of vehicles without active plans or null if none found
     */
    public function getUserVehiclesWithoutPlans($user_id) {
        // 检查protection_plans表中vehicle_id列是否存在
        $checkVehicleIdColumn = $this->db->prepareAndExecute("SHOW COLUMNS FROM protection_plans LIKE 'vehicle_id'", "", []);
        $hasVehicleIdColumn = $checkVehicleIdColumn && $checkVehicleIdColumn->num_rows > 0;
        
        if (!$hasVehicleIdColumn) {
            // 如果vehicle_id列不存在，返回所有车辆
            $sql = "SELECT v.* 
                    FROM vehicles v
                    WHERE v.user_id = ?
                    ORDER BY v.make, v.model";
            
            $result = $this->db->prepareAndExecute($sql, "i", [$user_id]);
        } else {
            // 如果vehicle_id列存在，查找没有活跃保护计划的车辆
            $sql = "SELECT v.* 
                    FROM vehicles v
                    LEFT JOIN (
                        SELECT vehicle_id 
                        FROM protection_plans 
                        WHERE status = 'active' AND end_date >= CURDATE()
                    ) p ON v.id = p.vehicle_id
                    WHERE v.user_id = ? AND p.vehicle_id IS NULL
                    ORDER BY v.make, v.model";
            
            $result = $this->db->prepareAndExecute($sql, "i", [$user_id]);
        }
        
        if ($result && $result->num_rows > 0) {
            $vehicles = [];
            while ($row = $result->fetch_assoc()) {
                $vehicles[] = $row;
            }
            return $vehicles;
        }
        
        return null;
    }
} 