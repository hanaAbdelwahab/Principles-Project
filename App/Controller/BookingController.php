<?php

namespace App\Controller;

class BookingController {
    private $db;
    private $bookingModel;

    public function __construct($db) {
        $this->db = $db;
        $this->bookingModel = new \App\Model\BookingModel($db);
    }

    /**
     * Display the booking page
     */
    public function index() {
        // For simplicity, we're assuming there's only one car
        $carId = 1;
        $car = $this->bookingModel->getCarById($carId);
        
        // Get car availability
        $availableFrom = $car['available_from'];
        $availableTo = $car['available_to'];
        
        // Get existing bookings to show unavailable dates
        $bookings = $this->bookingModel->getCarBookings($carId);
        
        // Load the view
        include_once 'App/View/booking.php';
    }
    
    /**
     * Process booking request
     */
    public function processBooking() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=booking');
            exit;
        }
        
        $carId = isset($_POST['car_id']) ? (int)$_POST['car_id'] : 1;
        $startDate = $_POST['start_date'];
        $endDate = $_POST['end_date'];
        
        // Validate dates
        if (empty($startDate) || empty($endDate)) {
            $_SESSION['error'] = 'Please select both start and end dates.';
            header('Location: index.php?page=booking');
            exit;
        }
        
        // Check availability
        $availability = $this->bookingModel->checkAvailability($carId, $startDate, $endDate);
        if (!$availability['available']) {
            $_SESSION['error'] = $availability['message'];
            header('Location: index.php?page=booking');
            exit;
        }
        
        // Calculate price
        $priceInfo = $this->bookingModel->calculatePrice($carId, $startDate, $endDate);
        
        // For simplicity, we'll use a dummy user ID
        $userId = 1;
        
        // Create a pending booking
        $bookingId = $this->bookingModel->createBooking(
            $carId, 
            $userId, 
            $startDate, 
            $endDate, 
            $priceInfo['total_price']
        );
        
        if (!$bookingId) {
            $_SESSION['error'] = 'Error creating booking. Please try again.';
            header('Location: index.php?page=booking');
            exit;
        }
        
        // Redirect to payment page
        $_SESSION['booking_id'] = $bookingId;
        header('Location: index.php?page=payment');
        exit;
    }
    
    /**
     * Check availability via AJAX
     */
    public function checkAvailability() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Invalid request method']);
            exit;
        }
        
        $carId = isset($_POST['car_id']) ? (int)$_POST['car_id'] : 1;
        $startDate = $_POST['start_date'];
        $endDate = $_POST['end_date'];
        
        // Validate dates
        if (empty($startDate) || empty($endDate)) {
            echo json_encode(['error' => 'Please select both start and end dates.']);
            exit;
        }
        
        // Check availability
        $availability = $this->bookingModel->checkAvailability($carId, $startDate, $endDate);
        
        if ($availability['available']) {
            // Calculate price
            $priceInfo = $this->bookingModel->calculatePrice($carId, $startDate, $endDate);
            $availability['price_info'] = $priceInfo;
        }
        
        echo json_encode($availability);
        exit;
    }
}