<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', '4j-pos');

class Database {
    private $host = DB_HOST;
    private $db_user = DB_USER;
    private $db_pass = DB_PASS;
    private $db_name = DB_NAME;
    private $mysqli;

    public function __construct() {
        $this->mysqli = new mysqli($this->host, $this->db_user, $this->db_pass, $this->db_name);
        
        // Check connection
        if ($this->mysqli->connect_error) {
            die("Connection failed: " . $this->mysqli->connect_error);
        }
        
        // Set charset to UTF-8
        $this->mysqli->set_charset("utf8mb4");
    }

    public function getConnection() {
        return $this->mysqli;
    }

    public function closeConnection() {
        $this->mysqli->close();
    }
}

// Initialize database instance
$database = new Database();
$mysqli = $database->getConnection();

?>
