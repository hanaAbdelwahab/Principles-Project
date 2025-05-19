<!--Cars.php-->
<?php
class Car {
    public $id;
    public $name, $model, $year, $price_per_day, $image_filename, $description,
           $color, $location, $start_date, $end_date, $rate, $renters,
           $transmission_type, $power_type, $wheels, $brakes;

    public function __construct($data) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
