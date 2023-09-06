-- --------------------------------------------------------
-- Hoszt:                        127.0.0.1
-- Szerver verzió:               10.4.27-MariaDB - mariadb.org binary distribution
-- Szerver OS:                   Win64
-- HeidiSQL Verzió:              12.3.0.6589
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Adatbázis struktúra mentése a orderdb.
DROP DATABASE IF EXISTS `orderdb`;
CREATE DATABASE IF NOT EXISTS `orderdb` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `orderdb`;

-- Struktúra mentése tábla orderdb. log
DROP TABLE IF EXISTS `log`;
CREATE TABLE IF NOT EXISTS `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `crud` varchar(10) NOT NULL,
  `table` varchar(50) NOT NULL,
  `msg` text DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=471 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tábla adatainak mentése orderdb.log: ~0 rows (hozzávetőleg)

-- Struktúra mentése tábla orderdb. orders
DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` varchar(25) NOT NULL,
  `title` varchar(250) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `deadline_date` timestamp NULL DEFAULT NULL,
  `close_date` timestamp NULL DEFAULT NULL,
  `location` int(11) DEFAULT NULL,
  `carring` int(11) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `note` text DEFAULT NULL,
  `files` text DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `location` (`location`),
  KEY `FK_orders_users` (`carring`),
  CONSTRAINT `FK_orders_users` FOREIGN KEY (`carring`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tábla adatainak mentése orderdb.orders: ~0 rows (hozzávetőleg)

-- Struktúra mentése tábla orderdb. status
DROP TABLE IF EXISTS `status`;
CREATE TABLE IF NOT EXISTS `status` (
  `order_id` varchar(25) NOT NULL,
  `name` varchar(25) NOT NULL,
  `line` int(11) NOT NULL,
  `step` int(11) NOT NULL,
  `user` int(11) DEFAULT NULL,
  `date` timestamp NULL DEFAULT NULL,
  `done` bit(1) NOT NULL DEFAULT b'0',
  `step_span` int(11) NOT NULL DEFAULT 1,
  `req` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`order_id`,`name`),
  UNIQUE KEY `order_id_line_step` (`order_id`,`line`,`step`),
  KEY `FK_status_users` (`user`),
  CONSTRAINT `FK_status_orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `FK_status_users` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tábla adatainak mentése orderdb.status: ~0 rows (hozzávetőleg)

-- Struktúra mentése tábla orderdb. users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `skills` text DEFAULT NULL,
  `role` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tábla adatainak mentése orderdb.users: ~1 rows (hozzávetőleg)
INSERT IGNORE INTO `users` (`id`, `name`, `skills`, `role`) VALUES
	(1, 'Admin', 'Tűzés', 2);

-- Struktúra mentése trigger orderdb. orders_after_insert
DROP TRIGGER IF EXISTS `orders_after_insert`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `orders_after_insert` AFTER INSERT ON `orders` FOR EACH ROW BEGIN
INSERT INTO `log`(`crud`,`table`,`msg`) VALUES ('create','orders',CONCAT('Új Rendelés hozzáadva: ',NEW.order_id));
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Struktúra mentése trigger orderdb. orders_after_update
DROP TRIGGER IF EXISTS `orders_after_update`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `orders_after_update` AFTER UPDATE ON `orders` FOR EACH ROW BEGIN
INSERT INTO `log`(`crud`,`table`,`msg`) VALUES ('update','orders',CONCAT('Rendelés szerkesztve: ',NEW.order_id));
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Struktúra mentése trigger orderdb. status_after_insert
DROP TRIGGER IF EXISTS `status_after_insert`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `status_after_insert` AFTER INSERT ON `status` FOR EACH ROW BEGIN
INSERT INTO `log`(`crud`,`table`,`msg`) VALUES ('create','status',CONCAT('Új Statusz hozzáadva: ',NEW.order_id));
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Struktúra mentése trigger orderdb. status_after_update
DROP TRIGGER IF EXISTS `status_after_update`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `status_after_update` AFTER UPDATE ON `status` FOR EACH ROW BEGIN
INSERT INTO `log`(`crud`,`table`,`msg`) VALUES ('update','status',CONCAT('Statusz modosítva: ',NEW.order_id));
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
