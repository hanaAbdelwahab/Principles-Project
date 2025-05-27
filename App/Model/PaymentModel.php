<?php
namespace App\Model;

use App\Model\PaymentStrategies\PaymentStrategy;

class PaymentModel {
    private $db;
    private $strategy;

    public function __construct($db, PaymentStrategy $strategy) {
        $this->db = $db;
        $this->strategy = $strategy;
    }

    public function getBookingById($bookingId) {
        $query = "SELECT b.*, c.name as car_name, c.model as car_model, c.price_per_day, b.total_price 
                  FROM bookings b
                  JOIN cars c ON b.car_id = c.id
                  WHERE b.id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$bookingId]);
        return $stmt->fetch();
    }

    public function processPayment($bookingId, $details) {
        return $this->strategy->pay($bookingId, $this->db, $details);
    }
}
