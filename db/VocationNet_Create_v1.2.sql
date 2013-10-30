SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table `roles`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `roles` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `roles` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(45) NOT NULL ,
  `descripcion` MEDIUMTEXT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `georeferencias`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `georeferencias` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `georeferencias` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `georeferencia_padre_id` INT NULL ,
  `georeferencia_nombre` VARCHAR(100) NULL ,
  `georeferencia_tipo` VARCHAR(70) NULL ,
  `georeferencia_codigo` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `colegios`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `colegios` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `colegios` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(45) NOT NULL ,
  `georeferencia_id` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_colegios_1` (`georeferencia_id` ASC) ,
  CONSTRAINT `fk_colegios_1`
    FOREIGN KEY (`georeferencia_id` )
    REFERENCES `georeferencias` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `usuarios`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `usuarios` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `usuarios` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `usuario_nombre` VARCHAR(45) NOT NULL ,
  `usuario_apellido` VARCHAR(45) NOT NULL ,
  `usuario_email` VARCHAR(100) NOT NULL ,
  `usuario_password` VARCHAR(45) NULL ,
  `usuario_hash` VARCHAR(45) NULL ,
  `usuario_estado` INT(2) NOT NULL ,
  `usuario_facebookid` INT NULL ,
  `rol_id` INT NOT NULL ,
  `georeferencia_id` INT NULL ,
  `usuario_fecha_nacimiento` DATE NULL ,
  `usuario_genero` VARCHAR(45) NULL ,
  `usuario_imagen` VARCHAR(155) NULL ,
  `usuario_tarjeta_profesional` VARCHAR(155) NULL ,
  `usuario_hoja_vida` VARCHAR(155) NULL ,
  `usuario_perfil_profesional` TEXT NULL ,
  `usuario_valor_mentoria` FLOAT NULL ,
  `colegio_id` INT NULL ,
  `usuario_curso_actual` INT NULL ,
  `usuario_fecha_planeacion` DATE NULL ,
  `created` DATETIME NULL ,
  `modified` DATETIME NULL ,
  `sync_linkedin` DATETIME NULL COMMENT 'Ultima sincronización realizada con linkedin' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_usuarios_1` (`rol_id` ASC) ,
  UNIQUE INDEX `usuario_email_UNIQUE` (`usuario_email` ASC) ,
  INDEX `fk_usuarios_georeferencias1_idx` (`georeferencia_id` ASC) ,
  INDEX `fk_usuarios_colegios1_idx` (`colegio_id` ASC) ,
  CONSTRAINT `fk_usuarios_1`
    FOREIGN KEY (`rol_id` )
    REFERENCES `roles` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_usuarios_georeferencias1`
    FOREIGN KEY (`georeferencia_id` )
    REFERENCES `georeferencias` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_usuarios_colegios1`
    FOREIGN KEY (`colegio_id` )
    REFERENCES `colegios` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `empresas`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `empresas` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `empresas` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(155) NOT NULL COMMENT '\\n	' ,
  `tipo` VARCHAR(45) NULL ,
  `size` VARCHAR(45) NULL ,
  `industria` VARCHAR(45) NULL ,
  `id_linkedin` INT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `trabajos`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `trabajos` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `trabajos` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `usuario_id` INT NOT NULL ,
  `empresa_id` INT NOT NULL ,
  `cargo` VARCHAR(150) NOT NULL ,
  `resumen` MEDIUMTEXT NULL ,
  `fecha_inicio` DATE NULL ,
  `fecha_final` DATE NULL ,
  `es_actual` INT(2) NOT NULL ,
  `id_linkedin` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_trabajos_2` (`empresa_id` ASC) ,
  INDEX `fk_trabajos_usuarios1_idx` (`usuario_id` ASC) ,
  CONSTRAINT `fk_trabajos_2`
    FOREIGN KEY (`empresa_id` )
    REFERENCES `empresas` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_trabajos_usuarios1`
    FOREIGN KEY (`usuario_id` )
    REFERENCES `usuarios` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `estudios`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `estudios` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `estudios` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nombre_institucion` VARCHAR(100) NULL ,
  `campo` VARCHAR(155) NULL ,
  `fecha_inicio` DATE NULL ,
  `fecha_final` DATE NULL ,
  `titulo` VARCHAR(155) NULL ,
  `actividad` TEXT NULL ,
  `notas` MEDIUMTEXT NULL ,
  `id_linkedin` INT NULL ,
  `usuario_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_estudios_usuarios1_idx` (`usuario_id` ASC) ,
  CONSTRAINT `fk_estudios_usuarios1`
    FOREIGN KEY (`usuario_id` )
    REFERENCES `usuarios` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `permisos`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `permisos` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `permisos` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `identificador` VARCHAR(45) NOT NULL ,
  `nombre` VARCHAR(60) NOT NULL ,
  `descripcion` MEDIUMTEXT NULL ,
  `permiso_routes` TEXT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `permisos_roles`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `permisos_roles` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `permisos_roles` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `permiso_id` INT NOT NULL ,
  `rol_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_permisos_has_roles_roles1_idx` (`rol_id` ASC) ,
  INDEX `fk_permisos_has_roles_permisos1_idx` (`permiso_id` ASC) ,
  CONSTRAINT `fk_permisos_has_roles_permisos1`
    FOREIGN KEY (`permiso_id` )
    REFERENCES `permisos` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_permisos_has_roles_roles1`
    FOREIGN KEY (`rol_id` )
    REFERENCES `roles` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `temas`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `temas` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `temas` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `carreras`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `carreras` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `carreras` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(250) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `foros`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `foros` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `foros` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `titulo` VARCHAR(255) NOT NULL ,
  `carrera_id` INT NOT NULL ,
  `tema_id` INT NOT NULL ,
  `uduario_id` INT NOT NULL ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`, `tema_id`) ,
  INDEX `fk_foros_1` (`carrera_id` ASC) ,
  INDEX `fk_foros_2` (`tema_id` ASC) ,
  INDEX `fk_foros_3` (`uduario_id` ASC) ,
  CONSTRAINT `fk_foros_1`
    FOREIGN KEY (`carrera_id` )
    REFERENCES `carreras` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_foros_2`
    FOREIGN KEY (`tema_id` )
    REFERENCES `temas` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_foros_3`
    FOREIGN KEY (`uduario_id` )
    REFERENCES `usuarios` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `comentarios`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `comentarios` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `comentarios` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  `texto` TEXT NOT NULL ,
  `estado` INT NOT NULL ,
  `foro_id` INT NOT NULL ,
  `comentario_id` INT NOT NULL ,
  `usuario_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_comentarios_1` (`foro_id` ASC) ,
  INDEX `fk_comentarios_2` (`comentario_id` ASC) ,
  INDEX `fk_comentarios_3` (`usuario_id` ASC) ,
  CONSTRAINT `fk_comentarios_1`
    FOREIGN KEY (`foro_id` )
    REFERENCES `foros` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_comentarios_2`
    FOREIGN KEY (`comentario_id` )
    REFERENCES `comentarios` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_comentarios_3`
    FOREIGN KEY (`usuario_id` )
    REFERENCES `usuarios` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `mensajes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mensajes` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `mensajes` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `asunto` VARCHAR(145) NOT NULL ,
  `contenido` TEXT NULL ,
  `fecha_envio` DATETIME NOT NULL ,
  `mensaje_id` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_mensajes_1_idx` (`mensaje_id` ASC) ,
  CONSTRAINT `fk_mensajes_1`
    FOREIGN KEY (`mensaje_id` )
    REFERENCES `mensajes` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `mensajes_usuarios`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mensajes_usuarios` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `mensajes_usuarios` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `mensaje_id` INT NOT NULL ,
  `usuario_id` INT NOT NULL ,
  `tipo` INT(2) NULL ,
  `estado` TINYINT(1) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_mensajes_usuarios_1_idx` (`mensaje_id` ASC) ,
  INDEX `fk_mensajes_usuarios_2_idx` (`usuario_id` ASC) ,
  CONSTRAINT `fk_mensajes_usuarios_1`
    FOREIGN KEY (`mensaje_id` )
    REFERENCES `mensajes` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_mensajes_usuarios_2`
    FOREIGN KEY (`usuario_id` )
    REFERENCES `usuarios` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `relaciones`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `relaciones` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `relaciones` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `usuario_id` INT NOT NULL ,
  `usuario_copy_id` INT NOT NULL ,
  `tipo` INT NOT NULL ,
  `created` DATETIME NOT NULL ,
  `estado` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_relaciones_1_idx` (`usuario_id` ASC) ,
  INDEX `fk_relaciones_2_idx` (`usuario_copy_id` ASC) ,
  CONSTRAINT `fk_relaciones_1`
    FOREIGN KEY (`usuario_id` )
    REFERENCES `usuarios` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_relaciones_2`
    FOREIGN KEY (`usuario_copy_id` )
    REFERENCES `usuarios` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
