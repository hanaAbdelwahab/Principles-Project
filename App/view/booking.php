<?php
session_start();
define('BASE_URL', '/Final-Principles');

require_once __DIR__ . '/../config/dp.php';
require_once __DIR__ . '/../Model/BookingModel.php';

$pdo = Database::getInstance();
$bookingModel = new \App\Model\BookingModel($pdo);

$carId = isset($_GET['car_id']) ? (int)$_GET['car_id'] : null;
$action = $_GET['action'] ?? null;

if (!$carId) {
    echo "No car selected.";
    exit;
}

// Handle AJAX availability check
if ($action === 'check_availability' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';

    if (!$startDate || !$endDate) {
        echo json_encode(['error' => 'Please select start and end dates.']);
        exit;
    }

    try {
        $availability = $bookingModel->checkAvailability($carId, $startDate, $endDate);
    } catch (Exception $ex) {
        echo json_encode(['error' => 'Server error: ' . $ex->getMessage()]);
        exit;
    }

    if ($availability['available']) {
        $availability['price_info'] = $bookingModel->calculatePrice($carId, $startDate, $endDate);
    }

    echo json_encode($availability);
    exit;
}

// Get car details
$car = $bookingModel->getCarById($carId);
if (!$car) {
    echo "Car not found.";
    exit;
}

// Get existing bookings for disabling dates in datepicker
$bookings = $bookingModel->getCarBookings($carId);

// Define car availability range or defaults
$availableFrom = $car['start_date'] ?? date('Y-m-d');
$availableTo = $car['end_date'] ?? date('Y-m-d', strtotime('+1 year'));

