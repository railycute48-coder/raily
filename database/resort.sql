-- Minimal schema for Resort Booking Management System
CREATE DATABASE IF NOT EXISTS resort_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE resort_db;

-- Admins
CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  fullname VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Customers
CREATE TABLE IF NOT EXISTS customers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fullname VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Rooms
CREATE TABLE IF NOT EXISTS rooms (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL,
  capacity INT DEFAULT 2,
  image VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cottages
CREATE TABLE IF NOT EXISTS cottages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL,
  capacity INT DEFAULT 4,
  image VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Reservations
CREATE TABLE IF NOT EXISTS reservations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT NOT NULL,
  room_id INT DEFAULT NULL,
  cottage_id INT DEFAULT NULL,
  check_in DATE NOT NULL,
  check_out DATE NOT NULL,
  guests INT DEFAULT 1,
  total_price DECIMAL(10,2) NOT NULL,
  status ENUM('pending','confirmed','cancelled','rejected') DEFAULT 'pending',
  payment_proof VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);

-- Payments
CREATE TABLE IF NOT EXISTS payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reservation_id INT NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  method VARCHAR(50),
  proof VARCHAR(255),
  status ENUM('pending','verified','rejected') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE CASCADE
);

-- Sample admin (password placeholder)
INSERT INTO admins (username, password, fullname) VALUES
('admin', '$2y$10$REPLACE_WITH_REAL_HASH', 'Administrator');

-- Sample rooms and cottages
INSERT INTO rooms (name, description, price, capacity) VALUES
('Deluxe Sea View', 'Sea-facing room with king bed', 3500.00, 2),
('Family Room', 'Spacious room for 4', 4500.00, 4);

INSERT INTO cottages (name, description, price, capacity) VALUES
('Beach Cottage', 'Private cottage near the shore', 7500.00, 6);
