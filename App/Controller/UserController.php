<?php
include_once __DIR__ . '/../Model/User.php';

class UserController {
    private $user;

    public function __construct($userModel = null) {
        $this->user = $userModel ?? new User(); // Dependency Injection
    }

    // CREATE USER
    public function createUser($username, $email, $password, $birthdate, $driver_license_path, $national_id_path, $favorite_color = null) {
        return $this->user->create($username, $email, $password, $birthdate, $driver_license_path, $national_id_path, $favorite_color);
    }

    // READ ALL USERS
    public function getAllUsers() {
        return $this->user->getAll();
    }

    // READ USER BY ID
    public function getUserById($id) {
        return $this->user->findById($id);
    }

    // UPDATE USER (Basic info only, not password/ID paths)
  public function updateUser($id, $username, $email, $birthdate, $favorite_color, $driver_license_path = null, $national_id_path = null) {
    return $this->user->update($id, $username, $email, $birthdate, $favorite_color, $driver_license_path, $national_id_path);
}


    // DELETE USER
    public function deleteUser($id) {
        return $this->user->delete($id);
    }

    // LOGIN FUNCTION
    public function login($usernameOrEmail, $password) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $loggedInUser = $this->user->login($usernameOrEmail, $password);

        if ($loggedInUser) {
            $_SESSION['id'] = $loggedInUser['id'];
            $_SESSION['username'] = $loggedInUser['username'];
            $_SESSION['email'] = $loggedInUser['email'];

            header("Location: ../View/index.php");
            exit();
        } else {
            return "Invalid username/email or password!";
        }
    }
}
