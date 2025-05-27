<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Car - Car Rental System</title>
    <link rel="stylesheet" href="Public/css/styles.css">
    <!-- Add jQuery and jQuery UI for datepicker -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
</head>
<body>
    <header>
        <h1>Car Rental System</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="index.php?page=booking" class="active">Book a Car</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="booking-section">
            <h2>Book a Car</h2>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php 
                        echo $_SESSION['error'];
                        unset($_SESSION['error']); 
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php 
                        echo $_SESSION['success'];
                        unset($_SESSION['success']); 
                    ?>
                </div>
            <?php endif; ?>
            
            <div class="car-details">
                <h3><?= htmlspecialchars($car['name']) ?> - <?= htmlspecialchars($car['model']) ?></h3>
                <div class="car-info">
                    <div class="car-image">
                        <img src="<?= htmlspecialchars($car['image_url']) ?>" alt="<?= htmlspecialchars($car['name']) ?>">
                    </div>
                    <div class="car-specs">
                        <p><strong>Location:</strong> <?= htmlspecialchars($car['location']) ?></p>
                        <p><strong>Price:</strong> $<?= htmlspecialchars($car['price_per_day']) ?> per day</p>
                        <p><strong>Available from:</strong> <?= date('F j, Y', strtotime($availableFrom)) ?></p>
                        <p><strong>Available until:</strong> <?= date('F j, Y', strtotime($availableTo)) ?></p>
                    </div>
                </div>
            </div>
            
            <form id="booking-form" action="index.php?page=booking&action=process" method="POST">
                <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
                
                <div class="form-group">
                    <label for="start_date">Start Date:</label>
                    <input type="text" id="start_date" name="start_date" class="datepicker" required>
                </div>
                
                <div class="form-group">
                    <label for="end_date">End Date:</label>
                    <input type="text" id="end_date" name="end_date" class="datepicker" required>
                </div>
                
                <div id="availability-message" class="hidden"></div>
                
                <div id="price-calculation" class="hidden">
                    <h4>Price Calculation</h4>
                    <p><strong>Price per day:</strong> $<span id="price-per-day"><?= htmlspecialchars($car['price_per_day']) ?></span></p>
                    <p><strong>Number of days:</strong> <span id="number-of-days">0</span></p>
                    <p><strong>Total price:</strong> $<span id="total-price">0</span></p>
                </div>
                
                <button type="submit" id="book-now-btn" class="btn btn-primary" disabled>Book Now</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Car Rental System. All rights reserved.</p>
    </footer>

    <script>
        // JavaScript to handle date selection and availability checking
        $(document).ready(function() {
            // Initialize datepickers
            var availableFrom = new Date('<?= $availableFrom ?>');
            var availableTo = new Date('<?= $availableTo ?>');
            
            // Create an array of booked dates
            var bookedDates = [
                <?php foreach ($bookings as $booking): ?>
                    <?php 
                        $start = new DateTime($booking['start_date']);
                        $end = new DateTime($booking['end_date']);
                        $interval = new DateInterval('P1D');
                        $dateRange = new DatePeriod($start, $interval, $end->modify('+1 day'));
                        
                        foreach ($dateRange as $date) {
                            echo "'" . $date->format('Y-m-d') . "',";
                        }
                    ?>
                <?php endforeach; ?>
            ];
            
            // Function to check if a date is booked
            function isDateBooked(date) {
                var dateString = $.datepicker.formatDate('yy-mm-dd', date);
                return $.inArray(dateString, bookedDates) !== -1;
            }
            
            $('.datepicker').datepicker({
                dateFormat: 'yy-mm-dd',
                minDate: availableFrom,
                maxDate: availableTo,
                beforeShowDay: function(date) {
                    var isBooked = isDateBooked(date);
                    return [!isBooked, isBooked ? 'booked-date' : ''];
                },
                onSelect: function() {
                    checkAvailability();
                }
            });
            
            // Function to check availability
            function checkAvailability() {
                var startDate = $('#start_date').val();
                var endDate = $('#end_date').val();
                
                if (startDate && endDate) {
                    $.ajax({
                        url: 'index.php?page=booking&action=check_availability',
                        method: 'POST',
                        data: {
                            car_id: <?= $car['id'] ?>,
                            start_date: startDate,
                            end_date: endDate
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.available) {
                                $('#availability-message')
                                    .removeClass('hidden error-message')
                                    .addClass('success-message')
                                    .text(response.message);
                                
                                // Update price calculation
                                $('#price-calculation').removeClass('hidden');
                                $('#price-per-day').text(response.price_info.price_per_day);
                                $('#number-of-days').text(response.price_info.days);
                                $('#total-price').text(response.price_info.total_price);
                                
                                // Enable booking button
                                $('#book-now-btn').prop('disabled', false);
                            } else {
                                $('#availability-message')
                                    .removeClass('hidden success-message')
                                    .addClass('error-message')
                                    .text(response.message);
                                
                                // Hide price calculation and disable button
                                $('#price-calculation').addClass('hidden');
                                $('#book-now-btn').prop('disabled', true);
                            }
                        },
                        error: function() {
                            $('#availability-message')
                                .removeClass('hidden success-message')
                                .addClass('error-message')
                                .text('Error checking availability. Please try again.');
                            
                            // Hide price calculation and disable button
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