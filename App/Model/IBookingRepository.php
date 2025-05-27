<?php

interface IBookingRepository {
    public function getAll();
    public function getCarById($carId);
    public function checkAvailability($carId, $startDate, $endDate);
    public function calculatePrice($carId, $startDate, $endDate);
    public function createBooking($carId, $userId, $startDate, $endDate, $totalPrice);
    public function delete($id);
}
    