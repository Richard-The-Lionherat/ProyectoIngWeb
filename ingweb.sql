-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 12, 2026 at 08:21 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ingweb`
--
CREATE DATABASE IF NOT EXISTS `ingweb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ingweb`;

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `sp_agregarIngrediente`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_agregarIngrediente` (IN `p_nombre` VARCHAR(64), IN `p_unidad` VARCHAR(8), IN `p_cantidad` INT, OUT `p_resultado` INT)   BEGIN
    DECLARE existe INT;

    -- Validar si el ingrediente ya existe (por nombre)
    SELECT COUNT(*) INTO existe
    FROM ingredientes
    WHERE ING_nombre = p_nombre;

    IF existe > 0 THEN
        SET p_resultado = 1; -- Ingrediente duplicado
    ELSE
        INSERT INTO ingredientes (ING_nombre, ING_unidadMedida, ING_cantidad)
        VALUES (p_nombre, p_unidad, p_cantidad);

        SET p_resultado = 0; -- Insert correcto
    END IF;
END$$

DROP PROCEDURE IF EXISTS `sp_agregarMetodoPago`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_agregarMetodoPago` (IN `p_emailUsuario` VARCHAR(64), IN `p_tipo` ENUM('TARJETA','TRANSFERENCIA','EFECTIVO'), IN `p_marca` VARCHAR(20), IN `p_ultimos4` CHAR(4), IN `p_exp_mes` TINYINT, IN `p_exp_anio` SMALLINT, IN `p_alias` VARCHAR(64), IN `p_predeterminado` TINYINT, OUT `p_res` INT)   proc: BEGIN
    DECLARE v_total INT DEFAULT 0;

    -- Valor por defecto
    SET p_res = 0;

    /* ================= DUPLICADO POR ALIAS ================= */
    IF EXISTS (
        SELECT 1
        FROM metodos_pago
        WHERE mp_emailUsuario = p_emailUsuario
          AND mp_alias = p_alias
          AND mp_activo = 1
    ) THEN
        SET p_res = 1;
        LEAVE proc;
    END IF;

    /* ================= DUPLICADO TARJETA ================= */
    IF p_tipo = 'TARJETA' AND EXISTS (
        SELECT 1
        FROM metodos_pago
        WHERE mp_emailUsuario = p_emailUsuario
          AND mp_tipo = 'TARJETA'
          AND mp_ultimos4 = p_ultimos4
          AND mp_activo = 1
    ) THEN
        SET p_res = 2;
        LEAVE proc;
    END IF;

    /* ================= CONTAR MÉTODOS ================= */
    SELECT COUNT(*)
    INTO v_total
    FROM metodos_pago
    WHERE mp_emailUsuario = p_emailUsuario
      AND mp_activo = 1;

    /* ================= PREDTERMINADO ================= */
    IF v_total = 0 THEN
        SET p_predeterminado = 1;
    END IF;

    IF p_predeterminado = 1 THEN
        UPDATE metodos_pago
        SET mp_predeterminado = 0
        WHERE mp_emailUsuario = p_emailUsuario;
    END IF;

    /* ================= INSERT ================= */
    INSERT INTO metodos_pago (
        mp_emailUsuario,
        mp_tipo,
        mp_marca,
        mp_ultimos4,
        mp_exp_mes,
        mp_exp_anio,
        mp_alias,
        mp_predeterminado
    ) VALUES (
        p_emailUsuario,
        p_tipo,
        p_marca,
        p_ultimos4,
        p_exp_mes,
        p_exp_anio,
        p_alias,
        p_predeterminado
    );

END proc$$

DROP PROCEDURE IF EXISTS `sp_agregarProducto`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_agregarProducto` (IN `p_categoria_id` INT, IN `p_nombre` VARCHAR(100), IN `p_descripcion` TEXT, IN `p_precio` DECIMAL(10,2), IN `p_unidad` VARCHAR(20), IN `p_cantidad` DECIMAL(10,2), IN `p_imagen_ruta` VARCHAR(255), OUT `p_resultado` INT)   proc: BEGIN

    DECLARE v_producto_id INT;

    /* ===== VALIDAR CATEGORÍA ===== */
    IF NOT EXISTS (
        SELECT 1 FROM categorias WHERE id = p_categoria_id
    ) THEN
        SET p_resultado = 1; -- categoría inválida
        LEAVE proc;
    END IF;

    /* ===== VALIDAR IMAGEN ===== */
    IF p_imagen_ruta IS NULL OR p_imagen_ruta = '' THEN
        SET p_resultado = 3; -- imagen obligatoria
        LEAVE proc;
    END IF;

    /* ===== VALIDAR PRODUCTO DUPLICADO ===== */
    IF EXISTS (
        SELECT 1
        FROM productos_bebidas
        WHERE nombre = p_nombre
          AND categoria_id = p_categoria_id
    ) THEN
        SET p_resultado = 2; -- producto duplicado
        LEAVE proc;
    END IF;

    /* ===== TRANSACCIÓN ===== */
    START TRANSACTION;

    /* ===== INSERT PRODUCTO ===== */
    INSERT INTO productos_bebidas (
        categoria_id,
        nombre,
        descripcion,
        precio,
        unidad_medida,
        cantidad
    ) VALUES (
        p_categoria_id,
        p_nombre,
        p_descripcion,
        p_precio,
        p_unidad,
        p_cantidad
    );

    SET v_producto_id = LAST_INSERT_ID();

    /* ===== INSERT IMAGEN ===== */
    INSERT INTO imagenes_comida (
        producto_id,
        imagen
    ) VALUES (
        v_producto_id,
        p_imagen_ruta
    );

    COMMIT;

    SET p_resultado = 0; -- éxito

