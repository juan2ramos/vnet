SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

DROP SCHEMA IF EXISTS `vocationet-dev` ;
CREATE SCHEMA IF NOT EXISTS `vocationet-dev` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ;
SHOW WARNINGS;
USE `vocationet-dev` ;

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
  `usuario_estado` INT(2) NOT NULL ,
  `usuario_hash` VARCHAR(45) NULL ,
  `usuario_facebookid` INT NULL ,
  `usuario_tipo` INT(2) NOT NULL ,
  `created` DATETIME NULL ,
  `modified` DATETIME NULL ,
  `rol_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_usuarios_1` (`rol_id` ASC) ,
  UNIQUE INDEX `usuario_email_UNIQUE` (`usuario_email` ASC) ,
  CONSTRAINT `fk_usuarios_1`
    FOREIGN KEY (`rol_id` )
    REFERENCES `roles` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

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
  `georeferencia_id` INT NOT NULL ,
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
-- Table `perfiles`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `perfiles` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `perfiles` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `usuario_id` INT NOT NULL ,
  `fechanacimiento` DATE NOT NULL ,
  `genero` VARCHAR(45) NOT NULL ,
  `georeferencia_id` INT NOT NULL ,
  `curso_actual` INT NULL ,
  `imagen` VARCHAR(155) NULL ,
  `tarjeta_profesional` VARCHAR(155) NULL ,
  `hojavida` VARCHAR(155) NULL ,
  `colegio_id` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_perfiles_1` (`usuario_id` ASC) ,
  INDEX `fk_perfiles_2` (`georeferencia_id` ASC) ,
  INDEX `fk_perfiles_colegios1_idx` (`colegio_id` ASC) ,
  CONSTRAINT `fk_perfiles_1`
    FOREIGN KEY (`usuario_id` )
    REFERENCES `usuarios` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_perfiles_2`
    FOREIGN KEY (`georeferencia_id` )
    REFERENCES `georeferencias` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_perfiles_colegios1`
    FOREIGN KEY (`colegio_id` )
    REFERENCES `colegios` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `empresas`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `empresas` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `empresas` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(155) NOT NULL COMMENT '\n	' ,
  `tipo` INT(2) NOT NULL ,
  `size` VARCHAR(45) NULL ,
  `industria` VARCHAR(45) NULL ,
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
  `cargo` VARCHAR(150) NOT NULL ,
  `resumen` MEDIUMTEXT NULL ,
  `fecha_inicio` DATE NULL ,
  `fecha_final` DATE NULL ,
  `es_actual` INT(2) NOT NULL ,
  `empresa_id` INT NOT NULL ,
  `perfil_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_trabajos_1` (`perfil_id` ASC) ,
  INDEX `fk_trabajos_2` (`empresa_id` ASC) ,
  CONSTRAINT `fk_trabajos_1`
    FOREIGN KEY (`perfil_id` )
    REFERENCES `perfiles` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_trabajos_2`
    FOREIGN KEY (`empresa_id` )
    REFERENCES `empresas` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `instituciones`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `instituciones` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `instituciones` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(155) NOT NULL ,
  `tipo` INT(2) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `estudios`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `estudios` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `estudios` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `perfil_id` INT NOT NULL ,
  `institucion_id` INT NOT NULL ,
  `campo` VARCHAR(155) NULL ,
  `fecha_inicio` DATE NULL ,
  `fecha_final` DATE NULL ,
  `titulo` VARCHAR(155) NULL ,
  `actividad` TEXT NULL ,
  `notas` MEDIUMTEXT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_estudios_1` (`perfil_id` ASC) ,
  INDEX `fk_estudios_2` (`institucion_id` ASC) ,
  CONSTRAINT `fk_estudios_1`
    FOREIGN KEY (`perfil_id` )
    REFERENCES `perfiles` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_estudios_2`
    FOREIGN KEY (`institucion_id` )
    REFERENCES `instituciones` (`id` )
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
  `permisoscol` VARCHAR(45) NOT NULL ,
  `nombre` VARCHAR(60) NOT NULL ,
  `descripcion` MEDIUMTEXT NULL ,
  `rol_id` INT NOT NULL ,
  `permiso_routes` TEXT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_permisos_1` (`rol_id` ASC) ,
  CONSTRAINT `fk_permisos_1`
    FOREIGN KEY (`rol_id` )
    REFERENCES `roles` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
