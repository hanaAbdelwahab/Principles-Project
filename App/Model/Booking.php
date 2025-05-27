<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/IBookingRepository.php';

class Booking implements IBookingRepository {
    private $db;

    public function __construct($db = null) {
        $this->db = $db ?? Database::getInstance()->getConnection();
    }

    public function getCarById($carId) {
        $stmt = $this->db->prepare("SELECT * FROM cars WHERE id = ?");
        $stmt->execute([$carId]);
        return $stmt->get_result()->fetch_assoc();
    }

    public function getCarBookings($carId) {
        $stmt = $this->db->prepare("SELECT * FROM bookings WHERE car_id = ? AND status != 'cancelled'");
        $stmt->execute([$carId]);
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function checkAvailability($carId, $startDate, $endDate) {
        $car = $this->getCarById($carId);
        $carAvailFrom = new DateTime($car['start_date']);
        $carAvailTo = new DateTime($car['end_date']);

        $requestStart = new DateTime($startDate);
        $requestEnd = new DateTime($endDate);

        if ($requestStart < $carAvailFrom || $requestEnd > $carAvailTo) {
            return ['available' => false, 'message' => 'Car not available in selected date range.'];
        }

        foreach ($this->getCarBookings($carId) as $booking) {
            $bookingStart = new DateTime($booking['start_date']);
            $bookingEnd = new DateTime($booking['end_date']);
            if (($requestStart <= $bookingEnd) && ($requestEnd >= $bookingStart)) {
                return ['available' => false, 'message' => 'Car already booked during this period.'];
            }
        }

        return ['available' => true, 'message' => 'Car available.'];
    }

    public function calculatePrice($carId, $startDate, $endDate) {
        $car = $this->getCarById($carId);
        $pricePerDay = $car['price_per_day'];
        $days = (new DateTime($startDate))->diff(new DateTime($endDate))->days + 1;

        return [
            'price_per_day' => $pricePerDay,
            'days' => $days,
            'total_price' => $pricePerDay * $days
        ];
    }

    public function createBooking($carId, $userId, $startDate, $endDate, $totalPrice) {
        $stmt = $this->db->prepare("INSERT INTO bookings (car_id, user_id, start_date, end_date, total_price, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
        $success = $stmt->execute([$carId, $userId, $startDate, $endDate, $totalPrice]);
        return $success ? $this->db->insert_id : false;
    }

    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM bookings");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM bookings WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
