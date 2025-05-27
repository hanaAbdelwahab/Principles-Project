<!--CarRepository.php-->
<?php
require_once __DIR__ . '/../Model/Cars.php';
require_once __DIR__ . '/../Model/Bookings.php';

class CarRepository {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getFilteredCars(array $filters, array $filterObjects): array {
        $sql = "SELECT * FROM cars WHERE 1=1";
        $params = [];

        foreach ($filterObjects as $filter) {
        $filter->apply($sql, $params);
        }


        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Filter by pickup and dropoff availability
        if (!empty($filters['pickup_date']) && !empty($filters['dropoff_date'])) {
            $pickup = new DateTime($filters['pickup_date']);
            $dropoff = new DateTime($filters['dropoff_date']);
            $validCars = [];
            foreach ($cars as $car) {
                $carStart = new DateTime($car['start_date']);
                $carEnd = new DateTime($car['end_date']);
                if ($pickup < $carStart || $dropoff > $carEnd) {
                    continue; // out of car's available range
                }
                // Check for booking conflict
                $booked = $this->pdo->prepare("SELECT COUNT(*) FROM bookings WHERE car_id = ? AND (start_date <= ? AND end_date >= ?)");
                $conflict = false;
                $scan = clone $pickup;
                while ($scan <= $dropoff) {
                    $scanStr = $scan->format('Y-m-d');
                    $booked->execute([$car['id'], $scanStr, $scanStr]);
                    if ($booked->fetchColumn() > 0) {
                        $conflict = true;
                        break;
                    }
                    $scan->modify('+1 day');
                }
                if (!$conflict) {
                    $validCars[] = new Car($car);
                }
            }
            return $validCars;
        }
        return array_map(fn($row) => new Car($row), $cars);
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
