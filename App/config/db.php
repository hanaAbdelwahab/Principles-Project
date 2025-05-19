<!--App/config/db.php-->
<?php

class Database {
    private static ?PDO $instance = null;

    public static function getInstance(): PDO {
        if (!self::$instance) {
            self::$instance = new PDO('mysql:host=localhost;dbname=principles', 'root', '');
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$instance;
    }
}
