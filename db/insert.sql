INSERT INTO `georeferencias` (`id`, `georeferencia_codigo`, `georeferencia_nombre`, `georeferencia_tipo`)
VALUES ('1', 'COD1', 'Codigo 1', 'tipo1'),
('2', 'COD2', 'Codigo 2', 'tipo2'),
('3', 'COD3', 'Codigo 3', 'tipo3'),
('4', 'COD4', 'Codigo 4', 'tip4o'),
('5', 'COD5', 'Codigo 5', 'tipo5'),
('6', 'COD6', 'Codigo 6', 'tipo6');

INSERT INTO `colegios` (`id`, `nombre`, `georeferencia_id`) VALUES ('1', 'colegio name 1', '1');
INSERT INTO `colegios` (`id`, `nombre`, `georeferencia_id`) VALUES ('2', 'colegio name 2', '2');
INSERT INTO `colegios` (`id`, `nombre`, `georeferencia_id`) VALUES ('3', 'colegio name 3', '3');

INSERT INTO `usuarios` (`id`, `usuario_nombre`, `usuario_apellido`, `usuario_email`, `usuario_estado`, `rol_id`, `colegio_id`) VALUES ('1', 'Usuario Nombre 1', 'apellido 1', 'mail@test1.com', '1', '1', '1');
INSERT INTO `usuarios` (`id`, `usuario_nombre`, `usuario_apellido`, `usuario_email`, `usuario_estado`, `rol_id`) VALUES ('2', 'Usuario Nombre 2', 'apellido 2', 'mail@test2.com', '1', '2');
INSERT INTO `usuarios` (`id`, `usuario_nombre`, `usuario_apellido`, `usuario_email`, `usuario_estado`, `rol_id`) VALUES ('3', 'Usuario Nombre 3', 'apellido 3', 'mail@test3.com', '1', '2');
INSERT INTO `usuarios` (`id`, `usuario_nombre`, `usuario_apellido`, `usuario_email`, `usuario_estado`, `rol_id`) VALUES ('4', 'Usuario Nombre 4', 'apellido 4', 'mail@test4.com', '0', '1');
INSERT INTO `usuarios` (`id`, `usuario_nombre`, `usuario_apellido`, `usuario_email`, `usuario_estado`, `rol_id`) VALUES ('5', 'Usuario Nombre 5', 'apellido 5', 'mail@test5.com', '0', '3');

INSERT INTO `empresas` (`nombre`) VALUES ('empresa 1');
INSERT INTO `empresas` (`nombre`) VALUES ('empresa 2');
INSERT INTO `empresas` (`nombre`) VALUES ('empresa 3');
INSERT INTO `empresas` (`nombre`) VALUES ('empresa 4');

INSERT INTO `estudios` (`usuario_id`, `nombre_institucion`, `campo`, `titulo`, `actividad`, `notas`) VALUES ('2', 'institucion 1', 'campo 1', 'titulo 1', 'actividad 1', 'notas 1');
INSERT INTO `estudios` (`usuario_id`, `nombre_institucion`, `campo`, `titulo`, `actividad`, `notas`) VALUES ('2', 'institucion 2', 'campo 2', 'titulo 2', 'actividad 2', 'notas 2');
INSERT INTO `estudios` (`usuario_id`, `nombre_institucion`, `campo`, `titulo`, `actividad`, `notas`) VALUES ('2', 'institucion 3', 'campo 3', 'titulo 3', 'actividad 3', 'notas 3');
INSERT INTO `estudios` (`usuario_id`, `nombre_institucion`, `campo`, `titulo`, `actividad`, `notas`) VALUES ('2', 'institucion 4', 'campo 4', 'titulo 4', 'actividad 4', 'notas 4');
INSERT INTO `estudios` (`usuario_id`, `nombre_institucion`, `campo`, `titulo`, `actividad`, `notas`) VALUES ('2', 'institucion 5', 'campo 5', 'titulo 5', 'actividad 5', 'notas 5');

INSERT INTO `trabajos` (`cargo`, `resumen`, `es_actual`, `empresa_id`, `usuario_id`) VALUES ('cargo 1', 'test resumen 1', '0', '1', '2');
INSERT INTO `trabajos` (`cargo`, `resumen`, `es_actual`, `empresa_id`, `usuario_id`) VALUES ('cargo 2', 'resumen 2', '1', '2', '2');
