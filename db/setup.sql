INSERT INTO `roles` (`id`,`nombre`,`descripcion`) VALUES 
(1,'estudiante','Estudiante'),
(2,'mentor_e','Mentor experto'),
(3,'mentor_ov','Mentor de orientación vocacional'),
(4,'administrador','Administrador de la aplicación');

INSERT INTO `permisos` (`id`, `identificador`, `nombre`, `descripcion`, `permiso_routes`) VALUES 
(1, 'acceso_basico', 'Acceso básico', 'Acceso básico a la aplicacion', 'homepage'),
(2, 'acceso_mensajes', 'Eviar y recibir mensajes', 'Acceso al envio y recepcion de mensajes', 'mensajes,enviar_mensajes,get_mensajes,show_mensaje,count_mensajes,update_mensaje,responder_mensaje,reenviar_mensaje'),
(3, 'acceso_perfiles', 'Acceso a perfiles', 'Acceso a perfiles, sincronizacion y edicion', 'perfil,perfil_edit,perfil_sincronizar,mentor_resenas,calificar_mentor'),
(4, 'acceso_agenda_mentor', 'Acceso a la agenda de mentor', 'Acceso a la agenda de mentorias para mentores', 'agenda_mentor,show_mentoria_mentor,delete_mentoria_mentor'),
(5, 'acceso_agenda_estudiante', 'Acceso a la agenda de estudiante', 'Acceso a la agenda de mentorias para estudiantes', 'agenda_estudiante,show_mentoria_estudiante,separar_mentoria_estudiante'),
(6, 'acceso_foros', 'Acceso a foros', 'Acceso a foros, y comentar', 'foros_temas,crear_comentario,crear_foro,edit_foro,delete_foro'),
(7, 'acceso_edit_delete_foro', 'Acceso a eliminar y editar foro','Acceso de edicion y eliminacion de foros', 'edit_foro,delete_foro'),
(8, 'acceso_contactos', 'Acceso a contactos','Acceso a contactos y a establecer relaciones entre ellos', 'contactos,busqueda,edit_estado_relacion'),
(9, 'acceso_test_vocacional', 'Acceso a Test Vocacional','Acceso a test Vocacional', 'test_vocacional'),
(10, 'admin_preguntas', 'Administración de preguntas', 'Administrar preguntas de los cuestionario', 'admin_formularios,delete_pregunta,edit_pregunta'),
(11, 'acceso_diagnostico', 'Prueba de diagnostico', 'Prueba de diagnostico', 'diagnostico,procesar_diagnostico'),
(12, 'acceso_evaluacion_360', 'Evaluación 360', 'Evaluación 360', 'evaluacion360,evaluacion360_evaluacion,procesar_evaluacion360'),
(13, 'acceso_mentor_ov', 'Acceso de administracion por parte del mentor OV', 'Acceso de administracion por parte del mentor de Orientación Vocacional', 'lista_usuarios_mentor,subir_informe_mercado_laboral'),
(14, 'acceso_respuestas_evaluacion_360', 'Acceso a ver resultados de evaluacion 360', 'Acceso a ver resultados de evaluacion 360', 'evaluacion360_resultados'),
(15, 'acceso_carreras', 'Acceso a admin carreras', 'Acceso a admin carreras', 'admin_carreras,admin_carreras_show,admin_carreras_new,admin_carreras_edit,admin_carreras_delete'),
(16, 'acceso_colegios', 'Acceso a admin colegios', 'Acceso a admin colegios', 'admin_colegios,admin_colegios_show,admin_colegios_new,admin_colegios_edit,admin_colegios_delete'),
(17, 'acceso_temas', 'Acceso a admin temas', 'Acceso a admin temas', 'admin_temas,admin_temas_show,admin_temas_new,admin_temas_edit,admin_temas_delete'),
(18, 'acceso_sidebar','Acceso a admin publicidad/informacion', 'Acceso a admin publicidad/informacion', 'admin_informacion,admin_informacion_show,admin_informacion_new,admin_informacion_edit,admin_informacion_delete'),
(19, 'acceso_diseno_vida', 'Diseño de vida', 'Diseño de vida', 'diseno_vida,procesar_disenovida'),
(20, 'acceso_respuestas_diseno_vida', 'Acceso a ver resultados de diseño de vida', 'Acceso a ver resultados de diseño de vida', 'disenovida_resultados'),
(21, 'acceso_admin_usuarios', 'Acceso a admin usuarios', 'Acceso a admin usuarios', 'admin_usuarios_e,admin_usuarios_me,admin_usuarios_e,admin_usuarios_mov,admin_usuarios_admin,admin_usuarios_show,edit_estado_rol_mentor'),
(22, 'acceso_ponderacion', 'Ponderación', 'Ponderación', 'ponderacion,procesar_ponderacion'),
(23, 'acceso_respuestas_ponderacion', 'Acceso a ver resultados de ponderación', 'Acceso a ver resultados de ponderación', 'ponderacion_resultados'),
(24, 'acceso_mentor_vocacional', 'Acceso a selección de mentores de orientacion vocacional', 'Acceso a selección de mentores de orientacion vocacional', 'lista_mentores_ov,seleccionar_mentor'),
(25, 'acceso_mentor_experto', 'Acceso a selección de mentores experto', 'Acceso a selección de mentores experto', 'red_mentores,seleccionar_mentor_experto'),
(26, 'acceso_pagos', 'Planes y pagos', 'Acceso a planes y pagos', 'planes,agregar_producto,eliminar_producto,pagos_mentorias,agregar_producto_mentoria,comprar,confirmar_comprar,payu_response,payu_confirmation');


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
('7', '4'),
('8', '1'),
('8', '2'),
('8', '3'),
('8', '4'),
('10', '4'),
('11', '1'),
('11', '4'),
('12', '1'),
('12', '2'),
('12', '3'),
('12', '4'),
('13', '3'),
('14', '2'),
('14', '3'),
('14', '4'),
('15', '4'),
('16', '4'),
('17', '4'),
('18', '4'),
('19', '1'),
('19', '2'),
('19', '3'),
('19', '4'),
('20', '2'),
('20', '3'),
('20', '4'),
('21', '4'),
('22', '1'),
('22', '2'),
('22', '3'),
('22', '4'),
('23', '2'),
('23', '3'),
('23', '4'),
('24', '1'), ('24', '2'), ('24', '3'), ('24', '4'),
('25', '1'), ('25', '2'), ('25', '3'), ('25', '4'),
('26', '1'),
('26', '4');

INSERT INTO `preguntas_tipos` (`id`, `nombre`) VALUES 
(1, 'selección múltiple con única respuesta'),
(2, 'selección múltiple con múltiple respuesta'),
(3, 'ordenación'),
(4, 'si o no'),
(5, 'numérica'),
(6, 'porcentual'),
(7, 'slider'),
(8, 'abierta');

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `valor`) VALUES ('1', 'Programa de orientación', 'Programa completo para la toma de una decisión consciente', '100000');
INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `valor`) VALUES ('2', 'Informe de mercado laboral', 'Acceso individual al informe de mercado laboral', '20000');
INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `valor`) VALUES ('3', 'Mentoría profesional', 'Una mentoría con mentor profesional', '0');
INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `valor`) VALUES ('4', 'Mentoría de orientación vocacional', 'una mentoría con experto en orientación vocacional', '30000');
