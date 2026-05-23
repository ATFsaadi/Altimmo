<?php

class Database
{
    private static $pdo = null;

    public static function getConnection()
    {
        if (self::$pdo === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

            try {
                self::$pdo = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                if (APP_DEBUG) {
                    die('Erreur de connexion a la base de donnees : ' . $e->getMessage());
                }

                die('Impossible de se connecter a la base de donnees.');
            }
        }

        return self::$pdo;
    }
}

