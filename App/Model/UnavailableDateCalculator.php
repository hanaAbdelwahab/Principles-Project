<!--App/Model/UnavailableDateCalculator.php-->
<?php
require_once 'Bookings.php';
require_once 'Cars.php';
class UnavailableDateCalculator {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getUnavailableDates(): array {
        $carAvailabilities = $this->pdo->query("SELECT id, start_date, end_date FROM cars")->fetchAll(PDO::FETCH_ASSOC);
        $availableDatesByCar = [];

        foreach ($carAvailabilities as $car) {
            $start = new DateTime($car['start_date']);
            $end = new DateTime($car['end_date']);
            while ($start <= $end) {
                $date = $start->format('Y-m-d');
                $availableDatesByCar[$car['id']][$date] = true;
                $start->modify('+1 day');
            }
        }

        $stmt = $this->pdo->query("SELECT car_id, start_date, end_date FROM bookings");
        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $bookedDateCounts = [];
        foreach ($bookings as $booking) {
            $carId = $booking['car_id'];
            $start = new DateTime($booking['start_date']);
            $end = new DateTime($booking['end_date']);
            while ($start <= $end) {
                $dateStr = $start->format('Y-m-d');
                if (isset($availableDatesByCar[$carId][$dateStr])) {
                    if (!isset($bookedDateCounts[$dateStr])) {
                        $bookedDateCounts[$dateStr] = [];
                    }
                    $bookedDateCounts[$dateStr][$carId] = true;
                }
                $start->modify('+1 day');
            }
        }

        $allAvailableDates = [];
        foreach ($availableDatesByCar as $carId => $dates) {
            foreach ($dates as $date => $_) {
                $allAvailableDates[$date][$carId] = true;
            }
        }

        $unavailableDates = [];
        foreach ($allAvailableDates as $date => $carsOfferingDate) {
            $carIdsOffering = array_keys($carsOfferingDate);
            $carIdsBooked = array_keys($bookedDateCounts[$date] ?? []);

            sort($carIdsOffering);
            sort($carIdsBooked);

            if ($carIdsOffering === $carIdsBooked) {
                $unavailableDates[] = $date;
            }
        }

        // Add out-of-range dates
        $dateBounds = $this->pdo->query("SELECT MIN(start_date) AS min_start, MAX(end_date) AS max_end FROM cars")->fetch(PDO::FETCH_ASSOC);
        $minDate = new DateTime($dateBounds['min_start']);
        $maxDate = new DateTime($dateBounds['max_end']);

        $fullSpan = [];
        $scan = new DateTimeImmutable($minDate->format('Y-m-d'));
        while ($scan < $minDate) {
            $fullSpan[] = $scan->format('Y-m-d');
            $scan = $scan->modify('+1 day');
        }

        $scan = (new DateTimeImmutable($maxDate->format('Y-m-d')))->modify('+1 day');
        $end = (new DateTimeImmutable())->modify('+2 years');
        while ($scan <= $end) {
            $fullSpan[] = $scan->format('Y-m-d');
            $scan = $scan->modify('+1 day');
        }

        return array_values(array_unique(array_merge($unavailableDates, $fullSpan)));
    }
}
