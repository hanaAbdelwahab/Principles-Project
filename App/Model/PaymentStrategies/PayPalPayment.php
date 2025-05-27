<?php
namespace App\Model\PaymentStrategies;

class PayPalPayment implements PaymentStrategy {
    public function pay($bookingId, $db, $details): bool {
        try {
            $paypalEmail = $details['paypal_email'];

            $db->beginTransaction();

            $stmt = $db->prepare("INSERT INTO payments 
                (booking_id, payment_method, card_last_four, card_holder, amount, status, created_at) 
                VALUES (?, 'paypal', ?, ?, 
                    (SELECT total_price FROM bookings WHERE id = ?), 
                    'completed', NOW())");
            $stmt->execute([$bookingId, '****', $paypalEmail, $bookingId]);

            $update = $db->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ?");
            $update->execute([$bookingId]);

            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollBack();
            error_log("PayPal Payment Error: " . $e->getMessage());
            return false;
        }
    }
}