END$$

DROP PROCEDURE IF EXISTS `sp_agregarUbicacion`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_agregarUbicacion` (IN `p_email` VARCHAR(64), IN `p_alias` VARCHAR(64), IN `p_colonia` VARCHAR(128), IN `p_ciudad` VARCHAR(128), IN `p_direccion` VARCHAR(255), IN `p_lat` DECIMAL(10,8), IN `p_lng` DECIMAL(11,8), OUT `p_res` INT)   BEGIN
    DECLARE EXIT HANDLER FOR 1062
    BEGIN
        SET p_res = 1; -- duplicado
    END;

    INSERT INTO ubicaciones (
        ubi_emailUsuario,
        ubi_alias,
        ubi_colonia,
        ubi_ciudad,
        ubi_direccion,
        ubi_latitud,
        ubi_longitud
    ) VALUES (
        p_email,
        p_alias,
        p_colonia,
        p_ciudad,
        p_direccion,
        p_lat,
        p_lng
    );

    SET p_res = 0; -- éxito
END$$

DROP PROCEDURE IF EXISTS `sp_cambiarCantidad`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cambiarCantidad` (IN `p_id` INT UNSIGNED, IN `p_cantidad` DECIMAL(10,2), OUT `p_resultado` INT)   BEGIN
    DECLARE existe INT;

    -- Validar existencia del ingrediente
    SELECT COUNT(*) INTO existe
    FROM ingredientes
    WHERE ING_id = p_id;

    IF existe = 0 THEN
        SET p_resultado = 1; -- Ingrediente no existe
    ELSE
        UPDATE ingredientes
        SET ING_cantidad = p_cantidad
        WHERE ING_id = p_id;

        SET p_resultado = 0; -- Actualización correcta
    END IF;
END$$

DROP PROCEDURE IF EXISTS `sp_cambiarImagenCategoria`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cambiarImagenCategoria` (IN `p_id` INT, IN `p_imagen` VARCHAR(255), OUT `p_resultado` INT)   BEGIN
    -- Verificar que exista la categoría
    IF NOT EXISTS (
        SELECT 1
        FROM categorias
        WHERE id = p_id
    ) THEN
        SET p_resultado = 1; -- Categoría no existe
    ELSE
        UPDATE categorias
        SET imagen = p_imagen
        WHERE id = p_id;

        SET p_resultado = 0; -- Éxito
    END IF;
END$$

DROP PROCEDURE IF EXISTS `sp_cambiarImagenProducto`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cambiarImagenProducto` (IN `p_imagen_id` INT, IN `p_nueva_ruta` VARCHAR(255), OUT `p_resultado` INT)   BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM imagenes_comida
        WHERE id = p_imagen_id
    ) THEN
        SET p_resultado = 1;
    ELSE
        UPDATE imagenes_comida
        SET imagen = p_nueva_ruta
        WHERE id = p_imagen_id;

        SET p_resultado = 0;
    END IF;
END$$

DROP PROCEDURE IF EXISTS `sp_cambiarMedida`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cambiarMedida` (IN `p_id` INT UNSIGNED, IN `p_unidad` VARCHAR(8), OUT `p_resultado` INT)   BEGIN
    DECLARE existe INT;

    -- Validar existencia
    SELECT COUNT(*) INTO existe
    FROM ingredientes
    WHERE ING_id = p_id;

    IF existe = 0 THEN
        SET p_resultado = 1; -- Ingrediente no existe
    ELSE
        UPDATE ingredientes
        SET ING_unidadMedida = p_unidad
        WHERE ING_id = p_id;

        SET p_resultado = 0; -- Actualización correcta
    END IF;
END$$

DROP PROCEDURE IF EXISTS `sp_cambiarNombre`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cambiarNombre` (IN `p_email` VARCHAR(64), IN `p_newNombre` VARCHAR(64))   BEGIN
    UPDATE usuariosweb
    SET userWEB_nombre = p_newNombre
    WHERE userWEB_emailID = p_email;
END$$

DROP PROCEDURE IF EXISTS `sp_cambiarProducto`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cambiarProducto` (IN `p_id` INT, IN `p_categoria_id` INT, IN `p_nombre` VARCHAR(100), IN `p_descripcion` TEXT, IN `p_precio` DECIMAL(10,2), IN `p_unidad_medida` VARCHAR(50), IN `p_cantidad` DECIMAL(10,3), OUT `p_resultado` INT)   proc: BEGIN

    -- Valor por defecto: error
    SET p_resultado = 1;

    -- Validaciones básicas
    IF p_id <= 0 THEN
        SET p_resultado = 2;
        LEAVE proc;
    END IF;

    IF p_categoria_id <= 0 OR p_nombre = '' OR p_precio <= 0 THEN
        SET p_resultado = 3;
        LEAVE proc;
    END IF;

    -- Verificar que el producto exista
    IF NOT EXISTS (
        SELECT 1 FROM productos_bebidas WHERE id = p_id
    ) THEN
        SET p_resultado = 4;
        LEAVE proc;
    END IF;

    -- Actualizar producto
    UPDATE productos_bebidas
    SET
        categoria_id   = p_categoria_id,
        nombre         = p_nombre,
        descripcion    = p_descripcion,
        precio         = p_precio,
        unidad_medida  = p_unidad_medida,
        cantidad       = p_cantidad
    WHERE id = p_id;

    -- Éxito
    SET p_resultado = 0;

END$$

DROP PROCEDURE IF EXISTS `sp_cambiarTipo`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cambiarTipo` (IN `p_email` VARCHAR(64), IN `p_newTipo` ENUM('A','C','E'))   BEGIN

