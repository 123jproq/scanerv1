-- Tabla de Áreas/Unidades
CREATE TABLE IF NOT EXISTS `areas` (
  `area_id` int(11) NOT NULL AUTO_INCREMENT,
  `area_name` varchar(100) NOT NULL,
  `area_active` tinyint(1) NOT NULL DEFAULT 1,
  `area_status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`area_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Áreas iniciales basadas en la imagen
INSERT INTO `areas` (`area_name`, `area_active`, `area_status`) VALUES
('Administración/Gerencia', 1, 1),
('Recepción/secretaría/telefonista', 1, 1),
('Aseo', 1, 1),
('Consulta médica', 1, 1),
('Consulta no médica', 1, 1),
('Unidad de apoyo laboratorio', 1, 1),
('Unidad de apoyo imagenología', 1, 1),
('Unidad de apoyo esterilización', 1, 1),
('Unidad de apoyo endoscopía/colonoscopía/cistoscopía', 1, 1),
('Unidad de apoyo kinesiología', 1, 1),
('Unidad de apoyo dental (odontología)', 1, 1),
('Unidad de apoyo pabellón', 1, 1),
('Unidad de apoyo enfermería', 1, 1);
