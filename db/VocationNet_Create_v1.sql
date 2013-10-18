SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `mydb` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ;
USE `mydb` ;

-- -----------------------------------------------------
-- Table `mydb`.`usuarios`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `mydb`.`usuarios` (
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
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `mydb`.`georeferencias`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `mydb`.`georeferencias` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `georeferencia_padre_id` INT NULL ,
  `georeferencia_nombre` VARCHAR(100) NULL ,
  `georeferencia_tipo` VARCHAR(5) NULL ,
  `georeferencia_codigo` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `mydb`.`perfiles`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `mydb`.`perfiles` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `usuario_id` INT NOT NULL ,
  `fechanacimiento` DATE NOT NULL ,
  `genero` VARCHAR(45) NOT NULL ,
  `georeferencia_id` INT NOT NULL ,
  `curso_actual` INT NULL ,
  `imagen` VARCHAR(155) NULL ,
  `tarjeta_profesional` VARCHAR(155) NULL ,
  `hojavida` VARCHAR(155) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_perfiles_1` (`usuario_id` ASC) ,
  INDEX `fk_perfiles_2` (`georeferencia_id` ASC) ,
  CONSTRAINT `fk_perfiles_1`
    FOREIGN KEY (`usuario_id` )
    REFERENCES `mydb`.`usuarios` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_perfiles_2`
    FOREIGN KEY (`georeferencia_id` )
    REFERENCES `mydb`.`georeferencias` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`colegios`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `mydb`.`colegios` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(45) NOT NULL ,
  `georeferencia_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_colegios_1` (`georeferencia_id` ASC) ,
  CONSTRAINT `fk_colegios_1`
    FOREIGN KEY (`georeferencia_id` )
    REFERENCES `mydb`.`georeferencias` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`colegios_perfiles`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `mydb`.`colegios_perfiles` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `colegio_id` INT NOT NULL ,
  `perfil_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_colegios_perfiles_1` (`colegio_id` ASC) ,
  INDEX `fk_colegios_perfiles_2` (`perfil_id` ASC) ,
  CONSTRAINT `fk_colegios_perfiles_1`
    FOREIGN KEY (`colegio_id` )
    REFERENCES `mydb`.`colegios` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_colegios_perfiles_2`
    FOREIGN KEY (`perfil_id` )
    REFERENCES `mydb`.`perfiles` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`empresas`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `mydb`.`empresas` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(155) NOT NULL COMMENT '\n	' ,
  `tipo` INT(2) NOT NULL ,
  `size` VARCHAR(45) NULL ,
  `industria` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT ``
    FOREIGN KEY ()
    REFERENCES `mydb`.`georeferencias` ()
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`trabajos`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `mydb`.`trabajos` (
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
    REFERENCES `mydb`.`perfiles` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_trabajos_2`
    FOREIGN KEY (`empresa_id` )
    REFERENCES `mydb`.`empresas` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`instituciones`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `mydb`.`instituciones` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(155) NOT NULL ,
  `tipo` INT(2) NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT ``
    FOREIGN KEY ()
    REFERENCES `mydb`.`georeferencias` ()
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`estudios`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `mydb`.`estudios` (
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
    REFERENCES `mydb`.`perfiles` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_estudios_2`
    FOREIGN KEY (`institucion_id` )
    REFERENCES `mydb`.`instituciones` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
