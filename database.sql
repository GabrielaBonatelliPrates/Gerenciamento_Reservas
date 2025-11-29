
CREATE DATABASE IF NOT EXISTS reservas_bd;
USE reservas_bd;

CREATE TABLE IF NOT EXISTS Reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    NomeCliente VARCHAR(100) NOT NULL,
    Telefone VARCHAR(16),
    Email VARCHAR(100),
    Produto VARCHAR(100) NOT NULL,
    PrecoProduto DECIMAL(10,2) NOT NULL,
    Quantidade INT NOT NULL,
    DataReserva DATE NOT NULL,
    DataRetirada DATE,
    Status ENUM('reservado', 'retirado') NOT NULL DEFAULT 'reservado'
);
