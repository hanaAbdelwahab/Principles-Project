-- Create database if it doesn't exist
-- Uncomment the line below if you need to create a new database
-- CREATE DATABASE IF NOT EXISTS car_rental_system;

-- Use the database
-- USE car_rental_system;

-- Drop the users table if it exists to avoid errors
DROP TABLE IF EXISTS users;

-- Create the users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    birthdate DATE NOT NULL,
    driver_license_path VARCHAR(255) NOT NULL,
    national_id_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    favorite_color VARCHAR(50) -- For password recovery question
);

-- Create index on email for faster lookups
CREATE INDEX idx_user_email ON users(email);

-- Optional: Insert a test user (same credentials as in your PHP file)
INSERT INTO users (username, email, password, birthdate, driver_license_path, national_id_path)
VALUES (
    'sarah',
    'sarah@gmail.com',
    -- Using a hashed password instead of plain text 'password'
    -- In PHP you would use: password_hash('password', PASSWORD_DEFAULT)
    '1234',
    '2003-01-01',
    'uploads/default_license.jpg',
    'uploads/default_id.jpg'
);

-- Create a table for password reset tokens
CREATE TABLE password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);