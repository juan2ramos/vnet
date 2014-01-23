INSERT INTO `roles` (`id`,`nombre`,`descripcion`) VALUES 
(1,'estudiante','Estudiante'),
(2,'mentor_e','Mentor experto'),
(3,'mentor_ov','Mentor de orientación vocacional'),
(4,'administrador','Administrador de la aplicación');

INSERT INTO `permisos` (`id`,`identificador`,`nombre`,`descripcion`,`permiso_routes`) VALUES (1,'acceso_basico','Acceso básico','Acceso básico a la aplicacion','homepage');
INSERT INTO `permisos` (`id`,`identificador`,`nombre`,`descripcion`,`permiso_routes`) VALUES (2,'acceso_mensajes','Eviar y recibir mensajes','Acceso al envio y recepcion de mensajes','mensajes,enviar_mensajes,get_mensajes,show_mensaje,count_mensajes,update_mensaje,responder_mensaje,reenviar_mensaje');
INSERT INTO `permisos` (`id`,`identificador`,`nombre`,`descripcion`,`permiso_routes`) VALUES (3,'acceso_perfiles','Acceso básico','Acceso a perfiles','perfil,perfil_edit,perfil_sincronizar,mentor_resenas,calificar_mentor,horarios_disponibles_mentor');
INSERT INTO `permisos` (`id`,`identificador`,`nombre`,`descripcion`,`permiso_routes`) VALUES (4,'acceso_foros','Acceso a foros, y comentar','Acceso a perfiles','foros_temas,crear_comentario,crear_foro,edit_foro,delete_foro');
INSERT INTO `permisos` (`id`,`identificador`,`nombre`,`descripcion`,`permiso_routes`) VALUES (5,'acceso_edit_delete_foro','Acceso a eliminar y editar foro','Acceso de edicion y eliminacion de foros','edit_foro,delete_foro');
INSERT INTO `permisos` (`id`,`identificador`,`nombre`,`descripcion`,`permiso_routes`) VALUES (6,'acceso_agenda_mentor','Acceso a la agenda de mentor','Acceso a la agenda de mentorias para mentores','agenda_mentor,show_mentoria_mentor,delete_mentoria_mentor,finalizar_mentoria_mentor');
INSERT INTO `permisos` (`id`,`identificador`,`nombre`,`descripcion`,`permiso_routes`) VALUES (7,'acceso_agenda_estudiante','Acceso a la agenda de estudiante','Acceso a la agenda de mentorias para estudiantes','agenda_estudiante,show_mentoria_estudiante,separar_mentoria_estudiante');
INSERT INTO `permisos` (`id`,`identificador`,`nombre`,`descripcion`,`permiso_routes`) VALUES (8,'acceso_contactos','Acceso a contactos','Acceso a contactos y a establecer relaciones entre ellos','contactos,busqueda,edit_estado_relacion');
INSERT INTO `permisos` (`id`,`identificador`,`nombre`,`descripcion`,`permiso_routes`) VALUES (9,'admin_preguntas','Administración de preguntas','Administrar preguntas de los cuestionario','admin_formularios,delete_pregunta,edit_pregunta');
INSERT INTO `permisos` (`id`,`identificador`,`nombre`,`descripcion`,`permiso_routes`) VALUES (11,'acceso_diagnostico','Prueba de diagnostico','Prueba de diagnostico','diagnostico,procesar_diagnostico');
INSERT INTO `permisos` (`id`,`identificador`,`nombre`,`descripcion`,`permiso_routes`) VALUES (12,'acceso_evaluacion_360','Evaluación 360','Evaluación 360','evaluacion360,evaluacion360_evaluacion,procesar_evaluacion360');
INSERT INTO `permisos` (`id`,`identificador`,`nombre`,`descripcion`,`permiso_routes`) VALUES (13,'acceso_mentor_ov','Acceso de administracion por parte del mentor OV','Acceso de administracion por parte del mentor de Orientación Vocacional','lista_usuarios_mentor,subir_informe_mercado_laboral');
INSERT INTO `permisos` (`id`,`identificador`,`nombre`,`descripcion`,`permiso_routes`) VALUES (14,'acceso_respuestas_evaluacion_360','Acceso a ver resultados de evaluacion 360','Acceso a ver resultados de evaluacion 360','evaluacion360_resultados');
INSERT INTO `permisos` (`id`,`identificador`,`nombre`,`descripcion`,`permiso_routes`) VALUES (15,'acceso_carreras','Acceso a admin carreras','Acceso a admin carreras','admin_carreras,admin_carreras_show,admin_carreras_new,admin_carreras_edit,admin_carreras_delete');
INSERT INTO `permisos` (`id`,`identificador`,`nombre`,`descripcion`,`permiso_routes`) VALUES (16,'acceso_colegios','Acceso a admin colegios','Acceso a admin colegios','admin_colegios,admin_colegios_show,admin_colegios_new,admin_colegios_edit,admin_colegios_delete');
INSERT INTO `permisos` (`id`,`identificador`,`nombre`,`descripcion`,`permiso_routes`) VALUES (17,'acceso_temas','Acceso a admin temas','Acceso a admin temas','admin_temas,admin_temas_show,admin_temas_new,admin_temas_edit,admin_temas_delete');
INSERT INTO `permisos` (`id`,`identificador`,`nombre`,`descripcion`,`permiso_routes`) VALUES (18,'acceso_sidebar','Acceso a admin publicidad/informacion','Acceso a admin publicidad/informacion','admin_informacion,admin_informacion_show,admin_informacion_new,admin_informacion_edit,admin_informacion_delete');
INSERT INTO `permisos` (`id`,`identificador`,`nombre`,`descripcion`,`permiso_routes`) VALUES (19,'acceso_diseno_vida','Diseño de vida','Diseño de vida','diseno_vida,procesar_disenovida');
INSERT INTO `permisos` (`id`,`identificador`,`nombre`,`descripcion`,`permiso_routes`) VALUES (20,'acceso_respuestas_diseno_vida','Acceso a ver resultados de diseño de vida','Acceso a ver resultados de diseño de vida','disenovida_resultados');
INSERT INTO `permisos` (`id`,`identificador`,`nombre`,`descripcion`,`permiso_routes`) VALUES (21,'acceso_admin_usuarios','Acceso a admin usuarios','Acceso a admin usuarios','admin_usuarios_e,admin_usuarios_me,admin_usuarios_e,admin_usuarios_mov,admin_usuarios_admin,admin_usuarios_show,edit_estado_rol_mentor');
INSERT INTO `permisos` (`id`,`identificador`,`nombre`,`descripcion`,`permiso_routes`) VALUES (22,'acceso_ponderacion','Ponderación','Ponderación','ponderacion,procesar_ponderacion');
INSERT INTO `permisos` (`id`,`identificador`,`nombre`,`descripcion`,`permiso_routes`) VALUES (23,'acceso_respuestas_ponderacion','Acceso a ver resultados de ponderación','Acceso a ver resultados de ponderación','ponderacion_resultados');
INSERT INTO `permisos` (`id`,`identificador`,`nombre`,`descripcion`,`permiso_routes`) VALUES (24,'acceso_mentor_vocacional','Acceso a selección de mentores de orientacion vocacional','Acceso a selección de mentores de orientacion vocacional','lista_mentores_ov,seleccionar_mentor');
INSERT INTO `permisos` (`id`,`identificador`,`nombre`,`descripcion`,`permiso_routes`) VALUES (25,'acceso_mentor_experto','Acceso a selección de mentores experto','Acceso a selección de mentores experto','red_mentores,seleccionar_mentor_experto');
INSERT INTO `permisos` (`id`,`identificador`,`nombre`,`descripcion`,`permiso_routes`) VALUES (26,'acceso_pagos','Planes y pagos','Acceso a planes y pagos','planes,agregar_producto,eliminar_producto,pagos_mentorias,agregar_producto_mentoria,comprar,confirmar_comprar,payu_response,payu_confirmation');


INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (1,1,1);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (2,1,2);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (3,1,3);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (4,1,4);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (5,3,1);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (6,3,2);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (7,3,3);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (8,3,4);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (9,4,1);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (10,4,2);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (11,4,3);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (12,4,4);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (13,2,1);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (14,2,2);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (15,2,3);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (16,2,4);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (31,5,4);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (32,6,2);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (33,6,3);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (34,6,4);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (35,7,1);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (36,7,4);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (37,8,1);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (38,8,2);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (39,8,3);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (40,8,4);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (41,8,1);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (42,8,2);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (43,8,3);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (44,8,4);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (45,9,4);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (46,11,1);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (47,11,4);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (48,12,1);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (49,12,2);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (50,12,3);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (51,12,4);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (52,13,3);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (53,14,2);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (54,14,3);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (55,14,4);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (56,15,4);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (57,16,4);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (58,17,4);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (59,18,4);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (60,19,1);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (61,19,2);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (62,19,3);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (63,19,4);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (64,20,2);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (65,20,3);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (66,20,4);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (67,21,4);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (68,22,1);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (69,22,2);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (70,22,3);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (71,22,4);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (72,23,2);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (73,23,3);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (74,23,4);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (75,24,1);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (76,25,1);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (77,26,1);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (78,26,4);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (79,24,2);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (80,24,3);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (81,24,4);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (82,25,2);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (83,25,3);
INSERT INTO `permisos_roles` (`id`,`permiso_id`,`rol_id`) VALUES (84,25,4);

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
