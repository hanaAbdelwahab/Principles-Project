<?php
session_start();
include_once(__DIR__ . '/../Controller/UserController.php');
include_once(__DIR__ . '/../Model/AdminUser.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = $_POST['usernameOrEmail'] ?? '';
    $password = $_POST['password'] ?? '';

    $userModel = new User();
    $controller = new UserController($userModel);
    $error = $controller->login($usernameOrEmail, $password);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 50px; }
        form { width: 300px; margin: auto; }
        input[type="text"], input[type="password"] {
            width: 100%; padding: 10px; margin: 5px 0;
        }
        input[type="submit"] {
            padding: 10px 20px;
        }
        .error { color: red; }
    </style>
</head>
<body>

<h2>Login</h2>

<?php if (!empty($error)): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" action="">
    <label for="usernameOrEmail">Username or Email:</label><br>
    <input type="text" name="usernameOrEmail" required><br>

    <label for="password">Password:</label><br>
    <input type="password" name="password" required><br>

    <input type="submit" value="Login">
</form>

</body>
</html>


