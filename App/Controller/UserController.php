<!-- Controller/UserController.php -->
<?php
// Include required files
require_once '../config/dp.php';
require_once '../Model/User.php';

class UserController {
    private $db;
    private $user;
    
    public function __construct() {
        // Get database connection
        $database = new Database();
        $db = $database->getConnection();
        
        // Initialize user object
        $this->user = new User($db);
    }
    
    // Handle user registration
    public function register($userData, $fileData) {
        // Input validation
        $errors = [];
        
        if(empty($userData['username'])) {
            $errors[] = "Username is required";
        }
        
        if(empty($userData['email']) || !filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Valid email is required";
        } else {
            // Check if email already exists
            $this->user->email = $userData['email'];
            if($this->user->emailExists()) {
                $errors[] = "Email already exists";
            }
        }
        
        if(empty($userData['password'])) {
            $errors[] = "Password is required";
        }
        
        if($userData['password'] !== $userData['confirm_password']) {
            $errors[] = "Passwords do not match";
        }
        
        if(empty($userData['birthdate'])) {
            $errors[] = "Birthdate is required";
        } else {
            // Validate birthdate - check if user is at least 18 years old and not in the future
            $birthdate = new DateTime($userData['birthdate']);
            $today = new DateTime('today');
            $age = $birthdate->diff($today)->y;
            
            // Check if birthdate is in the future
            if($birthdate > $today) {
                $errors[] = "Birthdate cannot be in the future";
            }
            
            // Check if user is at least 18 years old
            if($age < 18) {
                $errors[] = "You must be at least 18 years old to register";
            }
        }
        
        // File validation
        if($fileData['driver_license']['error'] !== 0 || !$this->isValidFile($fileData['driver_license'])) {
            $errors[] = "Valid driver's license is required";
        }
        
        if($fileData['national_id']['error'] !== 0 || !$this->isValidFile($fileData['national_id'])) {
            $errors[] = "Valid national ID is required";
        }
        
        // If there are validation errors, return them
        if(!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }
        
        // Process file uploads
        $driver_license_path = $this->user->uploadFile($fileData['driver_license']);
        $national_id_path = $this->user->uploadFile($fileData['national_id']);
        
        if(!$driver_license_path || !$national_id_path) {
            return [
                'success' => false,
                'errors' => ["File upload failed"]
            ];
        }
        
        // Set user properties
        $this->user->username = $userData['username'];
        $this->user->email = $userData['email'];
        $this->user->password = $userData['password'];
        $this->user->birthdate = $userData['birthdate'];
        $this->user->driver_license_path = $driver_license_path;
        $this->user->national_id_path = $national_id_path;
        $this->user->favorite_color = $userData['favorite_color'] ?? null;
        
        // Create the user
        if($this->user->create()) {
            return [
                'success' => true
            ];
        } else {
            return [
                'success' => false,
                'errors' => ["Unable to create account"]
            ];
        }
    }
    
    // Handle user login
    public function login($email, $password) {
        // Validate input
        if(empty($email) || empty($password)) {
            return [
                'success' => false,
                'error' => "Please enter both email and password"
            ];
        }
        
        // Check if email exists
        $this->user->email = $email;
        if($this->user->emailExists()) {
            // Verify password
            if(password_verify($password, $this->user->password)) {
                // Create session
                $_SESSION['user_id'] = $this->user->id;
                $_SESSION['user_name'] = $this->user->username;
                $_SESSION['user_email'] = $this->user->email;
                
                return [
                    'success' => true,
                    'redirect' => "Homepage.php"
                ];
            } else {
                return [
                    'success' => false,
                    'error' => "Invalid password"
                ];
            }
        } else {
            return [
                'success' => false,
                'error' => "Email not found"
            ];
        }
    }
    
    // Handle forgot password
    public function forgotPassword($email, $color) {
        if(empty($email) || empty($color)) {
            return [
                'success' => false,
                'error' => "Please provide email and your favorite color"
            ];
        }
        
        // Verify email and color
        if($this->user->verifyFavoriteColor($email, $color)) {
            // Generate reset token
            $token = $this->user->createPasswordResetToken();
            
            if($token) {
                // In a real application, you would send an email with a reset link
                // For demonstration, we'll just return a success message
                return [
                    'success' => true,
                    'message' => "Password reset link has been sent to your email"
                ];
            }
        }
        
        return [
            'success' => false,
            'error' => "Invalid email or favorite color"
        ];
    }
    
    // Validate file upload
    private function isValidFile($file) {
        // Check file size (max 5MB)
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        if($file['size'] > $maxFileSize) {
            return false;
        }
        
        // Check file type (image or PDF)
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
        if(!in_array($file['type'], $allowedTypes)) {
            return false;
        }
        
        return true;
    }
    
    // Logout user
    public function logout() {
        // Unset all session variables
        $_SESSION = [];
        
        // Destroy the session
        session_destroy();
        
        return [
            'success' => true,
            'redirect' => "Login.php"
        ];
    }
}