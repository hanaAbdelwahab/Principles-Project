<?php
session_start();
define('BASE_URL', '/Final-Principles');

require_once __DIR__ . '/../config/dp.php';
require_once __DIR__ . '/../Model/BookingModel.php';

$pdo = Database::getInstance();
$bookingModel = new \App\Model\BookingModel($pdo);

$carId = isset($_GET['car_id']) ? (int)$_GET['car_id'] : null;

if (!$carId) {
    echo "No car selected.";
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

// Handle form submission (from modal final confirm)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';
    $paymentMethod = $_POST['payment_method'] ?? '';

    // Validate required fields
    if (!$startDate || !$endDate) {
        $_SESSION['error'] = 'Please select start and end dates.';
        header("Location: booking.php?car_id={$carId}");
        exit;
    }

    if (!$paymentMethod) {
        $_SESSION['error'] = 'Please select a payment method.';
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
    $userId = 1; // TODO: replace with logged-in user id in production

    // Create booking
    $bookingId = $bookingModel->createBooking($carId, $userId, $startDate, $endDate, $priceInfo['total_amount']);
    if (!$bookingId) {
        $_SESSION['error'] = 'Failed to create booking. Please try again.';
        header("Location: booking.php?car_id={$carId}");
        exit;
    }

    // Prepare payment details array based on payment method
    $paymentDetails = [];
    switch ($paymentMethod) {
        case 'Credit Card':
            $paymentDetails = [
                'cc_number' => $_POST['cc_number'] ?? null,
                'cc_holder' => $_POST['cc_holder'] ?? null,  // Make sure this input exists in form
                'cc_expiry' => $_POST['cc_expiry'] ?? null,
                'cc_cvv' => $_POST['cc_cvv'] ?? null,
            ];
            break;
        case 'PayPal':
            $paymentDetails = [
                'paypal_email' => $_POST['paypal_email'] ?? null,
            ];
            break;
        case 'Bank Transfer':
            $paymentDetails = [
                'bank_txn_id' => $_POST['bank_txn_id'] ?? null,
            ];
            break;
    }

    // Save payment info
    $paymentSaved = $bookingModel->createPayment($bookingId, $paymentMethod, $paymentDetails);
    if (!$paymentSaved) {
        $_SESSION['error'] = 'Failed to save payment info. Please contact support.';
        header("Location: booking.php?car_id={$carId}");
        exit;
    }

    $_SESSION['success'] = 'Booking and payment saved successfully!';
    header("Location: booking.php?car_id={$carId}");
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
    /* ... your existing CSS styles here ... */
    .hidden { display: none; }
    .alert { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
    .alert-danger { background-color: #f8d7da; color: #842029; }
    .alert-success { background-color: #d1e7dd; color: #0f5132; }
    .modal {
  display: none; /* hidden initially */
  position: fixed;
  z-index: 9999;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.5);

  /* flexbox centering */
  justify-content: center;
  align-items: center;
  overflow: auto;
  /* DO NOT set display: flex here */
}


.modal-content {
  background-color: #fefefe;
  padding: 20px;
  border-radius: 8px;
  width: 90%;
  max-width: 500px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.3);
  position: relative;
}

    /* Add credit card holder input styling if needed */
  </style>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
</head>
<body>
  <header>
    <h1>Car Rental System</h1>
    <nav>
      <ul>
        <li><a href="Homepage.php">Home</a></li>
        <li><a href="booking.php?car_id=<?= $carId ?>" class="active">Book a Car</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <section class="booking-section">
      <h2>Book a Car</h2>

      <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
      <?php endif; ?>

      <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
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

      <form id="booking-form" action="" method="POST" style="margin-top:20px;">
        <input type="hidden" name="car_id" value="<?= $carId ?>">

        <div class="form-group" style="margin-bottom:15px;">
          <label for="start_date">Start Date:</label><br>
          <input type="text" id="start_date" name="start_date" class="datepicker" required autocomplete="off" style="padding:8px; width:200px;">
        </div>

        <div class="form-group" style="margin-bottom:15px;">
          <label for="end_date">End Date:</label><br>
          <input type="text" id="end_date" name="end_date" class="datepicker" required autocomplete="off" style="padding:8px; width:200px;">
        </div>

        <!-- Book Now button triggers modal -->
        <button type="button" id="book-now-btn" class="btn btn-primary" style="padding:10px 20px; font-size:16px;">Book Now</button>
      </form>
    </section>
  </main>

  <footer style="margin-top:30px; padding:20px; text-align:center; background:#f4f4f4;">
    <p>&copy; <?= date('Y') ?> Car Rental System. All rights reserved.</p>
  </footer>

  <!-- Modal for payment selection and input -->
  <div id="payment-modal" class="modal">
    <div class="modal-content">
      <span class="close-modal">&times;</span>
      <h3>Select Payment Method</h3>
      <form id="payment-form" method="POST" action="">

        <!-- Hidden inputs to forward booking data -->
        <input type="hidden" name="car_id" value="<?= $carId ?>">
        <input type="hidden" name="start_date" id="modal_start_date" value="">
        <input type="hidden" name="end_date" id="modal_end_date" value="">

        <div>
          <label for="payment_method">Payment Method:</label>
          <select name="payment_method" id="payment_method" required>
            <option value="">-- Select --</option>
            <option value="Credit Card">Credit Card</option>
            <option value="PayPal">PayPal</option>
            <option value="Bank Transfer">Bank Transfer</option>
          </select>
        </div>

        <div id="credit-card-form" class="payment-form hidden">
          <h4>Credit Card Details</h4>
          <div>
            <label>Card Holder Name:</label><br>
            <input type="text" name="cc_holder" placeholder="Name on Card" required>
          </div>
          <div>
            <label>Card Number:</label><br>
            <input type="text" name="cc_number" placeholder="1234 5678 9012 3456" pattern="\d{16}" title="Enter 16 digit card number" required>
          </div>
          <div>
            <label>Expiry Date:</label><br>
            <input type="month" name="cc_expiry" required>
          </div>
          <div>
            <label>CVV:</label><br>
            <input type="text" name="cc_cvv" pattern="\d{3}" title="Enter 3 digit CVV" required>
          </div>
        </div>

        <div id="paypal-form" class="payment-form hidden">
          <h4>PayPal Account</h4>
          <div>
            <label>PayPal Email:</label><br>
            <input type="email" name="paypal_email" placeholder="email@example.com" required>
          </div>
        </div>

        <div id="bank-transfer-form" class="payment-form hidden">
          <h4>Bank Transfer Details</h4>
          <p>Please transfer the total amount to the following account:</p>
          <ul>
            <li>Bank: Example Bank</li>
            <li>Account Number: 123456789</li>
            <li>IBAN: EX1234567890</li>
          </ul>
          <p>After transfer, enter your transaction ID below:</p>
          <div>
            <label>Transaction ID:</label><br>
            <input type="text" name="bank_txn_id" required>
          </div>
        </div>

        <div style="margin-top:20px;">
          <button type="submit" class="btn btn-success" style="padding:10px 20px; font-size:16px;">Confirm Payment & Book</button>
          <button type="button" id="cancel-modal" style="padding:10px 20px; font-size:16px; margin-left:10px;">Cancel</button>
        </div>
      </form>
    </div>
  </div>

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
          if (this.id === 'start_date') {
            $('#end_date').datepicker('option', 'minDate', selectedDate);
          }
          if (this.id === 'end_date') {
            $('#start_date').datepicker('option', 'maxDate', selectedDate);
          }
        }
      });

      var modal = $('#payment-modal');

      $('#book-now-btn').on('click', function() {
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();

        if (!startDate || !endDate) {
          alert('Please select start and end dates before booking.');
          return;
        }

        $('#modal_start_date').val(startDate);
        $('#modal_end_date').val(endDate);

        $('#payment_method').val('');
        $('.payment-form').addClass('hidden');
        modal.css('display', 'flex');
      });

      $('.close-modal, #cancel-modal').on('click', function() {
        modal.css('display', 'none');
      });

      $('#payment_method').on('change', function() {
        var method = $(this).val();
        $('.payment-form').addClass('hidden');

        if (method === 'Credit Card') {
          $('#credit-card-form').removeClass('hidden');
          $('#credit-card-form input').attr('required', true);
          $('#paypal-form input, #bank-transfer-form input').removeAttr('required');
        } else if (method === 'PayPal') {
          $('#paypal-form').removeClass('hidden');
          $('#paypal-form input').attr('required', true);
          $('#credit-card-form input, #bank-transfer-form input').removeAttr('required');
        } else if (method === 'Bank Transfer') {
          $('#bank-transfer-form').removeClass('hidden');
          $('#bank-transfer-form input').attr('required', true);
          $('#credit-card-form input, #paypal-form input').removeAttr('required');
        } else {
          $('#credit-card-form input, #paypal-form input, #bank-transfer-form input').removeAttr('required');
        }
      });

      $(window).on('click', function(event) {
        if ($(event.target).is(modal)) {
          modal.hide();
        }
      });
    });
  </script>
</body>
</html>
