<?php
require_once __DIR__ . '/../Controller/UserController.php';
require_once __DIR__ . '/../Model/User.php';


$controller = new UserController();

// Create user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add') {
    $licensePath = uploadFile($_FILES['driver_license_path']);
    $idPath = uploadFile($_FILES['national_id_path']);

    $controller->createUser(
        $_POST['username'],
        $_POST['email'],
        $_POST['password'],
        $_POST['birthdate'],
        $licensePath,
        $idPath,
        $_POST['favorite_color']
    );
    header("Location: manage-users.php");
    exit;
}

// Update user
// Update user (only when update form is submitted)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'update') {
    $licensePath = isset($_FILES['driver_license_path']) && $_FILES['driver_license_path']['size'] > 0
        ? uploadFile($_FILES['driver_license_path'])
        : null;

    $idPath = isset($_FILES['national_id_path']) && $_FILES['national_id_path']['size'] > 0
        ? uploadFile($_FILES['national_id_path'])
        : null;

    $controller->updateUser(
        $_POST['id'],
        $_POST['username'],
        $_POST['email'],
        $_POST['birthdate'],
        $_POST['favorite_color'],
        $licensePath,
        $idPath
    );

    header("Location: manage-users.php");
    exit;
}



// Delete user
if (isset($_GET['delete'])) {
    $controller->deleteUser($_GET['delete']);
    header("Location: manage-users.php");
    exit;
}

// File upload helper
function uploadFile($file) {
    $uploadDir = 'uploads/';  // relative to your project root
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileName = uniqid() . "_" . basename($file['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $targetPath;
    }
    return $uploadDir . 'default.jpg';
}


$users = $controller->getAllUsers();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    h2 {
        font-weight: bold;
        color: #343a40;
    }

    .table thead th {
        text-align: center;
    }

    .table td, .table th {
        vertical-align: middle;
        text-align: center;
    }

    .btn-primary {
        background-color: #4285f4;
        border: none;
    }

    .btn-primary:hover {
        background-color: #3367d6;
    }

    .btn-warning {
        color: white;
        background-color: #fbbc05;
        border: none;
    }

    .btn-warning:hover {
        background-color: #e0a800;
    }

    .btn-danger {
        background-color: #ea4335;
        border: none;
    }

    .btn-danger:hover {
        background-color: #c63225;
    }

    .modal-header {
        background-color: #4285f4;
        color: white;
    }

    .modal-footer {
        background-color: #f1f1f1;
    }

    .form-control:focus {
        border-color: #4285f4;
        box-shadow: 0 0 0 0.2rem rgba(66, 133, 244, 0.25);
    }

    .btn-close {
        background: white;
    }

    .btn-close:hover {
        background: #ddd;
    }

    .table a {
        color: #007bff;
        text-decoration: none;
    }

    .table a:hover {
        text-decoration: underline;
    }
</style>

</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4 text-center">Manage Users</h2>

    <!-- Add User Button -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">Add New User</button>

    <!-- Users Table -->
    <table class="table table-bordered table-hover bg-white">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Birthdate</th>
                <th>License</th>
                <th>ID</th>
                <th>Color</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['birthdate']) ?></td>
                <td><a href="<?= $user['driver_license_path'] ?>" target="_blank">View</a></td>
                <td><a href="<?= $user['national_id_path'] ?>" target="_blank">View</a></td>
                <td><?= htmlspecialchars($user['favorite_color']) ?></td>
                <td>
                    <button class="btn btn-sm btn-warning edit-btn"
                        data-id="<?= $user['id'] ?>"
                        data-username="<?= htmlspecialchars($user['username']) ?>"
                        data-email="<?= htmlspecialchars($user['email']) ?>"
                        data-birthdate="<?= $user['birthdate'] ?>"
                        data-favorite_color="<?= htmlspecialchars($user['favorite_color']) ?>"
                        data-bs-toggle="modal" data-bs-target="#editUserModal">Edit</button>
                    <a href="?delete=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add New User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
          <input type="hidden" name="action" value="add">
          <div class="mb-3"><label>Username</label><input type="text" name="username" class="form-control" required></div>
          <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
          <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
          <div class="mb-3"><label>Birthdate</label><input type="date" name="birthdate" class="form-control" required></div>
          <div class="mb-3"><label>Driver License</label><input type="file" name="driver_license_path" class="form-control" required></div>
          <div class="mb-3"><label>National ID</label><input type="file" name="national_id_path" class="form-control" required></div>
          <div class="mb-3"><label>Favorite Color</label><input type="text" name="favorite_color" class="form-control"></div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Add</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
  <div class="modal-dialog">
 <form method="POST" enctype="multipart/form-data" class="modal-content">
  <input type="hidden" name="action" value="update">
  <input type="hidden" name="id" id="edit-id">

  <div class="mb-3"><label>Username</label>
    <input type="text" name="username" id="edit-username" class="form-control" required>
  </div>

  <div class="mb-3"><label>Email</label>
    <input type="email" name="email" id="edit-email" class="form-control" required>
  </div>

  <div class="mb-3"><label>Birthdate</label>
    <input type="date" name="birthdate" id="edit-birthdate" class="form-control" required>
  </div>

  <div class="mb-3"><label>Favorite Color</label>
    <input type="text" name="favorite_color" id="edit-favorite-color" class="form-control">
  </div>

  <div class="mb-3"><label>Update Driver License</label>
    <input type="file" name="driver_license_path" class="form-control">
  </div>

  <div class="mb-3"><label>Update National ID</label>
    <input type="file" name="national_id_path" class="form-control">
  </div>

  <div class="modal-footer">
    <button type="submit" class="btn btn-warning">Update</button>
  </div>
</form>

  </div>
</div>

<!-- JS Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', () => {
        document.getElementById('edit-id').value = button.dataset.id;
        document.getElementById('edit-username').value = button.dataset.username;
        document.getElementById('edit-email').value = button.dataset.email;
        document.getElementById('edit-birthdate').value = button.dataset.birthdate;
        document.getElementById('edit-favorite-color').value = button.dataset.favorite_color;
    });
});

</script>
</body>
</html>