UPDATE usuariosweb
SET userWEB_tipo = p_newTipo
WHERE userWEB_emailID = p_email;

END$$

DROP PROCEDURE IF EXISTS `sp_crearCategoria`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_crearCategoria` (IN `p_nombre` VARCHAR(100), IN `p_imagen` VARCHAR(255), OUT `p_resultado` INT)   BEGIN
    DECLARE v_count INT DEFAULT 0;

    /* Validar nombre duplicado */
    SELECT COUNT(*) INTO v_count
    FROM categorias
    WHERE nombre = p_nombre;

    IF v_count > 0 THEN
        SET p_resultado = 1;
    ELSE
        /* Validar imagen duplicada */
        SELECT COUNT(*) INTO v_count
        FROM categorias
        WHERE imagen = p_imagen;

        IF v_count > 0 THEN
            SET p_resultado = 2;
        ELSE
            /* Insertar categoría */
            INSERT INTO categorias (nombre, imagen)
            VALUES (p_nombre, p_imagen);

            SET p_resultado = 0;
        END IF;
    END IF;

END$$

DROP PROCEDURE IF EXISTS `sp_eliminarCategoria`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_eliminarCategoria` (IN `p_categoria_id` INT, OUT `p_resultado` INT)   BEGIN
    DECLARE existe INT DEFAULT 0;

    -- Verificar existencia
    SELECT COUNT(*)
    INTO existe
    FROM categorias
    WHERE id = p_categoria_id;

    IF existe = 0 THEN
        SET p_resultado = 1; -- No existe
    ELSE
        -- Eliminar categoría
        -- Los productos asociados se eliminan por ON DELETE CASCADE
        DELETE FROM categorias
        WHERE id = p_categoria_id;

        SET p_resultado = 0; -- Éxito
    END IF;
END$$

DROP PROCEDURE IF EXISTS `sp_eliminarImagenProducto`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_eliminarImagenProducto` (IN `p_imagen_id` INT, OUT `p_resultado` INT)   BEGIN
    DECLARE v_producto_id INT;
    DECLARE v_total INT;

    -- Obtener producto asociado
    SELECT producto_id
    INTO v_producto_id
    FROM imagenes_comida
    WHERE id = p_imagen_id;

    IF v_producto_id IS NULL THEN
        SET p_resultado = 1;
    ELSE
        -- Contar imágenes del producto
        SELECT COUNT(*)
        INTO v_total
        FROM imagenes_comida
        WHERE producto_id = v_producto_id;

        IF v_total <= 1 THEN
            SET p_resultado = 2;
        ELSE
            DELETE FROM imagenes_comida
            WHERE id = p_imagen_id;

            SET p_resultado = 0;
        END IF;
    END IF;
END$$

DROP PROCEDURE IF EXISTS `sp_eliminarProducto`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_eliminarProducto` (IN `p_producto_id` INT, OUT `p_resultado` INT)   BEGIN
    DECLARE v_existe INT DEFAULT 0;

    -- Verificar existencia
    SELECT COUNT(*) INTO v_existe
    FROM productos_bebidas
    WHERE id = p_producto_id;

    IF v_existe = 0 THEN
        SET p_resultado = 1; -- no existe
    ELSE
        DELETE FROM productos_bebidas
        WHERE id = p_producto_id;

        SET p_resultado = 0; -- eliminado correctamente
    END IF;
END$$

DROP PROCEDURE IF EXISTS `sp_eliminarUbicacion`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_eliminarUbicacion` (IN `p_email` VARCHAR(64), IN `p_ubi_id` INT)   BEGIN
    DELETE FROM ubicaciones
    WHERE ubi_id = p_ubi_id
      AND ubi_emailUsuario = p_email;
END$$

DROP PROCEDURE IF EXISTS `sp_eliminarUsuario`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_eliminarUsuario` (IN `p_email` VARCHAR(64), OUT `p_resultado` INT)   BEGIN
    DECLARE existe INT;

    -- Revisar si existe el usuario
    SELECT COUNT(*) INTO existe
    FROM usuariosWEB
    WHERE userWEB_emailID = p_email;

    IF existe = 0 THEN
        SET p_resultado = 1; -- No existe
    ELSE
        DELETE FROM usuariosWEB
        WHERE userWEB_emailID = p_email;

        SET p_resultado = 0; -- Eliminado correctamente
    END IF;
END$$

DROP PROCEDURE IF EXISTS `sp_establecerUbicacionPredeterminada`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_establecerUbicacionPredeterminada` (IN `p_email` VARCHAR(64), IN `p_ubi_id` INT)   BEGIN
    -- Quitar predeterminada a todas las ubicaciones del usuario
    UPDATE ubicaciones
    SET ubi_predeterminada = 0
    WHERE ubi_emailUsuario = p_email;

    -- Establecer la seleccionada como predeterminada
    UPDATE ubicaciones
    SET ubi_predeterminada = 1
    WHERE ubi_id = p_ubi_id
      AND ubi_emailUsuario = p_email;
END$$

DROP PROCEDURE IF EXISTS `sp_obtenerPasswordUsuario`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtenerPasswordUsuario` (IN `p_email` VARCHAR(64))   BEGIN
    SELECT userWEB_password, userWEB_tipo, userWEB_nombre
    FROM usuariosWEB
    WHERE userWEB_emailID = p_email;
