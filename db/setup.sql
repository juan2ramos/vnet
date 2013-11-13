INSERT INTO `roles` (`id`,`nombre`,`descripcion`) VALUES 
(1,'estudiante','Estudiante'),
(2,'mentor_e','Mentor experto'),
(3,'mentor_ov','Mentor de orientación vocacional'),
(4,'administrador','Administrador de la aplicación');

INSERT INTO `permisos` (`id`, `identificador`, `nombre`, `descripcion`, `permiso_routes`) VALUES 
(1, 'acceso_basico', 'Acceso básico', 'Acceso básico a la aplicacion', 'homepage'),
(2, 'acceso_mensajes', 'Eviar y recibir mensajes', 'Acceso al envio y recepcion de mensajes', 'mensajes,enviar_mensajes,get_mensajes,show_mensaje,count_mensajes,update_mensaje,responder_mensaje,reenviar_mensaje'),
(3, 'acceso_perfiles', 'Acceso a perfiles', 'Acceso a perfiles, sincronizacion y edicion', 'perfil,perfil_edit,perfil_sincronizar'),
(4, 'acceso_agenda_mentor', 'Acceso a la agenda de mentor', 'Acceso a la agenda de mentorias para mentores', 'agenda_mentor,show_mentoria_mentor,delete_mentoria_mentor'),
(5, 'acceso_agenda_estudiante', 'Acceso a la agenda de estudiante', 'Acceso a la agenda de mentorias para estudiantes', 'agenda_estudiante,show_mentoria_estudiante,separar_mentoria_estudiante'),
(6, 'acceso_foros', 'Acceso a foros', 'Acceso a foros, y comentar', 'foros_temas,crear_comentario,crear_foro,edit_foro,delete_foro'),
(7, 'acceso_edit_delete_foro', 'Acceso a eliminar y editar foro','Acceso de edicion y eliminacion de foros', 'edit_foro,delete_foro');


INSERT INTO `permisos_roles` (`permiso_id`, `rol_id`) VALUES 
('1', '1'),
('1', '2'),
('1', '3'),
('1', '4'),
('2', '1'),
('2', '2'),
('2', '3'),
('2', '4'),
('3', '1'),
('3', '2'),
('3', '3'),
('3', '4'),
('4', '2'),
('4', '3'),
('4', '4'),
('5', '1'),
('5', '4'),
('6', '1'),
('6', '2'),
('6', '3'),
('6', '4'),
('7', '4');
