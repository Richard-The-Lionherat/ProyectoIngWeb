SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `ingweb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ingweb`;

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `imagen` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `imagenes_comida` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `ingredientes` (
  `ING_id` int(11) NOT NULL,
  `ING_nombre` varchar(64) NOT NULL,
  `ING_unidadMedida` varchar(8) NOT NULL,
  `ING_cantidad` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `pedido_detalle` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `producto_ingrediente` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `ingrediente_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `promociones` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `descuento` int(11) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `usuariosweb` (
  `userWEB_emailID` varchar(64) NOT NULL,
  `userWEB_nombre` varchar(64) NOT NULL,
  `userWEB_password` varchar(255) NOT NULL,
  `userWEB_tipo` enum('A','C','E') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `imagenes_comida`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`);

ALTER TABLE `ingredientes`
  ADD PRIMARY KEY (`ING_id`),
  ADD UNIQUE KEY `UQ_ingredientes_nombre` (`ING_nombre`);

ALTER TABLE `metodos_pago`
  ADD PRIMARY KEY (`mp_id`),
  ADD KEY `idx_mp_usuario` (`mp_emailUsuario`),
  ADD KEY `idx_mp_predeterminado` (`mp_emailUsuario`,`mp_predeterminado`);

ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `folio` (`folio`);

ALTER TABLE `pedido_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `producto_id` (`producto_id`);

ALTER TABLE `productos_bebidas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`);

ALTER TABLE `producto_ingrediente`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `producto_id` (`producto_id`,`ingrediente_id`),
  ADD KEY `fk_pi_ingrediente` (`ingrediente_id`);

ALTER TABLE `promociones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `promos` (`producto_id`);

ALTER TABLE `ubicaciones`
  ADD PRIMARY KEY (`ubi_id`),
  ADD UNIQUE KEY `uq_usuario_direccion` (`ubi_emailUsuario`,`ubi_direccion`,`ubi_colonia`,`ubi_ciudad`),
  ADD UNIQUE KEY `uq_usuario_alias` (`ubi_emailUsuario`,`ubi_alias`),
  ADD UNIQUE KEY `uq_usuario_gps` (`ubi_emailUsuario`,`ubi_latitud`,`ubi_longitud`);

ALTER TABLE `usuariosweb`
  ADD PRIMARY KEY (`userWEB_emailID`);


ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `imagenes_comida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ingredientes`
  MODIFY `ING_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `metodos_pago`
  MODIFY `mp_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `pedido_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `productos_bebidas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `producto_ingrediente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `promociones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ubicaciones`
  MODIFY `ubi_id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `imagenes_comida`
  ADD CONSTRAINT `imagenes_comida_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos_bebidas` (`id`) ON DELETE CASCADE;

ALTER TABLE `metodos_pago`
  ADD CONSTRAINT `fk_metodos_pago_usuario` FOREIGN KEY (`mp_emailUsuario`) REFERENCES `usuariosweb` (`userWEB_emailID`) ON DELETE CASCADE;

ALTER TABLE `pedido_detalle`
  ADD CONSTRAINT `pedido_detalle_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pedido_detalle_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos_bebidas` (`id`);

ALTER TABLE `productos_bebidas`
  ADD CONSTRAINT `productos_bebidas_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE;

ALTER TABLE `producto_ingrediente`
  ADD CONSTRAINT `fk_pi_ingrediente` FOREIGN KEY (`ingrediente_id`) REFERENCES `ingredientes` (`ING_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pi_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos_bebidas` (`id`) ON DELETE CASCADE;

ALTER TABLE `promociones`
  ADD CONSTRAINT `promos` FOREIGN KEY (`producto_id`) REFERENCES `productos_bebidas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `ubicaciones`
  ADD CONSTRAINT `fk_ubicaciones_usuario` FOREIGN KEY (`ubi_emailUsuario`) REFERENCES `usuariosweb` (`userWEB_emailID`) ON DELETE CASCADE;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
