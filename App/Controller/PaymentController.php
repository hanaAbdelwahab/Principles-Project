<?php
namespace App\Controller;

use App\Model\PaymentModel;
use App\Model\PaymentStrategies\PaymentStrategyFactory;

class PaymentController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function index() {
        if (!isset($_SESSION['booking_id'])) {
            $_SESSION['error'] = 'No booking found. Please start again.';
            header('Location: index.php?page=booking');
            exit;
        }

        $bookingId = $_SESSION['booking_id'];

        $strategy = PaymentStrategyFactory::create('visa');
        $paymentModel = new PaymentModel($this->db, $strategy);

        $booking = $paymentModel->getBookingById($bookingId);

        if (!$booking) {
            $_SESSION['error'] = 'Invalid booking. Please try again.';
            header('Location: index.php?page=booking');
            exit;
        }

        include_once 'App/View/payment.php';
    }

    public function processPayment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=payment');
            exit;
        }

        if (!isset($_SESSION['booking_id'])) {
            $_SESSION['error'] = 'No booking found. Please start again.';
            header('Location: index.php?page=booking');
            exit;
        }

        $bookingId = $_SESSION['booking_id'];
        $paymentMethod = $_POST['payment_method'] ?? '';
        $cardNumber = $_POST['card_number'] ?? '';
        $cardHolder = $_POST['card_holder'] ?? '';
        $expiryDate = $_POST['expiry_date'] ?? '';
        $cvv = $_POST['cvv'] ?? '';
        $paypalEmail = $_POST['paypal_email'] ?? '';

        try {
            $strategy = PaymentStrategyFactory::create($paymentMethod);
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: index.php?page=payment');
            exit;
        }

        $paymentModel = new PaymentModel($this->db, $strategy);

        $details = [
            'payment_method' => $paymentMethod,
            'card_number' => $cardNumber,
            'card_holder' => $cardHolder,
            'expiry_date' => $expiryDate,
            'cvv' => $cvv,
            'paypal_email' => $paypalEmail
        ];

        $success = $paymentModel->processPayment($bookingId, $details);

        if ($success) {
            unset($_SESSION['booking_id']);
            $_SESSION['success'] = 'Payment successful! Your booking is confirmed.';
            header('Location: index.php?page=booking_confirmation&id=' . $bookingId);
            exit;
        } else {
            $_SESSION['error'] = 'Payment failed. Please try again.';
            header('Location: index.php?page=payment');
            exit;
        }
    }
}
