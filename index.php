<?php
define('BASE_URL', '/Principles-Project');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/App/Controller/CarController.php';

$controller = new CarController();
$controller->showListings();
