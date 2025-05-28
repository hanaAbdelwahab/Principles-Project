<?php
require_once __DIR__ . '/../Model/History.php';
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: index.php?page=login');
    exit;
}

$history = History::getByUserId($_SESSION['id']);
require_once __DIR__ . '/../views/history.php';
