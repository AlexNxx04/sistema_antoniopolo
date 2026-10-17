-- Tabla: multas (Sanciones)
-- Ejecuta este SQL en tu base de datos MySQL

CREATE TABLE IF NOT EXISTS `multas` (
    `multa_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` BIGINT UNSIGNED NOT NULL,
    `turno_id` BIGINT UNSIGNED NULL,
    `salida_id` BIGINT UNSIGNED NULL,
    `llegada_id` BIGINT UNSIGNED NULL,
    `tipo` ENUM('retraso_llegada', 'ausencia', 'documento_vencido', 'indisciplina') NOT NULL,
    `monto` DECIMAL(10,2) NOT NULL,
    `minutos_retraso` INT NULL,
    `descripcion` TEXT NULL,
    `estado` ENUM('pendiente', 'cobrada', 'anulada') NOT NULL DEFAULT 'pendiente',
    `registrado_por` BIGINT UNSIGNED NULL,
    `registrado_en` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `cobrado_por` BIGINT UNSIGNED NULL,
    `cobrado_en` TIMESTAMP NULL,
    
    INDEX `multas_usuario_estado_idx` (`usuario_id`, `estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
