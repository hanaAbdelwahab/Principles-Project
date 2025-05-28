<?php
require_once __DIR__ . '/../Controller/AdminCarController.php';
require_once __DIR__ . '/../Model/AdminCar.php';

$controller = new AdminCarController();

function uploadImage($file) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileName = uniqid() . "_" . basename($file['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $targetPath;
    }

    return $uploadDir . 'default-car.jpg';
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["action"] === "add") {
    // Handle image upload for the new car
    $imagePath = uploadImage($_FILES['image_filename']);
    $_POST['image_filename'] = $imagePath;

    // Insert the new car data into the database
    $controller->createCar($_POST);
    header("Location: admin-manage-cars.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["action"] === "update") {
    // Handle image upload for the updated car, only if a new image is uploaded
    if (isset($_FILES['image_filename']) && $_FILES['image_filename']['size'] > 0) {
        $_POST['image_filename'] = uploadImage($_FILES['image_filename']);
    }

    // Update the car data in the database
    $controller->updateCar($_POST['id'], $_POST);
    header("Location: admin-manage-cars.php");
    exit;
}

if (isset($_GET["delete"])) {
    // Delete the car from the database
    $controller->deleteCar($_GET["delete"]);
    header("Location: admin-manage-cars.php");
    exit;
}

function getFilteredCars($filters = []) {
    $query = "SELECT * FROM cars WHERE 1=1";
    $params = [];
    $types = "";

    // Apply filters based on search, year, color, and transmission type
    if (!empty($filters['search'])) {
        $query .= " AND (name LIKE ? OR model LIKE ? OR location LIKE ?)";
        $searchTerm = "%" . $filters['search'] . "%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= "sss";
    }

    if (!empty($filters['year'])) {
        $query .= " AND year = ?";
        $params[] = $filters['year'];
        $types .= "i";
    }

    if (!empty($filters['color'])) {
        $query .= " AND color = ?";
        $params[] = $filters['color'];
        $types .= "s";
    }

    if (!empty($filters['transmission_type'])) {
        $query .= " AND transmission_type = ?";
        $params[] = $filters['transmission_type'];
        $types .= "s";
    }

    $stmt = Database::getInstance()->prepare($query);

    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$filters = [
    'search' => $_GET['search'] ?? '',
    'year' => $_GET['year'] ?? '',
    'color' => $_GET['color'] ?? '',
    'transmission_type' => $_GET['transmission_type'] ?? '',
];
$cars = getFilteredCars($filters);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Cars</title>
    <link rel="stylesheet" href="../../Public/css/styles.css">
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
                        <a href="admin-analytics-overview.php"><span>Analytics Overview</span></a>
                    </li>
                    <li class="nav-item active">
                        <a href="#">
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

<style>
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
        <!-- Add Car Modal -->
        <div class="modal" id="addCarModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Add New Car</h3>
                    <button class="close-modal" onclick="document.getElementById('addCarModal').classList.remove('active')">✕</button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add">
                        <input type="text" name="name" placeholder="Car Name" required>
                        <input type="text" name="model" placeholder="Model" required>
                        <input type="number" name="year" placeholder="Year" required>
                        <input type="number" step="0.01" name="price_per_day" placeholder="Price Per Day" required>
                        <textarea name="description" placeholder="Description" required></textarea>
                        <input type="text" name="color" placeholder="Color" required>
                        <input type="text" name="location" placeholder="Location" required>
                        <input type="date" name="start_date" required>
                        <input type="date" name="end_date" required>
                        <input type="text" name="transmission_type" placeholder="Transmission Type" required>
                        <input type="text" name="power_type" placeholder="Power Type" required>
                        <input type="text" name="wheels" placeholder="Wheels" required>
                        <input type="text" name="brakes" placeholder="Brakes" required>
                        <input type="file" name="image_filename" required>

                        <div class="modal-footer">
                            <button type="submit" class="save-btn">Add Car</button>
                            <button type="button" class="cancel-btn" onclick="document.getElementById('addCarModal').classList.remove('active')">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <main class="main-content">
            <header class="content-header">
                <div class="header-left">
                    <h2>Manage Cars</h2>
                </div>
                <div class="header-right">
                    <div class="search-container">
                    </div>
                </div>
            </header>

            <div class="dashboard-content">
                <section class="content-section active">
                    <div class="section-header">
                        <h3>Car List</h3>
                        <div class="actions">
                            <button class="add-btn" onclick="document.getElementById('addCarModal').classList.add('active')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                Add Car
                            </button>
                        </div>
                    </div>

                    <form method="GET" class="filter-form">
                        <input type="text" name="search" placeholder="Search by name, model, or location" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        <select name="year">
                            <option value="">All Years</option>
                            <?php for ($y = 2015; $y <= date("Y"); $y++): ?>
                                <option value="<?= $y ?>" <?= (isset($_GET['year']) && $_GET['year'] == $y) ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                        <select name="color">
                            <option value="">All Colors</option>
                            <option value="Red" <?= ($_GET['color'] ?? '') == 'Red' ? 'selected' : '' ?>>Red</option>
                            <option value="Blue" <?= ($_GET['color'] ?? '') == 'Blue' ? 'selected' : '' ?>>Blue</option>
                            <option value="Black" <?= ($_GET['color'] ?? '') == 'Black' ? 'selected' : '' ?>>Black</option>
                            <option value="White" <?= ($_GET['color'] ?? '') == 'White' ? 'selected' : '' ?>>White</option>
                        </select>
                        <select name="transmission_type">
                            <option value="">All Transmissions</option>
                            <option value="Automatic" <?= ($_GET['transmission_type'] ?? '') == 'Automatic' ? 'selected' : '' ?>>Automatic</option>
                            <option value="Manual" <?= ($_GET['transmission_type'] ?? '') == 'Manual' ? 'selected' : '' ?>>Manual</option>
                        </select>
                        <button type="submit">Apply Filters</button>
                    </form>

                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Model</th>
                                    <th>Year</th>
                                    <th>Price/Day</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cars as $car): ?>
                                    <tr>
                                        <td><?= $car['id'] ?></td>
                                        <td><?= htmlspecialchars($car['name']) ?></td>
                                        <td><?= htmlspecialchars($car['model']) ?></td>
                                        <td><?= htmlspecialchars($car['year']) ?></td>
                                        <td><?= htmlspecialchars($car['price_per_day']) ?></td>
                                        <td>
                                            <button class="table-btn view-btn" onclick="openEditModal(<?= $car['id'] ?>)">View</button>
                                            <a href="?delete=<?= $car['id'] ?>" class="table-btn delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
                                        </td>
                                    </tr>
                                    <!-- Modal for Editing the Car -->
                                    <div class="modal" id="modal-<?= $car['id'] ?>">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h3>Edit Car - <?= htmlspecialchars($car['name']) ?></h3>
                                                <button class="close-modal" onclick="closeModal(<?= $car['id'] ?>)">✕</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="POST" enctype="multipart/form-data">
                                                    <input type="hidden" name="action" value="update">
                                                    <input type="hidden" name="id" value="<?= $car['id'] ?>">
                                                    <input type="text" name="name" id="name-<?= $car['id'] ?>" value="<?= htmlspecialchars($car['name']) ?>" required disabled>
                                                    <input type="text" name="model" id="model-<?= $car['id'] ?>" value="<?= htmlspecialchars($car['model']) ?>" required disabled>
                                                    <input type="number" name="year" id="year-<?= $car['id'] ?>" value="<?= htmlspecialchars($car['year']) ?>" required disabled>
                                                    <input type="number" step="0.01" name="price_per_day" id="price-<?= $car['id'] ?>" value="<?= htmlspecialchars($car['price_per_day']) ?>" required disabled>
                                                    <textarea name="description" id="desc-<?= $car['id'] ?>" disabled><?= htmlspecialchars($car['description']) ?></textarea>
                                                    <input type="text" name="color" id="color-<?= $car['id'] ?>" value="<?= htmlspecialchars($car['color']) ?>" disabled>
                                                    <input type="text" name="location" id="location-<?= $car['id'] ?>" value="<?= htmlspecialchars($car['location']) ?>" disabled>
                                                    <input type="file" name="image_filename" id="file-<?= $car['id'] ?>" style="display: none;">
                                                    <img src="<?= htmlspecialchars($car['image_filename']) ?>" style="max-width: 100%; border-radius: 10px; margin-top: 10px;">
                                                    <div class="modal-footer">
                                                        <button type="button" class="edit-btn" onclick="enableEdit(<?= $car['id'] ?>)">Edit</button>
                                                        <button type="submit" class="save-btn" id="update-btn-<?= $car['id'] ?>" style="display:none;">Update</button>
                                                        <button type="button" class="cancel-btn" onclick="closeModal(<?= $car['id'] ?>)">Cancel</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <script>
        function openEditModal(id) {
            document.getElementById('modal-' + id).classList.add('active');
        }

        function closeModal(id) {
            document.getElementById('modal-' + id).classList.remove('active');
        }

        function enableEdit(id) {
            document.getElementById('name-' + id).disabled = false;
            document.getElementById('model-' + id).disabled = false;
            document.getElementById('year-' + id).disabled = false;
            document.getElementById('price-' + id).disabled = false;
            document.getElementById('desc-' + id).disabled = false;
            document.getElementById('color-' + id).disabled = false;
            document.getElementById('location-' + id).disabled = false;
            document.getElementById('file-' + id).style.display = 'block';
            document.getElementById('update-btn-' + id).style.display = 'inline-block';
        }
    </script>
</body>
</html>
