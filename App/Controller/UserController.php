<?php
require_once '../config/dp.php';
require_once '../Model/User.php';

/* --- SOLID LSP & ISP (inline interfaces) --- */

// ISP: Split small interfaces for User
interface Authenticatable {
    public function emailExists();
}
interface Registrable {
    public function create();
}

// LSP: Use interface for type hints
class UserController {
    private $db;
    private $user; // type: Authenticatable & Registrable

    // LSP: Allow injection of any Authenticatable+Registrable object
    public function __construct($user = null) {
        $database = new Database();
        $db = $database->getConnection();
        $this->user = $user ?: new User($db); // fallback to User if not supplied
    }

    // Strategy Pattern: Inline, for registration validation
    private function validateRegistration($userData, $fileData) {
        $errors = [];
        if(empty($userData['username'])) $errors[] = "Username is required";
        if(empty($userData['email']) || !filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Valid email is required";
        } else {
            $this->user->email = $userData['email'];
            if($this->user->emailExists()) $errors[] = "Email already exists";
        }
        if(empty($userData['password'])) $errors[] = "Password is required";
        if($userData['password'] !== $userData['confirm_password']) $errors[] = "Passwords do not match";
        if(empty($userData['birthdate'])) {
            $errors[] = "Birthdate is required";
        } else {
            $birthdate = new DateTime($userData['birthdate']);
            $today = new DateTime('today');
            $age = $birthdate->diff($today)->y;
            if($birthdate > $today) $errors[] = "Birthdate cannot be in the future";
            if($age < 18) $errors[] = "You must be at least 18 years old to register";
        }
        if($fileData['driver_license']['error'] !== 0 || !$this->isValidFile($fileData['driver_license'])) {
            $errors[] = "Valid driver's license is required";
        }
        if($fileData['national_id']['error'] !== 0 || !$this->isValidFile($fileData['national_id'])) {
            $errors[] = "Valid national ID is required";
        }
        return $errors;
    }

    // Command Pattern: Inline class for registration
    private function registerCommand($userObj) {
        return $userObj->create();
    }

    // Decorator Pattern: Inline, for logging (example, does nothing extra)
    private function userDecorator($user) {
        return $user; // could wrap for logging, etc.
    }

    // Adapter Pattern: Inline, for possible external user class
    private function adaptUser($user) {
        return $user; // could adapt interface if needed
    }

    // Handle user registration
    public function register($userData, $fileData) {
        // Use Strategy
        $errors = $this->validateRegistration($userData, $fileData);
        if(!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        $driver_license_path = $this->user->uploadFile($fileData['driver_license']);
        $national_id_path = $this->user->uploadFile($fileData['national_id']);

        if(!$driver_license_path || !$national_id_path) {
            return [
                'success' => false,
                'errors' => ["File upload failed"]
            ];
        }

        $this->user->username = $userData['username'];
        $this->user->email = $userData['email'];
        $this->user->password = $userData['password'];
        $this->user->birthdate = $userData['birthdate'];
        $this->user->driver_license_path = $driver_license_path;
        $this->user->national_id_path = $national_id_path;
        $this->user->favorite_color = $userData['favorite_color'] ?? null;

        // Use Decorator and Command
        $decoratedUser = $this->userDecorator($this->user);
        $result = $this->registerCommand($decoratedUser);

        if($result) {
            return ['success' => true];
        } else {
            return [
                'success' => false,
                'errors' => ["Unable to create account"]
            ];
        }
    }

    // Handle user login
    public function login($email, $password) {
        if(empty($email) || empty($password)) {
            return [
                'success' => false,
                'error' => "Please enter both email and password"
            ];
        }
        if ($email === 'admin@gmail.com' && $password === '1111') {
        // Set admin session variables if needed
        $_SESSION['admin_email'] = $email;
        return [
            'success' => true,
            'redirect' => 'admin-analytics-overview.php'
        ];
    }
        $this->user->email = $email;
        if($this->user->emailExists()) {
            if(password_verify($password, $this->user->password)) {
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
        if($this->user->verifyFavoriteColor($email, $color)) {
            $token = $this->user->createPasswordResetToken();
            if($token) {
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
        $maxFileSize = 5 * 1024 * 1024;
        if($file['size'] > $maxFileSize) return false;
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
        if(!in_array($file['type'], $allowedTypes)) return false;
        return true;
    }

    // Logout user
    public function logout() {
        $_SESSION = [];
        session_destroy();
        return [
            'success' => true,
            'redirect' => "Login.php"
        ];
    }
}
?>