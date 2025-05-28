<?php
require_once __DIR__ . '/../config/dp.php';

class AdminUser {
    private $conn;

    public function __construct($pdo = null) {
        $this->conn = $pdo ?? Database::getInstance();
    }

    // LOGIN
    public function login($usernameOrEmail, $password) {
        $query = "SELECT * FROM users WHERE (username = :user OR email = :user) LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user', $usernameOrEmail);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

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
            VALUES (:username, :email, :password, :birthdate, :license, :id_path, :fav_color)
        ");
        return $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':birthdate' => $birthdate,
            ':license' => $driver_license_path,
            ':id_path' => $national_id_path,
            ':fav_color' => $favorite_color
        ]);
    }

    // READ - Get all users
    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM users");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // READ - Get user by ID
    public function findById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // UPDATE
    public function update($id, $username, $email, $birthdate, $favorite_color, $licensePath = null, $idPath = null) {
        $query = "UPDATE users SET username = :username, email = :email, birthdate = :birthdate, favorite_color = :fav_color";
        $params = [
            ':username' => $username,
            ':email' => $email,
            ':birthdate' => $birthdate,
            ':fav_color' => $favorite_color,
            ':id' => $id
        ];

        if ($licensePath !== null) {
            $query .= ", driver_license_path = :license";
            $params[':license'] = $licensePath;
        }

        if ($idPath !== null) {
            $query .= ", national_id_path = :id_path";
            $params[':id_path'] = $idPath;
        }

        $query .= " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params);
    }

    // DELETE
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
