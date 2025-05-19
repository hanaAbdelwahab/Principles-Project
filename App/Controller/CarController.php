<?php
include_once __DIR__ . '/../Model/Car.php';

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

class CarController {
    private $car;

    public function __construct($carModel = null) {
        $this->car = $carModel ?? new Car(); // Dependency Injection (SOLID)
    }

    // === FACADE PATTERN IMPLEMENTED INLINE === //
    public function getCarsWithAdjustedPricing($strategyType = 'daily') {
        switch (strtolower($strategyType)) {
            case 'weekend':
                $strategy = new WeekendRateStrategy(); break;
            case 'holiday':
                $strategy = new HolidayRateStrategy(); break;
            default:
                $strategy = new DailyRateStrategy(); break;
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
