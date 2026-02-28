CREATE DATABASE IF NOT EXISTS siscatalogo DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE siscatalogo;

-- Perfil de la Empresa
CREATE TABLE `company_profile` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `ruc_nit` VARCHAR(50) DEFAULT NULL,
    `address` TEXT DEFAULT NULL,
    `phone_whatsapp` VARCHAR(20) DEFAULT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `logo_url` VARCHAR(255) DEFAULT NULL,
    `facebook_url` VARCHAR(255) DEFAULT NULL,
    `pinterest_url` VARCHAR(255) DEFAULT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Usuarios Administradores y Clientes
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'customer') DEFAULT 'customer',
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categorías
CREATE TABLE `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `type` ENUM('physical', 'digital') NOT NULL DEFAULT 'physical'
);

-- Productos
CREATE TABLE `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `slug` VARCHAR(150) NOT NULL UNIQUE,
    `description` TEXT,
    `price_unit` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `price_dozen` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `price_combo` DECIMAL(10,2) DEFAULT NULL,
    `image_url` VARCHAR(255) DEFAULT NULL,
    `is_digital` BOOLEAN DEFAULT 0,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE RESTRICT
);

-- Transporte de Proforma
CREATE TABLE `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `customer_name` VARCHAR(150) NOT NULL,
    `customer_email` VARCHAR(150) NOT NULL,
    `customer_phone` VARCHAR(20),
    `customer_city` VARCHAR(100) NOT NULL,
    `customer_address` TEXT,
    `total_amount` DECIMAL(10,2) NOT NULL,
    `status` ENUM('pending', 'approved', 'completed', 'cancelled') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Detalles Proforma
CREATE TABLE `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `quantity` INT NOT NULL,
    `price_applied` DECIMAL(10,2) NOT NULL,
    `subtotal` DECIMAL(10,2) NOT NULL,
    `custom_note` TEXT,
    `custom_logo_link` VARCHAR(255),
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT
);

-- Descargas Protegidas
CREATE TABLE `digital_access` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_item_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `download_token` VARCHAR(100) NOT NULL UNIQUE,
    `expires_at` DATETIME NOT NULL,
    `downloads_count` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_item_id`) REFERENCES `order_items`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- Portafolio
CREATE TABLE `success_cases` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `description` TEXT,
    `image_url` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Inserción Inicial
INSERT INTO `company_profile` (`name`, `ruc_nit`, `email`) VALUES ('Láser Personalización', '123456789001', 'contacto@empresa.com');
-- Password es 'admin123' encryptado vía password_hash() BCRYPT
INSERT INTO `users` (`username`, `password_hash`, `role`, `email`) VALUES ('admin', '$2y$10$wJ.0WqBmsO64gYF1A0M96OFmP7wFIt/X/L0wU4RQQrXqOTdOEXgQ2', 'admin', 'admin@empresa.com');
