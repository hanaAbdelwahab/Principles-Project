<?php
require_once __DIR__ . '/../Model/CarRepository.php';

class CarController {
    private $repo;

    public function __construct() {
        $this->repo = new CarRepository(require __DIR__ . '/../config/db.php');
    }

    public function showListings() {
        $filters = [];

        if (!empty($_GET)) {
            $filters = [
                'brand' => $_GET['brand'] ?? null,
                'location' => $_GET['location'] ?? null,
                'color' => $_GET['color'] ?? null,
                'pickup_date' => $_GET['pickup_date'] ?? null,
                'dropoff_date' => $_GET['dropoff_date'] ?? null,
                'max_price' => isset($_GET['max_price']) ? (int)$_GET['max_price'] : null
            ];
        }

        // Get filtered cars including new fields like wheels and brakes
        $cars = $this->repo->getFilteredCars($filters);

        // Get available filter options (e.g., brands, colors, locations, date range)
        $options = $this->repo->getFilterOptions();

        $brands = $options['brands'] ?? [];
        $locations = $options['locations'] ?? [];
        $colors = $options['colors'] ?? [];
        $dateRange = $options['dateRange'] ?? ['min_start' => '', 'max_end' => ''];

        // Render the listings view
        require __DIR__ . '/../view/codeListing.php';
    }
}
