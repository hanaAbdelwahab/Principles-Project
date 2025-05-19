<!--index.php-->
<?php
define('BASE_URL', '/Principles-Project');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/App/config/db.php';
require_once __DIR__ . '/App/Controller/CarController.php';
require_once __DIR__ . '/App/Model/CarRepository.php';
require_once __DIR__ . '/App/Model/UnavailableDateCalculator.php';
require_once __DIR__ . '/App/Filters/FilterFactory.php';

$pdo = Database::getInstance();
$repo = new CarRepository($pdo);
$calculator = new UnavailableDateCalculator($pdo);
$filterFactory = new FilterFactory();

$controller = new CarController($repo, $calculator, $filterFactory);
$controller->showListings();
