<?php

namespace App\Model;

class BookingModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getCarById($carId) {
        $query = "SELECT * FROM cars WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$carId]);
        return $stmt->fetch();
    }

    public function getCarBookings($carId) {
        $query = "SELECT * FROM bookings WHERE car_id = ? AND status != 'cancelled'";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$carId]);
        return $stmt->fetchAll();
    }

    public function checkAvailability($carId, $startDate, $endDate) {
        $car = $this->getCarById($carId);
        $carAvailFrom = new \DateTime($car['start_date']);
        $carAvailTo = new \DateTime($car['end_date']);

        $requestStart = new \DateTime($startDate);
        $requestEnd = new \DateTime($endDate);

        if ($requestStart < $carAvailFrom || $requestEnd > $carAvailTo) {
            return [
                'available' => false,
                'message' => 'Car is not available for the requested dates.'
            ];
        }

        $bookings = $this->getCarBookings($carId);
        foreach ($bookings as $booking) {
            $bookingStart = new \DateTime($booking['start_date']);
            $bookingEnd = new \DateTime($booking['end_date']);

            if (($requestStart <= $bookingEnd) && ($requestEnd >= $bookingStart)) {
                return [
                    'available' => false,
                    'message' => 'Car is already booked for some or all of the requested dates.'
                ];
            }
        }

        return [
            'available' => true,
            'message' => 'Car is available for the requested dates.'
        ];
    }

    public function calculatePrice($carId, $startDate, $endDate) {
        $car = $this->getCarById($carId);
        $pricePerDay = $car['price_per_day'];

        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $interval = $start->diff($end);
        $days = $interval->days + 1;

        return [
            'price_per_day' => $pricePerDay,
            'days' => $days,
            'total_price' => $pricePerDay * $days
        ];
    }

    public function createBooking($carId, $userId, $startDate, $endDate, $totalPrice) {
        $query = "INSERT INTO bookings (car_id, user_id, start_date, end_date, total_price, status, created_at) 
                  VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
        $stmt = $this->db->prepare($query);
        $success = $stmt->execute([$carId, $userId, $startDate, $endDate, $totalPrice]);

        if ($success) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }
}
