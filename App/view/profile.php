<?php
session_start();
echo '<pre>';
print_r($_SESSION);
echo '</pre>';
?>
<!DOCTYPE html>
<html>
<head><title>Profile</title></head>
<body>
<h2>Welcome, <?= $_SESSION['user']['username'] ?></h2>
<form method="POST" enctype="multipart/form-data">
    Email: <input type="email" name="email" value="<?= $_SESSION['email'] ?>" required><br>
    Birthday: <input type="date" name="birthday" value="<?= $_SESSION['birthday'] ?>" required><br>
    Driver's License: <input type="file" name="driver_license"><br>
    National ID: <input type="file" name="national_id"><br>
    <button type="submit">Update Profile</button>
</form>
<a href="index.php?page=history">View History</a>
</body>
</html>
