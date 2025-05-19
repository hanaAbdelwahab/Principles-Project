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

    return $uploadDir . 'default-car.jpg'; // fallback
}

// ADD CAR
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["action"] === "add") {
    $imagePath = uploadImage($_FILES['image_filename']);
    $_POST['image_filename'] = $imagePath;

    $controller->createCar($_POST);
    header("Location: manage-cars.php");
    exit;
}

// UPDATE CAR
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["action"] === "update") {
    if (isset($_FILES['image_filename']) && $_FILES['image_filename']['size'] > 0) {
        $_POST['image_filename'] = uploadImage($_FILES['image_filename']);
    }

    $controller->updateCar($_POST['id'], $_POST);
    header("Location: manage-cars.php");
    exit;
}

// DELETE CAR
if (isset($_GET["delete"])) {
    $controller->deleteCar($_GET["delete"]);
    header("Location: manage-cars.php");
    exit;
}

$cars = $controller->getAllCars();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Cars</title>
    <style>
        body { font-family: Arial; background-color: #f4f7ff; padding: 30px; }
        h1 { color: #3366ff; text-align: center; }
        form { display: flex; justify-content: center; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
        input, select, textarea { padding: 10px; border-radius: 5px; border: 1px solid #ccc; }
        table { width: 95%; margin: auto; border-collapse: collapse; background: white; }
        th { background: #3366ff; color: white; padding: 12px; }
        td { text-align: center; padding: 10px; border: 1px solid #ddd; }
        .btn { padding: 8px 14px; border: none; border-radius: 5px; color: white; cursor: pointer; min-width: 80px; }
        .btn-edit { background: #c039fb; }
        .btn-update { background: #4cd964; }
        .btn-delete { background: #ff9933; }
        .btn-add { background: #3366ff; }
        .close-btn {
            position: absolute;
            top: 12px;
            right: 16px;
            font-size: 22px;
            color: #888;
            cursor: pointer;
            background: none;
            border: none;
        }
        .close-btn:hover {
            color: #000;
        }
       #addCarModal {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.65);
  z-index: 1000;
  overflow-y: auto;
  padding: 40px 0;
  backdrop-filter: blur(4px);
}

       
        @media (max-width: 768px) {
  #addCarModal form {
    width: 95% !important;
    padding: 25px !important;
  }
}

    </style>
</head>
<body>

<h1>Manage Cars</h1>

<!-- Add Car Modal -->
<div id="addCarModal" style="
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.6);
    z-index: 1000;
    overflow-y: auto;
    padding: 40px 0;
">
<form method="POST" enctype="multipart/form-data" style="
  background: white;
  margin: auto;
  padding: 40px 50px;
  width: 95%;
  max-width: 900px;
  border-radius: 16px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.25);
  font-size: 16px;
  position: relative;
  max-height: 95vh;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 20px;
">

>
    <input type="hidden" name="action" value="add">
    <button type="button" class="close-btn" onclick="closeModal('addCarModal')">✕</button>

    <h2 style="color: #3366ff; margin-bottom: 20px;">Add New Car</h2>

    <input name="name" placeholder="Car Name" required style="width: 100%; margin-bottom: 10px;">
    <input name="model" placeholder="Model" required style="width: 100%; margin-bottom: 10px;">
    <input type="number" name="year" placeholder="Year" required style="width: 100%; margin-bottom: 10px;">
    <input type="number" step="0.01" name="price_per_day" placeholder="Price/Day" required style="width: 100%; margin-bottom: 10px;">
    <input type="file" name="image_filename" accept="image/*" required style="width: 100%; margin-bottom: 10px;">
    <textarea name="description" placeholder="Description" rows="2" style="width: 100%; margin-bottom: 10px;"></textarea>
    <input name="color" placeholder="Color" style="width: 100%; margin-bottom: 10px;">
    <input name="location" placeholder="Location" style="width: 100%; margin-bottom: 10px;">
    <input type="date" name="start_date" style="width: 100%; margin-bottom: 10px;">
    <input type="date" name="end_date" style="width: 100%; margin-bottom: 10px;">
    
    <select name="transmission_type" style="width: 100%; margin-bottom: 10px;">
        <option value="">Transmission</option>
        <option value="Automatic">Automatic</option>
        <option value="Manual">Manual</option>
    </select>
    <select name="power_type" style="width: 100%; margin-bottom: 10px;">
        <option value="">Power Type</option>
        <option value="Electric">Electric</option>
        <option value="Fuel">Fuel</option>
    </select>
    <input name="wheels" placeholder="Wheels (e.g., forged V13)" style="width: 100%; margin-bottom: 10px;">
    <input name="brakes" placeholder="Brakes (e.g., disc)" style="width: 100%; margin-bottom: 10px;">

    <div style="margin-top: 20px; display: flex; justify-content: space-between;">
      <button type="submit" class="btn btn-add">Add Car</button>
      <button type="button" onclick="closeModal('addCarModal')" class="btn btn-delete">Cancel</button>
    </div>
  </form>
</div>

<!-- Add Car Button (top-right) -->
<div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
  <button class="btn btn-add" onclick="document.getElementById('addCarModal').style.display='block'">
    + Add New Car
  </button>
</div>


<!-- Cars Table -->
<table>
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
            <button class="btn btn-edit" onclick="openModal(<?= $car['id'] ?>)">View</button>
            <a href="?delete=<?= $car['id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
        </td>
    </tr>

    <!-- Modal -->
    <div id="modal-<?= $car['id'] ?>" class="modal" style="
        display: none;
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.6);
        z-index: 1000;
        overflow-y: auto;
        padding: 40px 0;
    ">
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" value="<?= $car['id'] ?>">

        <div style="
            background: white;
            margin: auto;
            padding: 30px 40px;
            width: 90%;
            max-width: 650px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            font-size: 15px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        ">
            <button type="button" class="close-btn" onclick="closeModal(<?= $car['id'] ?>)">✕</button>

            <h2 style="color: #3366ff; margin-bottom: 25px;">
                <?= htmlspecialchars($car['name']) ?> (<?= htmlspecialchars($car['model']) ?>)
            </h2>

            <div style="margin-bottom: 15px;">
                <strong>Year:</strong>
                <input type="number" name="year" value="<?= $car['year'] ?>" disabled style="width: 100%;">
            </div>

            <div style="margin-bottom: 15px;">
                <strong>Price/Day:</strong>
                <input type="number" name="price_per_day" value="<?= $car['price_per_day'] ?>" disabled style="width: 100%;">
            </div>

           <div style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
    <strong>Car Image:</strong>
    <a href="javascript:void(0);" onclick="toggleImage(<?= $car['id'] ?>)" class="btn btn-edit" style="margin-left: auto;">View Image</a>
</div>

<div id="car-image-<?= $car['id'] ?>" style="display:none; margin-top: 15px;">
    <img src="<?= $car['image_filename'] ?>" alt="Car Image" style="max-width: 100%; border-radius: 8px;">
</div>

<!-- Hidden image file input for editing -->
<div style="margin-top: 10px;">
    <input type="file" name="image_filename" id="file-<?= $car['id'] ?>" accept="image/*" style="width: 100%; display: none;">
</div>



            <div style="margin-bottom: 15px;">
                <strong>Description:</strong>
                <textarea name="description" disabled style="width: 100%; height: 60px; border-radius: 5px;"><?= htmlspecialchars($car['description']) ?></textarea>
            </div>

            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <?php
                $fields = [
                    'Color' => 'color',
                    'Location' => 'location',
                    'Start Date' => 'start_date',
                    'End Date' => 'end_date',
                    'Transmission' => 'transmission_type',
                    'Power' => 'power_type',
                    'Wheels' => 'wheels',
                    'Brakes' => 'brakes'
                ];
                foreach ($fields as $label => $name): ?>
                    <div style="flex: 1 1 45%;">
                        <strong><?= $label ?>:</strong>
                        <input name="<?= $name ?>" value="<?= $car[$name] ?>" disabled style="width: 100%;">
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="margin-top: 25px; display: flex; justify-content: space-between;">
                <button type="button" onclick="enableEdit(<?= $car['id'] ?>)" class="btn btn-edit">Edit</button>
                <button type="submit" class="btn btn-update" id="update-btn-<?= $car['id'] ?>" style="display:none;">Update</button>
                <button type="button" onclick="closeModal(<?= $car['id'] ?>)" class="btn btn-delete">Close</button>
            </div>
        </div>
      </form>
    </div>
<?php endforeach; ?>
  </tbody>
</table>

<script>
function toggleImage(id) {
    const imgDiv = document.getElementById('car-image-' + id);
    imgDiv.style.display = imgDiv.style.display === 'none' ? 'block' : 'none';
}

function openModal(id) {
    document.getElementById('modal-' + id).style.display = 'block';
    document.addEventListener('keydown', escHandler);
}

function closeModal(id) {
    document.getElementById('modal-' + id).style.display = 'none';
    document.removeEventListener('keydown', escHandler);
}

function escHandler(e) {
    if (e.key === "Escape") {
        document.querySelectorAll('.modal').forEach(modal => modal.style.display = 'none');
        document.removeEventListener('keydown', escHandler);
    }
}

function enableEdit(id) {
    const modal = document.getElementById('modal-' + id);
    const inputs = modal.querySelectorAll('input, textarea');
    inputs.forEach(el => {
        if (el.type !== 'file') el.disabled = false;
    });

    // Show file input for image upload
    const fileInput = document.getElementById('file-' + id);
    if (fileInput) {
        fileInput.style.display = 'block';
    }

    modal.querySelector('.btn-edit').style.display = 'none';
    modal.querySelector('.btn-update').style.display = 'inline-block';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

document.addEventListener('keydown', function(e) {
    if (e.key === "Escape") {
        document.querySelectorAll('[id$="Modal"]').forEach(modal => modal.style.display = 'none');
    }
});

</script>

</body>
</html>
