<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head><title>History</title></head>
<body>
<h2><?= $_SESSION['user_name'] ?>'s History</h2>
<ul>
    <?php foreach ($history as $h): ?>
        <li><?= $h['action'] ?> - <?= $h['created_at'] ?></li>
    <?php endforeach; ?>
</ul>
<a href="index.php?page=profile">Back to Profile</a>
</body>
</html>
