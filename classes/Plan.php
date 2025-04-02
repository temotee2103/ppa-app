<?php
/**
 * Plan class for managing protection plans
 */
class Plan {
    private $db;
    
    /**
     * Constructor - initialize database connection
     */
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Get all active plans for a user
     * 
     * @param int $user_id User ID
     * @return array|null Array of active plans or null if none found
     */
    public function getUserActivePlans($user_id) {
        // 检查protection_plans表中是否存在user_id列
        $checkUserColumn = $this->db->prepareAndExecute("SHOW COLUMNS FROM protection_plans LIKE 'user_id'", "", []);
        
        // 检查protection_plans表中是否存在end_date列
        $checkEndDateColumn = $this->db->prepareAndExecute("SHOW COLUMNS FROM protection_plans LIKE 'end_date'", "", []);
        $hasEndDate = $checkEndDateColumn && $checkEndDateColumn->num_rows > 0;
        
        // 根据可用列构建WHERE子句
        $whereClause = "";
        if ($checkUserColumn && $checkUserColumn->num_rows > 0) {
            $whereClause = "WHERE p.user_id = ? AND p.status = 'active'";
        } else {
            $whereClause = "WHERE v.user_id = ? AND p.status = 'active'";
        }
        
        // 如果end_date列存在，添加日期条件
        if ($hasEndDate) {
            $whereClause .= " AND p.end_date >= CURDATE()";
        }
        
        // 检查vehicle_id列
        $checkVehicleIdColumn = $this->db->prepareAndExecute("SHOW COLUMNS FROM protection_plans LIKE 'vehicle_id'", "", []);
        $hasVehicleIdColumn = $checkVehicleIdColumn && $checkVehicleIdColumn->num_rows > 0;
        
        // 构建SQL查询
        if ($hasVehicleIdColumn) {
            $sql = "SELECT p.*, 
                    CONCAT(v.make, ' ', v.model, ' (', v.reg_number, ')') as vehicle_name,
                    v.make, v.model, v.year, v.reg_number
                    FROM protection_plans p
                    JOIN vehicles v ON p.vehicle_id = v.id
                    " . $whereClause . "
                    ORDER BY " . ($hasEndDate ? "p.start_date" : "p.id") . " DESC";
        } else {
            // 如果没有vehicle_id列，尝试使用其他列进行关联（例如vehicle表的id）
            $sql = "SELECT p.*, 
                    CONCAT(v.make, ' ', v.model, ' (', v.reg_number, ')') as vehicle_name,
                    v.make, v.model, v.year, v.reg_number
                    FROM protection_plans p
                    JOIN vehicles v ON p.id = v.id
                    " . $whereClause . "
                    ORDER BY " . ($hasEndDate ? "p.start_date" : "p.id") . " DESC";
        }
                
        $result = $this->db->prepareAndExecute($sql, "i", [$user_id]);
        
        if ($result && $result->num_rows > 0) {
            $plans = [];
            while ($row = $result->fetch_assoc()) {
                $plans[] = $row;
            }
            return $plans;
        }
        
        return null;
    }
    
