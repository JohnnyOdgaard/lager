-- Opret tabel til opladning af elbil
CREATE TABLE IF NOT EXISTS opladning (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dato VARCHAR(10) NOT NULL,
    opladningsgrad INT NOT NULL,
    km_stand INT NOT NULL,
    kwh DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
