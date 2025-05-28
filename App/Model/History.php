<?php
require_once __DIR__ . '/../config/dp.php';

class History {
    public static function getByUserId($userId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM history WHERE user_id = ? ORDER BY date_rented DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
