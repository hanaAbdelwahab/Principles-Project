<?php
require_once __DIR__ . '/../Model/Cars.php';
require_once __DIR__ . '/../config/db.php';

class CarRepository {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getFilteredCars($filters) {
        $sql = "SELECT * FROM cars WHERE 1=1";
        $params = [];

        if (!empty($filters['brand'])) {
            $sql .= " AND name = ?";
            $params[] = $filters['brand'];
        }
        if (!empty($filters['location'])) {
            $sql .= " AND location = ?";
            $params[] = $filters['location'];
        }
        if (!empty($filters['color'])) {
            $sql .= " AND color = ?";
            $params[] = $filters['color'];
        }
        if (!empty($filters['pickup_date']) && !empty($filters['dropoff_date'])) {
            $sql .= " AND (? >= start_date AND ? <= end_date)";
            $params[] = $filters['pickup_date'];
            $params[] = $filters['dropoff_date'];
        }
if (!empty($filters['max_price']) && $filters['max_price'] > 0) {
    $sql .= " AND price_per_day >= ?";
    $params[] = $filters['max_price'];
}


        $stmt = $this->pdo->prepare($sql);
        
        $stmt->execute($params);
        return array_map(fn($row) => new Car($row), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getFilterOptions() {
        return [
            'brands' => $this->pdo->query("SELECT DISTINCT name FROM cars")->fetchAll(PDO::FETCH_COLUMN),
            'locations' => $this->pdo->query("SELECT DISTINCT location FROM cars WHERE location IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN),
            'colors' => $this->pdo->query("SELECT DISTINCT color FROM cars WHERE color IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN),
            'dateRange' => $this->pdo->query("SELECT MIN(start_date) AS min_start, MAX(end_date) AS max_end FROM cars")->fetch(PDO::FETCH_ASSOC)
        ];
    }
}
