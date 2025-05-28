<?php
require_once __DIR__ . '/../Model/User.php';

class LoginService {
    public function authenticate($username) {
        return User::getByUsername($username);
    }
}
