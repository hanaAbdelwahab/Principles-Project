<?php

class SessionManager {
    public static function start() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function setUser($user) {
        $_SESSION['user'] = $user;
    }
}
