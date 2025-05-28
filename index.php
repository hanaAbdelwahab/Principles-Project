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

switch ($page) {
    case 'payment':
        require_once 'App/Controller/PaymentController.php';
        $controller = new App\Controller\PaymentController($pdo);
        
        if ($action === 'process') {
            $controller->processPayment();
        } else {
            $controller->index();
        }
        break;

    case 'booking':
        require_once 'App/Controller/BookingController.php';
        $controller = new App\Controller\BookingController($pdo);
        
        if ($action === 'process') {
            $controller->processBooking();  // or your actual method name for booking processing
        } else {
            $controller->index();
        }
        break;

    // other cases...

    default:
        header('Location: ' . BASE_URL . '/index.php?page=booking');
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