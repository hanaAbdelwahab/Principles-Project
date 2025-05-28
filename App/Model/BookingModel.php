<?php
namespace App\Model;
require_once __DIR__ . '/../Model/Payments.php';
use PDO;
use Exception;

class BookingModel {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getCarById(int $carId) {
        $stmt = $this->pdo->prepare("SELECT * FROM cars WHERE id = ?");
        $stmt->execute([$carId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCarBookings(int $carId) {
        $stmt = $this->pdo->prepare("SELECT start_date, end_date FROM bookings WHERE car_id = ?");
        $stmt->execute([$carId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkAvailability(int $carId, string $startDate, string $endDate): array {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) as count FROM bookings 
             WHERE car_id = ? 
             AND NOT (end_date < ? OR start_date > ?)"
        );
        $stmt->execute([$carId, $startDate, $endDate]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            return [
                'available' => false,
                'message' => 'The selected dates overlap with existing bookings.'
            ];
        }

        return ['available' => true];
    }

    public function calculatePrice(int $carId, string $startDate, string $endDate): array {
        // Simple price calculation: price per day * number of days
        $car = $this->getCarById($carId);
        if (!$car) {
            throw new Exception("Car not found");
        }

        $pricePerDay = $car['price_per_day'];
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $interval = $start->diff($end);
        $days = $interval->days + 1; // inclusive

        $totalAmount = $pricePerDay * $days;

        return [
            'days' => $days,
            'price_per_day' => $pricePerDay,
            'total_amount' => $totalAmount
        ];
    }

    public function createBooking(int $carId, int $userId, string $startDate, string $endDate, float $totalAmount) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO bookings (car_id, user_id, start_date, end_date, total_amount, status, created_at)
             VALUES (:car_id, :user_id, :start_date, :end_date, :total_amount, 'pending', NOW())"
        );

        $success = $stmt->execute([
            ':car_id' => $carId,
            ':user_id' => $userId,
            ':start_date' => $startDate,
            ':end_date' => $endDate,
            ':total_amount' => $totalAmount,
        ]);

        if (!$success) {
            return false;
        }

        return $this->pdo->lastInsertId();
    }

    public function createPayment(int $bookingId, string $paymentMethod, array $paymentDetails) {
        try{
        $stmt = $this->pdo->prepare(
    "INSERT INTO payments 
        (booking_id, payment_method, card_number, card_holder, expiry_date, cvv, paypal_email)
     VALUES
        (:booking_id, :payment_method, :card_number, :card_holder, :expiry_date, :cvv, :paypal_email)"
);

return $stmt->execute([
    ':booking_id' => $bookingId,
    ':payment_method' => $paymentMethod,
    ':card_number' => $paymentDetails['cc_number'],
    ':card_holder' => $paymentDetails['cc_holder'],
    ':expiry_date' => $paymentDetails['cc_expiry'],
    ':cvv' => $paymentDetails['cc_cvv'],
    ':paypal_email' => $paymentDetails['paypal_email'] ?? null,
]);

    }catch(PDOException $e){
         error_log("Payment insert failed: " . $e->getMessage());
        return false;
    }
    }
}
