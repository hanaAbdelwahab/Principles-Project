<?php
// Database connection configuration
return [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'car_rental_system',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
];