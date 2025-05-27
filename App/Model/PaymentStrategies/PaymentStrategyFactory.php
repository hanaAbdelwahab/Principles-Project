<?php
namespace App\Model\PaymentStrategies;

class PaymentStrategyFactory {
    public static function create(string $method): PaymentStrategy {
        switch ($method) {
            case 'visa':
                return new CardPayment();
            case 'paypal':
                return new PayPalPayment();
            default:
                throw new \Exception("Unsupported payment method: " . $method);
        }
    }
}
