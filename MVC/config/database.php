<?php
namespace config;

use mysqli;

class Database {
    private static $connection = null;

    public static function getConnection() {
        if (self::$connection === null) {
            self::$connection = new mysqli('localhost', 'root', '', 'expense_tracker');
            if (self::$connection->connect_error) {
                die("Connection failed: " . self::$connection->connect_error);
            }
        }
        return self::$connection;
    }
}
?>
