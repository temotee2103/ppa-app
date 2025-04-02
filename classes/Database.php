<?php
/**
 * 数据库连接类
 * 处理与MySQL数据库的连接和交互
 */
class Database {
    private $connection;
    private static $instance = null;
    
    /**
     * 构造函数 - 创建数据库连接
     */
    private function __construct() {
        global $config;
        
        try {
            $this->connection = new mysqli(
                $config['db_host'],
                $config['db_user'],
                $config['db_pass'],
                $config['db_name']
            );
            
            if ($this->connection->connect_error) {
                throw new Exception("Database connection failed: " . $this->connection->connect_error);
            }
            
            // 设置字符集为UTF-8
            $this->connection->set_charset("utf8mb4");
        } catch (Exception $e) {
            error_log($e->getMessage());
            die("Database connection error. Please try again later.");
        }
    }
    
    /**
     * 获取单例实例
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * 执行SQL查询
     */
    public function query($sql) {
        $result = $this->connection->query($sql);
        if (!$result) {
            error_log("Query error: " . $this->connection->error . " in query: " . $sql);
            return false;
        }
        return $result;
    }
    
    /**
     * 准备并执行参数化查询
     */
    public function prepareAndExecute($sql, $types, $params) {
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->connection->error . " in query: " . $sql);
            return false;
        }
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $result = $stmt->execute();
        if (!$result) {
            error_log("Execute failed: " . $stmt->error);
            $stmt->close();
            return false;
        }
        
        // 检查语句类型，SELECT语句返回结果集，其他语句返回true
        if ($stmt->field_count > 0) {
            // SELECT语句
            $resultSet = $stmt->get_result();
            $stmt->close();
            return $resultSet;
        } else {
            // INSERT, UPDATE, DELETE等语句
            $affectedRows = $stmt->affected_rows;
            $stmt->close();
            return ($affectedRows >= 0); // 返回true表示执行成功
        }
    }
    
    /**
     * 获取最后插入的ID
     */
    public function getLastInsertId() {
        return $this->connection->insert_id;
    }
    
    /**
     * 关闭数据库连接
     */
    public function closeConnection() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
    
    /**
     * 转义字符串以防止SQL注入
     */
    public function escapeString($string) {
        return $this->connection->real_escape_string($string);
    }
    
    /**
     * 开始一个事务
     */
    public function beginTransaction() {
        $this->connection->begin_transaction();
    }
    
    /**
     * 提交一个事务
     */
    public function commit() {
        $this->connection->commit();
    }
    
    /**
     * 回滚一个事务
     */
    public function rollback() {
        $this->connection->rollback();
    }
    
    /**
     * 获取最后一次执行的SQL语句的错误信息
     * 
     * @return string 错误信息
     */
    public function getError() {
        return $this->connection->error;
    }
} 