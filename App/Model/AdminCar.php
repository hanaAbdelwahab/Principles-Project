<?php
require_once __DIR__ . '/../config/dp.php';

class AdminCar {
    private $conn;

    public function __construct($pdo = null) {
        $this->conn = $pdo ?? Database::getInstance();
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
            die("Prepare failed");
        }

        return $stmt->execute([
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
        ]);
    }

    // READ - Get all cars
    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM cars");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // READ - Get car by ID
    public function findById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM cars WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // UPDATE
    public function update($id, $data) {
        $existing = $this->findById($id);
        if (!$existing) {
            return false;
        }

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

        return $stmt->execute([
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
        ]);
    }

    // DELETE
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM cars WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
