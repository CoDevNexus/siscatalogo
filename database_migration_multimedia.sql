-- Añadir nuevas columnas a products para personalización del cliente
ALTER TABLE `products`
    ADD COLUMN IF NOT EXISTS `price_combo`        DECIMAL(10,2)   DEFAULT NULL AFTER `price_dozen`,
    ADD COLUMN IF NOT EXISTS `allow_client_note`  TINYINT(1)      DEFAULT 0    AFTER `price_combo`,
    ADD COLUMN IF NOT EXISTS `allow_client_logo`  TINYINT(1)      DEFAULT 0    AFTER `allow_client_note`,
    ADD COLUMN IF NOT EXISTS `digital_file_path`  VARCHAR(255)    DEFAULT NULL AFTER `allow_client_logo`;

-- Tabla de imágenes múltiples por producto
CREATE TABLE IF NOT EXISTS `product_images` (
    `id`          INT AUTO_INCREMENT PRIMARY KEY,
    `product_id`  INT NOT NULL,
    `source`      ENUM('local','url','api') NOT NULL DEFAULT 'local',
    `image_path`  VARCHAR(500) NOT NULL,
    `is_primary`  TINYINT(1) NOT NULL DEFAULT 0,
    `sort_order`  INT NOT NULL DEFAULT 0,
    `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
);

-- Índice para búsquedas rápidas
CREATE INDEX IF NOT EXISTS idx_product_images_product ON product_images(product_id);
