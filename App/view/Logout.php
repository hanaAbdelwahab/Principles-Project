<?php
// Start the session
session_start();

// Include the UserController
require_once 'Controller/UserController.php';
$userController = new UserController();

// Process logout
$result = $userController->logout();

// Store logout message in session to be displayed on login page
$_SESSION['message'] = "You have been successfully logged out.";
$_SESSION['message_type'] = "success";

// Redirect to login page
header("Location: " . $result['redirect']);
exit();
?>