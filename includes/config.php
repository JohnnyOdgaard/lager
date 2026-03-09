<?php
$host = "localhost";
$db   = "lager";
$user = "root";
$pass = "!Syn!Db!0812";
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Databaseforbindelse fejlede: " . $e->getMessage());
}
