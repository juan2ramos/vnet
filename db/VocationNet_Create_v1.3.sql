SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';


-- -----------------------------------------------------
-- Table `roles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `roles` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(45) NOT NULL,
  `descripcion` MEDIUMTEXT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `georeferencias`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `georeferencias` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `georeferencia_padre_id` INT NULL,
  `georeferencia_nombre` VARCHAR(100) NULL,
  `georeferencia_tipo` VARCHAR(70) NULL,
  `georeferencia_codigo` VARCHAR(45) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `colegios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `colegios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(45) NOT NULL,
  `georeferencia_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_colegios_1_idx` (`georeferencia_id` ASC),
  CONSTRAINT `fk_colegios_1`
    FOREIGN KEY (`georeferencia_id`)
    REFERENCES `georeferencias` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `avatars`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `avatars` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(60) NOT NULL,
  `imagen` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `usuarios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `usuario_nombre` VARCHAR(45) NOT NULL,
  `usuario_apellido` VARCHAR(45) NOT NULL,
  `usuario_email` VARCHAR(100) NOT NULL,
  `usuario_password` VARCHAR(45) NULL,
  `usuario_hash` VARCHAR(45) NULL,
  `usuario_estado` INT(2) NOT NULL,
  `usuario_facebookid` INT NULL,
  `rol_id` INT NOT NULL,
  `usuario_rol_estado` INT(2) NULL DEFAULT 0 COMMENT 'Variable para controlar el estado del rol del usuario 0-No seleccionado, 1-Seleccionado 2-Aprobado',
  `georeferencia_id` INT NULL,
  `usuario_fecha_nacimiento` DATE NULL,
  `usuario_genero` VARCHAR(45) NULL,
  `usuario_imagen` VARCHAR(155) NULL,
  `usuario_tarjeta_profesional` VARCHAR(155) NULL,
  `usuario_hoja_vida` VARCHAR(155) NULL,
  `usuario_profesion` VARCHAR(70) NULL,
  `usuario_puntos` FLOAT NULL DEFAULT 0,
  `usuario_perfil_profesional` TEXT NULL,
  `usuario_valor_mentoria` FLOAT NULL,
  `colegio_id` INT NULL,
  `usuario_curso_actual` INT NULL,
  `usuario_fecha_planeacion` DATE NULL,
  `created` DATETIME NULL,
  `modified` DATETIME NULL,
  `sync_linkedin` DATETIME NULL COMMENT 'Ultima sincronización realizada con linkedin',
  `avatar_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_usuarios_1_idx` (`rol_id` ASC),
  UNIQUE INDEX `usuario_email_UNIQUE` (`usuario_email` ASC),
  INDEX `fk_usuarios_georeferencias1_idx` (`georeferencia_id` ASC),
  INDEX `fk_usuarios_colegios1_idx` (`colegio_id` ASC),
  INDEX `fk_usuarios_avatars1_idx` (`avatar_id` ASC),
  CONSTRAINT `fk_usuarios_1`
    FOREIGN KEY (`rol_id`)
    REFERENCES `roles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_usuarios_georeferencias1`
    FOREIGN KEY (`georeferencia_id`)
    REFERENCES `georeferencias` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_usuarios_colegios1`
    FOREIGN KEY (`colegio_id`)
    REFERENCES `colegios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_usuarios_avatars1`
    FOREIGN KEY (`avatar_id`)
    REFERENCES `avatars` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `empresas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `empresas` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(155) NOT NULL COMMENT '\n	',
  `tipo` VARCHAR(45) NULL,
  `size` VARCHAR(45) NULL,
  `industria` VARCHAR(45) NULL,
  `id_linkedin` INT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `trabajos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `trabajos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `usuario_id` INT NOT NULL,
  `empresa_id` INT NOT NULL,
  `cargo` VARCHAR(150) NOT NULL,
  `resumen` MEDIUMTEXT NULL,
  `fecha_inicio` DATE NULL,
  `fecha_final` DATE NULL,
  `es_actual` INT(2) NOT NULL,
  `id_linkedin` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_trabajos_2_idx` (`empresa_id` ASC),
  INDEX `fk_trabajos_usuarios1_idx` (`usuario_id` ASC),
  CONSTRAINT `fk_trabajos_2`
    FOREIGN KEY (`empresa_id`)
    REFERENCES `empresas` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_trabajos_usuarios1`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `estudios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `estudios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre_institucion` VARCHAR(100) NULL,
  `campo` VARCHAR(155) NULL,
  `fecha_inicio` DATE NULL,
  `fecha_final` DATE NULL,
  `titulo` VARCHAR(155) NULL,
  `actividad` TEXT NULL,
  `notas` MEDIUMTEXT NULL,
  `id_linkedin` INT NULL,
  `usuario_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_estudios_usuarios1_idx` (`usuario_id` ASC),
  CONSTRAINT `fk_estudios_usuarios1`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `permisos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `permisos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `identificador` VARCHAR(45) NOT NULL,
  `nombre` VARCHAR(60) NOT NULL,
  `descripcion` MEDIUMTEXT NULL,
  `permiso_routes` TEXT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `permisos_roles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `permisos_roles` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `permiso_id` INT NOT NULL,
  `rol_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_permisos_has_roles_roles1_idx` (`rol_id` ASC),
  INDEX `fk_permisos_has_roles_permisos1_idx` (`permiso_id` ASC),
  CONSTRAINT `fk_permisos_has_roles_permisos1`
    FOREIGN KEY (`permiso_id`)
    REFERENCES `permisos` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_permisos_has_roles_roles1`
    FOREIGN KEY (`rol_id`)
    REFERENCES `roles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `carreras`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `carreras` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(250) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `temas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `temas` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(255) NOT NULL,
  `carrera_id` INT NOT NULL COMMENT 'Id de la carrera',
  PRIMARY KEY (`id`),
  INDEX `fk_temas_carreras1_idx` (`carrera_id` ASC),
  CONSTRAINT `fk_temas_carreras1`
    FOREIGN KEY (`carrera_id`)
    REFERENCES `carreras` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `foros`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `foros` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `foro_titulo` VARCHAR(255) NOT NULL COMMENT 'Titulo del foro',
  `foro_texto` VARCHAR(45) NULL COMMENT 'Contenido del foro',
  `tema_id` INT NOT NULL COMMENT 'Id del tema',
  `usuario_id` INT NOT NULL COMMENT 'Id del usuario creador',
  `created` DATETIME NOT NULL COMMENT 'Fecha de creacion',
  `modified` DATETIME NOT NULL COMMENT 'Fecha de ultima modificación',
  PRIMARY KEY (`id`),
  INDEX `fk_foros_2_idx` (`tema_id` ASC),
  INDEX `fk_foros_3_idx` (`usuario_id` ASC),
  CONSTRAINT `fk_foros_2`
    FOREIGN KEY (`tema_id`)
    REFERENCES `temas` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_foros_3`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `comentarios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comentarios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `created` DATETIME NOT NULL,
  `modified` DATETIME NOT NULL,
  `texto` TEXT NOT NULL,
  `estado` INT NOT NULL,
  `foro_id` INT NOT NULL,
  `comentario_id` INT NULL,
  `usuario_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_comentarios_1_idx` (`foro_id` ASC),
  INDEX `fk_comentarios_2_idx` (`comentario_id` ASC),
  INDEX `fk_comentarios_3_idx` (`usuario_id` ASC),
  CONSTRAINT `fk_comentarios_1`
    FOREIGN KEY (`foro_id`)
    REFERENCES `foros` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_comentarios_2`
    FOREIGN KEY (`comentario_id`)
    REFERENCES `comentarios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_comentarios_3`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mensajes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mensajes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `asunto` VARCHAR(145) NOT NULL,
  `contenido` TEXT NULL,
  `fecha_envio` DATETIME NOT NULL,
  `mensaje_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_mensajes_1_idx` (`mensaje_id` ASC),
  CONSTRAINT `fk_mensajes_1`
    FOREIGN KEY (`mensaje_id`)
    REFERENCES `mensajes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mensajes_usuarios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mensajes_usuarios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `mensaje_id` INT NOT NULL,
  `usuario_id` INT NOT NULL,
  `tipo` INT(2) NULL,
  `estado` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_mensajes_usuarios_1_idx` (`mensaje_id` ASC),
  INDEX `fk_mensajes_usuarios_2_idx` (`usuario_id` ASC),
  CONSTRAINT `fk_mensajes_usuarios_1`
    FOREIGN KEY (`mensaje_id`)
    REFERENCES `mensajes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_mensajes_usuarios_2`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `relaciones`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `relaciones` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `usuario_id` INT NOT NULL,
  `usuario2_id` INT NOT NULL,
  `tipo` INT NOT NULL,
  `created` DATETIME NOT NULL,
  `estado` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_relaciones_1_idx` (`usuario_id` ASC),
  INDEX `fk_relaciones_2_idx` (`usuario2_id` ASC),
  CONSTRAINT `fk_relaciones_1`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_relaciones_2`
    FOREIGN KEY (`usuario2_id`)
    REFERENCES `usuarios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `archivos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `archivos` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Id unico por registro',
  `archivo_nombre` VARCHAR(100) NOT NULL COMMENT 'Nombre del archivo para el usuario cliente',
  `archivo_path` VARCHAR(100) NOT NULL COMMENT 'Nombre real en la carpeta en donde se almacenan los archivos',
  `archivo_size` VARCHAR(100) NULL COMMENT 'Tamaño del archivo',
  `created` DATETIME NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `foros_archivos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `foros_archivos` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Identificador único',
  `foro_id` INT NOT NULL COMMENT 'Id del foro',
  `archivo_id` INT NOT NULL COMMENT 'Id del archivo',
  INDEX `fk_foros_has_archivos_archivos1_idx` (`archivo_id` ASC),
  INDEX `fk_foros_has_archivos_foros1_idx` (`foro_id` ASC),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_foros_has_archivos_foros1`
    FOREIGN KEY (`foro_id`)
    REFERENCES `foros` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_foros_has_archivos_archivos1`
    FOREIGN KEY (`archivo_id`)
    REFERENCES `archivos` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Tabla para archivos adjuntos en un foro';


-- -----------------------------------------------------
-- Table `mensajes_archivos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mensajes_archivos` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Identificados único',
  `mensaje_id` INT NOT NULL COMMENT 'Id del mensaje',
  `archivo_id` INT NOT NULL COMMENT 'Id del archivo',
  PRIMARY KEY (`id`),
  INDEX `fk_mensajes_has_archivos_archivos1_idx` (`archivo_id` ASC),
  INDEX `fk_mensajes_has_archivos_mensajes1_idx` (`mensaje_id` ASC),
  CONSTRAINT `fk_mensajes_has_archivos_mensajes1`
    FOREIGN KEY (`mensaje_id`)
    REFERENCES `mensajes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_mensajes_has_archivos_archivos1`
    FOREIGN KEY (`archivo_id`)
    REFERENCES `archivos` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Tabla para controlar archivos adjuntos de un mensaje';


-- -----------------------------------------------------
-- Table `mentorias`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mentorias` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `usuario_mentor_id` INT NOT NULL COMMENT 'id de usuario mentor que registra la mentoria',
  `usuario_estudiante_id` INT NULL COMMENT 'id de estudiante que toma la mentoria',
  `mentoria_inicio` DATETIME NOT NULL COMMENT 'fecha de inicio',
  `mentoria_fin` DATETIME NOT NULL COMMENT 'fecha fin',
  `mentoria_estado` TINYINT(2) NULL COMMENT 'estado de la mentoria (0|null: sin finalizar, 1: finalizada)',
  `calificacion` INT(2) NULL COMMENT 'Calificacion del estudiante ',
  `resena` VARCHAR(100) NULL COMMENT 'Descripcion de la calificacion	',
  PRIMARY KEY (`id`),
  INDEX `fk_mentorias_usuarios1_idx` (`usuario_mentor_id` ASC),
  INDEX `fk_mentorias_usuarios2_idx` (`usuario_estudiante_id` ASC),
  CONSTRAINT `fk_mentorias_usuarios1`
    FOREIGN KEY (`usuario_mentor_id`)
    REFERENCES `usuarios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_mentorias_usuarios2`
    FOREIGN KEY (`usuario_estudiante_id`)
    REFERENCES `usuarios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'tabla para control de mentorias';


-- -----------------------------------------------------
-- Table `productos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `productos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(155) NOT NULL COMMENT 'nombre del producto',
  `descripcion` TEXT NULL COMMENT 'descripcion del producto',
  `valor` FLOAT NOT NULL COMMENT 'valor del producto',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
COMMENT = 'Tabla de productos';


-- -----------------------------------------------------
-- Table `ordenes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ordenes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `valor_total` FLOAT NOT NULL COMMENT 'Valor total de la compra',
  `fecha_hora_compra` DATETIME NOT NULL COMMENT 'fecha de la compra',
  `usuario_id` INT NOT NULL COMMENT 'id de usuario que realiza la compra',
  `estado` INT(2) NOT NULL DEFAULT 0 COMMENT 'estado de la compra (0: sin pagar, 1: pago)',
  PRIMARY KEY (`id`),
  INDEX `fk_ordenes_2_idx` (`usuario_id` ASC),
  CONSTRAINT `fk_ordenes_2`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Tabla para las compras de los estudiantes';


