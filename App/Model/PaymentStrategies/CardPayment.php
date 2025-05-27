<?php
namespace App\Model\PaymentStrategies;

class CardPayment implements PaymentStrategy {
    public function pay($bookingId, $db, $details): bool {
        try {
            $cardNumber = preg_replace('/\D/', '', $details['card_number']);
            $cardLastFour = substr($cardNumber, -4);
            $cardHolder = $details['card_holder'];

            $db->beginTransaction();

            $stmt = $db->prepare("INSERT INTO payments 
                (booking_id, payment_method, card_last_four, card_holder, amount, status, created_at) 
                VALUES (?, ?, ?, ?, 
                    (SELECT total_price FROM bookings WHERE id = ?), 
                    'completed', NOW())");
            $stmt->execute([$bookingId, 'card', $cardLastFour, $cardHolder, $bookingId]);

            $update = $db->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ?");
            $update->execute([$bookingId]);

            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollBack();
            error_log("Card Payment Error: " . $e->getMessage());
            return false;
        }
    }
}
