<?php
session_start();
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Clear session and destroy
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();

    // Redirect after logout
    header("Location: Homepage.php");
    exit;
}
require_once __DIR__ . '/../Controller/AdminUserController.php';
require_once __DIR__ . '/../Model/AdminUser.php';


$controller = new AdminUserController();

function uploadFile($file) {
    $uploadDir = 'uploads/';
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

// Handle Add
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
    header("Location: admin-manage-users.php");
    exit;
}

// Handle Update
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
    header("Location: admin-manage-users.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $controller->deleteUser($_GET['delete']);
    header("Location: admin-manage-users.php");
    exit;
}

// FILTERING
$search = $_GET['search'] ?? '';
$colorFilter = $_GET['color'] ?? '';
$users = array_filter($controller->getAllUsers(), function($user) use ($search, $colorFilter) {
    $matchesSearch = empty($search) || stripos($user['username'], $search) !== false || stripos($user['email'], $search) !== false;
    $matchesColor = empty($colorFilter) || $user['favorite_color'] === $colorFilter;
    return $matchesSearch && $matchesColor;
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
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
                <li class="nav-item">
                    <a href="admin-manage-cars.php"><span>Manage Cars</span></a>
                </li>
                <li class="nav-item active">
                    <a href="#"><span>Manage Users</span></a>
                </li>
                  <li class="nav-item">
                <a href="admin-manage-bookings.php"><span>Manage Bookings</span></a>
            </li>
            <li class="nav-item">
      <a href="?action=logout"><span>Logout</span></a> <!-- Added logout link -->
    </li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header class="content-header">
            <div class="header-left">
                <h2>Manage Users</h2>
            </div>
        </header>

        <div class="dashboard-content">
            <section class="content-section active">
                <div class="section-header">
                    <h3>User List</h3>
                    <div class="actions">
                        <button class="add-btn" onclick="document.getElementById('addUserModal').classList.add('active')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            Add User
                        </button>
                    </div>
                </div>

                <!-- Filter Form -->
                <form method="GET" class="filter-form" style="margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
                    <input type="text" name="search" placeholder="Search by username or email" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    <select name="color">
                        <option value="">All Colors</option>
                        <option value="Red" <?= ($_GET['color'] ?? '') === 'Red' ? 'selected' : '' ?>>Red</option>
                        <option value="Blue" <?= ($_GET['color'] ?? '') === 'Blue' ? 'selected' : '' ?>>Blue</option>
                        <option value="Green" <?= ($_GET['color'] ?? '') === 'Green' ? 'selected' : '' ?>>Green</option>
                        <option value="Yellow" <?= ($_GET['color'] ?? '') === 'Yellow' ? 'selected' : '' ?>>Yellow</option>
                    </select>
                    <button type="submit">Apply Filters</button>
                </form>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
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
                                      <button class="table-btn view-btn" onclick="editUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>', '<?= htmlspecialchars($user['email']) ?>', '<?= $user['birthdate'] ?>', '<?= htmlspecialchars($user['favorite_color']) ?>')">View</button>


                                        <a href="?delete=<?= $user['id'] ?>" class="table-btn delete-btn" onclick="return confirm('Delete this user?')">Delete</a>
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

<!-- Add User Modal -->
<div class="modal" id="addUserModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add New User</h3>
            <button class="close-modal" onclick="document.getElementById('addUserModal').classList.remove('active')">✕</button>
        </div>
        <div class="modal-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="date" name="birthdate" required>
               <div class="file-input-row">
  <label for="edit-license">Driver License</label>
  <input type="file" name="driver_license_path" id="edit-license">
</div>

<div class="file-input-row">
  <label for="edit-idfile">National ID</label>
  <input type="file" name="national_id_path" id="edit-idfile">
</div>

                <input type="text" name="favorite_color" placeholder="Favorite Color">
                <div class="modal-footer">
                    <button type="submit" class="save-btn">Add User</button>
                    <button type="button" class="cancel-btn" onclick="document.getElementById('addUserModal').classList.remove('active')">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal" id="editUserModal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Edit User</h3>
      <button class="close-modal" onclick="closeEditUserModal()">✕</button>
    </div>
    <div class="modal-body">
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" id="edit-id">

        <input type="text" name="username" id="edit-username" placeholder="Username" required disabled>
        <input type="email" name="email" id="edit-email" placeholder="Email" required disabled>
        <input type="date" name="birthdate" id="edit-birthdate" required disabled>
        <input type="text" name="favorite_color" id="edit-favorite-color" placeholder="Favorite Color" disabled>
        <input type="file" name="driver_license_path" id="edit-license" style="display:none;">
        <input type="file" name="national_id_path" id="edit-idfile" style="display:none;">

        <div class="modal-footer">
          <button type="button" class="edit-btn" onclick="enableUserEdit()">Edit</button>
          <button type="submit" class="save-btn" id="update-btn" style="display: none;">Update User</button>
          <button type="button" class="cancel-btn" onclick="closeEditUserModal()">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
    
/* FILTER FORM STYLES */
.filter-form {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  align-items: center;
  margin-bottom: 20px;
}

.filter-form input,
.filter-form select,
.filter-form button {
  padding: 10px 14px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  background-color: #f9fafb;
  color: #333;
  box-shadow: none;
  transition: border 0.2s, background-color 0.2s;
}

.filter-form input::placeholder {
  color: #888;
}

.filter-form input:focus,
.filter-form select:focus,
.filter-form button:focus {
  border-color: #007bff;
  background-color: #fff;
  outline: none;
  box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.1);
}

.filter-form button {
  background-color: #f0f0f0;
  color: #333;
  cursor: pointer;
  font-weight: 500;
}

.filter-form button:hover {
  background-color: #e2e2e2;
}

/* MODAL OVERLAY */
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
  padding: 40px 20px;
  overflow-y: auto;
}

