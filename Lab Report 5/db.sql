-- Create database first (run in phpMyAdmin or MySQL client)
CREATE DATABASE IF NOT EXISTS biodata_app CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE biodata_app;

-- Users table
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Biodata table
CREATE TABLE IF NOT EXISTS biodata (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  fullname VARCHAR(150) NOT NULL,
  father VARCHAR(150),
  mother VARCHAR(150),
  dob DATE,
  gender VARCHAR(20),
  address TEXT,
  phone VARCHAR(30),
  email VARCHAR(150),
  linkedin VARCHAR(255),
  github VARCHAR(255),
  education TEXT,
  skills TEXT,
  languages VARCHAR(255),
  marital_status VARCHAR(20),
  hobbies TEXT,
  blood VARCHAR(10),
  website VARCHAR(255),
  photo_path VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_biodata_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
