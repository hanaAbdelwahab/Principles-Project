<?php

require_once __DIR__ . '/../Model/IBookingRepository.php';

class BookingController {
    private $bookingRepo;

    public function __construct(IBookingRepository $repo) {
        $this->bookingRepo = $repo;
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
