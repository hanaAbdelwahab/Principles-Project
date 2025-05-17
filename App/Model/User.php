<?php
// Include database configuration
require_once '../config/dp.php';

class User {
    // Database connection and table name
    private $conn;
    private $table_name = "users";
    
    // Object properties
    public $id;
    public $username;
    public $email;
    public $password;
    public $birthdate;
    public $driver_license_path;
    public $national_id_path;
    public $favorite_color;
    public $created_at;
    public $updated_at;
    
    // Constructor with DB connection
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Create new user
    public function create() {
        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    username=:username, 
                    email=:email, 
                    password=:password, 
                    birthdate=:birthdate, 
                    driver_license_path=:driver_license_path, 
                    national_id_path=:national_id_path,
                    favorite_color=:favorite_color";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize input
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        // Password will be hashed, no need to sanitize
        $this->birthdate = htmlspecialchars(strip_tags($this->birthdate));
        $this->driver_license_path = htmlspecialchars(strip_tags($this->driver_license_path));
        $this->national_id_path = htmlspecialchars(strip_tags($this->national_id_path));
        $this->favorite_color = htmlspecialchars(strip_tags($this->favorite_color));
        
        // Hash the password
        $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
        
        // Bind values
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":birthdate", $this->birthdate);
        $stmt->bindParam(":driver_license_path", $this->driver_license_path);
        $stmt->bindParam(":national_id_path", $this->national_id_path);
        $stmt->bindParam(":favorite_color", $this->favorite_color);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Check if email exists
    public function emailExists() {
        // Query to check if email exists
        $query = "SELECT id, username, password, email
                FROM " . $this->table_name . "
                WHERE email = ?
                LIMIT 0,1";
        
        // Prepare the query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->email = htmlspecialchars(strip_tags($this->email));
        
        // Bind given email value
        $stmt->bindParam(1, $this->email);
        
        // Execute the query
        $stmt->execute();
        
        // Get row count
        $num = $stmt->rowCount();
        
        // If email exists, assign values to object properties for easy access and use for php sessions
        if($num > 0) {
            // Get record details
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Assign values to object properties
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->password = $row['password'];
            $this->email = $row['email'];
            
            return true;
        }
        
        return false;
    }
    
    // Check if color matches user's favorite color (for password reset)
    public function verifyFavoriteColor($email, $color) {
        // Query to check if email and color match
        $query = "SELECT id FROM " . $this->table_name . "
                WHERE email = ? AND favorite_color = ?
                LIMIT 0,1";
        
        // Prepare the query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $email = htmlspecialchars(strip_tags($email));
        $color = htmlspecialchars(strip_tags($color));
        
        // Bind parameters
        $stmt->bindParam(1, $email);
        $stmt->bindParam(2, $color);
        
        // Execute the query
        $stmt->execute();
        
        // If color matches, return true
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            return true;
        }
        
        return false;
    }
    
    // Create password reset token
    public function createPasswordResetToken() {
        // Generate token
        $token = bin2hex(random_bytes(32));
        
        // Set expiry time (1 hour from now)
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Delete any existing tokens for this user
        $query = "DELETE FROM password_reset_tokens WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        // Create new token
        $query = "INSERT INTO password_reset_tokens SET
                user_id = ?,
                token = ?,
                expires_at = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->bindParam(2, $token);
        $stmt->bindParam(3, $expiry);
        
        if($stmt->execute()) {
            return $token;
        }
        
        return false;
    }
    
    // Update user password
    public function updatePassword($new_password) {
        // Query to update password
        $query = "UPDATE " . $this->table_name . "
                SET password = :password
                WHERE id = :id";
        
        // Prepare the query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize and hash
        $new_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Bind parameters
        $stmt->bindParam(':password', $new_password);
        $stmt->bindParam(':id', $this->id);
        
        // Execute the query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }

    // Upload file and return path
    public function uploadFile($file, $destination_folder = "uploads/") {
        // Check if uploads directory exists, create it if not
        if (!file_exists($destination_folder)) {
            mkdir($destination_folder, 0777, true);
        }

        // Generate unique filename to prevent overwriting
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
        $destination = $destination_folder . $new_filename;
        
        // Move uploaded file to destination
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $destination;
        }
        
        return false;
    }
}