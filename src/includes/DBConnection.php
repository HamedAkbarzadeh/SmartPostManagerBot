<?php

namespace src\includes;

use PDO;
use PDOException;

require_once __DIR__ . '/../core/initialize.php';
class DBConnection
{
    private static $dbInstance = null;

    private static function pdoInstance()
    {
        try {
            $option = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ];
            $db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD, $option);
            $db->exec("SET NAMES 'utf8mb4'");
            $db->exec("SET CHARACTER SET utf8mb4");
            $db->exec("SET SESSION collation_connection = 'utf8mb4_unicode_ci'");
            return $db;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    public static function getInstance()
    {
        if (self::$dbInstance == null) {
            self::$dbInstance = self::pdoInstance();
        }
        return self::$dbInstance;
    }
}
