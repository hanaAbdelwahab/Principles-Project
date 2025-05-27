<?php
include_once(__DIR__ . '/../config/db.php');

class User {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    // LOGIN
    public function login($usernameOrEmail, $password) {
        $query = "SELECT * FROM users WHERE (username = ? OR email = ?) LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ss', $usernameOrEmail, $usernameOrEmail);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }

    // CREATE
    public function create($username, $email, $password, $birthdate, $driver_license_path, $national_id_path, $favorite_color = null) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("
            INSERT INTO users (username, email, password, birthdate, driver_license_path, national_id_path, favorite_color)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssssss", $username, $email, $hashedPassword, $birthdate, $driver_license_path, $national_id_path, $favorite_color);
        return $stmt->execute();
    }

    // READ - Get all users
    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM users");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // READ - Get user by ID
    public function findById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // UPDATE
 public function update($id, $username, $email, $birthdate, $favorite_color, $licensePath = null, $idPath = null) {
    $query = "UPDATE users SET username = ?, email = ?, birthdate = ?, favorite_color = ?";
    $params = [$username, $email, $birthdate, $favorite_color];
    $types = "ssss";

    if ($licensePath) {
        $query .= ", driver_license_path = ?";
        $params[] = $licensePath;
        $types .= "s";
    }

    if ($idPath) {
        $query .= ", national_id_path = ?";
        $params[] = $idPath;
        $types .= "s";
    }

    $query .= " WHERE id = ?";
    $params[] = $id;
    $types .= "i";

    $stmt = $this->conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt->affected_rows > 0;
}


    // DELETE
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }
}
