<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load database configuration
$dbConfig = require_once 'App/config/database.php';

// Connect to database
function connectToDatabase() {
    global $dbConfig;
    
    try {
        $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}";
        $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
        return $pdo;
    } catch (PDOException $e) {
        die('Database connection failed: ' . $e->getMessage());
    }
}

// Autoloader for classes
spl_autoload_register(function($className) {
    $className = str_replace('\\', '/', $className);
    $file = __DIR__ . '/' . $className . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    }
});

// Get database connection
$db = connectToDatabase();

// Route the request
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

// Call the appropriate action method
switch ($action) {
    case 'process':
        $controller->{'process' . ucfirst($page)}();
        break;
    case 'check_availability':
        $controller->checkAvailability();
        break;
    default:
        $controller->index();
        break;
}