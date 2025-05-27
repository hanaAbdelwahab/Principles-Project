<?php
define('BASE_URL', '/Final-Principles');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'App/view/Homepage.php';
// Include the Homepage.php file
require_once __DIR__ . '/App/config/dp.php';
require_once __DIR__ . '/App/Controller/CarController.php';
require_once __DIR__ . '/App/Model/CarRepository.php';
require_once __DIR__ . '/App/Model/UnavailableDateCalculator.php';
require_once __DIR__ . '/App/Filters/FilterFactory.php';

$pdo = Database::getInstance();
$repo = new CarRepository($pdo);
$calculator = new UnavailableDateCalculator($pdo);
$filterFactory = new FilterFactory();

$controller = new CarController($repo, $calculator, $filterFactory);
$data = $controller->showListings();
extract($data); // make $cars, $brands, etc. available

$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'index';

// Initialize controller based on page
switch ($page) {
     case 'booking':
        require_once 'App/Controller/BookingController.php';
        $controller = new App\Controller\BookingController($db);
        break;
    case 'payment':
        require_once 'App/Controller/PaymentController.php';
        $controller = new App\Controller\PaymentController($db);
        break;
    case 'booking_confirmation':
        // You can create a confirmation controller/page if needed
        require_once 'App/View/booking_confirmation.php';
        exit;
    default:
        // Redirect to booking page for now
        header('Location: index.php?page=booking');
        exit;
}
switch ($action) {
    case 'process':
        $controller->{'process' . ucfirst($page)}();
        break;
    case 'check_availability':
        $controller->checkAvailability();
        break;
    default:
        $controller->index();  // <-- This loads booking.php view with data
        break;
}
?>