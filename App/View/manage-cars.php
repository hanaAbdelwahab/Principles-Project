<?php
require_once __DIR__ . '/../Controller/CarController.php';
require_once __DIR__ . '/../Model/Car.php';

$controller = new CarController();

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
    $imagePath = uploadImage($_FILES['image_filename']);
    $_POST['image_filename'] = $imagePath;

    $controller->createCar($_POST);
    header("Location: manage-cars.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["action"] === "update") {
    if (isset($_FILES['image_filename']) && $_FILES['image_filename']['size'] > 0) {
        $_POST['image_filename'] = uploadImage($_FILES['image_filename']);
    }

    $controller->updateCar($_POST['id'], $_POST);
    header("Location: manage-cars.php");
    exit;
}

if (isset($_GET["delete"])) {
    $controller->deleteCar($_GET["delete"]);
    header("Location: manage-cars.php");
    exit;
}

function getFilteredCars($filters = []) {
    $query = "SELECT * FROM cars WHERE 1=1";
    $params = [];
    $types = "";

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

    $stmt = Database::getInstance()->getConnection()->prepare($query);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
                <a href="analytics-overview.php"><span>Analytics Overview</span></a>
            </li>
                    <li class="nav-item active">
                        <a href="#">
                            <span>Manage Cars</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="manage-users.php">
                            <span>Manage Users</span>
                        </a>
                    </li>
                      <li class="nav-item">
                <a href="manage-bookings.php"><span>Manage Bookings</span></a>
            </li>
                </ul>
            </nav>
        </aside>
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
<style>
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(5px);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    padding: 20px;
    overflow-y: auto;
}

.modal.active {
    display: flex;
}

.modal-content {
    background: #fff;
    border-radius: 16px;
    padding: 30px 40px;
    width: 90%;
    max-width: 600px;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
    animation: slideIn 0.3s ease-out forwards;
    position: relative;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

.modal-header h3 {
    margin: 0;
    font-size: 22px;
    font-weight: bold;
}

.modal-header .close-modal {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #555;
}

.modal-body input,
.modal-body select,
.modal-body textarea {
    width: 100%;
    padding: 12px;
    margin: 12px 0;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 15px;
    background-color: #f5f5f5;
    transition: border-color 0.3s, background-color 0.3s;
}

.modal-body input:focus,
.modal-body select:focus,
.modal-body textarea:focus {
    border-color: #007bff;
    background-color: #ffffff;
    outline: none;
}


.modal-body .image-preview {
    width: 100%;
    height: auto;
    margin: 12px 0;
    border-radius: 8px;
    border: 1px solid #ccc;
    object-fit: cover;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 25px;
}

.modal-footer .edit-btn,
.modal-footer .save-btn,
.modal-footer .cancel-btn {
    padding: 10px 22px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: background-color 0.3s;
}

.modal-footer .edit-btn {
    background-color: #007bff;
    color: white;
}

.modal-footer .edit-btn:hover {
    background-color: #0069d9;
}

.modal-footer .save-btn {
    background-color: #28a745;
    color: white;
}

.modal-footer .save-btn:hover {
    background-color: #218838;
}

.modal-footer .cancel-btn {
    background-color: #dc3545;
    color: white;
}

.modal-footer .cancel-btn:hover {
    background-color: #c82333;
}
.modal {
    transition: opacity 0.3s ease;
}
.filter-form {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 20px;
    align-items: center;
}

.filter-form input,
.filter-form select,
.filter-form button {
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
}

</style>

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