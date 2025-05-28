<?php

require_once __DIR__ . '/../Model/IAdminBookingRepository.php';
require_once __DIR__ . '/../config/dp.php';  // Include your Database class

class AdminBookingController {
    private $bookingRepo;

    public function __construct(IAdminBookingRepository $repo = null) {
        if ($repo) {
            $this->bookingRepo = $repo;
        } else {
            $pdo = Database::getInstance();  // Use singleton PDO connection
            $this->bookingRepo = new AdminBookingRepository($pdo); // Make sure this class exists and accepts PDO
        }
    }

    public function getAllBookings() {
        return $this->bookingRepo->getAll();
    }

    public function createBooking($carId, $userId, $startDate, $endDate) {
        $availability = $this->bookingRepo->checkAvailability($carId, $startDate, $endDate);
        if (!$availability['available']) {
            return $availability;
        }

        $price = $this->bookingRepo->calculatePrice($carId, $startDate, $endDate);
        $bookingId = $this->bookingRepo->createBooking($carId, $userId, $startDate, $endDate, $price['total_price']);

        return $bookingId ? ['success' => true, 'id' => $bookingId] : ['success' => false];
    }

    public function deleteBooking($id) {
        return $this->bookingRepo->delete($id);
    }
}
