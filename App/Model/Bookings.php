<!--Bookings.php-->
<?php
class bookings {
    public $id;
    public $car_id,$start_date,$end_date,$status,$total_amount,$created_at,$user_id;

    public function __construct($data) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}