    /**
     * Get all plans (including expired) for a user
     * 
     * @param int $user_id User ID
     * @return array|null Array of all plans or null if none found
     */
    public function getUserAllPlans($user_id) {
        // 检查protection_plans表中是否存在user_id列
        $checkUserColumn = $this->db->prepareAndExecute("SHOW COLUMNS FROM protection_plans LIKE 'user_id'", "", []);
        
        // 根据可用列构建WHERE子句
        if ($checkUserColumn && $checkUserColumn->num_rows > 0) {
            $whereClause = "WHERE p.user_id = ?";
        } else {
            $whereClause = "WHERE v.user_id = ?";
        }
        
        // 检查start_date列是否存在，用于ORDER BY
        $checkStartDateColumn = $this->db->prepareAndExecute("SHOW COLUMNS FROM protection_plans LIKE 'start_date'", "", []);
        $hasStartDate = $checkStartDateColumn && $checkStartDateColumn->num_rows > 0;
        
        // 检查vehicle_id列
        $checkVehicleIdColumn = $this->db->prepareAndExecute("SHOW COLUMNS FROM protection_plans LIKE 'vehicle_id'", "", []);
        $hasVehicleIdColumn = $checkVehicleIdColumn && $checkVehicleIdColumn->num_rows > 0;
        
        // 构建SQL查询
        if ($hasVehicleIdColumn) {
            $sql = "SELECT p.*, 
                    CONCAT(v.make, ' ', v.model, ' (', v.reg_number, ')') as vehicle_name,
                    v.make, v.model, v.year, v.reg_number
                    FROM protection_plans p
                    JOIN vehicles v ON p.vehicle_id = v.id
                    " . $whereClause . "
                    ORDER BY " . ($hasStartDate ? "p.start_date" : "p.id") . " DESC";
        } else {
            // 如果没有vehicle_id列，尝试使用其他列进行关联
            $sql = "SELECT p.*, 
                    CONCAT(v.make, ' ', v.model, ' (', v.reg_number, ')') as vehicle_name,
                    v.make, v.model, v.year, v.reg_number
                    FROM protection_plans p
                    JOIN vehicles v ON p.id = v.id
                    " . $whereClause . "
                    ORDER BY " . ($hasStartDate ? "p.start_date" : "p.id") . " DESC";
        }
                
        $result = $this->db->prepareAndExecute($sql, "i", [$user_id]);
        
        if ($result && $result->num_rows > 0) {
            $plans = [];
            while ($row = $result->fetch_assoc()) {
                $plans[] = $row;
            }
            return $plans;
        }
        
        return null;
    }
    
