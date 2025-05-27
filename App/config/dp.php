<!--dp.php-->
<?php
// Database configuration class
class Database {
    // Database credentials
    private $host = "localhost";
    private $db_name = "Principles";
    private $username = "root";
    private $password = "";
    public $conn;
    private static ?PDO $instance = null;
    // Get database connection
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            // Set PDO error mode to exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
     public static function getInstance(): PDO {
        if (!self::$instance) {
            self::$instance = new PDO('mysql:host=localhost;dbname=principles', 'root', '');
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$instance;
    }
}