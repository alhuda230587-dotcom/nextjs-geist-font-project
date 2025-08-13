<?php
/**
 * Database connection file for PHP School Tuition Payment System (SPP)
 * Uses PDO for secure database operations
 */

require_once 'config.php';

class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $conn;

    /**
     * Get database connection
     */
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch(PDOException $exception) {
            error_log("Connection error: " . $exception->getMessage());
            die("Database connection failed. Please try again later.");
        }

        return $this->conn;
    }

    /**
     * Close database connection
     */
    public function closeConnection() {
        $this->conn = null;
    }
}

/**
 * Get database connection instance
 */
function getDBConnection() {
    $database = new Database();
    return $database->getConnection();
}

/**
 * Execute a prepared statement with parameters
 */
function executeQuery($sql, $params = []) {
    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch(PDOException $e) {
        error_log("Query error: " . $e->getMessage());
        throw new Exception("Database operation failed");
    }
}

/**
 * Fetch single row from database
 */
function fetchSingle($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetch();
}

/**
 * Fetch multiple rows from database
 */
function fetchAll($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetchAll();
}

/**
 * Insert data and return last insert ID
 */
function insertData($sql, $params = []) {
    $conn = getDBConnection();
    $stmt = executeQuery($sql, $params);
    return $conn->lastInsertId();
}

/**
 * Update or delete data and return affected rows
 */
function updateData($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->rowCount();
}

/**
 * Check if table exists
 */
function tableExists($tableName) {
    $sql = "SHOW TABLES LIKE ?";
    $result = fetchSingle($sql, [$tableName]);
    return !empty($result);
}

/**
 * Get total count from a table with conditions
 */
function getCount($table, $conditions = [], $params = []) {
    $sql = "SELECT COUNT(*) as count FROM " . $table;
    
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }
    
    $result = fetchSingle($sql, $params);
    return $result['count'] ?? 0;
}

/**
 * Get paginated results
 */
function getPaginatedResults($sql, $params = [], $page = 1, $perPage = RECORDS_PER_PAGE) {
    $offset = ($page - 1) * $perPage;
    $sql .= " LIMIT $perPage OFFSET $offset";
    
    return fetchAll($sql, $params);
}

/**
 * Begin transaction
 */
function beginTransaction() {
    $conn = getDBConnection();
    return $conn->beginTransaction();
}

/**
 * Commit transaction
 */
function commitTransaction() {
    $conn = getDBConnection();
    return $conn->commit();
}

/**
 * Rollback transaction
 */
function rollbackTransaction() {
    $conn = getDBConnection();
    return $conn->rollback();
}

/**
 * Escape string for LIKE queries
 */
function escapeLike($string) {
    return str_replace(['%', '_'], ['\%', '\_'], $string);
}

/**
 * Build search conditions for queries
 */
function buildSearchConditions($searchFields, $searchTerm) {
    if (empty($searchTerm) || empty($searchFields)) {
        return ['', []];
    }
    
    $conditions = [];
    $params = [];
    $escapedTerm = '%' . escapeLike($searchTerm) . '%';
    
    foreach ($searchFields as $field) {
        $conditions[] = "$field LIKE ?";
        $params[] = $escapedTerm;
    }
    
    $whereClause = '(' . implode(' OR ', $conditions) . ')';
    
    return [$whereClause, $params];
}

/**
 * Validate database connection
 */
function validateConnection() {
    try {
        $conn = getDBConnection();
        $stmt = $conn->query("SELECT 1");
        return true;
    } catch(Exception $e) {
        return false;
    }
}

/**
 * Get database version
 */
function getDatabaseVersion() {
    try {
        $result = fetchSingle("SELECT VERSION() as version");
        return $result['version'] ?? 'Unknown';
    } catch(Exception $e) {
        return 'Unknown';
    }
}
?>