    /**
     * Get a single plan by ID
     * 
     * @param int $plan_id Plan ID
     * @param int $user_id User ID for verification
     * @return array|null Plan details or null if not found
     */
    public function getPlanById($plan_id, $user_id) {
        // 检查protection_plans表中是否存在user_id列
        $checkColumn = $this->db->prepareAndExecute("SHOW COLUMNS FROM protection_plans LIKE 'user_id'", "", []);
        
        // 检查vehicle_id列
        $checkVehicleIdColumn = $this->db->prepareAndExecute("SHOW COLUMNS FROM protection_plans LIKE 'vehicle_id'", "", []);
        $hasVehicleIdColumn = $checkVehicleIdColumn && $checkVehicleIdColumn->num_rows > 0;
        
        if ($hasVehicleIdColumn) {
            if ($checkColumn && $checkColumn->num_rows > 0) {
                // 如果user_id列和vehicle_id列都存在
                $sql = "SELECT p.*, 
                        CONCAT(v.make, ' ', v.model, ' (', v.reg_number, ')') as vehicle_name,
                        v.make, v.model, v.year, v.reg_number, v.color, v.mileage
                        FROM protection_plans p
                        JOIN vehicles v ON p.vehicle_id = v.id
                        WHERE p.id = ? AND p.user_id = ?";
            } else {
                // 如果只有vehicle_id列存在
                $sql = "SELECT p.*, 
                        CONCAT(v.make, ' ', v.model, ' (', v.reg_number, ')') as vehicle_name,
                        v.make, v.model, v.year, v.reg_number, v.color, v.mileage
                        FROM protection_plans p
                        JOIN vehicles v ON p.vehicle_id = v.id
                        WHERE p.id = ? AND v.user_id = ?";
            }
        } else {
            // 如果vehicle_id列不存在，使用替代关联方式
            if ($checkColumn && $checkColumn->num_rows > 0) {
                $sql = "SELECT p.*, 
                        CONCAT(v.make, ' ', v.model, ' (', v.reg_number, ')') as vehicle_name,
                        v.make, v.model, v.year, v.reg_number, v.color, v.mileage
                        FROM protection_plans p
                        JOIN vehicles v ON p.id = v.id
                        WHERE p.id = ? AND p.user_id = ?";
            } else {
                $sql = "SELECT p.*, 
                        CONCAT(v.make, ' ', v.model, ' (', v.reg_number, ')') as vehicle_name,
                        v.make, v.model, v.year, v.reg_number, v.color, v.mileage
                        FROM protection_plans p
                        JOIN vehicles v ON p.id = v.id
                        WHERE p.id = ? AND v.user_id = ?";
            }
        }
                
        $result = $this->db->prepareAndExecute($sql, "ii", [$plan_id, $user_id]);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Get all available plan types
     * 
     * @return array|null Array of plan types or null if none found
     */
    public function getAvailablePlanTypes() {
        // Check if plan_types table exists, if not, return samples
        $checkTable = $this->db->prepareAndExecute("SHOW TABLES LIKE 'plan_types'", "", []);
        if (!$checkTable || $checkTable->num_rows === 0) {
            // Return sample plan types
            return [
                [
                    'id' => 1,
                    'name' => 'Basic Protection',
                    'description' => 'Essential coverage for your vehicle',
                    'coverage_amount' => 5000,
                    'price' => 99.99
                ],
                [
                    'id' => 2,
                    'name' => 'Premium Protection',
                    'description' => 'Comprehensive coverage with additional benefits',
                    'coverage_amount' => 10000,
                    'price' => 199.99
                ]
            ];
        }
        
        $sql = "SELECT * FROM plan_types WHERE active = 1 ORDER BY price";
        $result = $this->db->prepareAndExecute($sql, "", []);
        
        if ($result && $result->num_rows > 0) {
            $planTypes = [];
            while ($row = $result->fetch_assoc()) {
                $planTypes[] = $row;
            }
            return $planTypes;
        }
        
        return null;
    }
    
    /**
     * Get vehicles without active plans for a user
     * 
     * @param int $user_id User ID
     * @return array|null Array of vehicles without plans or null if none found
     */
    public function getVehiclesWithoutPlans($user_id) {
        // 检查protection_plans表中是否存在user_id列
        $checkUserColumn = $this->db->prepareAndExecute("SHOW COLUMNS FROM protection_plans LIKE 'user_id'", "", []);
        $hasUserIdColumn = $checkUserColumn && $checkUserColumn->num_rows > 0;
        
        // 检查end_date列是否存在
        $checkEndDateColumn = $this->db->prepareAndExecute("SHOW COLUMNS FROM protection_plans LIKE 'end_date'", "", []);
        $hasEndDate = $checkEndDateColumn && $checkEndDateColumn->num_rows > 0;
        
        // 检查vehicle_id列是否存在
        $checkVehicleIdColumn = $this->db->prepareAndExecute("SHOW COLUMNS FROM protection_plans LIKE 'vehicle_id'", "", []);
        $hasVehicleIdColumn = $checkVehicleIdColumn && $checkVehicleIdColumn->num_rows > 0;
        
        // 构建子查询，根据可用列
        if (!$hasVehicleIdColumn) {
            // 如果vehicle_id列不存在，直接返回所有车辆
            $sql = "SELECT v.* 
                    FROM vehicles v
                    WHERE v.user_id = ?
                    ORDER BY v.make, v.model";
        } else {
            if ($hasUserIdColumn) {
                $subquery = "SELECT vehicle_id FROM protection_plans WHERE status = 'active'";
                if ($hasEndDate) {
                    $subquery .= " AND end_date >= CURDATE()";
                }
                $subquery .= " AND user_id = ?";
            } else {
                $subquery = "SELECT pp.vehicle_id 
                             FROM protection_plans pp
                             JOIN vehicles veh ON pp.vehicle_id = veh.id
                             WHERE pp.status = 'active'";
                if ($hasEndDate) {
                    $subquery .= " AND pp.end_date >= CURDATE()";
                }
                $subquery .= " AND veh.user_id = ?";
            }
            
            $sql = "SELECT v.* 
                    FROM vehicles v
                    LEFT JOIN (" . $subquery . ") p ON v.id = p.vehicle_id
                    WHERE v.user_id = ? AND p.vehicle_id IS NULL
                    ORDER BY v.make, v.model";
        }
                
        $result = $hasVehicleIdColumn 
                  ? $this->db->prepareAndExecute($sql, "ii", [$user_id, $user_id])
                  : $this->db->prepareAndExecute($sql, "i", [$user_id]);
        
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
     * Add a new protection plan
     * 
     * @param array $plan_data Plan data including vehicle_id, plan_type_id, start_date, etc.
     * @param int $user_id User ID for verification
     * @return bool|int False on failure, plan ID on success
     */
    public function addPlan($plan_data, $user_id) {
        // Verify vehicle belongs to user
        $sql = "SELECT id FROM vehicles WHERE id = ? AND user_id = ?";
        $result = $this->db->prepareAndExecute($sql, "ii", [$plan_data['vehicle_id'], $user_id]);
        
        if (!$result || $result->num_rows === 0) {
            return false;
        }
        
        // Check if vehicle already has an active plan
        // Check if end_date exists in the protection_plans table
        $checkEndDateColumn = $this->db->prepareAndExecute("SHOW COLUMNS FROM protection_plans LIKE 'end_date'", "", []);
        $hasEndDate = $checkEndDateColumn && $checkEndDateColumn->num_rows > 0;
        
        $sql = "SELECT id FROM protection_plans WHERE vehicle_id = ? AND status = 'active'";
        if ($hasEndDate) {
            $sql .= " AND end_date >= CURDATE()";
        }
        
        $result = $this->db->prepareAndExecute($sql, "i", [$plan_data['vehicle_id']]);
        
        if ($result && $result->num_rows > 0) {
            return false; // Vehicle already has an active plan
        }
        
        // Get plan type details - either from plan_types table or from the provided data
        $plan_name = $plan_data['plan_name'] ?? 'Custom Protection Plan';
        $description = $plan_data['description'] ?? 'Custom protection plan for your vehicle';
        $coverage_amount = $plan_data['coverage_amount'] ?? 5000;
        $price = $plan_data['price'] ?? 99.99;
        
        if (isset($plan_data['plan_type_id'])) {
            $sql = "SELECT * FROM plan_types WHERE id = ?";
            $result = $this->db->prepareAndExecute($sql, "i", [$plan_data['plan_type_id']]);
            
            if ($result && $result->num_rows > 0) {
                $plan_type = $result->fetch_assoc();
                $plan_name = $plan_type['name'];
                $description = $plan_type['description'];
                $coverage_amount = $plan_type['coverage_amount'] ?? $coverage_amount;
                $price = $plan_type['price'] ?? $price;
            }
        }
        
        // Check if start_date and end_date columns exist
        $checkStartDateColumn = $this->db->prepareAndExecute("SHOW COLUMNS FROM protection_plans LIKE 'start_date'", "", []);
        $hasStartDate = $checkStartDateColumn && $checkStartDateColumn->num_rows > 0;
        
        // Calculate end date (1 year from start date by default)
        $start_date = date('Y-m-d', strtotime($plan_data['start_date']));
        $end_date = date('Y-m-d', strtotime("+1 year", strtotime($start_date)));
        
        // Check if user_id column exists in protection_plans table
        $checkUserColumn = $this->db->prepareAndExecute("SHOW COLUMNS FROM protection_plans LIKE 'user_id'", "", []);
        $hasUserIdColumn = $checkUserColumn && $checkUserColumn->num_rows > 0;
        
        // Build insert query based on available columns
        $columns = [];
        $values = [];
        $placeholders = [];
        $types = "";
        $params = [];
        
        // Add user_id if column exists
        if ($hasUserIdColumn) {
            $columns[] = "user_id";
            $placeholders[] = "?";
            $types .= "i";
            $params[] = $user_id;
        }
        
        // Add common fields
        $columns[] = "vehicle_id";
        $placeholders[] = "?";
        $types .= "i";
        $params[] = $plan_data['vehicle_id'];
        
        $columns[] = "plan_id";
        $placeholders[] = "?";
        $types .= "i";
        $params[] = $plan_data['plan_id'] ?? 1;
        
        $columns[] = "plan_name";
        $placeholders[] = "?";
        $types .= "s";
        $params[] = $plan_name;
        
        $columns[] = "price";
        $placeholders[] = "?";
        $types .= "d";
        $params[] = $price;
        
        $columns[] = "coverage_details";
        $placeholders[] = "?";
        $types .= "s";
        $params[] = $description;
        
        // Add start_date and end_date if columns exist
        if ($hasStartDate) {
            $columns[] = "start_date";
            $placeholders[] = "?";
            $types .= "s";
            $params[] = $start_date;
        }
        
        if ($hasEndDate) {
            $columns[] = "end_date";
            $placeholders[] = "?";
            $types .= "s";
            $params[] = $end_date;
        }
        
        // Add status and created_at
        $columns[] = "status";
        $placeholders[] = "'active'";
        
        $columns[] = "created_at";
        $placeholders[] = "NOW()";
        
        // Build the final query
        $sql = "INSERT INTO protection_plans (" . implode(", ", $columns) . ") 
                VALUES (" . implode(", ", $placeholders) . ")";
        
        $result = $this->db->prepareAndExecute($sql, $types, $params);
        
        if ($result) {
            return $this->db->getLastInsertId();
        }
        
        return false;
    }
    
    /**
     * Cancel a protection plan
     * 
     * @param int $plan_id Plan ID
     * @param int $user_id User ID for verification
     * @return bool True on success, false on failure
     */
    public function cancelPlan($plan_id, $user_id) {
        // Check if user_id column exists in protection_plans table
        $checkUserColumn = $this->db->prepareAndExecute("SHOW COLUMNS FROM protection_plans LIKE 'user_id'", "", []);
        $hasUserIdColumn = $checkUserColumn && $checkUserColumn->num_rows > 0;
        
        // Verify plan belongs to user
        if ($hasUserIdColumn) {
            $sql = "SELECT id FROM protection_plans WHERE id = ? AND user_id = ?";
            $result = $this->db->prepareAndExecute($sql, "ii", [$plan_id, $user_id]);
        } else {
            $sql = "SELECT p.id 
                    FROM protection_plans p
                    JOIN vehicles v ON p.vehicle_id = v.id
                    WHERE p.id = ? AND v.user_id = ?";
            $result = $this->db->prepareAndExecute($sql, "ii", [$plan_id, $user_id]);
        }
        
        if (!$result || $result->num_rows === 0) {
            return false;
        }
        
        // Check if there are any active claims for this plan's vehicle
        $sql = "SELECT c.id
                FROM claims c
                JOIN protection_plans p ON c.vehicle_id = p.vehicle_id
                WHERE p.id = ? AND c.status IN ('pending', 'under_review', 'approved', 'in_progress')";
        $result = $this->db->prepareAndExecute($sql, "i", [$plan_id]);
        
        if ($result && $result->num_rows > 0) {
            return false; // Can't cancel plan with active claims
        }
        
        // Check if updated_at column exists
        $checkUpdatedAtColumn = $this->db->prepareAndExecute("SHOW COLUMNS FROM protection_plans LIKE 'updated_at'", "", []);
        $hasUpdatedAt = $checkUpdatedAtColumn && $checkUpdatedAtColumn->num_rows > 0;
        
        // Update plan status
        $sql = "UPDATE protection_plans SET status = 'cancelled'";
        
        // Add updated_at if column exists
        if ($hasUpdatedAt) {
            $sql .= ", updated_at = NOW()";
        }
        
        // Add WHERE clause based on columns
        if ($hasUserIdColumn) {
            $sql .= " WHERE id = ? AND user_id = ?";
            $result = $this->db->prepareAndExecute($sql, "ii", [$plan_id, $user_id]);
        } else {
            $sql .= " WHERE id = ?";
            $result = $this->db->prepareAndExecute($sql, "i", [$plan_id]);
        }
        
        return $result ? true : false;
    }
    
    /**
     * Get all user plans with detailed information
     * 
     * @param int $user_id User ID
     * @return array|null Array of all plans with detailed info or null if none found
     */
    public function getUserPlans($user_id) {
        // 检查protection_plans表中是否存在user_id列
        $checkUserColumn = $this->db->prepareAndExecute("SHOW COLUMNS FROM protection_plans LIKE 'user_id'", "", []);
        
        // 根据可用列构建WHERE子句
        if ($checkUserColumn && $checkUserColumn->num_rows > 0) {
            $whereClause = "WHERE p.user_id = ?";
        } else {
            $whereClause = "WHERE v.user_id = ?";
        }
        
        // 检查start_date和end_date列是否存在
        $checkStartDateColumn = $this->db->prepareAndExecute("SHOW COLUMNS FROM protection_plans LIKE 'start_date'", "", []);
        $hasStartDate = $checkStartDateColumn && $checkStartDateColumn->num_rows > 0;
        
        $checkEndDateColumn = $this->db->prepareAndExecute("SHOW COLUMNS FROM protection_plans LIKE 'end_date'", "", []);
        $hasEndDate = $checkEndDateColumn && $checkEndDateColumn->num_rows > 0;
        
        // 检查vehicle_id列是否存在
        $checkVehicleIdColumn = $this->db->prepareAndExecute("SHOW COLUMNS FROM protection_plans LIKE 'vehicle_id'", "", []);
        $hasVehicleIdColumn = $checkVehicleIdColumn && $checkVehicleIdColumn->num_rows > 0;
        
        // 构建SQL查询
        if ($hasVehicleIdColumn) {
            $sql = "SELECT p.*, 
                    " . ($hasEndDate ? "p.end_date as expiry_date, " : "DATE_ADD(" . ($hasStartDate ? "p.start_date" : "p.created_at") . ", INTERVAL 1 YEAR) as expiry_date, ") . "
                    99.99 as monthly_premium,
                    'Full Coverage' as coverage_type,
                    v.make as vehicle_make, 
                    v.model as vehicle_model, 
                    v.reg_number as vehicle_reg_number,
                    CONCAT(v.make, ' ', v.model, ' (', v.reg_number, ')') as vehicle_name
                    FROM protection_plans p
                    JOIN vehicles v ON p.vehicle_id = v.id
                    " . $whereClause . "
                    ORDER BY " . ($hasStartDate ? "p.start_date" : "p.created_at") . " DESC";
        } else {
            // 如果没有vehicle_id列，尝试使用其他列进行关联
            $sql = "SELECT p.*, 
                    " . ($hasEndDate ? "p.end_date as expiry_date, " : "DATE_ADD(" . ($hasStartDate ? "p.start_date" : "p.created_at") . ", INTERVAL 1 YEAR) as expiry_date, ") . "
                    99.99 as monthly_premium,
                    'Full Coverage' as coverage_type,
                    v.make as vehicle_make, 
                    v.model as vehicle_model, 
                    v.reg_number as vehicle_reg_number,
                    CONCAT(v.make, ' ', v.model, ' (', v.reg_number, ')') as vehicle_name
                    FROM protection_plans p
                    JOIN vehicles v ON p.id = v.id
                    " . $whereClause . "
                    ORDER BY " . ($hasStartDate ? "p.start_date" : "p.created_at") . " DESC";
        }
                
        $result = $this->db->prepareAndExecute($sql, "i", [$user_id]);
        
        if ($result && $result->num_rows > 0) {
            $plans = [];
            while ($row = $result->fetch_assoc()) {
                $plans[] = $row;
            }
            return $plans;
        }
        
        return null;
    }
} 