END$$

DROP PROCEDURE IF EXISTS `sp_registrarUsuario`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_registrarUsuario` (IN `p_email` VARCHAR(64), IN `p_nombre` VARCHAR(64), IN `p_password` VARCHAR(255), IN `p_tipo` ENUM('A','C','E'), OUT `p_resultado` INT)   BEGIN
    DECLARE existe INT;
    -- Verificar si el correo ya existe
    SELECT COUNT(*) INTO existe FROM usuariosWEB WHERE userWEB_emailID = p_email;
    IF existe > 0 THEN SET p_resultado = 1; -- 1 = correo duplicado
    ELSE
    -- Insertar el usuario
    INSERT INTO usuariosWEB (userWEB_emailID, userWEB_nombre, userWEB_password, userWEB_tipo)
    VALUES (p_email, p_nombre, p_password, p_tipo); SET p_resultado = 0; -- 0 = registro exitoso
    END IF;
    END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `imagen` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `imagen`) VALUES
(1, 'Bebidas Calientes', '/ProyectoIngWeb/images/caramel_macchiato.jpg'),
(2, 'Bebidas Frías', '/ProyectoIngWeb/images/caramel_frappuccino.jpg'),
(3, 'Postres y Panadería ', '/ProyectoIngWeb/images/apple_croissant.jpg'),
(4, 'Comida/ Sándwiches ', '/ProyectoIngWeb/images/grilled_cheese.jpg'),
(7, 'Chimichangas', '/ProyectoIngWeb/images/cat_6965073e3e4e70.61777060.png');

-- --------------------------------------------------------

--
-- Table structure for table `imagenes_comida`
--

DROP TABLE IF EXISTS `imagenes_comida`;
CREATE TABLE `imagenes_comida` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `imagenes_comida`
--

INSERT INTO `imagenes_comida` (`id`, `producto_id`, `imagen`) VALUES
(1, 1, '/ProyectoIngWeb/images/espresso_1.jpg'),
(2, 1, '/ProyectoIngWeb/images/espresso_2.jpg'),
(3, 2, '/ProyectoIngWeb/images/latte_1.jpg'),
(4, 2, '/ProyectoIngWeb/images/latte_2.jpg'),
(5, 3, '/ProyectoIngWeb/images/mocha_1.jpg'),
(6, 3, '/ProyectoIngWeb/images/mocha_2.jpg'),
(7, 4, '/ProyectoIngWeb/images/cappuccino_1.jpg'),
(8, 4, '/ProyectoIngWeb/images/cappuccino_2.jpg'),
(9, 5, '/ProyectoIngWeb/images/chocolate_caliente_1.jpg'),
(10, 5, '/ProyectoIngWeb/images/chocolate_caliente_2.jpg'),
(11, 6, '/ProyectoIngWeb/images/pink_drink_1.jpg'),
(12, 6, '/ProyectoIngWeb/images/pink_drink_2.jpg'),
(13, 7, '/ProyectoIngWeb/images/strawberry_acai_1.jpg'),
(14, 7, '/ProyectoIngWeb/images/strawberry_acai_2.jpg'),
(15, 8, '/ProyectoIngWeb/images/mango_dragonfruit_refresher_1.jpg'),
(16, 8, '/ProyectoIngWeb/images/mango_dragonfruit_refresher_2.jpg'),
(17, 9, '/ProyectoIngWeb/images/lemon_black_tea_1.jpg'),
(18, 9, '/ProyectoIngWeb/images/lemon_black_tea_2.jpg'),
(19, 10, '/ProyectoIngWeb/images/cappuccino_helado_1.jpg'),
(20, 10, '/ProyectoIngWeb/images/cappuccino_helado_2.jpg'),
(21, 11, '/ProyectoIngWeb/images/tarta_moras_1.jpg'),
(22, 11, '/ProyectoIngWeb/images/tarta_moras_2.jpg'),
(23, 12, '/ProyectoIngWeb/images/pastel_zanahoria_1.jpg'),
(24, 12, '/ProyectoIngWeb/images/pastel_zanahoria_2.jpg'),
(25, 13, '/ProyectoIngWeb/images/muffin_blueberry_1.jpg'),
(26, 13, '/ProyectoIngWeb/images/muffin_blueberry_2.jpg'),
(27, 14, '/ProyectoIngWeb/images/dona_caramelo_1.jpg'),
(28, 14, '/ProyectoIngWeb/images/dona_caramelo_2.jpg'),
(29, 15, '/ProyectoIngWeb/images/biscuit_arandanos_1.jpg'),
(30, 15, '/ProyectoIngWeb/images/biscuit_arandanos_2.jpg'),
(31, 16, '/ProyectoIngWeb/images/sandwich_pavo_1.jpg'),
(32, 16, '/ProyectoIngWeb/images/sandwich_pavo_2.jpg'),
(33, 17, '/ProyectoIngWeb/images/baguette_suprema_1.jpg'),
(34, 17, '/ProyectoIngWeb/images/baguette_suprema_2.jpg'),
(35, 18, '/ProyectoIngWeb/images/panini_3_quesos_1.jpg'),
(36, 18, '/ProyectoIngWeb/images/panini_3_quesos_2.jpg'),
(37, 19, '/ProyectoIngWeb/images/ensalada_cesar_1.jpg'),
(38, 19, '/ProyectoIngWeb/images/ensalada_cesar_2.jpg'),
(39, 20, '/ProyectoIngWeb/images/envuelto_poblano_1.jpg'),
(40, 20, '/ProyectoIngWeb/images/envuelto_poblano_2.jpg'),
(48, 24, '/ProyectoIngWeb/images/prod_6964fbdd2a4e1.webp');

-- --------------------------------------------------------

--
-- Table structure for table `ingredientes`
--

DROP TABLE IF EXISTS `ingredientes`;
CREATE TABLE `ingredientes` (
  `ING_id` int(11) NOT NULL,
  `ING_nombre` varchar(64) NOT NULL,
  `ING_unidadMedida` varchar(8) NOT NULL,
  `ING_cantidad` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ingredientes`
