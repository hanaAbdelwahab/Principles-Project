<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Car Rental System</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/Public/css/styles.css">
    <style>
        .hidden {
            display: none;
        }
        .form-row {
            display: flex;
            gap: 1rem;
        }
        .half {
            flex: 1;
        }
    </style>
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
    <section class="payment-section">
        <h2>Complete Your Payment</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']); 
                ?>
            </div>
        <?php endif; ?>

        <div class="booking-summary">
            <h3>Booking Summary</h3>
            <p><strong>Car:</strong> <?= htmlspecialchars($booking['car_name']) ?> - <?= htmlspecialchars($booking['car_model']) ?></p>
            <p><strong>Start Date:</strong> <?= date('F j, Y', strtotime($booking['start_date'])) ?></p>
            <p><strong>End Date:</strong> <?= date('F j, Y', strtotime($booking['end_date'])) ?></p>
            <p><strong>Duration:</strong> 
                <?php
                    $start = new DateTime($booking['start_date']);
                    $end = new DateTime($booking['end_date']);
                    $interval = $start->diff($end);
                    echo $interval->days + 1;
                ?> days
            </p>
            <p><strong>Total Amount:</strong> $<?= htmlspecialchars($booking['total_price']) ?></p>
        </div>

        <form id="payment-form" action="index.php?page=payment&action=process" method="POST">
            <h3>Payment Details</h3>

            <div class="form-group">
                <label for="payment_method">Payment Method:</label>
                <select id="payment_method" name="payment_method" required>
                    <option value="">Select Payment Method</option>
                    <option value="visa">Visa</option>
                    <option value="paypal">PayPal</option>
                </select>
            </div>

            <!-- Card Payment Fields -->
            <div class="card-fields">
                <div class="form-group">
                    <label for="card_number">Card Number:</label>
                    <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" pattern="\d{4} \d{4} \d{4} \d{4}">
                </div>

                <div class="form-row">
                    <div class="form-group half">
                        <label for="expiry_date">Expiry Date:</label>
                        <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/YY" pattern="\d{2}/\d{2}">
                    </div>
                    <div class="form-group half">
                        <label for="cvv">CVV:</label>
                        <input type="text" id="cvv" name="cvv" placeholder="123" pattern="\d{3}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="card_holder">Card Holder Name:</label>
                    <input type="text" id="card_holder" name="card_holder" placeholder="John Doe">
                </div>
            </div>

            <!-- PayPal Email -->
            <div class="form-group hidden" id="paypal-email-group">
                <label for="paypal_email">PayPal Email:</label>
                <input type="email" id="paypal_email" name="paypal_email" placeholder="example@paypal.com">
            </div>

            <button type="submit" class="btn btn-primary">Complete Payment</button>
        </form>
    </section>
</main>

<footer>
    <p>&copy; <?= date('Y') ?> Car Rental System. All rights reserved.</p>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const methodSelect = document.getElementById('payment_method');
    const cardFields = document.querySelector('.card-fields');
    const paypalGroup = document.getElementById('paypal-email-group');

    const cardNumberInput = document.getElementById('card_number');
    const expiryDateInput = document.getElementById('expiry_date');
    const cvvInput = document.getElementById('cvv');

    methodSelect.addEventListener('change', function () {
        const method = this.value;
        if (method === 'paypal') {
            cardFields.style.display = 'none';
            paypalGroup.classList.remove('hidden');
        } else {
            cardFields.style.display = 'block';
            paypalGroup.classList.add('hidden');
        }
    });

    cardNumberInput.addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        let formattedValue = '';
        for (let i = 0; i < value.length; i++) {
            if (i > 0 && i % 4 === 0) {
                formattedValue += ' ';
            }
            formattedValue += value[i];
        }
        e.target.value = formattedValue.substring(0, 19);
    });

    expiryDateInput.addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        e.target.value = value.substring(0, 5);
    });

    cvvInput.addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        e.target.value = value.substring(0, 3);
    });
});
</script>
</body>
</html>