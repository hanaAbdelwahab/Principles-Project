<?php
require_once __DIR__ . '/../Model/User.php';
require_once __DIR__ . '/../Model/History.php';
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: index.php?page=login');
    exit;
}

$user = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $uploadPath = 'Public/uploads/';
    if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

    $driverLicensePath = !empty($_FILES['driver_license']['name']) 
        ? $uploadPath . basename($_FILES['driver_license']['name']) 
        : $user['driver_license'];

    $nationalIdPath = !empty($_FILES['national_id']['name']) 
        ? $uploadPath . basename($_FILES['national_id']['name']) 
        : $user['national_id'];

    if (!empty($_FILES['driver_license']['name'])) {
        move_uploaded_file($_FILES['driver_license']['tmp_name'], $driverLicensePath);
    }

    if (!empty($_FILES['national_id']['name'])) {
        move_uploaded_file($_FILES['national_id']['tmp_name'], $nationalIdPath);
    }

    $data = [
        'username' => $_POST['username'],
        'password' => $_POST['password'], // You can hash this if needed
        'email' => $_POST['email'],
        'birthday' => $_POST['birthday'],
        'driver_license' => $driverLicensePath,
        'national_id' => $nationalIdPath
    ];

    User::updateProfile($data, $user['id']);
    $_SESSION['user'] = User::getById($user['id']);
    header("Location: index.php?page=viewprofile");
    exit;
}

$history = History::getByUserId($user['id']);
require_once __DIR__ . '/../views/viewprofile.php';
