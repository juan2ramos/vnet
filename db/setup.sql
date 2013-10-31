INSERT INTO `roles` (`id`,`nombre`,`descripcion`) VALUES 
(1,'estudiante','Estudiante'),
(2,'mentor_e','Mentor experto'),
(3,'mentor_ov','Mentor de orientaci�n voacional'),
(4,'administrador','Administrador de la aplicaci�n');

INSERT INTO `permisos` (`id`, `identificador`, `nombre`, `descripcion`, `permiso_routes`) VALUES 
(1, 'acceso_basico', 'Acceso b�sico', 'Acceso b�sico a la aplicacion', 'homepage'),
(2, 'acceso_mensajes', 'Eviar y recibir mensajes', 'Acceso al envio y recepcion de mensajes', 'mensajes');

INSERT INTO `permisos_roles` (`permisos_id`, `roles_id`) VALUES ('1', '1');

INSERT INTO `vocationet-dev`.`permisos_roles` (`permisos_id`, `roles_id`) VALUES 
('1', '1'),
('1', '2'),
('1', '3'),
('1', '4');
