CREATE TABLE IF NOT EXISTS lager_log (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    handling VARCHAR(20) NOT NULL,
    varenavn VARCHAR(255) NOT NULL,
    kobsdato DATE NULL,
    maengde DECIMAL(12,2) NULL,
    enhed VARCHAR(50) NULL,
    aktionsdato DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_aktionsdato (aktionsdato),
    KEY idx_handling (handling),
    KEY idx_varenavn (varenavn)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
