-- phpMyAdmin SQL Dump
-- version 3.4.10.1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 16-05-2012 a las 22:56:50
-- Versión del servidor: 5.1.62
-- Versión de PHP: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `freestat_web`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clients`
--

CREATE TABLE IF NOT EXISTS `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(255) NOT NULL,
  `hostname` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `last_connection` int(11) NOT NULL,
  `requests` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Volcado de datos para la tabla `clients`
--

INSERT INTO `clients` (`id`, `ip`, `hostname`, `status`, `last_connection`, `requests`) VALUES
(1, '123.123.123.123', 'server2.yoursite.com', 0, 1329628440, 5),
(3, '111.111.111.111', 'otorion.house.net', 1, 1329631818, 1),
(4, '161.67.27.109', 'despacho01.uclm.es', 1, 1329764955, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `client_widgets`
--

CREATE TABLE IF NOT EXISTS `client_widgets` (
  `client_id` int(11) NOT NULL,
  `widget_id` int(11) NOT NULL,
  `widget_data` int(11) NOT NULL,
  PRIMARY KEY (`client_id`,`widget_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `user` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `user`, `password`, `email`) VALUES
(0, 'root', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'admin@root.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `widgets`
--

CREATE TABLE IF NOT EXISTS `widgets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Volcado de datos para la tabla `widgets`
--

INSERT INTO `widgets` (`id`, `name`, `description`, `image`) VALUES
(1, 'browser', 'Create a integrated browser with a default page', ''),
(2, 'logoarea', 'Render a image as logo', ''),
(3, 'titledisplay', 'Print a text as title', ''),
(4, 'menuactionsarea', 'Create a custom menu', ''),
(5, 'mountdetector', 'Monitor and detect USB devices', ''),
(6, 'mountinfo', 'Gets info from USB devices', ''),
(7, 'videoarea', 'Render a video', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
