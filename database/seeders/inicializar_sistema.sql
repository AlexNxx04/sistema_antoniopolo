-- ============================================
-- Script para inicializar datos del sistema
-- Sistema de Gestión Operativa Antonio Polo
-- ============================================

USE sistema_antoniopolo_sa;

-- 1. Actualizar created_at y updated_at de usuarios existentes
UPDATE usuarios SET created_at = NOW(), updated_at = NOW() WHERE created_at IS NULL;

-- 2. Verificar que existan los roles (si no existen, insertarlos)
INSERT IGNORE INTO roles (rol_id, nombre, descripcion, activo, created_at) VALUES
(1, 'Gerente', 'Administrador del sistema', 1, NOW()),
(2, 'Tarjetera', 'Supervisora operativa', 1, NOW()),
(3, 'Conductor', 'Operador de unidades', 1, NOW());

-- 3. Verificar que existan los puntos (si no existen, insertarlos)
INSERT IGNORE INTO puntos (punto_id, nombre, latitud, longitud, radio_geofence_m, activo, created_at, updated_at) VALUES
(1, 'Guaranda', -1.60330000, -79.00180000, 50, 1, NOW(), NOW()),
(2, 'Salinas', -1.40000000, -79.01670000, 50, 1, NOW(), NOW());

-- 4. Crear ciclo activo si no existe
INSERT IGNORE INTO ciclos_anuales (anio, fecha_inicio, fecha_fin, activo, creado_por, created_at, updated_at) 
SELECT 2026, '2026-01-01', '2026-12-31', 1, 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM ciclos_anuales WHERE activo = 1);

-- 5. Crear parámetros operativos si no existen
INSERT IGNORE INTO parametros_operativos (clave, valor, tipo_dato, descripcion, updated_by, created_at, updated_at) VALUES
('multa_por_minuto_retraso', '1.00', 'decimal', 'Valor por minuto de retraso', 1, NOW(), NOW()),
('tiempo_estimado_ruta', '120', 'int', 'Tiempo estimado de ruta en minutos', 1, NOW(), NOW()),
('hora_inicio_operacion', '05:30', 'string', 'Hora inicio operacion', 1, NOW(), NOW()),
('hora_fin_operacion', '19:30', 'string', 'Hora fin operacion', 1, NOW(), NOW()),
('telefono_empresa', '0991234567', 'string', 'Telefono empresa', 1, NOW(), NOW()),
('whatsapp_empresa', '0991234567', 'string', 'WhatsApp empresa', 1, NOW(), NOW());

-- 6. Crear algunas unidades de ejemplo si no existen
INSERT IGNORE INTO unidades (placa, numero_interno, celular_gps, punto_id, estado, created_at, updated_at) VALUES
('ABC-001', 1, '0991111001', 1, 'activo', NOW(), NOW()),
('ABC-002', 2, '0991111002', 1, 'activo', NOW(), NOW()),
('ABC-003', 3, '0991111003', 1, 'activo', NOW(), NOW()),
('ABC-004', 4, '0991111004', 2, 'activo', NOW(), NOW()),
('ABC-005', 5, '0991111005', 2, 'activo', NOW(), NOW()),
('ABC-006', 6, '0991111006', 1, 'mantenimiento', NOW(), NOW());

-- 7. Crear algunos conductores de ejemplo si no existen
-- Nota: La contraseña es 'password123' (hash bcrypt)
INSERT IGNORE INTO usuarios (cedula, nombres, apellidos, telefono, correo, username, password, rol_id, punto_id, unidad_id, activo, created_at, updated_at)
SELECT '0990000001', 'Juan', 'Pérez', '0990000001', 'juan.perez@antoniopolo.com', 'jperez001', '$2y$12$LQv3c1yqBWVHxkd0PJA3QOJksMqJ5WhdLzf0cP5ZN5B9g5B5P5z6', 3, 1, 1, 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE cedula = '0990000001');

INSERT IGNORE INTO usuarios (cedula, nombres, apellidos, telefono, correo, username, password, rol_id, punto_id, unidad_id, activo, created_at, updated_at)
SELECT '0990000002', 'María', 'García', '0990000002', 'maria.garcia@antoniopolo.com', 'mgarcia002', '$2y$12$LQv3c1yqBWVHxkd0PJA3QOJksMqJ5WhdLzf0cP5ZN5B9g5B5P5z6', 3, 1, 2, 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE cedula = '0990000002');

INSERT IGNORE INTO usuarios (cedula, nombres, apellidos, telefono, correo, username, password, rol_id, punto_id, unidad_id, activo, created_at, updated_at)
SELECT '0990000003', 'Carlos', 'López', '0990000003', 'carlos.lopez@antoniopolo.com', 'clopez003', '$2y$12$LQv3c1yqBWVHxkd0PJA3QOJksMqJ5WhdLzf0cP5ZN5B9g5B5P5z6', 3, 1, 3, 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE cedula = '0990000003');

INSERT IGNORE INTO usuarios (cedula, nombres, apellidos, telefono, correo, username, password, rol_id, punto_id, unidad_id, activo, created_at, updated_at)
SELECT '0990000004', 'Ana', 'Rodríguez', '0990000004', 'ana.rodriguez@antoniopolo.com', 'arodriguez004', '$2y$12$LQv3c1yqBWVHxkd0PJA3QOJksMqJ5WhdLzf0cP5ZN5B9g5B5P5z6', 3, 2, 4, 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE cedula = '0990000004');

