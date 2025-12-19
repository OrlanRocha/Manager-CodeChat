-- Banco de dados para o CodeChat Manager
-- Ajuste o nome do banco conforme necessário.
CREATE DATABASE IF NOT EXISTS codechat_manager
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE codechat_manager;

-- Tabela de instâncias gerenciadas
CREATE TABLE IF NOT EXISTS instances (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  instance_name VARCHAR(100) NOT NULL UNIQUE,
  api_key VARCHAR(255) DEFAULT NULL,
  webhook_url VARCHAR(255) DEFAULT NULL,
  status ENUM('connected', 'disconnected', 'pending') NOT NULL DEFAULT 'disconnected',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
