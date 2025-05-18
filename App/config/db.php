<?php
$pdo = new PDO("mysql:host=localhost;dbname=principles", 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
return $pdo;
