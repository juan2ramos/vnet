-- RUTAS DE CERTIFICADO, Y RELACION CON PERFILES (ESTUDIANTE - SUPERADMIN)
INSERT INTO `permisos` (`id`,`identificador`,`nombre`,`descripcion`,`permiso_routes`)
VALUES (28,'acceso_certificado','Acceso a certificado','Acceso a certificado','certificado,download_certificado');
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (null,28,1), (null,28,5);
