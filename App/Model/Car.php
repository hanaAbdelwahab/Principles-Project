<?php
include_once(__DIR__ . '/../config/db.php');

class Car {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    // CREATE
    public function create($data) {
        $stmt = $this->conn->prepare("
            INSERT INTO cars 
            (name, model, year, price_per_day, image_filename, description, color, location, start_date, end_date, rate, renters, transmission_type, power_type, wheels, brakes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "sssdssssssdiisss",
            $data['name'], $data['model'], $data['year'], $data['price_per_day'],
            $data['image_filename'], $data['description'], $data['color'], $data['location'],
            $data['start_date'], $data['end_date'], $data['rate'], $data['renters'],
            $data['transmission_type'], $data['power_type'], $data['wheels'], $data['brakes']
        );

        return $stmt->execute();
    }

    // READ - Get all cars
    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM cars");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // READ - Get car by ID
    public function findById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM cars WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // UPDATE
    public function update($id, $data) {
        $stmt = $this->conn->prepare("
            UPDATE cars SET 
                name = ?, model = ?, year = ?, price_per_day = ?, image_filename = ?, description = ?, 
                color = ?, location = ?, start_date = ?, end_date = ?, rate = ?, renters = ?, 
                transmission_type = ?, power_type = ?, wheels = ?, brakes = ? 
            WHERE id = ?
        ");

        $stmt->bind_param(
            "sssdssssssdiisssi",
            $data['name'], $data['model'], $data['year'], $data['price_per_day'],
            $data['image_filename'], $data['description'], $data['color'], $data['location'],
            $data['start_date'], $data['end_date'], $data['rate'], $data['renters'],
            $data['transmission_type'], $data['power_type'], $data['wheels'], $data['brakes'], $id
        );

        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    // DELETE
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM cars WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }
}
