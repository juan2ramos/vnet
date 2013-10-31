INSERT INTO `roles` (`id`,`nombre`,`descripcion`) VALUES 
(1,'estudiante','Estudiante'),
(2,'mentor_e','Mentor experto'),
(3,'mentor_ov','Mentor de orientación voacional'),
(4,'administrador','Administrador de la aplicación');

INSERT INTO `permisos` (`id`, `identificador`, `nombre`, `descripcion`, `permiso_routes`) VALUES 
(1, 'acceso_basico', 'Acceso básico', 'Acceso básico a la aplicacion', 'homepage'),
(2, 'acceso_mensajes', 'Eviar y recibir mensajes', 'Acceso al envio y recepcion de mensajes', 'mensajes');

INSERT INTO `permisos_roles` (`permisos_id`, `roles_id`) VALUES ('1', '1');

INSERT INTO `vocationet-dev`.`permisos_roles` (`permisos_id`, `roles_id`) VALUES 
('1', '1'),
('1', '2'),
('1', '3'),
('1', '4');
