<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../Model/User.php';
require_once __DIR__ . '/../Model/Car.php';
require_once __DIR__ . '/../Model/Booking.php';

try {
    $userModel = new User();
    $carModel = new Car();
    $bookingModel = new Booking();

    $totalUsers = count($userModel->getAll());
    $totalCars = count($carModel->getAll());
    $totalBookings = count($bookingModel->getAll());
} catch (Throwable $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Analytics Overview</title>
  <link rel="stylesheet" href="../../Public/css/styles.css">
  <style>
    .overview-boxes {
      display: flex;
      gap: 24px;
      margin-top: 20px;
      flex-wrap: wrap;
    }

    .box {
      background: #fff;
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      flex: 1;
      min-width: 250px;
      text-align: center;
    }

    .box h3 {
      margin-bottom: 12px;
      color: #555;
      font-size: 18px;
    }

    .box span {
      font-size: 36px;
      color: #007bff;
      font-weight: bold;
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
        <li class="nav-item active"><a href="analytics-overview.php"><span>Analytics Overview</span></a></li>
        <li class="nav-item"><a href="manage-cars.php"><span>Manage Cars</span></a></li>
        <li class="nav-item"><a href="manage-users.php"><span>Manage Users</span></a></li>
        <li class="nav-item"><a href="manage-bookings.php"><span>Manage Bookings</span></a></li>
      </ul>
    </nav>
  </aside>

  <main class="main-content">
    <header class="content-header">
      <div class="header-left">
        <h2>Dashboard Overview</h2>
      </div>
    </header>

    <div class="dashboard-content">
      <section class="content-section active">
        <div class="section-header">
          <h3>Analytics Summary</h3>
        </div>

        <div class="overview-boxes">
          <div class="box">
            <h3>Total Users</h3>
            <span><?= htmlspecialchars($totalUsers) ?></span>
          </div>
          <div class="box">
            <h3>Total Cars</h3>
            <span><?= htmlspecialchars($totalCars) ?></span>
          </div>
          <div class="box">
            <h3>Total Bookings</h3>
            <span><?= htmlspecialchars($totalBookings) ?></span>
          </div>
        </div>
      </section>
    </div>
  </main>
</div>
</body>
</html>
