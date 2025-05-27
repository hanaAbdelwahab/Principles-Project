<!--CarController.php-->
<?php
require_once __DIR__ . '/../Model/CarRepository.php';
require_once __DIR__ . '/../Model/UnavailableDateCalculator.php';
require_once __DIR__ . '/../config/dp.php';
require_once __DIR__ . '/../Filters/FilterFactory.php';

class CarController {
    private $repo;
    private $unavailableCalculator; 
    private $filterFactory;

    public function __construct(CarRepository $repo, UnavailableDateCalculator $calculator, FilterFactory $factory) {
        $this->repo = $repo;
        $this->unavailableCalculator = $calculator;
        $this->filterFactory = $factory;
    }

    public function showListings() {
        $filters = [
            'brand' => $_GET['brand'] ?? null,
            'location' => $_GET['location'] ?? null,
            'color' => $_GET['color'] ?? null,
            'pickup_date' => $_GET['pickup_date'] ?? null,
            'dropoff_date' => $_GET['dropoff_date'] ?? null,
            'max_price' => isset($_GET['max_price']) ? (int)$_GET['max_price'] : null
        ];

        $filterObjects = $this->filterFactory->create($filters);
        $cars = $this->repo->getFilteredCars($filters, $filterObjects);
        $options = $this->repo->getFilterOptions();

        $brands = $options['brands'] ?? [];
        $locations = $options['locations'] ?? [];
        $colors = $options['colors'] ?? [];
        $unavailableDates = $this->unavailableCalculator->getUnavailableDates();
        $dateRange = $options['dateRange'] ?? ['min_start' => '', 'max_end' => ''];

        return [
    'cars' => $cars,
    'brands' => $brands,
    'locations' => $locations,
    'colors' => $colors,
    'unavailableDates' => $unavailableDates,
    'dateRange' => $dateRange
];

    }
}