// Handle form submission to create booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action !== 'check_availability') {
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';

    if (!$startDate || !$endDate) {
        $_SESSION['error'] = 'Please select start and end dates.';
        header("Location: booking.php?car_id={$carId}");
        exit;
    }

    try {
        $availability = $bookingModel->checkAvailability($carId, $startDate, $endDate);
    } catch (Exception $ex) {
        $_SESSION['error'] = 'Server error: ' . $ex->getMessage();
        header("Location: booking.php?car_id={$carId}");
        exit;
    }

    if (!$availability['available']) {
        $_SESSION['error'] = $availability['message'];
        header("Location: booking.php?car_id={$carId}");
        exit;
    }

    $priceInfo = $bookingModel->calculatePrice($carId, $startDate, $endDate);
    $userId = 1; // replace with logged-in user id in production

    $bookingId = $bookingModel->createBooking($carId, $userId, $startDate, $endDate, $priceInfo['total_price']);

    if (!$bookingId) {
        $_SESSION['error'] = 'Failed to create booking. Please try again.';
        header("Location: booking.php?car_id={$carId}");
        exit;
    }

    $_SESSION['success'] = 'Booking created successfully!';
    header("Location: payment.php?booking_id={$bookingId}");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Book a Car - Car Rental System</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/Public/css/styles.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
  <style>
    .hidden { display: none; }
    .success-message { color: green; }
    .error-message { color: red; }
    .booked-date a { background-color: #f44336 !important; color: white !important; }
    .alert { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
    .alert-danger { background-color: #f8d7da; color: #842029; }
    .alert-success { background-color: #d1e7dd; color: #0f5132; }
  </style>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
</head>
<body>
  <header>
    <h1>Car Rental System</h1>
    <nav>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="booking.php?car_id=<?= $carId ?>" class="active">Book a Car</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <section class="booking-section">
      <h2>Book a Car</h2>

      <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
          <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
      <?php endif; ?>

      <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
          <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
      <?php endif; ?>

      <div class="car-details" style="display:flex; gap:20px; align-items:center;">
        <div class="car-image" style="max-width:300px;">
          <img src="<?= BASE_URL ?>/Public/images/<?= htmlspecialchars($car['image_filename']) ?>" alt="<?= htmlspecialchars($car['name']) ?>" style="width:100%; border-radius:8px;" />
        </div>
        <div class="car-specs">
          <h3><?= htmlspecialchars($car['name']) ?> - <?= htmlspecialchars($car['model']) ?></h3>
          <p><strong>Location:</strong> <?= htmlspecialchars($car['location']) ?></p>
          <p><strong>Price:</strong> $<?= htmlspecialchars($car['price_per_day']) ?> per day</p>
          <p><strong>Available from:</strong> <?= date('F j, Y', strtotime($availableFrom)) ?></p>
          <p><strong>Available until:</strong> <?= date('F j, Y', strtotime($availableTo)) ?></p>
        </div>
      </div>

      <form id="booking-form" action="booking.php?car_id=<?= $carId ?>" method="POST" style="margin-top:20px;">
        <input type="hidden" name="car_id" value="<?= $carId ?>">

        <div class="form-group" style="margin-bottom:15px;">
          <label for="start_date">Start Date:</label><br>
          <input type="text" id="start_date" name="start_date" class="datepicker" required autocomplete="off" style="padding:8px; width:200px;">
        </div>

        <div class="form-group" style="margin-bottom:15px;">
          <label for="end_date">End Date:</label><br>
          <input type="text" id="end_date" name="end_date" class="datepicker" required autocomplete="off" style="padding:8px; width:200px;">
        </div>

        <div id="availability-message" class="hidden" style="margin-bottom:15px;"></div>

        <div id="price-calculation" class="hidden" style="margin-bottom:15px;">
          <h4>Price Calculation</h4>
          <p><strong>Price per day:</strong> $<span id="price-per-day"><?= htmlspecialchars($car['price_per_day']) ?></span></p>
          <p><strong>Number of days:</strong> <span id="number-of-days">0</span></p>
          <p><strong>Total price:</strong> $<span id="total-price">0</span></p>
        </div>

        <button type="submit" id="book-now-btn" class="btn btn-primary" disabled style="padding:10px 20px; font-size:16px;">Book Now</button>
      </form>
    </section>
  </main>

  <footer style="margin-top:30px; padding:20px; text-align:center; background:#f4f4f4;">
    <p>&copy; <?= date('Y') ?> Car Rental System. All rights reserved.</p>
  </footer>

  <script>
    $(function() {
      var availableFrom = new Date('<?= $availableFrom ?>');
      var availableTo = new Date('<?= $availableTo ?>');

      var bookedDates = [
        <?php
          foreach ($bookings as $booking) {
              $start = new DateTime($booking['start_date']);
              $end = new DateTime($booking['end_date']);
              $interval = new DateInterval('P1D');
              $dateRange = new DatePeriod($start, $interval, $end->modify('+1 day'));
              foreach ($dateRange as $date) {
                  echo "'" . $date->format('Y-m-d') . "',";
              }
          }
        ?>
      ];

      function isDateBooked(date) {
        var dateString = $.datepicker.formatDate('yy-mm-dd', date);
        return $.inArray(dateString, bookedDates) !== -1;
      }

      $('.datepicker').datepicker({
        dateFormat: 'yy-mm-dd',
        minDate: availableFrom,
        maxDate: availableTo,
        beforeShowDay: function(date) {
          var booked = isDateBooked(date);
          return [!booked, booked ? 'booked-date' : ''];
        },
        onSelect: function(selectedDate) {
          // Sync date pickers so end_date can't be before start_date and vice versa
          if (this.id === 'start_date') {
            $('#end_date').datepicker('option', 'minDate', selectedDate);
          }
          if (this.id === 'end_date') {
            $('#start_date').datepicker('option', 'maxDate', selectedDate);
          }
          checkAvailability();
        }
      });

      function checkAvailability() {
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();

        if (startDate && endDate) {
          $.ajax({
            url: 'booking.php?car_id=<?= $carId ?>&action=check_availability',
            method: 'POST',
            data: {
              car_id: <?= $carId ?>,
              start_date: startDate,
              end_date: endDate
            },
            dataType: 'json',
            success: function(response) {
              if (response.error) {
                $('#availability-message')
                  .removeClass('hidden success-message')
                  .addClass('error-message')
                  .text(response.error);

                $('#price-calculation').addClass('hidden');
                $('#book-now-btn').prop('disabled', true);
                return;
              }

              if (response.available) {
                $('#availability-message')
                  .removeClass('hidden error-message')
                  .addClass('success-message')
                  .text(response.message);

                $('#price-calculation').removeClass('hidden');
                $('#price-per-day').text(response.price_info.price_per_day);
                $('#number-of-days').text(response.price_info.days);
                $('#total-price').text(response.price_info.total_price);

                $('#book-now-btn').prop('disabled', false);
              } else {
                $('#availability-message')
                  .removeClass('hidden success-message')
                  .addClass('error-message')
                  .text(response.message);

                $('#price-calculation').addClass('hidden');
                $('#book-now-btn').prop('disabled', true);
              }
            },
            error: function() {
              $('#availability-message')
                .removeClass('hidden success-message')
                .addClass('error-message')
                .text('Error checking availability. Please try again.');

              $('#price-calculation').addClass('hidden');
              $('#book-now-btn').prop('disabled', true);
            }
          });
        }
      }
    });
  </script>
</body>
</html>