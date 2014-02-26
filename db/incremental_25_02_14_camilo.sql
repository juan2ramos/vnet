-- RUTAS DE CERTIFICADO, Y RELACION CON PERFILES (ESTUDIANTE - SUPERADMIN) EJECUTADOS EN DEV
INSERT INTO `permisos` (`id`,`identificador`,`nombre`,`descripcion`,`permiso_routes`)
VALUES (28,'acceso_certificado','Acceso a certificado','Acceso a certificado','certificado,download_certificado');
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (null,28,1), (null,28,5);

-- Agregar campo de destino para control de A quien va direccionada (estudiante, mentor)
ALTER TABLE .`informacion` 
CHANGE COLUMN `modified` `modified` DATETIME NULL DEFAULT NULL COMMENT 'Campo para identificar si la información esta direccionada a estudiantes o a mentores' ,
ADD COLUMN `informacion_destino` VARCHAR(45) NULL AFTER `informacion_estado`;


-- PENDIENTE AGREGARLOS EN SCRIPT DB, Y SETUP