-- -----------------------------------------------------
-- Table `preguntas_tipos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `preguntas_tipos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `formularios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `formularios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `numero` INT NULL,
  `descripcion` TEXT NULL,
  `encabezado` TEXT NULL,
  `formulario_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_formularios_formularios1_idx` (`formulario_id` ASC),
  CONSTRAINT `fk_formularios_formularios1`
    FOREIGN KEY (`formulario_id`)
    REFERENCES `formularios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `preguntas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `preguntas` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `pregunta` VARCHAR(255) NULL,
  `numero` INT NULL,
  `preguntastipo_id` INT NULL,
  `formulario_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_pregunta_2_idx` (`preguntastipo_id` ASC),
  INDEX `fk_preguntas_1_idx` (`formulario_id` ASC),
  CONSTRAINT `fk_pregunta_2`
    FOREIGN KEY (`preguntastipo_id`)
    REFERENCES `preguntas_tipos` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_preguntas_1`
    FOREIGN KEY (`formulario_id`)
    REFERENCES `formularios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `permisos_productos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `permisos_productos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `permiso_id` INT NULL,
  `producto_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_permisos_productos_1_idx` (`permiso_id` ASC),
  INDEX `fk_permisos_productos_2_idx` (`producto_id` ASC),
  CONSTRAINT `fk_permisos_productos_1`
    FOREIGN KEY (`permiso_id`)
    REFERENCES `permisos` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_permisos_productos_2`
    FOREIGN KEY (`producto_id`)
    REFERENCES `productos` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `opciones`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `opciones` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(255) NULL,
  `pregunta_id` INT NULL,
  `peso` INT NULL,
  `factor` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_opciones_1_idx` (`pregunta_id` ASC),
  CONSTRAINT `fk_opciones_1`
    FOREIGN KEY (`pregunta_id`)
    REFERENCES `preguntas` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `alternativas_estudios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `alternativas_estudios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `usuario_id` INT NOT NULL,
  `carrera_id` INT NOT NULL,
  INDEX `fk_usuarios_has_carreras_carreras1_idx` (`carrera_id` ASC),
  INDEX `fk_usuarios_has_carreras_usuarios1_idx` (`usuario_id` ASC),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_usuarios_has_carreras_usuarios1`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_usuarios_has_carreras_carreras1`
    FOREIGN KEY (`carrera_id`)
    REFERENCES `carreras` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `participaciones`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `participaciones` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `fecha` DATETIME NOT NULL,
  `formulario_id` INT NOT NULL,
  `usuario_participa_id` INT NULL,
  `usuario_evaluado_id` INT NULL,
  `carrera_id` INT NULL,
  `correo_invitacion` VARCHAR(100) NULL,
  `estado` TINYINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `fk_participaciones_usuarios1_idx` (`usuario_participa_id` ASC),
  INDEX `fk_participaciones_usuarios2_idx` (`usuario_evaluado_id` ASC),
  INDEX `fk_participaciones_formularios1_idx` (`formulario_id` ASC),
  INDEX `fk_participaciones_carreras1_idx` (`carrera_id` ASC),
  CONSTRAINT `fk_participaciones_usuarios1`
    FOREIGN KEY (`usuario_participa_id`)
    REFERENCES `usuarios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_participaciones_usuarios2`
    FOREIGN KEY (`usuario_evaluado_id`)
    REFERENCES `usuarios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_participaciones_formularios1`
    FOREIGN KEY (`formulario_id`)
    REFERENCES `formularios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_participaciones_carreras1`
    FOREIGN KEY (`carrera_id`)
    REFERENCES `carreras` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `respuestas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `respuestas` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `participacion_id` INT NOT NULL,
  `pregunta_id` INT NOT NULL,
  `respuesta_numerica` FLOAT NULL,
  `respuesta_texto` VARCHAR(255) NULL,
  `valor` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_respuestas_participaciones1_idx` (`participacion_id` ASC),
  INDEX `fk_respuestas_preguntas1_idx` (`pregunta_id` ASC),
  CONSTRAINT `fk_respuestas_participaciones1`
    FOREIGN KEY (`participacion_id`)
    REFERENCES `participaciones` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_respuestas_preguntas1`
    FOREIGN KEY (`pregunta_id`)
    REFERENCES `preguntas` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `informacion`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `informacion` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `informacion_titulo` VARCHAR(250) NULL,
  `informacion_imagen` VARCHAR(100) NULL,
  `informacion_link` VARCHAR(100) NULL,
  `informacion_estado` TINYINT(1) NULL DEFAULT 0,
  `created` DATETIME NULL,
  `modified` DATETIME NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `respuestas_adicionales`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `respuestas_adicionales` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `respuesta_key` VARCHAR(45) NOT NULL,
  `respuesta_json` TEXT NOT NULL,
  `participacion_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_respuestas_adicionales_participaciones1_idx` (`participacion_id` ASC),
  CONSTRAINT `fk_respuestas_adicionales_participaciones1`
    FOREIGN KEY (`participacion_id`)
    REFERENCES `participaciones` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ordenes_productos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ordenes_productos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `producto_id` INT NOT NULL COMMENT 'id de producto',
  `orden_id` INT NOT NULL COMMENT 'id de la orden de compra',
  `mentor_id` INT NULL COMMENT 'id del mentor para el caso de comprar mentorias',
  `valor` FLOAT NOT NULL COMMENT 'valor del producto',
  `estado` INT(2) NOT NULL DEFAULT 0 COMMENT 'estado del producto (0:sin usar, 1:usado)',
  PRIMARY KEY (`id`),
  INDEX `fk_ordenes_productos_productos1_idx` (`producto_id` ASC),
  INDEX `fk_ordenes_productos_ordenes1_idx` (`orden_id` ASC),
  CONSTRAINT `fk_ordenes_productos_productos1`
    FOREIGN KEY (`producto_id`)
    REFERENCES `productos` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ordenes_productos_ordenes1`
    FOREIGN KEY (`orden_id`)
    REFERENCES `ordenes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Tabla que relaciona los productos de una compra';


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
