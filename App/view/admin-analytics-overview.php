<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/dp.php';          // Use dp.php for DB connection
require_once __DIR__ . '/../Model/AdminUser.php';
require_once __DIR__ . '/../Model/AdminCar.php';
require_once __DIR__ . '/../Model/AdminBooking.php';

try {
    $pdo = Database::getInstance();

    $userModel = new AdminUser($pdo);
    $carModel = new AdminCar($pdo);
    $bookingModel = new AdminBooking($pdo);

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
  <meta charset="UTF-8" />
  <title>Analytics Overview</title>
  <link rel="stylesheet" href="../../Public/css/styles.css" />
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
    .sidebar-nav ul {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column; /* makes list vertical */
  gap: 10px; /* optional spacing between items */
}

.nav-item {
  display: block;
}

.nav-item a {
  display: block;
  padding: 10px 15px;
  color: #333;
  text-decoration: none;
  border-radius: 8px;
  transition: background-color 0.3s;
}

.nav-item a:hover,
.nav-item.active a {
  background-color: #f0f0f0;
  color: #007bff;
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
                     <li class="nav-item active">
                <a href="admin-analytics-overview.php"><span>Analytics Overview</span></a>
            </li>
                    <li class="nav-item">
                        <a href="admin-manage-cars.php">
                            <span>Manage Cars</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="admin-manage-users.php">
                            <span>Manage Users</span>
                        </a>
                    </li>
                      <li class="nav-item">
                <a href="admin-manage-bookings.php"><span>Manage Bookings</span></a>
            </li>
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
