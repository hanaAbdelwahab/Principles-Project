<?php
namespace App\Model\PaymentStrategies;

interface PaymentStrategy {
    public function pay($bookingId, $db, $details): bool;
}
