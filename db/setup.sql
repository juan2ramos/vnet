INSERT INTO `roles` (`id`,`nombre`,`descripcion`) VALUES 
(1,'estudiante','Estudiante'),
(2,'mentor_e','Mentor experto'),
(3,'mentor_ov','Mentor de orientación voacional'),
(4,'administrador','Administrador de la aplicación');

INSERT INTO `permisos` (`id`, `identificador`, `nombre`, `descripcion`, `permiso_routes`) VALUES 
(1, 'acceso_basico', 'Acceso básico', 'Acceso básico a la aplicacion', 'homepage'),
(2, 'acceso_mensajes', 'Eviar y recibir mensajes', 'Acceso al envio y recepcion de mensajes', 'mensajes'),
(3, 'acceso_perfiles', 'Acceso básico', 'Acceso a perfiles', 'perfil,perfil_edit,perfil_sincronizar');

INSERT INTO `permisos_roles` (`permiso_id`, `rol_id`) VALUES 
('1', '1'),
('1', '2'),
('1', '3'),
('1', '4'),
('3', '1'),
('3', '2'),
('3', '3'),
('3', '4');