--

INSERT INTO `ingredientes` (`ING_id`, `ING_nombre`, `ING_unidadMedida`, `ING_cantidad`) VALUES
(1, 'Agua', 'ml', 10000.00),
(2, 'Café molido', 'g', 5000.00),
(3, 'Leche', 'ml', 15000.00),
(4, 'Leche deslactosada', 'ml', 8000.00),
(5, 'Espuma de leche', 'ml', 3000.00),
(6, 'Hielo', 'g', 10000.00),
(7, 'Pulpa de fresa', 'ml', 5000.00),
(8, 'Pulpa de mango', 'ml', 5000.00),
(9, 'Pulpa de limón', 'ml', 3000.00),
(10, 'Pulpa de acai', 'ml', 3000.00),
(11, 'Agua mineral', 'ml', 6000.00),
(12, 'Harina', 'g', 8000.00),
(13, 'Huevo', 'pz', 300.00),
(14, 'Mantequilla', 'g', 4000.00),
(15, 'Queso crema', 'g', 3000.00),
(16, 'Zanahoria', 'g', 3000.00),
(17, 'Arándanos', 'g', 2000.00),
(18, 'Pan artesanal', 'pz', 200.00),
(19, 'Pan baguette', 'pz', 150.00),
(20, 'Pavo', 'g', 4000.00),
(21, 'Queso', 'g', 3500.00),
(22, 'Pechuga ahumada', 'g', 3000.00),
(23, 'Aderezo César', 'ml', 2000.00),
(24, 'Lechuga', 'g', 3000.00),
(25, 'Tomate', 'g', 2000.00),
(26, 'Leche de coco', 'ml', 5000.00),
(27, 'Base refrescante', 'ml', 5000.00),
(28, 'Pulpa de dragonfruit', 'ml', 3000.00),
(29, 'Té negro', 'g', 1500.00),
(30, 'Jugo de limón', 'ml', 2000.00),
(31, 'Crema para batir', 'ml', 2000.00),
(32, 'Moras', 'g', 2000.00),
(33, 'Polvo para hornear', 'g', 1000.00),
(34, 'Sal', 'g', 500.00),
(35, 'Canela', 'g', 500.00),
(36, 'Queso parmesano', 'g', 2000.00),
(37, 'Queso mozzarella', 'g', 2000.00),
(38, 'Chile poblano', 'g', 3000.00),
(39, 'Tortilla de harina', 'pz', 300.00),
(40, 'Crema', 'ml', 2000.00),
(41, 'Chocolate', 'g', 10000.00),
(42, 'Azúcar ', 'g', 8000.00),
(43, 'Caramelo ', 'g', 8000.00);

-- --------------------------------------------------------

--
-- Table structure for table `metodos_pago`
--

