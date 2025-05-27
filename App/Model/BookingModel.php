<?php

namespace App\Model;

class BookingModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Fetch car details by ID
    public function getCarById(int $carId): ?array {
        $query = "SELECT * FROM cars WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$carId]);
        $car = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $car ?: null;
    }

    // Fetch bookings for the given car that are not cancelled
    public function getCarBookings(int $carId): array {
        $query = "SELECT * FROM bookings WHERE car_id = ? AND status != 'cancelled'";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$carId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Check availability between requested dates for the car
    public function checkAvailability(int $carId, string $startDate, string $endDate): array {
        $car = $this->getCarById($carId);
        if (!$car) {
            return ['available' => false, 'message' => 'Car not found.'];
        }

        $carAvailFrom = new \DateTime($car['start_date']);
        $carAvailTo = new \DateTime($car['end_date']);
        $requestStart = new \DateTime($startDate);
        $requestEnd = new \DateTime($endDate);

        if ($requestStart < $carAvailFrom || $requestEnd > $carAvailTo) {
            return ['available' => false, 'message' => 'Car is not available for the requested dates.'];
        }

        $bookings = $this->getCarBookings($carId);
        foreach ($bookings as $booking) {
            $bookingStart = new \DateTime($booking['start_date']);
            $bookingEnd = new \DateTime($booking['end_date']);
            // Check if requested dates overlap with existing bookings
            if (($requestStart <= $bookingEnd) && ($requestEnd >= $bookingStart)) {
                return ['available' => false, 'message' => 'Car is already booked for some or all of the requested dates.'];
            }
        }

        return ['available' => true, 'message' => 'Car is available for the requested dates.'];
    }

    // Calculate total price based on price per day and number of days
    public function calculatePrice(int $carId, string $startDate, string $endDate): array {
        $car = $this->getCarById($carId);
        if (!$car) {
            return ['price_per_day' => 0, 'days' => 0, 'total_price' => 0];
        }

        $pricePerDay = (float) $car['price_per_day'];
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $interval = $start->diff($end);
        $days = $interval->days + 1;

        return [
            'price_per_day' => $pricePerDay,
            'days' => $days,
            'total_price' => $pricePerDay * $days,
        ];
    }

    // Create a booking record
    public function createBooking(int $carId, int $userId, string $startDate, string $endDate, float $totalPrice) {
        $query = "INSERT INTO bookings (car_id, user_id, start_date, end_date, total_price, status, created_at)
                  VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
        $stmt = $this->db->prepare($query);
        $success = $stmt->execute([$carId, $userId, $startDate, $endDate, $totalPrice]);

        if ($success) {
            return $this->db->lastInsertId();
        }
        return false;
    }
}