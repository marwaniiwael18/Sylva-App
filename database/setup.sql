-- Sylva Database Setup Script
-- Run this in phpMyAdmin to create the database

-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `sylva` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Use the database
USE `sylva`;

-- Create a user specifically for Sylva (optional, for better security)
-- You can run this if you want a dedicated database user:
-- CREATE USER 'sylva_user'@'localhost' IDENTIFIED BY 'sylva_password';
-- GRANT ALL PRIVILEGES ON sylva.* TO 'sylva_user'@'localhost';
-- FLUSH PRIVILEGES;

-- Note: Laravel migrations will create the actual tables
-- This script only creates the database structure