<?php

require_once __DIR__ . '/../config/dp.php';
require_once __DIR__ . '/IAdminBookingRepository.php';

class AdminBooking implements IAdminBookingRepository {
    private $db;

    public function __construct($db = null) {
        $this->db = $db ?? Database::getInstance();
    }

    public function getCarById($carId) {
        $stmt = $this->db->prepare("SELECT * FROM cars WHERE id = :id");
        $stmt->bindValue(':id', $carId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCarBookings($carId) {
        $stmt = $this->db->prepare("SELECT * FROM bookings WHERE car_id = :car_id AND status != 'cancelled'");
        $stmt->bindValue(':car_id', $carId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkAvailability($carId, $startDate, $endDate) {
        $car = $this->getCarById($carId);
        if (!$car) {
            return ['available' => false, 'message' => 'Car not found.'];
        }

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
        if (!$car) {
            return ['price_per_day' => 0, 'days' => 0, 'total_price' => 0];
        }
        $pricePerDay = $car['price_per_day'];
        $days = (new DateTime($startDate))->diff(new DateTime($endDate))->days + 1;

        return [
            'price_per_day' => $pricePerDay,
            'days' => $days,
            'total_price' => $pricePerDay * $days
        ];
    }

    public function createBooking($carId, $userId, $startDate, $endDate, $totalPrice) {
        $stmt = $this->db->prepare("INSERT INTO bookings (car_id, user_id, start_date, end_date, total_price, status, created_at) VALUES (:car_id, :user_id, :start_date, :end_date, :total_price, 'pending', NOW())");
        $stmt->bindValue(':car_id', $carId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':start_date', $startDate);
        $stmt->bindValue(':end_date', $endDate);
        $stmt->bindValue(':total_price', $totalPrice);
        $success = $stmt->execute();

        return $success ? $this->db->lastInsertId() : false;
    }

    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM bookings");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM bookings WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