.modal.active {
  display: flex;
}

/* MODAL CONTENT */
.modal-content {
  background: #fff;
  border-radius: 16px;
  padding: 30px 40px;
  width: 90%;
  max-width: 600px;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
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

/* MODAL HEADER */
.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #eee;
  padding-bottom: 12px;
  margin-bottom: 24px;
}

.modal-header h3 {
  font-size: 22px;
  font-weight: bold;
  margin: 0;
}

.modal-header .close-modal {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #777;
}

/* FORM INPUTS */
.modal-body form {
  display: flex;
  flex-direction: column;
}

.modal-body input,
.modal-body select,
.modal-body textarea {
  width: 100%;
  padding: 12px;
  margin-bottom: 16px;
  border: 1px solid #ccc;
  border-radius: 10px;
  font-size: 15px;
  background-color: #f9f9f9;
  transition: border-color 0.3s;
}

.modal-body input:focus,
.modal-body select:focus,
.modal-body textarea:focus {
  border-color: #007bff;
  background-color: #fff;
  outline: none;
}

/* MODAL FOOTER BUTTONS */
.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 16px;
}

.modal-footer .save-btn {
  background-color: #28a745;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
}

.modal-footer .save-btn:hover {
  background-color: #218838;
}

.modal-footer .cancel-btn {
  background-color: #dc3545;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
}

.modal-footer .cancel-btn:hover {
  background-color: #c82333;
}

/* EDIT BUTTON (DARKER BLUE) */
.modal-footer .edit-btn {
  background-color: #007bff;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.modal-footer .edit-btn:hover {
  background-color: #0056b3;
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

<script>
function editUser(id, username, email, birthdate, color) {
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-username').value = username;
    document.getElementById('edit-email').value = email;
    document.getElementById('edit-birthdate').value = birthdate;
    document.getElementById('edit-favorite-color').value = color;
    document.getElementById('editUserModal').classList.add('active');
}

</script>
<script>
function editUser(id, username, email, birthdate, color) {
  // Fill values
  document.getElementById('edit-id').value = id;
  document.getElementById('edit-username').value = username;
  document.getElementById('edit-email').value = email;
  document.getElementById('edit-birthdate').value = birthdate;
  document.getElementById('edit-favorite-color').value = color;

  // Disable inputs
  document.getElementById('edit-username').disabled = true;
  document.getElementById('edit-email').disabled = true;
  document.getElementById('edit-birthdate').disabled = true;
  document.getElementById('edit-favorite-color').disabled = true;
  document.getElementById('edit-license').style.display = 'none';
  document.getElementById('edit-idfile').style.display = 'none';

  // Hide Update button
  document.getElementById('update-btn').style.display = 'none';

  // Show Modal
  document.getElementById('editUserModal').classList.add('active');
}

function enableUserEdit() {
  // Enable fields
  document.getElementById('edit-username').disabled = false;
  document.getElementById('edit-email').disabled = false;
  document.getElementById('edit-birthdate').disabled = false;
  document.getElementById('edit-favorite-color').disabled = false;
  document.getElementById('edit-license').style.display = 'block';
  document.getElementById('edit-idfile').style.display = 'block';

  // Show Update button
  document.getElementById('update-btn').style.display = 'inline-block';
}

function closeEditUserModal() {
  document.getElementById('editUserModal').classList.remove('active');
}
</script>

</body>
</html>
