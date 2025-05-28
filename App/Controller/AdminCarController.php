<?php

require_once __DIR__ . '/../Model/AdminCar.php';
require_once __DIR__ . '/../config/dp.php'; // Include the Database class

interface PricingStrategy {
    public function calculate($baseRate);
}

class DailyRateStrategy implements PricingStrategy {
    public function calculate($baseRate) {
        return $baseRate;
    }
}

class WeekendRateStrategy implements PricingStrategy {
    public function calculate($baseRate) {
        return $baseRate * 1.2;
    }
}

class HolidayRateStrategy implements PricingStrategy {
    public function calculate($baseRate) {
        return $baseRate * 1.5;
    }
}

class AdminCarController {
    private $car;

    public function __construct($carModel = null) {
        if ($carModel) {
            $this->car = $carModel;
        } else {
            $pdo = Database::getInstance(); // Get PDO instance via singleton
            $this->car = new AdminCar($pdo); // Make sure AdminCar accepts PDO in constructor
        }
    }

    // === FACADE PATTERN IMPLEMENTED INLINE === //
    public function getCarsWithAdjustedPricing($strategyType = 'daily') {
        switch (strtolower($strategyType)) {
            case 'weekend':
                $strategy = new WeekendRateStrategy();
                break;
            case 'holiday':
                $strategy = new HolidayRateStrategy();
                break;
            default:
                $strategy = new DailyRateStrategy();
                break;
        }

        $cars = $this->car->getAll();
        foreach ($cars as &$car) {
            $car['adjusted_price'] = $strategy->calculate($car['price_per_day']);
        }
        return $cars;
    }

    // === CRUD METHODS === //

    // CREATE
    public function createCar($data) {
        return $this->car->create($data);
    }

    // READ
    public function getAllCars() {
        return $this->car->getAll();
    }

    public function getCarById($id) {
        return $this->car->findById($id);
    }

    // UPDATE
    public function updateCar($id, $data) {
        return $this->car->update($id, $data);
    }

    // DELETE
    public function deleteCar($id) {
        return $this->car->delete($id);
    }
}
