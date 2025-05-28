<?php
require_once __DIR__ . '/../Service/LoginService.php';
require_once __DIR__ . '/../Utils/SessionManager.php';

SessionManager::start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $loginService = new LoginService();
    $user = $loginService->authenticate($username);

    if ($user) {
        SessionManager::setUser($user);
        header('Location: index.php?page=viewprofile');
        exit;
    } else {
        $error = "User not found.";
    }
}

require_once __DIR__ . '/../views/login.php';
