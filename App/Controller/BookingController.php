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
        $carId = isset($_GET['car_id']) ? (int)$_GET['car_id'] : 1;
        $car = $this->bookingModel->getCarById($carId);

        if (!$car) {
            echo "Car not found.";
            exit;
        }

        $availableFrom = $car['start_date'] ?? date('Y-m-d');
        $availableTo = $car['end_date'] ?? date('Y-m-d', strtotime('+1 month'));
        $bookings = $this->bookingModel->getCarBookings($carId);

        include_once 'App/View/booking.php';
    }

    /**
     * Process booking and payment submission
     */
    public function processBooking() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=booking');
            exit;
        }

        // Get POST data
        $carId = isset($_POST['car_id']) ? (int)$_POST['car_id'] : 1;
        $startDate = $_POST['start_date'] ?? '';
        $endDate = $_POST['end_date'] ?? '';
        $paymentMethod = $_POST['payment_method'] ?? '';

        // Validate dates
        if (empty($startDate) || empty($endDate)) {
            $_SESSION['error'] = 'Please select both start and end dates.';
            header("Location: index.php?page=booking&car_id={$carId}");
            exit;
        }

        // Validate payment method
        if (empty($paymentMethod)) {
            $_SESSION['error'] = 'Please select a payment method.';
            header("Location: index.php?page=booking&car_id={$carId}");
            exit;
        }

        // Check availability
        $availability = $this->bookingModel->checkAvailability($carId, $startDate, $endDate);
        if (!$availability['available']) {
            $_SESSION['error'] = $availability['message'];
            header("Location: index.php?page=booking&car_id={$carId}");
            exit;
        }

        // Calculate price
        $priceInfo = $this->bookingModel->calculatePrice($carId, $startDate, $endDate);

        // Dummy user ID for now (replace with logged-in user)
        $userId = 1;

        // Create booking
        $bookingId = $this->bookingModel->createBooking(
            $carId,
            $userId,
            $startDate,
            $endDate,
            $priceInfo['total_price']
        );

        if (!$bookingId) {
            $_SESSION['error'] = 'Error creating booking. Please try again.';
            header("Location: index.php?page=booking&car_id={$carId}");
            exit;
        }

        // Prepare payment details from POST depending on payment method
        $paymentDetails = [];

        switch ($paymentMethod) {
            case 'Credit Card':
                $paymentDetails = [
                    'cc_holder' => $_POST['cc_holder'] ?? null,
                    'cc_number' => $_POST['cc_number'] ?? null,
                    'cc_expiry' => $_POST['cc_expiry'] ?? null,
                    'cc_cvv' => $_POST['cc_cvv'] ?? null,
                ];
                break;

            case 'PayPal':
                $paymentDetails = [
                    'paypal_email' => $_POST['paypal_email'] ?? null,
                ];
                break;

            case 'Bank Transfer':
                $paymentDetails = [
                    'bank_txn_id' => $_POST['bank_txn_id'] ?? null,
                ];
                break;
        }

        // Save payment details
        $paymentSaved = $this->bookingModel->createPayment($bookingId, $paymentMethod, $paymentDetails);

        if (!$paymentSaved) {
            // Optional: You could rollback the booking here if you have transactions enabled
            $_SESSION['error'] = 'Payment processing failed. Please contact support.';
            header("Location: index.php?page=booking&car_id={$carId}");
            exit;
        }

        // Success
        $_SESSION['success'] = "Booking and payment confirmed successfully using {$paymentMethod}!";
        header("Location: index.php?page=booking_confirmation&booking_id={$bookingId}");
        exit;
    }

    /**
     * AJAX handler to check availability
     */
    public function checkAvailability() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Invalid request method']);
            exit;
        }

        $carId = isset($_POST['car_id']) ? (int)$_POST['car_id'] : 1;
        $startDate = $_POST['start_date'] ?? '';
        $endDate = $_POST['end_date'] ?? '';

        if (empty($startDate) || empty($endDate)) {
            echo json_encode(['error' => 'Please select both start and end dates.']);
            exit;
        }

        $availability = $this->bookingModel->checkAvailability($carId, $startDate, $endDate);

        if ($availability['available']) {
            $priceInfo = $this->bookingModel->calculatePrice($carId, $startDate, $endDate);
            $availability['price_info'] = $priceInfo;
        }

        echo json_encode($availability);
        exit;
    }
}
