<!--Bookings.php-->
<?php
class payments {
    public $id;
    public $booking_id,$payment_method,$card_number,$card_holder,$expiry_date,$cvv,$paypal_email;

    public function __construct($data) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}