INSERT IGNORE INTO usuarios (cedula, nombres, apellidos, telefono, correo, username, password, rol_id, punto_id, unidad_id, activo, created_at, updated_at)
SELECT '0990000005', 'Pedro', 'Martínez', '0990000005', 'pedro.martinez@antoniopolo.com', 'pmartinez005', '$2y$12$LQv3c1yqBWVHxkd0PJA3QOJksMqJ5WhdLzf0cP5ZN5B9g5B5P5z6', 3, 2, 5, 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE cedula = '0990000005');

-- 8. Crear tarjetera de ejemplo si no existe
INSERT IGNORE INTO usuarios (cedula, nombres, apellidos, telefono, correo, username, password, rol_id, punto_id, unidad_id, activo, created_at, updated_at)
SELECT '0990000006', 'Laura', 'Fernández', '0990000006', 'laura.fernandez@antoniopolo.com', 'lfernandez006', '$2y$12$LQv3c1yqBWVHxkd0PJA3QOJksMqJ5WhdLzf0cP5ZN5B9g5B5P5z6', 2, 1, NULL, 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE cedula = '0990000006');

-- 9. Crear documentos de ejemplo para los conductores
INSERT IGNORE INTO documentos (usuario_id, tipo, numero, fecha_emision, fecha_vencimiento, estado, updated_by, created_at, updated_at)
SELECT u.usuario_id, 'licencia', CONCAT('LIC-', u.cedula), DATE_SUB(NOW(), INTERVAL 1 YEAR), DATE_ADD(NOW(), INTERVAL 1 YEAR), 'vigente', 1, NOW(), NOW()
FROM usuarios u WHERE u.rol_id = 3 AND u.activo = 1
AND NOT EXISTS (
    SELECT 1 FROM documentos d WHERE d.usuario_id = u.usuario_id AND d.tipo = 'licencia'
);

INSERT IGNORE INTO documentos (usuario_id, tipo, numero, fecha_emision, fecha_vencimiento, estado, updated_by, created_at, updated_at)
SELECT u.usuario_id, 'matricula', CONCAT('MAT-', u.cedula), DATE_SUB(NOW(), INTERVAL 1 YEAR), DATE_ADD(NOW(), INTERVAL 1 YEAR), 'vigente', 1, NOW(), NOW()
FROM usuarios u WHERE u.rol_id = 3 AND u.activo = 1 AND u.unidad_id IS NOT NULL
AND NOT EXISTS (
    SELECT 1 FROM documentos d WHERE d.usuario_id = u.usuario_id AND d.tipo = 'matricula'
);

-- 10. Crear documentos por vencer (30 días)
INSERT IGNORE INTO documentos (usuario_id, tipo, numero, fecha_emision, fecha_vencimiento, estado, updated_by, created_at, updated_at)
SELECT u.usuario_id, 'licencia', CONCAT('LIC-V-', u.cedula), DATE_SUB(NOW(), INTERVAL 1 YEAR), DATE_ADD(NOW(), INTERVAL 25 DAY), 'vigente', 1, NOW(), NOW()
FROM usuarios u WHERE u.rol_id = 3 AND u.activo = 1
AND NOT EXISTS (
    SELECT 1 FROM documentos d WHERE d.usuario_id = u.usuario_id AND d.tipo = 'licencia' AND d.numero LIKE '%V-%'
);

-- ============================================
-- Verificación de datos insertados
-- ============================================
SELECT '=== ROLES ===' AS '';
SELECT * FROM roles;

SELECT '=== PUNTOS ===' AS '';
SELECT * FROM puntos;

SELECT '=== CICLO ACTIVO ===' AS '';
SELECT * FROM ciclos_anuales WHERE activo = 1;

SELECT '=== PARÁMETROS OPERATIVOS ===' AS '';
SELECT * FROM parametros_operativos;

SELECT '=== UNIDADES ===' AS '';
SELECT * FROM unidades;

SELECT '=== USUARIOS ===' AS '';
SELECT usuario_id, cedula, nombres, apellidos, rol_id, activo, created_at FROM usuarios;

SELECT '=== DOCUMENTOS ===' AS '';
SELECT d.*, u.nombres, u.apellidos FROM documentos d JOIN usuarios u ON d.usuario_id = u.usuario_id;

-- ============================================
-- RESUMEN
-- ============================================
SELECT '=== RESUMEN ===' AS '';
SELECT 
    (SELECT COUNT(*) FROM roles WHERE activo = 1) AS roles_activos,
    (SELECT COUNT(*) FROM puntos WHERE activo = 1) AS puntos_activos,
    (SELECT COUNT(*) FROM ciclos_anuales WHERE activo = 1) AS ciclos_activos,
    (SELECT COUNT(*) FROM unidades WHERE estado = 'activo') AS unidades_activas,
    (SELECT COUNT(*) FROM usuarios WHERE activo = 1) AS usuarios_activos,
    (SELECT COUNT(*) FROM documentos) AS documentos_registrados;