DROP TABLE IF EXISTS `metodos_pago`;
CREATE TABLE `metodos_pago` (
  `mp_id` int(11) NOT NULL,
  `mp_emailUsuario` varchar(64) NOT NULL,
  `mp_tipo` enum('TARJETA','TRANSFERENCIA','EFECTIVO') NOT NULL,
  `mp_marca` varchar(20) DEFAULT NULL,
  `mp_ultimos4` char(4) DEFAULT NULL,
  `mp_exp_mes` tinyint(4) DEFAULT NULL,
  `mp_exp_anio` smallint(6) DEFAULT NULL,
  `mp_alias` varchar(64) NOT NULL,
  `mp_predeterminado` tinyint(1) DEFAULT 0,
  `mp_activo` tinyint(1) DEFAULT 1,
  `mp_creado` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pedidos`
--

DROP TABLE IF EXISTS `pedidos`;
CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `folio` varchar(30) DEFAULT NULL,
  `email_usuario` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `ubicacion_colonia` varchar(100) DEFAULT NULL,
  `ubicacion_ciudad` varchar(100) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `estado_pago` varchar(50) DEFAULT NULL,
  `referencia` varchar(50) DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `estado` varchar(50) DEFAULT 'recibido',
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pedidos`
--

INSERT INTO `pedidos` (`id`, `folio`, `email_usuario`, `telefono`, `direccion`, `ubicacion_colonia`, `ubicacion_ciudad`, `total`, `metodo_pago`, `estado_pago`, `referencia`, `notas`, `estado`, `fecha`) VALUES
(1, NULL, '', '5549777476', 'bosquesde viena Mz 2 Lt 4, 3', 'Bosques de Viena', 'Estado de Mexico', 60.00, 'Efectivo', 'Pagado', '', '', 'recibido', '2025-12-18 02:05:23'),
(2, NULL, '', '5549777476', 'bosquesde viena Mz 2 Lt 4, 3', 'Bosques de Viena', 'Estado de Mexico', 30.25, 'Tarjeta', 'Pagado', '**** 7894', '', 'recibido', '2025-12-18 02:12:31'),
(3, 'JAV-2025-000003', '', '5549777476', 'bosquesde viena Mz 2 Lt 4, 3', 'Bosques de Viena', 'Estado de Mexico', 38.00, 'Transferencia', 'Pagado', '45789523', 'caliente', 'recibido', '2025-12-18 02:56:48');

-- --------------------------------------------------------

--
-- Table structure for table `pedido_detalle`
--

DROP TABLE IF EXISTS `pedido_detalle`;
CREATE TABLE `pedido_detalle` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pedido_detalle`
--

INSERT INTO `pedido_detalle` (`id`, `pedido_id`, `producto_id`, `cantidad`, `precio`) VALUES
(1, 1, 7, 1, 60.00),
(2, 2, 2, 1, 55.00),
(3, 3, 14, 1, 38.00);

-- --------------------------------------------------------

--
-- Table structure for table `productos_bebidas`
--

DROP TABLE IF EXISTS `productos_bebidas`;
CREATE TABLE `productos_bebidas` (
  `id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `estrellas` int(11) DEFAULT 5,
  `unidad_medida` varchar(10) NOT NULL DEFAULT 'pz',
  `cantidad` decimal(10,3) NOT NULL DEFAULT 1.000
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `productos_bebidas`
--

INSERT INTO `productos_bebidas` (`id`, `categoria_id`, `nombre`, `descripcion`, `precio`, `estrellas`, `unidad_medida`, `cantidad`) VALUES
(1, 1, 'Espresso', 'Café concentrado intenso.', 45.00, 5, 'pz', 1.000),
(2, 1, 'Latte', 'Café con leche vaporizada.', 55.00, 4, 'pz', 1.000),
(3, 1, 'Mocha', 'Café con chocolate y leche vaporizada.', 58.00, 5, 'pz', 1.000),
(4, 1, 'Cappuccino', 'Café con espuma de leche.', 52.00, 5, 'pz', 1.000),
(5, 1, 'Chocolate Caliente', 'Bebida dulce de cacao.', 48.00, 4, 'pz', 1.000),
(6, 2, 'Pink Drink', 'Bebida refrescante de coco con fresa.', 62.00, 5, 'pz', 1.000),
(7, 2, 'Strawberry Açaí', 'Refresco de fresa con notas de açaí.', 60.00, 5, 'pz', 1.000),
(8, 2, 'Mango Dragonfruit Refresher', 'Bebida tropical de mango y dragonfruit.', 63.00, 5, 'pz', 1.000),
(9, 2, 'Lemon Black Tea', 'Té negro frío con limón.', 52.00, 4, 'pz', 1.000),
(10, 2, 'Cappuccino Helado', 'Cappuccino servido con hielo.', 58.00, 4, 'pz', 1.000),
(11, 3, 'Tarta de Moras', 'Tarta dulce rellena de moras naturales.', 58.00, 5, 'pz', 1.000),
(12, 3, 'Pastel de Zanahoria', 'Pastel esponjoso con zanahoria y especias.', 62.00, 5, 'pz', 1.000),
(13, 3, 'Muffin Blueberry', 'Muffin suave con arándanos.', 45.00, 4, 'pz', 1.000),
(14, 3, 'Dona de Caramelo', 'Dona glaseada con caramelo.', 38.00, 4, 'pz', 1.000),
(15, 3, 'Biscuit de Arándano', 'Biscuit horneado con arándanos.', 42.00, 4, 'pz', 1.000),
(16, 4, 'Sándwich de Pavo', 'Pan artesanal con pavo y queso.', 65.00, 5, 'pz', 1.000),
(17, 4, 'Baguette Suprema', 'Baguette con lomo canadiense, pechuga ahumada, aderezo.', 68.00, 4, 'pz', 1.000),
(18, 4, 'Panini 3 Quesos', 'Panini con grilled cheese y parmesano.', 70.00, 5, 'pz', 1.000),
(19, 4, 'Ensalada César', 'Ensalada fresca con aderezo.', 58.00, 4, 'pz', 1.000),
(20, 4, 'Envuelto Poblano', 'Relleno de queso crema y rajas poblanas.', 55.00, 4, 'pz', 1.000),
(24, 7, 'Chimichanga Ximenesca', 'Platillo salido del genio culinario de Ximena.', 50.00, 5, 'piezas', 3.000);

-- --------------------------------------------------------

--
-- Table structure for table `producto_ingrediente`
--

DROP TABLE IF EXISTS `producto_ingrediente`;
CREATE TABLE `producto_ingrediente` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `ingrediente_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `producto_ingrediente`
--

INSERT INTO `producto_ingrediente` (`id`, `producto_id`, `ingrediente_id`, `cantidad`) VALUES
(4, 1, 2, 18.00),
(5, 1, 1, 30.00),
(7, 2, 2, 18.00),
(8, 2, 1, 30.00),
(9, 2, 3, 200.00),
(10, 3, 2, 18.00),
(11, 3, 1, 30.00),
(12, 3, 3, 150.00),
(13, 3, 41, 25.00),
(17, 4, 2, 18.00),
(18, 4, 1, 30.00),
(19, 4, 3, 120.00),
(20, 4, 5, 60.00),
(24, 5, 3, 200.00),
(25, 5, 41, 30.00),
(26, 5, 42, 10.00),
(27, 6, 7, 120.00),
(28, 6, 26, 150.00),
(29, 6, 6, 150.00),
(30, 7, 7, 120.00),
(31, 7, 10, 120.00),
(32, 7, 1, 150.00),
(33, 7, 6, 150.00),
(37, 8, 8, 120.00),
(38, 8, 28, 100.00),
(39, 8, 1, 150.00),
(40, 8, 6, 150.00),
(44, 9, 29, 5.00),
(45, 9, 1, 200.00),
(46, 9, 30, 30.00),
(47, 9, 42, 10.00),
(48, 9, 6, 150.00),
(51, 10, 2, 18.00),
(52, 10, 1, 30.00),
(53, 10, 3, 120.00),
(54, 10, 6, 150.00),
(58, 11, 12, 120.00),
(59, 11, 13, 2.00),
(60, 11, 14, 60.00),
(61, 11, 42, 50.00),
(62, 11, 32, 80.00),
(63, 11, 33, 5.00),
(65, 12, 12, 150.00),
(66, 12, 13, 3.00),
(67, 12, 14, 80.00),
(68, 12, 42, 70.00),
(69, 12, 16, 100.00),
(70, 12, 35, 3.00),
(71, 12, 34, 2.00),
(72, 12, 33, 6.00),
(80, 13, 12, 80.00),
(81, 13, 13, 1.00),
(82, 13, 14, 40.00),
(83, 13, 42, 40.00),
(84, 13, 17, 50.00),
(85, 13, 33, 5.00),
(87, 14, 12, 90.00),
(88, 14, 13, 1.00),
(89, 14, 14, 40.00),
(90, 14, 42, 30.00),
(94, 14, 43, 20.00),
(95, 16, 18, 2.00),
(96, 16, 20, 60.00),
(97, 16, 21, 40.00),
(98, 16, 24, 30.00),
(99, 16, 25, 20.00),
(102, 17, 19, 1.00),
(103, 17, 22, 60.00),
(104, 17, 21, 40.00),
(105, 17, 24, 30.00),
(109, 15, 12, 100.00),
(110, 15, 13, 1.00),
(111, 15, 14, 50.00),
(112, 15, 42, 40.00),
(113, 15, 17, 60.00),
(114, 15, 33, 5.00),
(115, 15, 34, 2.00),
(116, 18, 18, 1.00),
(117, 18, 37, 30.00),
(118, 18, 36, 30.00),
(119, 18, 21, 30.00),
(123, 19, 24, 100.00),
(124, 19, 22, 80.00),
(125, 19, 36, 20.00),
(126, 19, 23, 30.00),
(130, 20, 39, 1.00),
(131, 20, 38, 80.00),
(132, 20, 21, 40.00),
(133, 20, 40, 30.00);

-- --------------------------------------------------------

--
-- Table structure for table `promociones`
--

DROP TABLE IF EXISTS `promociones`;
CREATE TABLE `promociones` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `descuento` int(11) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promociones`
--

INSERT INTO `promociones` (`id`, `producto_id`, `titulo`, `descripcion`, `descuento`, `activo`) VALUES
(1, 1, 'Espresso Clásico', 'Disfruta nuestro espresso intenso a precio especial.', 35, 1),
(2, 2, 'Latte Cremoso', 'Latte con leche vaporizada con descuento por tiempo limitado.', 45, 1),
(3, 3, 'Mocha Especial', 'Mocha con chocolate y leche a precio promocional.', 48, 1),
(4, 6, 'Pink Drink Fresh', 'Bebida refrescante de coco y fresa en promoción.', 52, 1),
(5, 13, 'Muffin Blueberry', 'Muffin de arándanos recién horneado con descuento.', 35, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ubicaciones`
--

DROP TABLE IF EXISTS `ubicaciones`;
CREATE TABLE `ubicaciones` (
  `ubi_id` int(11) NOT NULL,
  `ubi_emailUsuario` varchar(64) NOT NULL,
  `ubi_alias` varchar(64) DEFAULT NULL,
  `ubi_colonia` varchar(128) NOT NULL,
  `ubi_ciudad` varchar(128) NOT NULL,
  `ubi_direccion` varchar(255) NOT NULL,
  `ubi_latitud` decimal(10,8) DEFAULT NULL,
  `ubi_longitud` decimal(11,8) DEFAULT NULL,
  `ubi_predeterminada` tinyint(1) DEFAULT 0,
  `ubi_creada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ubicaciones`
--

INSERT INTO `ubicaciones` (`ubi_id`, `ubi_emailUsuario`, `ubi_alias`, `ubi_colonia`, `ubi_ciudad`, `ubi_direccion`, `ubi_latitud`, `ubi_longitud`, `ubi_predeterminada`, `ubi_creada`) VALUES
(38, 'someone@example.com', 'Casa', 'Villa de las Flores', 'Coacalco de Berriozábal', 'Calle Acacias, Coacalco de Berriozábal, Estado de México, 55710, México', NULL, NULL, 0, '2026-01-05 07:33:08'),
(39, 'someone@example.com', 'Escuela', 'Colonia Zacatenco Lindavista', 'Ciudad de México', 'Bubbles Coffe, 2580, Avenida Instituto Politécnico Nacional, Colonia Zacatenco Lindavista, Ciudad de México, Gustavo A. Madero, Ciudad de México, 07340, México', NULL, NULL, 1, '2026-01-06 19:52:24');

-- --------------------------------------------------------

--
-- Table structure for table `usuariosweb`
--

DROP TABLE IF EXISTS `usuariosweb`;
CREATE TABLE `usuariosweb` (
  `userWEB_emailID` varchar(64) NOT NULL,
  `userWEB_nombre` varchar(64) NOT NULL,
  `userWEB_password` varchar(255) NOT NULL,
  `userWEB_tipo` enum('A','C','E') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuariosweb`
--

INSERT INTO `usuariosweb` (`userWEB_emailID`, `userWEB_nombre`, `userWEB_password`, `userWEB_tipo`) VALUES
('empleado1@javacoffee.com', 'Uriel Oswaldo Arreola Fernández', '$2y$10$NSZi4rBc3ZKm/cgfCUnv1eo46dc82uBmLMBeQ1sdUFdFSbUaNE82a', 'E'),
('hazielcanelo16@gmail.com', 'Haziel Osvaldo Rodriguez Vega', '$2y$10$mVPbjqSy8Yx9CDwkM3lfCehCXv3QuLJKiY.qKLcedNn2el7A.53m2', 'C'),
('master@admin.com', 'Diana Ximena Ruiz Cambrano', '$2y$10$uCu55aAFK9SY2KvD.xn.5OXFKzWEkf9XS1UHMKt80xtXmc9py.8s.', 'A'),
('someone@example.com', 'Saúl Ricardo Arreola Fernández', '$2y$10$izGU4LH9rLE9tpmrr3VHpeoxb/shne6f/lf7dtrbs3BOfsqQaiTR6', 'C');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `imagenes_comida`
--
ALTER TABLE `imagenes_comida`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indexes for table `ingredientes`
--
ALTER TABLE `ingredientes`
  ADD PRIMARY KEY (`ING_id`),
  ADD UNIQUE KEY `UQ_ingredientes_nombre` (`ING_nombre`);

--
-- Indexes for table `metodos_pago`
--
ALTER TABLE `metodos_pago`
  ADD PRIMARY KEY (`mp_id`),
  ADD KEY `idx_mp_usuario` (`mp_emailUsuario`),
  ADD KEY `idx_mp_predeterminado` (`mp_emailUsuario`,`mp_predeterminado`);

--
-- Indexes for table `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `folio` (`folio`);

--
-- Indexes for table `pedido_detalle`
--
ALTER TABLE `pedido_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indexes for table `productos_bebidas`
--
ALTER TABLE `productos_bebidas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indexes for table `producto_ingrediente`
--
ALTER TABLE `producto_ingrediente`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `producto_id` (`producto_id`,`ingrediente_id`),
  ADD KEY `fk_pi_ingrediente` (`ingrediente_id`);

--
-- Indexes for table `promociones`
--
ALTER TABLE `promociones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `promos` (`producto_id`);

--
-- Indexes for table `ubicaciones`
--
ALTER TABLE `ubicaciones`
  ADD PRIMARY KEY (`ubi_id`),
  ADD UNIQUE KEY `uq_usuario_direccion` (`ubi_emailUsuario`,`ubi_direccion`,`ubi_colonia`,`ubi_ciudad`),
  ADD UNIQUE KEY `uq_usuario_alias` (`ubi_emailUsuario`,`ubi_alias`),
  ADD UNIQUE KEY `uq_usuario_gps` (`ubi_emailUsuario`,`ubi_latitud`,`ubi_longitud`);

--
-- Indexes for table `usuariosweb`
--
ALTER TABLE `usuariosweb`
  ADD PRIMARY KEY (`userWEB_emailID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `imagenes_comida`
--
ALTER TABLE `imagenes_comida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `ingredientes`
--
ALTER TABLE `ingredientes`
  MODIFY `ING_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `metodos_pago`
--
ALTER TABLE `metodos_pago`
  MODIFY `mp_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pedido_detalle`
--
ALTER TABLE `pedido_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `productos_bebidas`
--
ALTER TABLE `productos_bebidas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `producto_ingrediente`
--
ALTER TABLE `producto_ingrediente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- AUTO_INCREMENT for table `promociones`
--
ALTER TABLE `promociones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ubicaciones`
--
ALTER TABLE `ubicaciones`
  MODIFY `ubi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `imagenes_comida`
--
ALTER TABLE `imagenes_comida`
  ADD CONSTRAINT `imagenes_comida_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos_bebidas` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `metodos_pago`
--
ALTER TABLE `metodos_pago`
  ADD CONSTRAINT `fk_metodos_pago_usuario` FOREIGN KEY (`mp_emailUsuario`) REFERENCES `usuariosweb` (`userWEB_emailID`) ON DELETE CASCADE;

--
-- Constraints for table `pedido_detalle`
--
ALTER TABLE `pedido_detalle`
  ADD CONSTRAINT `pedido_detalle_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pedido_detalle_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos_bebidas` (`id`);

--
-- Constraints for table `productos_bebidas`
--
ALTER TABLE `productos_bebidas`
  ADD CONSTRAINT `productos_bebidas_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `producto_ingrediente`
--
ALTER TABLE `producto_ingrediente`
  ADD CONSTRAINT `fk_pi_ingrediente` FOREIGN KEY (`ingrediente_id`) REFERENCES `ingredientes` (`ING_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pi_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos_bebidas` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `promociones`
--
ALTER TABLE `promociones`
  ADD CONSTRAINT `promos` FOREIGN KEY (`producto_id`) REFERENCES `productos_bebidas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ubicaciones`
--
ALTER TABLE `ubicaciones`
  ADD CONSTRAINT `fk_ubicaciones_usuario` FOREIGN KEY (`ubi_emailUsuario`) REFERENCES `usuariosweb` (`userWEB_emailID`) ON DELETE CASCADE;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
