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
        INSERT INTO cars (
            name, model, year, price_per_day, image_filename,
            description, color, location, start_date, end_date,
            renters, rate, transmission_type, power_type, wheels, brakes
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        die("Prepare failed: " . $this->conn->error);
    }

    $stmt->bind_param(
        "ssisssssssssss",
        $data['name'],
        $data['model'],
        $data['year'],
        $data['price_per_day'],
        $data['image_filename'],
        $data['description'],
        $data['color'],
        $data['location'],
        $data['start_date'],
        $data['end_date'],
        $data['transmission_type'],
        $data['power_type'],
        $data['wheels'],
        $data['brakes']
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
        $existing = $this->findById($id);

        $fields = ['name', 'model', 'year', 'price_per_day', 'image_filename', 'description', 'color', 'location', 'start_date', 'end_date', 'transmission_type', 'power_type', 'wheels', 'brakes'];

        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $data[$field] = $existing[$field];
            }
        }

        $stmt = $this->conn->prepare("
            UPDATE cars SET 
                name = ?, model = ?, year = ?, price_per_day = ?, image_filename = ?, description = ?, color = ?, location = ?, 
                start_date = ?, end_date = ?, transmission_type = ?, power_type = ?, wheels = ?, brakes = ?
            WHERE id = ?
        ");

        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }

        // 15 values bound, so 15 types (s, s, i, s...)
        $stmt->bind_param(
            "ssisssssssssssi",
            $data['name'],
            $data['model'],
            $data['year'],
            $data['price_per_day'],
            $data['image_filename'],
            $data['description'],
            $data['color'],
            $data['location'],
            $data['start_date'],
            $data['end_date'],
            $data['transmission_type'],
            $data['power_type'],
            $data['wheels'],
            $data['brakes'],
            $id
        );

        return $stmt->execute();
    }

    // DELETE
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM cars WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }
}
