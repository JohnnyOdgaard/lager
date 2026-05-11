<?php
// Script to create opladning table
require_once __DIR__ . "/../includes/config.php";

try {
    $sql = "
    CREATE TABLE IF NOT EXISTS opladning (
        id INT AUTO_INCREMENT PRIMARY KEY,
        dato VARCHAR(10) NOT NULL,
        opladningsgrad INT NOT NULL,
        km_stand INT NOT NULL,
        kwh DECIMAL(10,2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    
    $pdo->exec($sql);
    echo "Tabellen 'opladning' er oprettet succesfuldt!\n";
} catch (PDOException $e) {
    echo "Fejl: " . $e->getMessage() . "\n";
}
