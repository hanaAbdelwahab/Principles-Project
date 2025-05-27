<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - Car Rental System</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/Public/css/styles.css">
</head>
<body>
    <header>
        <h1>Car Rental System</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="index.php?page=booking">Book a Car</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="confirmation-section">
            <h2>Booking Confirmation</h2>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php 
                        echo $_SESSION['success'];
                        unset($_SESSION['success']); 
                    ?>
                </div>
            <?php endif; ?>
            
            <?php
            // Get booking details
            $bookingId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            
            if ($bookingId > 0) {
                // Connect to database and get booking details
                try {
                    $db = connectToDatabase();
                    $query = "SELECT b.*, c.name as car_name, c.model as car_model, c.location, c.price_per_day, 
                              p.status as payment_status
                              FROM bookings b
                              JOIN cars c ON b.car_id = c.id
                              LEFT JOIN payments p ON p.booking_id = b.id
                              WHERE b.id = ?";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$bookingId]);
                    $booking = $stmt->fetch();
                    
                    if ($booking) {
                        // Calculate days
                        $start = new DateTime($booking['start_date']);
                        $end = new DateTime($booking['end_date']);
                        $interval = $start->diff($end);
                        $days = $interval->days + 1;
            ?>
                        <div class="confirmation-details">
                            <h3>Thank You for Your Booking!</h3>
                            <p>Your booking has been confirmed and paid. Below are your booking details:</p>
                            
                            <div class="booking-summary">
                                <p><strong>Booking ID:</strong> #<?= $bookingId ?></p>
                                <p><strong>Car:</strong> <?= htmlspecialchars($booking['car_name']) ?> - <?= htmlspecialchars($booking['car_model']) ?></p>
                                <p><strong>Pick-up Location:</strong> <?= htmlspecialchars($booking['location']) ?></p>
                                <p><strong>Start Date:</strong> <?= date('F j, Y', strtotime($booking['start_date'])) ?></p>
                                <p><strong>End Date:</strong> <?= date('F j, Y', strtotime($booking['end_date'])) ?></p>
                                <p><strong>Duration:</strong> <?= $days ?> days</p>
                                <p><strong>Total Amount Paid:</strong> $<?= htmlspecialchars($booking['total_price']) ?></p>
                                <p><strong>Status:</strong> <span class="status-confirmed">Confirmed</span></p>
                            </div>
                            
                            <div class="next-steps">
                                <h4>Next Steps</h4>
                                <ol>
                                    <li>Please arrive at the pick-up location on your start date</li>
                                    <li>Bring your driver's license and the credit card used for payment</li>
                                    <li>A confirmation email has been sent to your registered email address</li>
                                </ol>
                            </div>
                            
                            <div class="action-buttons">
                                <a href="index.php?page=booking" class="btn btn-primary">Book Another Car</a>
                            </div>
                        </div>
            <?php
                    } else {
                        echo '<div class="alert alert-danger">Booking not found.</div>';
                    }
                } catch (PDOException $e) {
                    echo '<div class="alert alert-danger">Error retrieving booking information.</div>';
                }
            } else {
                echo '<div class="alert alert-danger">Invalid booking ID.</div>';
            }
            ?>
        </section>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Car Rental System. All rights reserved.</p>
    </footer>
</body>
</html>