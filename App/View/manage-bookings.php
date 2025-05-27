<?php
require_once __DIR__ . '/../Controller/BookingController.php';
require_once __DIR__ . '/../Model/Booking.php';

$bookingRepo = new Booking();
$bookingController = new BookingController($bookingRepo);

$filterUserId = $_GET['user_id'] ?? '';
$filterCarId = $_GET['car_id'] ?? '';

$bookings = array_filter($bookingController->getAllBookings(), function($booking) use ($filterUserId, $filterCarId) {
    $matchUser = empty($filterUserId) || $booking['user_id'] == $filterUserId;
    $matchCar = empty($filterCarId) || $booking['car_id'] == $filterCarId;
    return $matchUser && $matchCar;
});

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $bookingController->deleteBooking($_POST['delete_id']);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Bookings</title>
  <link rel="stylesheet" href="../../Public/css/styles.css">
  <style>
    .filter-form {
      display: flex;
      gap: 12px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }
    .filter-form input,
    .filter-form button {
      padding: 10px 14px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
    }
    .view-btn, .edit-btn {
      background-color: #e0f0ff;
      color: #007bff;
      padding: 5px 10px;
      border-radius: 6px;
      border: none;
      cursor: pointer;
    }
    .view-btn:hover, .edit-btn:hover {
      background-color: #cfe7ff;
    }
    .delete-btn {
      background-color: #ffe0e0;
      color: #dc3545;
      padding: 5px 10px;
      border-radius: 6px;
      border: none;
      cursor: pointer;
    }
    .delete-btn:hover {
      background-color: #ffcccc;
    }
    .modal input {
      width: 100%;
      margin: 8px 0;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #ccc;
    }
  </style>
</head>
<body>
<div class="dashboard">
  <aside class="sidebar">
    <div class="sidebar-header">
      <h1 class="logo">CarHub Admin</h1>
    </div>
    <nav class="sidebar-nav">
      <ul>
         <li class="nav-item">
                <a href="analytics-overview.php"><span>Analytics Overview</span></a>
            </li>
        <li class="nav-item"><a href="manage-cars.php"><span>Manage Cars</span></a></li>
        <li class="nav-item"><a href="manage-users.php"><span>Manage Users</span></a></li>
        <li class="nav-item active"><a href="#"><span>Manage Bookings</span></a></li>
      </ul>
    </nav>
  </aside>

  <main class="main-content">
    <header class="content-header">
      <div class="header-left">
        <h2>Manage Bookings</h2>
      </div>
    </header>

    <div class="dashboard-content">
      <section class="content-section active">
        <div class="section-header">
          <h3>Booking List</h3>
          <div class="actions">
            <button class="add-btn" onclick="document.getElementById('addBookingModal').classList.add('active')">+ Add Booking</button>
          </div>
        </div>

        <form method="GET" class="filter-form">
          <input type="text" name="user_id" placeholder="Filter by User ID" value="<?= htmlspecialchars($filterUserId) ?>">
          <input type="text" name="car_id" placeholder="Filter by Car ID" value="<?= htmlspecialchars($filterCarId) ?>">
          <button type="submit">Apply Filters</button>
        </form>

        <div class="table-container">
          <table class="data-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Car ID</th>
                <th>User ID</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($bookings as $booking): ?>
              <tr>
                <td><?= htmlspecialchars($booking['id']) ?></td>
                <td><?= htmlspecialchars($booking['car_id']) ?></td>
                <td><?= htmlspecialchars($booking['user_id']) ?></td>
                <td><?= htmlspecialchars($booking['start_date']) ?></td>
                <td><?= htmlspecialchars($booking['end_date']) ?></td>
                <td><?= htmlspecialchars($booking['total_price']) ?></td>
                <td><?= htmlspecialchars($booking['status']) ?></td>
                <td><?= htmlspecialchars($booking['created_at']) ?></td>
                <td>
                  <button class="view-btn" onclick='editBooking(<?= json_encode($booking) ?>)'>View</button>
                  <form method="post" style="display:inline" onsubmit="return confirm('Delete this booking?');">
                    <input type="hidden" name="delete_id" value="<?= $booking['id'] ?>">
                    <button type="submit" class="delete-btn">Delete</button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </main>
</div>

<!-- Add Booking Modal -->
<div class="modal" id="addBookingModal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Add New Booking</h3>
      <button class="close-modal" onclick="document.getElementById('addBookingModal').classList.remove('active')">&times;</button>
    </div>
    <div class="modal-body">
      <form method="POST" action="create-booking.php">
        <input type="number" name="car_id" placeholder="Car ID" required>
        <input type="number" name="user_id" placeholder="User ID" required>
        <input type="date" name="start_date" required>
        <input type="date" name="end_date" required>
        <input type="number" name="total_price" step="0.01" placeholder="Total Price" required>
        <div class="modal-footer">
          <button type="submit" class="save-btn">Add Booking</button>
          <button type="button" class="cancel-btn" onclick="document.getElementById('addBookingModal').classList.remove('active')">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- View/Edit Modal -->
<div class="modal" id="editBookingModal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Edit Booking</h3>
      <button class="close-modal" onclick="closeEditModal()">&times;</button>
    </div>
    <form method="POST" action="update-booking.php">
      <div class="modal-body">
        <input type="hidden" name="id" id="edit-id">
        <input type="text" name="car_id" id="edit-car" required disabled>
        <input type="text" name="user_id" id="edit-user" required disabled>
        <input type="date" name="start_date" id="edit-start" required disabled>
        <input type="date" name="end_date" id="edit-end" required disabled>
        <input type="text" name="total_price" id="edit-price" required disabled>
        <input type="text" name="status" id="edit-status" required disabled>
      </div>
      <div class="modal-footer">
        <button type="button" class="edit-btn" onclick="enableEdit()">Edit</button>
        <button type="submit" class="save-btn" id="update-btn" style="display:none;">Update</button>
        <button type="button" class="cancel-btn" onclick="closeEditModal()">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script>
function editBooking(booking) {
  document.getElementById('edit-id').value = booking.id;
  document.getElementById('edit-car').value = booking.car_id;
  document.getElementById('edit-user').value = booking.user_id;
  document.getElementById('edit-start').value = booking.start_date;
  document.getElementById('edit-end').value = booking.end_date;
  document.getElementById('edit-price').value = booking.total_price;
  document.getElementById('edit-status').value = booking.status;
  document.getElementById('editBookingModal').classList.add('active');
}

function enableEdit() {
  ['edit-car', 'edit-user', 'edit-start', 'edit-end', 'edit-price', 'edit-status'].forEach(id => {
    document.getElementById(id).disabled = false;
  });
  document.getElementById('update-btn').style.display = 'inline-block';
}

function closeEditModal() {
  document.getElementById('editBookingModal').classList.remove('active');
}
</script>

</body>
</html>