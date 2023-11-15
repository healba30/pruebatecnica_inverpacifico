-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-11-2023 a las 07:06:03
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ventas_sucursales`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `empleado_id` int(11) NOT NULL,
  `nombre_empleado` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`empleado_id`, `nombre_empleado`) VALUES
(1, 'Juan Pérez'),
(2, 'María García'),
(3, 'Carlos Rodríguez'),
(4, 'Laura Martínez'),
(5, 'Pedro López');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metas_incentivos`
--

CREATE TABLE `metas_incentivos` (
  `meta_id` int(11) NOT NULL,
  `empleado_id` int(11) NOT NULL,
  `meta_cantidad` int(11) NOT NULL,
  `filtro` varchar(2) NOT NULL,
  `incentivo` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `metas_incentivos`
--

INSERT INTO `metas_incentivos` (`meta_id`, `empleado_id`, `meta_cantidad`, `filtro`, `incentivo`) VALUES
(1, 1, 100, '>=', 50.00),
(2, 2, 150, '>', 75.00),
(3, 3, 120, '=', 60.00),
(4, 4, 200, '<=', 100.00),
(5, 5, 180, '<', 90.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `producto_id` int(11) NOT NULL,
  `nombre_producto` varchar(255) DEFAULT NULL,
  `precio_unitario` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`producto_id`, `nombre_producto`, `precio_unitario`) VALUES
(1, 'Producto A', 20.50),
(2, 'Producto B', 15.75),
(3, 'Producto C', 30.00),
(4, 'Producto D', 25.25),
(5, 'Producto E', 18.90);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sedes`
--

CREATE TABLE `sedes` (
  `sede_id` int(11) NOT NULL,
  `ubicacion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sedes`
--

INSERT INTO `sedes` (`sede_id`, `ubicacion`) VALUES
(1, 'Sede norte'),
(2, 'Sede sur'),
(3, 'Sede oriente'),
(4, 'Sede occidente'),
(5, 'Sede central');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `venta_id` int(11) NOT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `sede_id` int(11) DEFAULT NULL,
  `empleado_id` int(11) DEFAULT NULL,
  `fecha_venta` date DEFAULT NULL,
  `cantidad_vendida` int(11) DEFAULT NULL,
  `monto_total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`venta_id`, `producto_id`, `sede_id`, `empleado_id`, `fecha_venta`, `cantidad_vendida`, `monto_total`) VALUES
(1, 1, 1, 1, '2023-11-01', 5, 102.50),
(2, 2, 2, 2, '2023-11-02', 3, 47.25),
(3, 3, 3, 3, '2023-11-03', 8, 240.00),
(4, 4, 4, 4, '2023-11-04', 2, 50.50),
(5, 5, 5, 5, '2023-11-05', 6, 113.40);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`empleado_id`);

--
-- Indices de la tabla `metas_incentivos`
--
ALTER TABLE `metas_incentivos`
  ADD PRIMARY KEY (`meta_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`producto_id`);

--
-- Indices de la tabla `sedes`
--
ALTER TABLE `sedes`
  ADD PRIMARY KEY (`sede_id`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`venta_id`),
  ADD KEY `producto_id` (`producto_id`),
  ADD KEY `sede_id` (`sede_id`),
  ADD KEY `empleado_id` (`empleado_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `empleado_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `metas_incentivos`
--
ALTER TABLE `metas_incentivos`
  MODIFY `meta_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `producto_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `sedes`
--
ALTER TABLE `sedes`
  MODIFY `sede_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `venta_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`producto_id`),
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`sede_id`) REFERENCES `sedes` (`sede_id`),
  ADD CONSTRAINT `ventas_ibfk_3` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`empleado_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
