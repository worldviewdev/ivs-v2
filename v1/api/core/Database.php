<?php

class Database {
    private static $instance;

    public static function conn() {
        if (!self::$instance) {
            $dsn = "mysql:host=localhost;dbname=brazilgr_ivs;charset=utf8mb4";
            self::$instance = new PDO($dsn, "root", "", [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        }
        return self::$instance;
    }
}
