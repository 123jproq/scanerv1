-- Tabla para registrar movimientos de inventario
-- Ejecutar en phpMyAdmin o MySQL

CREATE TABLE IF NOT EXISTS `stock_movements` (
  `movement_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `barcode` varchar(255) DEFAULT NULL,
  `movement_type` ENUM('entrada', 'salida') NOT NULL,
  `quantity` int(11) NOT NULL,
  `stock_before` int(11) NOT NULL,
  `stock_after` int(11) NOT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`movement_id`),
  INDEX `idx_product` (`product_id`),
  INDEX `idx_date` (`created_at`),
  INDEX `idx_type` (`movement_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
