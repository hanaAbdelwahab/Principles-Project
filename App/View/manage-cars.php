<?php
require_once __DIR__ . '/../Controller/CarController.php';
require_once __DIR__ . '/../Model/Car.php';


$controller = new CarController();

// ADD CAR
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["action"] === "add") {
    $controller->createCar($_POST);
    header("Location: manage-cars.php");
    exit;
}

// UPDATE CAR
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["action"] === "update") {
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
        .edit-mode input, .edit-mode select, .edit-mode textarea { background-color: #fff9e6; border: 1px solid #aaa; }
        .modal p {
    margin: 8px 0;
    font-size: 15px;
    color: #333;
}
.modal h2 {
    margin-bottom: 15px;
    color: #3366ff;
}

    </style>
    <script>
        function enableEdit(id) {
            const row = document.getElementById("row-" + id);
            row.classList.add("edit-mode");
            const inputs = row.querySelectorAll("input, select, textarea");
            inputs.forEach(el => el.disabled = false);
            row.querySelector(".btn-edit").style.display = "none";
            row.querySelector(".btn-update").style.display = "inline-block";
        }
    </script>
</head>
<body>

<h1>Manage Cars</h1>

<!-- Add Car -->
<form method="POST">
    <input type="hidden" name="action" value="add">
    <input name="name" placeholder="Car Name" required>
    <input name="model" placeholder="Model" required>
    <input type="number" name="year" placeholder="Year" required>
    <input type="number" step="0.01" name="price_per_day" placeholder="Price/Day" required>
    <input name="image_filename" placeholder="Image Filename (e.g., car.png)">
    <textarea name="description" placeholder="Description" rows="2" cols="30"></textarea>
    <input name="color" placeholder="Color">
    <input name="location" placeholder="Location">
    <input type="date" name="start_date">
    <input type="date" name="end_date">
    <input type="number" step="0.1" name="rate" placeholder="Rating (0–5)">
    <input type="number" name="renters" placeholder="Renters">
    <select name="transmission_type">
        <option value="">Transmission</option>
        <option value="Automatic">Automatic</option>
        <option value="Manual">Manual</option>
    </select>
    <select name="power_type">
        <option value="">Power Type</option>
        <option value="Electric">Electric</option>
        <option value="Fuel">Fuel</option>
    </select>
    <input name="wheels" placeholder="Wheels (e.g., forged V13)">
    <input name="brakes" placeholder="Brakes (e.g., disc)">
    <button class="btn btn-add" type="submit">Add</button>
</form>

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

    <!-- Modal HTML -->
    <div id="modal-<?= $car['id'] ?>" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:1000;">
        <div style="background:white; margin:5% auto; padding:20px; width:90%; max-width:600px; border-radius:8px; position:relative;">
            <h2><?= htmlspecialchars($car['name']) ?> (<?= htmlspecialchars($car['model']) ?>)</h2>
            <p><strong>Year:</strong> <?= $car['year'] ?></p>
            <p><strong>Price/Day:</strong> <?= $car['price_per_day'] ?> EGP</p>
            <p><strong>Image Filename:</strong> <?= htmlspecialchars($car['image_filename']) ?></p>
            <p><strong>Description:</strong> <?= htmlspecialchars($car['description']) ?></p>
            <p><strong>Color:</strong> <?= htmlspecialchars($car['color']) ?></p>
            <p><strong>Location:</strong> <?= htmlspecialchars($car['location']) ?></p>
            <p><strong>Start Date:</strong> <?= $car['start_date'] ?></p>
            <p><strong>End Date:</strong> <?= $car['end_date'] ?></p>
            <p><strong>Rate:</strong> <?= $car['rate'] ?></p>
            <p><strong>Renters:</strong> <?= $car['renters'] ?></p>
            <p><strong>Transmission:</strong> <?= htmlspecialchars($car['transmission_type']) ?></p>
            <p><strong>Power:</strong> <?= htmlspecialchars($car['power_type']) ?></p>
            <p><strong>Wheels:</strong> <?= htmlspecialchars($car['wheels']) ?></p>
            <p><strong>Brakes:</strong> <?= htmlspecialchars($car['brakes']) ?></p>
            <button onclick="closeModal(<?= $car['id'] ?>)" class="btn btn-delete" style="margin-top:15px;">Close</button>
        </div>
    </div>
<?php endforeach; ?>
</tbody>


</table>

<script>
    function openModal(id) {
        document.getElementById('modal-' + id).style.display = 'block';
    }

    function closeModal(id) {
        document.getElementById('modal-' + id).style.display = 'none';
    }
</script>
<script>
    function openModal(id) {
        document.getElementById('modal-' + id).style.display = 'block';
    }

    function closeModal(id) {
        document.getElementById('modal-' + id).style.display = 'none';
    }
</script>

</body>
</html>
