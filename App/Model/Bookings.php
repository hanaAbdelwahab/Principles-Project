<!--Bookings.php-->
<?php
class bookings {
    public $id;
    public $start_date,$end_date,$car_id,$status,$total_amount;

    public function __construct($data) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}