<?php
session_start();

require_once __DIR__ . '/../Controller/AdminUserController.php';
require_once __DIR__ . '/../Model/AdminUser.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = $_POST['usernameOrEmail'] ?? '';
    $password = $_POST['password'] ?? '';

    // Create PDO connection and inject to model & controller
    $pdo = \Database::getInstance();
    $userModel = new AdminUser($pdo);
    $controller = new AdminUserController($userModel);

    $error = $controller->login($usernameOrEmail, $password);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Login</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 50px; background-color: #f5f5f5; }
        form { width: 320px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        input[type="text"], input[type="password"] {
            width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px;
        }
        input[type="submit"] {
            padding: 10px 20px; background-color: #007bff; border: none; color: white; font-weight: bold; border-radius: 4px; cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .error { color: red; text-align: center; margin-bottom: 15px; }
    </style>
</head>
<body>

<h2 style="text-align:center;">Login</h2>

<?php if (!empty($error)): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" action="">
    <label for="usernameOrEmail">Username or Email:</label><br />
    <input type="text" name="usernameOrEmail" id="usernameOrEmail" required autofocus /><br />

    <label for="password">Password:</label><br />
    <input type="password" name="password" id="password" required /><br />

    <input type="submit" value="Login" />
</form>

</body>
</html>
