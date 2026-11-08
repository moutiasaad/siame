-- SIAME — supervision des machines par GPRS (MySQL / MariaDB)
CREATE DATABASE IF NOT EXISTS siame CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE siame;

DROP TABLE IF EXISTS commands, alarms, measurements, machines, users;

CREATE TABLE users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  first_name    VARCHAR(60)  NOT NULL,
  last_name     VARCHAR(60)  NOT NULL,
  email         VARCHAR(120) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('admin','operator') NOT NULL DEFAULT 'operator',
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Une machine = une carte Arduino + SIM900 (identifiant envoyé par le firmware : MACHINE_ID)
CREATE TABLE machines (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(80)  NOT NULL,
  model       VARCHAR(80)  NOT NULL DEFAULT 'Billion Hercule H2000',
  location    VARCHAR(80)  NOT NULL,
  state       ENUM('on','off') NOT NULL DEFAULT 'off',   -- état demandé depuis le site
  temperature DECIMAL(5,1) NULL,                          -- dernière mesure reçue
  humidity    DECIMAL(5,1) NULL,
  temp_max    DECIMAL(5,1) NOT NULL DEFAULT 60.0,         -- seuil d'alarme (armoire / groupe hydraulique)
  last_sync   DATETIME NULL,                              -- dernier message de la carte
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE measurements (
  id          BIGINT AUTO_INCREMENT PRIMARY KEY,
  machine_id  INT NOT NULL,
  temperature DECIMAL(5,1) NOT NULL,
  humidity    DECIMAL(5,1) NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_machine_time (machine_id, created_at),
  FOREIGN KEY (machine_id) REFERENCES machines(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE alarms (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  machine_id      INT NULL,
  type            ENUM('intrusion','temperature') NOT NULL,
  message         VARCHAR(255) NOT NULL,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  acknowledged_at DATETIME NULL,
  acknowledged_by INT NULL,
  INDEX idx_open (acknowledged_at, created_at),
  FOREIGN KEY (machine_id) REFERENCES machines(id) ON DELETE CASCADE,
  FOREIGN KEY (acknowledged_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Historique des marche / arrêt commandés depuis le site
CREATE TABLE commands (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  machine_id  INT NOT NULL,
  user_id     INT NULL,
  state       ENUM('on','off') NOT NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (machine_id) REFERENCES machines(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;
