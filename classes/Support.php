<?php
/**
 * Support Class
 * Handles support tickets and interactions
 */
class Support {
    private $db;
    
    /**
     * Constructor
     * 
     * @param PDO $db Database connection
     */
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Get user's support tickets
     * 
     * @param int $userId User ID
     * @return array|bool Array of tickets or false on failure
     */
    public function getUserTickets($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT t.*, 
                       c.name as category_name,
                       COALESCE(
                           (SELECT MAX(r.created_at) 
                            FROM support_replies r 
                            WHERE r.ticket_id = t.id), 
                           t.created_at
                       ) as last_activity
                FROM support_tickets t
                LEFT JOIN support_categories c ON t.category = c.id
                WHERE t.user_id = :user_id
                ORDER BY last_activity DESC
            ");
            
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            return [];
        } catch (PDOException $e) {
            error_log('Support::getUserTickets Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create a new support ticket
     * 
     * @param array $ticketData Ticket data
     * @return int|bool Ticket ID or false on failure
     */
    public function createTicket($ticketData) {
        try {
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("
                INSERT INTO support_tickets (
                    user_id, category, subject, message, priority, status, created_at
                ) VALUES (
                    :user_id, :category, :subject, :message, :priority, :status, :created_at
                )
            ");
            
            $stmt->bindParam(':user_id', $ticketData['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':category', $ticketData['category'], PDO::PARAM_STR);
            $stmt->bindParam(':subject', $ticketData['subject'], PDO::PARAM_STR);
            $stmt->bindParam(':message', $ticketData['message'], PDO::PARAM_STR);
            $stmt->bindParam(':priority', $ticketData['priority'], PDO::PARAM_STR);
            $stmt->bindParam(':status', $ticketData['status'], PDO::PARAM_STR);
            $stmt->bindParam(':created_at', $ticketData['created_at'], PDO::PARAM_STR);
            
            $stmt->execute();
            
            $ticketId = $this->db->lastInsertId();
            
            // Create notification for admins
            $notificationStmt = $this->db->prepare("
                INSERT INTO notifications (
                    user_id, type, reference_id, message, is_read, created_at
                ) VALUES (
                    0, 'new_ticket', :ticket_id, :message, 0, :created_at
                )
            ");
            
            $notificationMessage = 'New support ticket: ' . $ticketData['subject'];
            $notificationStmt->bindParam(':ticket_id', $ticketId, PDO::PARAM_INT);
            $notificationStmt->bindParam(':message', $notificationMessage, PDO::PARAM_STR);
            $notificationStmt->bindParam(':created_at', $ticketData['created_at'], PDO::PARAM_STR);
            $notificationStmt->execute();
            
            $this->db->commit();
            
            return $ticketId;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log('Support::createTicket Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Add attachments to a ticket
     * 
     * @param int $ticketId Ticket ID
     * @param array $files Files array from $_FILES
     * @return bool Success status
     */
    public function addAttachments($ticketId, $files) {
        try {
            // Create uploads directory if it doesn't exist
            $uploadsDir = '../uploads/support/';
            if (!file_exists($uploadsDir)) {
                mkdir($uploadsDir, 0755, true);
            }
            
            $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            $maxFiles = 3;
            
            // Limit number of files
            $fileCount = count($files['name']);
            if ($fileCount > $maxFiles) {
                $fileCount = $maxFiles;
            }
            
            for ($i = 0; $i < $fileCount; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $tmpName = $files['tmp_name'][$i];
                    $name = $files['name'][$i];
                    $type = $files['type'][$i];
                    $size = $files['size'][$i];
                    
                    // Validate file type and size
                    if (!in_array($type, $allowedTypes) || $size > $maxSize) {
                        continue;
                    }
                    
                    // Generate unique filename
                    $extension = pathinfo($name, PATHINFO_EXTENSION);
                    $newFilename = 'ticket_' . $ticketId . '_' . uniqid() . '.' . $extension;
                    
                    // Move file to uploads directory
                    if (move_uploaded_file($tmpName, $uploadsDir . $newFilename)) {
                        // Save file record in database
                        $stmt = $this->db->prepare("
                            INSERT INTO support_attachments (
                                ticket_id, reply_id, filename, original_name, file_type, file_size, created_at
                            ) VALUES (
                                :ticket_id, NULL, :filename, :original_name, :file_type, :file_size, NOW()
                            )
                        ");
                        
                        $stmt->bindParam(':ticket_id', $ticketId, PDO::PARAM_INT);
                        $stmt->bindParam(':filename', $newFilename, PDO::PARAM_STR);
                        $stmt->bindParam(':original_name', $name, PDO::PARAM_STR);
                        $stmt->bindParam(':file_type', $type, PDO::PARAM_STR);
                        $stmt->bindParam(':file_size', $size, PDO::PARAM_INT);
                        
                        $stmt->execute();
                    }
                }
            }
            
            return true;
        } catch (Exception $e) {
            error_log('Support::addAttachments Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Add reply to a ticket
     * 
     * @param array $replyData Reply data
     * @return int|bool Reply ID or false on failure
     */
    public function addReply($replyData) {
        try {
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("
                INSERT INTO support_replies (
                    ticket_id, user_id, message, created_at
                ) VALUES (
                    :ticket_id, :user_id, :message, :created_at
                )
            ");
            
            $stmt->bindParam(':ticket_id', $replyData['ticket_id'], PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $replyData['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':message', $replyData['message'], PDO::PARAM_STR);
            $stmt->bindParam(':created_at', $replyData['created_at'], PDO::PARAM_STR);
            
            $stmt->execute();
            
            $replyId = $this->db->lastInsertId();
            
            // Update ticket status and last updated
            $updateStmt = $this->db->prepare("
                UPDATE support_tickets 
                SET status = 'in_progress', updated_at = :updated_at
                WHERE id = :ticket_id
            ");
            
            $updateStmt->bindParam(':ticket_id', $replyData['ticket_id'], PDO::PARAM_INT);
            $updateStmt->bindParam(':updated_at', $replyData['created_at'], PDO::PARAM_STR);
            $updateStmt->execute();
            
            // Create notification for the other party
            $ticketStmt = $this->db->prepare("
                SELECT user_id, subject FROM support_tickets WHERE id = :ticket_id
            ");
            $ticketStmt->bindParam(':ticket_id', $replyData['ticket_id'], PDO::PARAM_INT);
            $ticketStmt->execute();
            $ticket = $ticketStmt->fetch(PDO::FETCH_ASSOC);
            
            $notificationUserId = 0; // Default to admin notification
            
            // If reply is from user, notify admin, else notify the user
            if ($replyData['user_id'] == $ticket['user_id']) {
                $notificationType = 'ticket_reply_user';
                $notificationUserId = 0; // For admin
            } else {
                $notificationType = 'ticket_reply_admin';
                $notificationUserId = $ticket['user_id']; // For user
            }
            
            $notificationStmt = $this->db->prepare("
                INSERT INTO notifications (
                    user_id, type, reference_id, message, is_read, created_at
                ) VALUES (
                    :user_id, :type, :ticket_id, :message, 0, :created_at
                )
            ");
            
            $notificationMessage = 'New reply to ticket: ' . $ticket['subject'];
            $notificationStmt->bindParam(':user_id', $notificationUserId, PDO::PARAM_INT);
            $notificationStmt->bindParam(':type', $notificationType, PDO::PARAM_STR);
            $notificationStmt->bindParam(':ticket_id', $replyData['ticket_id'], PDO::PARAM_INT);
            $notificationStmt->bindParam(':message', $notificationMessage, PDO::PARAM_STR);
            $notificationStmt->bindParam(':created_at', $replyData['created_at'], PDO::PARAM_STR);
            $notificationStmt->execute();
            
            $this->db->commit();
            
            return $replyId;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log('Support::addReply Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Add attachments to a reply
     * 
     * @param int $replyId Reply ID
     * @param array $files Files array from $_FILES
     * @return bool Success status
     */
    public function addReplyAttachments($replyId, $files) {
        try {
            // First get the ticket ID for this reply
            $stmt = $this->db->prepare("
                SELECT ticket_id FROM support_replies WHERE id = :reply_id
            ");
            $stmt->bindParam(':reply_id', $replyId, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($stmt->rowCount() === 0) {
                return false;
            }
            
            $ticketId = $stmt->fetch(PDO::FETCH_COLUMN);
            
            // Create uploads directory if it doesn't exist
            $uploadsDir = '../uploads/support/';
            if (!file_exists($uploadsDir)) {
                mkdir($uploadsDir, 0755, true);
            }
            
            $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            $maxFiles = 3;
            
            // Limit number of files
            $fileCount = count($files['name']);
            if ($fileCount > $maxFiles) {
                $fileCount = $maxFiles;
            }
            
            for ($i = 0; $i < $fileCount; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $tmpName = $files['tmp_name'][$i];
                    $name = $files['name'][$i];
                    $type = $files['type'][$i];
                    $size = $files['size'][$i];
                    
                    // Validate file type and size
                    if (!in_array($type, $allowedTypes) || $size > $maxSize) {
                        continue;
                    }
                    
                    // Generate unique filename
                    $extension = pathinfo($name, PATHINFO_EXTENSION);
                    $newFilename = 'reply_' . $replyId . '_' . uniqid() . '.' . $extension;
                    
                    // Move file to uploads directory
                    if (move_uploaded_file($tmpName, $uploadsDir . $newFilename)) {
                        // Save file record in database
                        $stmt = $this->db->prepare("
                            INSERT INTO support_attachments (
                                ticket_id, reply_id, filename, original_name, file_type, file_size, created_at
                            ) VALUES (
                                :ticket_id, :reply_id, :filename, :original_name, :file_type, :file_size, NOW()
                            )
                        ");
                        
                        $stmt->bindParam(':ticket_id', $ticketId, PDO::PARAM_INT);
                        $stmt->bindParam(':reply_id', $replyId, PDO::PARAM_INT);
                        $stmt->bindParam(':filename', $newFilename, PDO::PARAM_STR);
                        $stmt->bindParam(':original_name', $name, PDO::PARAM_STR);
                        $stmt->bindParam(':file_type', $type, PDO::PARAM_STR);
                        $stmt->bindParam(':file_size', $size, PDO::PARAM_INT);
                        
                        $stmt->execute();
                    }
                }
            }
            
            return true;
        } catch (Exception $e) {
            error_log('Support::addReplyAttachments Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get ticket details with replies and attachments
     * 
     * @param int $ticketId Ticket ID
     * @return array|bool Ticket details or false on failure
     */
    public function getTicketDetails($ticketId) {
        try {
            // Get ticket details
            $stmt = $this->db->prepare("
                SELECT t.*, 
                       c.name as category_name,
                       u.first_name as user_first_name,
                       u.last_name as user_last_name,
                       CONCAT(u.first_name, ' ', u.last_name) as user_name
                FROM support_tickets t
                LEFT JOIN support_categories c ON t.category = c.id
                JOIN users u ON t.user_id = u.id
                WHERE t.id = :ticket_id
            ");
            
            $stmt->bindParam(':ticket_id', $ticketId, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($stmt->rowCount() === 0) {
                return false;
            }
            
            $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Get ticket attachments
            $attachmentsStmt = $this->db->prepare("
                SELECT id, filename, original_name, file_type, file_size, created_at
                FROM support_attachments
                WHERE ticket_id = :ticket_id AND reply_id IS NULL
            ");
            
            $attachmentsStmt->bindParam(':ticket_id', $ticketId, PDO::PARAM_INT);
            $attachmentsStmt->execute();
            
            $ticket['attachments'] = $attachmentsStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get ticket replies
            $repliesStmt = $this->db->prepare("
                SELECT r.*,
                       u.first_name as user_first_name,
                       u.last_name as user_last_name,
                       CONCAT(u.first_name, ' ', u.last_name) as user_name,
                       u.role_name,
                       (u.role_name = 'admin' OR u.role_name = 'staff') as is_admin
                FROM support_replies r
                JOIN users u ON r.user_id = u.id
                WHERE r.ticket_id = :ticket_id
                ORDER BY r.created_at ASC
            ");
            
            $repliesStmt->bindParam(':ticket_id', $ticketId, PDO::PARAM_INT);
            $repliesStmt->execute();
            
            $replies = $repliesStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get attachments for each reply
            foreach ($replies as &$reply) {
                $replyAttachmentsStmt = $this->db->prepare("
                    SELECT id, filename, original_name, file_type, file_size, created_at
                    FROM support_attachments
                    WHERE reply_id = :reply_id
                ");
                
                $replyAttachmentsStmt->bindParam(':reply_id', $reply['id'], PDO::PARAM_INT);
                $replyAttachmentsStmt->execute();
                
                $reply['attachments'] = $replyAttachmentsStmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            $ticket['replies'] = $replies;
            
            return $ticket;
        } catch (PDOException $e) {
            error_log('Support::getTicketDetails Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update ticket status
     * 
     * @param int $ticketId Ticket ID
     * @param string $status New status
     * @return bool Success status
     */
    public function updateTicketStatus($ticketId, $status) {
        try {
            $stmt = $this->db->prepare("
                UPDATE support_tickets
                SET status = :status, updated_at = NOW()
                WHERE id = :ticket_id
            ");
            
            $stmt->bindParam(':ticket_id', $ticketId, PDO::PARAM_INT);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('Support::updateTicketStatus Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get support categories
     * 
     * @return array|bool Array of categories or false on failure
     */
    public function getCategories() {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM support_categories ORDER BY name ASC
            ");
            
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Support::getCategories Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all support requests for a specific user
     * 
     * @param int $user_id User ID
     * @return array|null Array of support requests or null if none found
     */
    public function getUserSupportRequests($user_id) {
        $sql = "SELECT s.*, 
                v.make as vehicle_make,
                v.model as vehicle_model,
                v.reg_number as vehicle_reg
                FROM support_requests s
                LEFT JOIN vehicles v ON s.vehicle_id = v.id
                WHERE s.user_id = ?
                ORDER BY s.created_at DESC";
                
        $result = $this->db->prepareAndExecute($sql, "i", [$user_id]);
        
        if ($result && $result->num_rows > 0) {
            $requests = [];
            while ($row = $result->fetch_assoc()) {
                $requests[] = $row;
            }
            return $requests;
        }
        
        return null;
    }
    
    /**
     * Get a single support request by ID
     * 
     * @param int $request_id Support request ID
     * @param int $user_id User ID for verification
     * @return array|null Support request details or null if not found
     */
    public function getSupportRequestById($request_id, $user_id) {
        $sql = "SELECT s.*, 
                v.make as vehicle_make,
                v.model as vehicle_model,
                v.reg_number as vehicle_reg
                FROM support_requests s
                LEFT JOIN vehicles v ON s.vehicle_id = v.id
                WHERE s.id = ? AND s.user_id = ?";
                
        $result = $this->db->prepareAndExecute($sql, "ii", [$request_id, $user_id]);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Create a new support request
     * 
     * @param array $request_data Support request data including user_id, vehicle_id, subject, etc.
     * @return bool|int False on failure, request ID on success
     */
    public function createSupportRequest($request_data) {
        $sql = "INSERT INTO support_requests (
                    user_id, vehicle_id, subject, description, 
                    priority, status, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, NOW())";
                
        $params = [
            $request_data['user_id'],
            $request_data['vehicle_id'],
            $request_data['subject'],
            $request_data['description'],
            $request_data['priority'],
            $request_data['status']
        ];
        
        $result = $this->db->prepareAndExecute($sql, "iissss", $params);
        
        if ($result) {
            return $this->db->getLastInsertId();
        }
        
        return false;
    }
    
    /**
     * Update support request status
     * 
     * @param int $request_id Support request ID
     * @param string $status New status
     * @param int $user_id User ID for verification
     * @return bool True on success, false on failure
     */
    public function updateSupportRequestStatus($request_id, $status, $user_id) {
        $sql = "UPDATE support_requests 
                SET status = ?, updated_at = NOW() 
                WHERE id = ? AND user_id = ?";
                
        $result = $this->db->prepareAndExecute($sql, "sii", [$status, $request_id, $user_id]);
        
        return $result ? true : false;
    }
    
    /**
     * Add a reply to a support request
     * 
     * @param array $reply_data Reply data including request_id, user_id, message, etc.
     * @return bool|int False on failure, reply ID on success
     */
    public function addSupportReply($reply_data) {
        $sql = "INSERT INTO support_replies (
                    request_id, user_id, message, created_at
                ) VALUES (?, ?, ?, NOW())";
                
        $params = [
            $reply_data['request_id'],
            $reply_data['user_id'],
            $reply_data['message']
        ];
        
        $result = $this->db->prepareAndExecute($sql, "iis", $params);
        
        if ($result) {
            return $this->db->getLastInsertId();
        }
        
        return false;
    }
    
    /**
     * Get all replies for a support request
     * 
     * @param int $request_id Support request ID
     * @return array|null Array of replies or null if none found
     */
    public function getSupportReplies($request_id) {
        $sql = "SELECT r.*, u.first_name, u.last_name
                FROM support_replies r
                LEFT JOIN users u ON r.user_id = u.id
                WHERE r.request_id = ?
                ORDER BY r.created_at ASC";
                
        $result = $this->db->prepareAndExecute($sql, "i", [$request_id]);
        
        if ($result && $result->num_rows > 0) {
            $replies = [];
            while ($row = $result->fetch_assoc()) {
                $replies[] = $row;
            }
            return $replies;
        }
        
        return null;
    }
}
?> 