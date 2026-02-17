-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Фев 17 2026 г., 20:03
-- Версия сервера: 5.7.39
-- Версия PHP: 8.0.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `CourseWork`
--

-- --------------------------------------------------------

--
-- Структура таблицы `action_logs`
--

CREATE TABLE `action_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `api_keys`
--

CREATE TABLE `api_keys` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `api_key` varchar(100) NOT NULL,
  `secret_key` varchar(100) NOT NULL,
  `status` varchar(20) DEFAULT 'active',
  `permissions` text,
  `last_used` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NULL DEFAULT NULL,
  `revoked_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `api_keys`
--

INSERT INTO `api_keys` (`id`, `name`, `api_key`, `secret_key`, `status`, `permissions`, `last_used`, `created_at`, `expires_at`, `revoked_at`) VALUES
(1, 'api_keys', 'sk_live_9f91e34cecf030985f13e1eeae02e6b3', 'sk_305308cd266a815f26aebb00613a6e2e9d9028b9946fe003', 'active', 'read,write', NULL, '2026-01-11 12:27:36', '2027-01-11 12:27:36', NULL),
(2, 'secret', 'sk_live_0530805657771205c63ddf970a3b4365', 'sk_3d6b3db0920b13298822604a87e2e7ea83be75a7d0d363ea', 'revoked', 'read,write', NULL, '2026-01-11 14:24:16', '2027-01-11 14:24:16', '2026-01-28 18:27:17'),
(4, 'Лал-Авто (user2)', 'sk_live_f03ea03f574f8afda76d8ea3cf6ee7be', 'sk_0615af086cdd350ca4ce64e4eb7363cbdee715d124c2aa86', 'active', 'read,write,products,orders', NULL, '2026-01-28 18:07:43', '2027-01-28 18:07:43', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `backup_logs`
--

CREATE TABLE `backup_logs` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `source` varchar(50) DEFAULT 'manual',
  `action` varchar(50) DEFAULT 'backup',
  `error_message` text,
  `downloads` int(11) DEFAULT '0',
  `last_download` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `backup_logs`
--

INSERT INTO `backup_logs` (`id`, `filename`, `file_size`, `status`, `source`, `action`, `error_message`, `downloads`, `last_download`, `created_at`) VALUES
(9, 'backup_2026-01-11_15-45-53.sql', 28680, 'success', 'manual', 'backup', NULL, 0, NULL, '2026-01-11 12:45:53'),
(10, 'uploaded_backup_backup_2026-01-11_15-45-53_2026-01-11_15-46-08.sql', 28680, 'uploaded', 'user_upload', 'backup', NULL, 0, NULL, '2026-01-11 12:46:08');

-- --------------------------------------------------------

--
-- Структура таблицы `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_type` varchar(50) DEFAULT NULL COMMENT 'part - запчасть, oil - масло, accessory - аксессуар',
  `product_name` varchar(255) NOT NULL,
  `product_image` varchar(255) DEFAULT 'no-image.png',
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `product_type`, `product_name`, `product_image`, `price`, `quantity`, `created_at`, `updated_at`) VALUES
(8, 2, 2, 'part', 'Тормозные колодки Audi A6 C7', 'uploads/products/696392655986c.png', '3890.00', 2, '2026-02-13 16:19:59', '2026-02-14 15:51:46'),
(9, 2, 3, 'part', 'Свечи зажигания Audi Q5 2.0 TDI', 'uploads/products/696392655986c.png', '850.00', 2, '2026-02-13 16:20:00', '2026-02-13 16:25:41'),
(12, 2, 51, 'oil', 'Liqui Moly Special Tec AA 5W-30', 'uploads/products/696392655986c.png', '4210.00', 1, '2026-02-13 16:23:04', '2026-02-13 16:23:04'),
(20, 3, 2, 'part', 'Тормозные колодки Audi A6 C7', 'uploads/products/696392655986c.png', '3890.00', 1, '2026-02-13 16:28:12', '2026-02-13 16:28:12'),
(21, 3, 12, 'part', 'Тормозные колодки BMW 1 series F20', 'uploads/products/696392655986c.png', '5200.00', 1, '2026-02-13 16:28:13', '2026-02-13 16:28:13'),
(22, 3, 50, 'oil', 'Mobil Super 3000 X1 5W-40', 'uploads/products/696392655986c.png', '3450.00', 1, '2026-02-13 16:28:22', '2026-02-13 16:28:22'),
(23, 3, 53, 'oil', 'Total Quartz 9000 5W-40', 'uploads/products/696392655986c.png', '3650.00', 1, '2026-02-13 16:28:24', '2026-02-13 16:28:24'),
(24, 3, 104, 'accessory', 'Камера заднего вида', 'uploads/products/696392655986c.png', '4290.00', 1, '2026-02-13 16:28:28', '2026-02-13 16:28:28');

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Запчасти', 'Автомобильные запчасти', '2026-01-06 17:15:45'),
(2, 'Масла', 'Моторные и трансмиссионные масла', '2026-01-06 17:15:45'),
(3, 'Аксессуары', 'Аксессуары для автомобилей', '2026-01-06 17:15:45');

-- --------------------------------------------------------

--
-- Структура таблицы `category_products`
--

CREATE TABLE `category_products` (
  `id` int(11) NOT NULL,
  `category_type` varchar(50) NOT NULL COMMENT 'antifreeze, brake-fluid, cooling-fluid, power-steering, special-fluid, kit, transmission-oil, motor-oil',
  `title` varchar(255) NOT NULL,
  `art` varchar(100) DEFAULT NULL,
  `volume` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock` tinyint(1) DEFAULT '1',
  `hit` tinyint(1) DEFAULT '0',
  `brand` varchar(100) DEFAULT NULL,
  `image` varchar(500) DEFAULT 'uploads/products/696392655986c.png',
  `type` varchar(100) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `viscosity` varchar(50) DEFAULT NULL,
  `standard` varchar(50) DEFAULT NULL,
  `application` varchar(100) DEFAULT NULL,
  `freezing` varchar(20) DEFAULT NULL,
  `dry_boil` varchar(20) DEFAULT NULL,
  `wet_boil` varchar(20) DEFAULT NULL,
  `contents` text,
  `api` varchar(50) DEFAULT NULL,
  `acea` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `category_products`
--

INSERT INTO `category_products` (`id`, `category_type`, `title`, `art`, `volume`, `price`, `stock`, `hit`, `brand`, `image`, `type`, `color`, `viscosity`, `standard`, `application`, `freezing`, `dry_boil`, `wet_boil`, `contents`, `api`, `acea`, `created_at`, `updated_at`) VALUES
(1, 'antifreeze', 'Motul Inugel Optimal', 'ANTI001', '2 л', '1100.00', 1, 1, 'Motul', 'uploads/products/696392655986c.png', 'G12', 'Красный', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(2, 'antifreeze', 'Shell Zone Ultra', 'SHELL-AF01', '5 л', '1650.00', 1, 0, 'Shell', 'uploads/products/696392655986c.png', 'G13', 'Фиолетовый', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(3, 'antifreeze', 'Liqui Moly Kuhlerfrostschutz', 'LM-AF001', '1.5 л', '1250.00', 1, 1, 'Liqui Moly', 'uploads/products/696392655986c.png', 'G12++', 'Синий', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(4, 'antifreeze', 'Castrol Radicool SF', 'CAST-AF01', '5 л', '1890.00', 1, 0, 'Castrol', 'uploads/products/696392655986c.png', 'G11', 'Зеленый', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(5, 'antifreeze', 'Total Glacelf Auto Supra', 'TOTAL-AF01', '5 л', '1450.00', 0, 0, 'Total', 'uploads/products/696392655986c.png', 'G12', 'Синий', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(6, 'antifreeze', 'Mobil Antifreeze Advanced', 'MOB-AF001', '1 л', '680.00', 1, 1, 'Mobil', 'uploads/products/696392655986c.png', 'G12++', 'Оранжевый', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(7, 'antifreeze', 'Febi Bilstein Antifreeze', 'FEBI-AF01', '1.5 л', '850.00', 1, 0, 'Febi', 'uploads/products/696392655986c.png', 'G12', 'Синий', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(8, 'antifreeze', 'Ravenol Original Green', 'RAV-AF001', '1.5 л', '920.00', 1, 0, 'Ravenol', 'uploads/products/696392655986c.png', 'G11', 'Зеленый', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(9, 'antifreeze', 'SWAG Antifreeze', 'SWAG-AF01', '5 л', '1580.00', 1, 1, 'SWAG', 'uploads/products/696392655986c.png', 'G12+', 'Красный', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(10, 'antifreeze', 'Hepu Antifreeze', 'HEPU-AF01', '1.5 л', '780.00', 1, 0, 'Hepu', 'uploads/products/696392655986c.png', 'G13', 'Фиолетовый', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(11, 'antifreeze', 'Motul Inugel Expert', 'ANTI002', '5 л', '2200.00', 1, 0, 'Motul', 'uploads/products/696392655986c.png', 'G13', 'Фиолетовый', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(12, 'antifreeze', 'Shell Helix Ultra', 'SHELL-AF02', '2 л', '1350.00', 1, 1, 'Shell', 'uploads/products/696392655986c.png', 'G12+', 'Оранжевый', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(13, 'antifreeze', 'Liqui Moly G12 Plus', 'LM-AF002', '5 л', '1950.00', 1, 0, 'Liqui Moly', 'uploads/products/696392655986c.png', 'G12+', 'Красный', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(14, 'antifreeze', 'Castrol SF Concentrate', 'CAST-AF02', '1 л', '950.00', 1, 0, 'Castrol', 'uploads/products/696392655986c.png', 'G11', 'Зеленый', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(15, 'antifreeze', 'Total Antifreeze', 'TOTAL-AF02', '2 л', '1200.00', 0, 0, 'Total', 'uploads/products/696392655986c.png', 'G12', 'Синий', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(16, 'brake-fluid', 'Liqui Moly Bremsflussigkeit DOT 4', 'BRAKE001', '0.5 л', '650.00', 1, 1, 'Liqui Moly', 'uploads/products/696392655986c.png', NULL, NULL, NULL, 'DOT 4', NULL, NULL, '255°C', '165°C', NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(17, 'brake-fluid', 'Castrol React DOT 4', 'CAST-BF01', '0.5 л', '580.00', 1, 0, 'Castrol', 'uploads/products/696392655986c.png', NULL, NULL, NULL, 'DOT 4', NULL, NULL, '250°C', '160°C', NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(18, 'brake-fluid', 'Motul DOT 5.1', 'MOT-BF01', '0.5 л', '890.00', 1, 1, 'Motul', 'uploads/products/696392655986c.png', NULL, NULL, NULL, 'DOT 5.1', NULL, NULL, '270°C', '180°C', NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(19, 'brake-fluid', 'Brembo LCF 600 Plus DOT 4', 'BREM-BF01', '0.5 л', '720.00', 1, 0, 'Brembo', 'uploads/products/696392655986c.png', NULL, NULL, NULL, 'DOT 4', NULL, NULL, '260°C', '170°C', NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(20, 'brake-fluid', 'ATE SL.6 DOT 4', 'ATE-BF001', '1 л', '950.00', 1, 1, 'ATE', 'uploads/products/696392655986c.png', NULL, NULL, NULL, 'DOT 4', NULL, NULL, '255°C', '165°C', NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(21, 'brake-fluid', 'TRW PFG550 DOT 4', 'TRW-BF001', '0.5 л', '520.00', 0, 0, 'TRW', 'uploads/products/696392655986c.png', NULL, NULL, NULL, 'DOT 4', NULL, NULL, '250°C', '160°C', NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(22, 'brake-fluid', 'Bosch ESI6-32N DOT 4', 'BOSCH-BF01', '1 л', '780.00', 1, 0, 'Bosch', 'uploads/products/696392655986c.png', NULL, NULL, NULL, 'DOT 4', NULL, NULL, '265°C', '175°C', NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(23, 'brake-fluid', 'Febi Bilstein DOT 4', 'FEBI-BF01', '0.5 л', '480.00', 1, 0, 'Febi', 'uploads/products/696392655986c.png', NULL, NULL, NULL, 'DOT 4', NULL, NULL, '250°C', '160°C', NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(24, 'brake-fluid', 'Ravenol DOT 5.1', 'RAV-BF001', '0.5 л', '820.00', 1, 1, 'Ravenol', 'uploads/products/696392655986c.png', NULL, NULL, NULL, 'DOT 5.1', NULL, NULL, '270°C', '180°C', NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(25, 'brake-fluid', 'Shell DOT 4', 'SHELL-BF01', '0.5 л', '550.00', 1, 0, 'Shell', 'uploads/products/696392655986c.png', NULL, NULL, NULL, 'DOT 4', NULL, NULL, '255°C', '165°C', NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(26, 'brake-fluid', 'Liqui Moly DOT 5.1', 'BRAKE002', '0.5 л', '920.00', 1, 0, 'Liqui Moly', 'uploads/products/696392655986c.png', NULL, NULL, NULL, 'DOT 5.1', NULL, NULL, '275°C', '185°C', NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(27, 'brake-fluid', 'Castrol React DOT 5.1', 'CAST-BF02', '0.5 л', '850.00', 1, 0, 'Castrol', 'uploads/products/696392655986c.png', NULL, NULL, NULL, 'DOT 5.1', NULL, NULL, '270°C', '180°C', NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(28, 'brake-fluid', 'Motul DOT 4', 'MOT-BF02', '1 л', '1100.00', 1, 1, 'Motul', 'uploads/products/696392655986c.png', NULL, NULL, NULL, 'DOT 4', NULL, NULL, '265°C', '175°C', NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(29, 'brake-fluid', 'Brembo DOT 5.1', 'BREM-BF02', '0.5 л', '950.00', 0, 0, 'Brembo', 'uploads/products/696392655986c.png', NULL, NULL, NULL, 'DOT 5.1', NULL, NULL, '275°C', '185°C', NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(30, 'brake-fluid', 'ATE TYP 200 DOT 4', 'ATE-BF002', '1 л', '1200.00', 1, 1, 'ATE', 'uploads/products/696392655986c.png', NULL, NULL, NULL, 'DOT 4', NULL, NULL, '260°C', '170°C', NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(31, 'cooling-fluid', 'Liqui Moly Kuhlerfrostschutz GTL 12 Plus', 'COOL001', '1.5 л', '1250.00', 1, 1, 'Liqui Moly', 'uploads/products/696392655986c.png', 'G12++', 'Синий', NULL, NULL, NULL, '-40°C', NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(32, 'cooling-fluid', 'Castrol Radicool SF', 'CAST-CF01', '5 л', '1890.00', 1, 0, 'Castrol', 'uploads/products/696392655986c.png', 'G11', 'Зеленый', NULL, NULL, NULL, '-35°C', NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(33, 'cooling-fluid', 'Motul Inugel Optimal', 'MOT-CF001', '2 л', '1100.00', 1, 1, 'Motul', 'uploads/products/696392655986c.png', 'G12', 'Красный', NULL, NULL, NULL, '-37°C', NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(34, 'cooling-fluid', 'Shell Zone Ultra', 'SHELL-CF01', '5 л', '1650.00', 1, 0, 'Shell', 'uploads/products/696392655986c.png', 'G13', 'Фиолетовый', NULL, NULL, NULL, '-40°C', NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(35, 'cooling-fluid', 'Total Glacelf Auto Supra', 'TOTAL-CF01', '5 л', '1450.00', 0, 0, 'Total', 'uploads/products/696392655986c.png', 'G12', 'Синий', NULL, NULL, NULL, '-35°C', NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(36, 'cooling-fluid', 'Mobil Antifreeze Advanced', 'MOB-CF001', '1 л', '680.00', 1, 1, 'Mobil', 'uploads/products/696392655986c.png', 'G12++', 'Оранжевый', NULL, NULL, NULL, '-37°C', NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(37, 'cooling-fluid', 'Febi Bilstein Kuhlerfrostschutz', 'FEBI-CF01', '1.5 л', '850.00', 1, 0, 'Febi', 'uploads/products/696392655986c.png', 'G12', 'Синий', NULL, NULL, NULL, '-40°C', NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(38, 'cooling-fluid', 'Ravenol Original Green', 'RAV-CF001', '1.5 л', '920.00', 1, 0, 'Ravenol', 'uploads/products/696392655986c.png', 'G11', 'Зеленый', NULL, NULL, NULL, '-35°C', NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(39, 'cooling-fluid', 'SWAG Antifreeze', 'SWAG-CF01', '5 л', '1580.00', 1, 1, 'SWAG', 'uploads/products/696392655986c.png', 'G12+', 'Красный', NULL, NULL, NULL, '-40°C', NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(40, 'cooling-fluid', 'Hepu Antifreeze', 'HEPU-CF01', '1.5 л', '780.00', 1, 0, 'Hepu', 'uploads/products/696392655986c.png', 'G13', 'Фиолетовый', NULL, NULL, NULL, '-37°C', NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(41, 'cooling-fluid', 'Liqui Moly G13', 'COOL002', '1.5 л', '1350.00', 1, 0, 'Liqui Moly', 'uploads/products/696392655986c.png', 'G13', 'Фиолетовый', NULL, NULL, NULL, '-40°C', NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(42, 'cooling-fluid', 'Castrol G12++', 'CAST-CF02', '5 л', '1950.00', 1, 1, 'Castrol', 'uploads/products/696392655986c.png', 'G12++', 'Синий', NULL, NULL, NULL, '-40°C', NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(43, 'cooling-fluid', 'Motul G11', 'MOT-CF002', '2 л', '950.00', 1, 0, 'Motul', 'uploads/products/696392655986c.png', 'G11', 'Зеленый', NULL, NULL, NULL, '-35°C', NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(44, 'cooling-fluid', 'Shell G12+', 'SHELL-CF02', '5 л', '1750.00', 0, 0, 'Shell', 'uploads/products/696392655986c.png', 'G12+', 'Красный', NULL, NULL, NULL, '-37°C', NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(45, 'cooling-fluid', 'Mobil G13', 'MOB-CF002', '1 л', '720.00', 1, 1, 'Mobil', 'uploads/products/696392655986c.png', 'G13', 'Фиолетовый', NULL, NULL, NULL, '-40°C', NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:29', '2026-02-16 17:49:29'),
(46, 'power-steering', 'Liqui Moly Lenkungs-Getriebeoil', 'PSF001', '1 л', '1450.00', 1, 1, 'Liqui Moly', 'uploads/products/696392655986c.png', 'ATF', 'Красный', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(47, 'power-steering', 'Febi Bilstein Hydraulikol', 'FEBI-PS01', '1 л', '980.00', 1, 0, 'Febi', 'uploads/products/696392655986c.png', 'PSF', 'Зеленый', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(48, 'power-steering', 'Ravenol Hydraulik Fluid', 'RAV-PS001', '1 л', '1120.00', 1, 1, 'Ravenol', 'uploads/products/696392655986c.png', 'ATF', 'Красный', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(49, 'power-steering', 'SWAG Power Steering Fluid', 'SWAG-PS01', '1 л', '890.00', 0, 0, 'SWAG', 'uploads/products/696392655986c.png', 'PSF', 'Зеленый', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(50, 'power-steering', 'Mannol Power Steering Fluid', 'MANN-PS01', '1 л', '760.00', 1, 0, 'Mannol', 'uploads/products/696392655986c.png', 'ATF', 'Красный', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(51, 'power-steering', 'Motul Multi ATF', 'MOT-PS001', '1 л', '1280.00', 1, 1, 'Motul', 'uploads/products/696392655986c.png', 'ATF', 'Красный', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(52, 'power-steering', 'Castrol Transmax ATF', 'CAST-PS01', '1 л', '1350.00', 1, 0, 'Castrol', 'uploads/products/696392655986c.png', 'ATF', 'Красный', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(53, 'power-steering', 'Mobil ATF 320', 'MOB-PS001', '1 л', '1100.00', 1, 0, 'Mobil', 'uploads/products/696392655986c.png', 'ATF', 'Красный', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(54, 'power-steering', 'Pentosin CHF 11S', 'PENT-PS01', '1 л', '1650.00', 1, 1, 'Pentosin', 'uploads/products/696392655986c.png', 'CHF', 'Зеленый', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(55, 'power-steering', 'Comma PSF-MV', 'COMM-PS01', '1 л', '820.00', 1, 0, 'Comma', 'uploads/products/696392655986c.png', 'PSF', 'Красный', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(56, 'power-steering', 'Liqui Moly ATF Synth', 'PSF002', '1 л', '1550.00', 1, 0, 'Liqui Moly', 'uploads/products/696392655986c.png', 'ATF', 'Красный', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(57, 'power-steering', 'Febi Hydraulic Oil', 'FEBI-PS02', '1 л', '1050.00', 1, 0, 'Febi', 'uploads/products/696392655986c.png', 'PSF', 'Зеленый', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(58, 'power-steering', 'Ravenol ATF Fluid', 'RAV-PS002', '1 л', '1250.00', 1, 1, 'Ravenol', 'uploads/products/696392655986c.png', 'ATF', 'Красный', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(59, 'power-steering', 'Motul Dexron III', 'MOT-PS002', '1 л', '1380.00', 1, 0, 'Motul', 'uploads/products/696392655986c.png', 'ATF', 'Красный', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(60, 'power-steering', 'Pentosin CHF 202', 'PENT-PS02', '1 л', '1750.00', 1, 1, 'Pentosin', 'uploads/products/696392655986c.png', 'CHF', 'Зеленый', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(61, 'special-fluid', 'Liqui Moly Scheiben-Reiniger', 'SPEC001', '2 л', '450.00', 1, 1, 'Liqui Moly', 'uploads/products/696392655986c.png', 'Омыватель', NULL, NULL, NULL, 'Лобовое стекло', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(62, 'special-fluid', 'Sonax AdBlue', 'SONAX-AB01', '10 л', '890.00', 1, 0, 'Sonax', 'uploads/products/696392655986c.png', 'AdBlue', NULL, NULL, NULL, 'Система SCR', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(63, 'special-fluid', 'Wynns Injector Cleaner', 'WYNNS-IC01', '0.25 л', '680.00', 1, 1, 'Wynns', 'uploads/products/696392655986c.png', 'Очиститель', NULL, NULL, NULL, 'Инжектор', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(64, 'special-fluid', 'Motul Clean Brake', 'MOT-SP001', '0.4 л', '520.00', 1, 0, 'Motul', 'uploads/products/696392655986c.png', 'Очиститель', NULL, NULL, NULL, 'Тормоза', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(65, 'special-fluid', 'Bardahl No Frost', 'BARD-SP01', '0.5 л', '320.00', 0, 0, 'Bardahl', 'uploads/products/696392655986c.png', 'Антиобледенитель', NULL, NULL, NULL, 'Замки', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(66, 'special-fluid', 'Gunk Engine Degreaser', 'GUNK-SP01', '0.5 л', '580.00', 1, 1, 'Gunk', 'uploads/products/696392655986c.png', 'Очиститель', NULL, NULL, NULL, 'Двигатель', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(67, 'special-fluid', 'CRC Contact Cleaner', 'CRC-SP001', '0.4 л', '420.00', 1, 0, 'CRC', 'uploads/products/696392655986c.png', 'Очиститель', NULL, NULL, NULL, 'Электрика', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(68, 'special-fluid', 'Permatex Anti-Seize', 'PERM-SP01', '0.1 л', '350.00', 1, 0, 'Permatex', 'uploads/products/696392655986c.png', 'Смазка', NULL, NULL, NULL, 'Резьба', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(69, 'special-fluid', 'WD-40 Specialist', 'WD40-SP01', '0.4 л', '480.00', 1, 1, 'WD-40', 'uploads/products/696392655986c.png', 'Смазка', NULL, NULL, NULL, 'Универсальная', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(70, 'special-fluid', '3M Windshield Wash', '3M-SP001', '1 л', '290.00', 1, 0, '3M', 'uploads/products/696392655986c.png', 'Омыватель', NULL, NULL, NULL, 'Стекло', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(71, 'special-fluid', 'Liqui Moly Kühlerschutz', 'SPEC002', '1.5 л', '550.00', 1, 0, 'Liqui Moly', 'uploads/products/696392655986c.png', 'Охлаждающая', NULL, NULL, NULL, 'Радиатор', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(72, 'special-fluid', 'Sonax Glass Cleaner', 'SONAX-GC01', '0.5 л', '380.00', 1, 0, 'Sonax', 'uploads/products/696392655986c.png', 'Очиститель', NULL, NULL, NULL, 'Стекло', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(73, 'special-fluid', 'Wynns Diesel Cleaner', 'WYNNS-DC01', '0.25 л', '720.00', 1, 1, 'Wynns', 'uploads/products/696392655986c.png', 'Очиститель', NULL, NULL, NULL, 'Дизель', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(74, 'special-fluid', 'Motul Chain Clean', 'MOT-SP002', '0.4 л', '610.00', 1, 0, 'Motul', 'uploads/products/696392655986c.png', 'Очиститель', NULL, NULL, NULL, 'Цепь', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(75, 'special-fluid', 'Bardahl Injector Clean', 'BARD-SP02', '0.3 л', '490.00', 0, 0, 'Bardahl', 'uploads/products/696392655986c.png', 'Очиститель', NULL, NULL, NULL, 'Инжектор', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(76, 'kit', 'Комплект замены масла Castrol', 'KIT001', '1 компл', '5200.00', 1, 1, 'Castrol', 'uploads/products/696392655986c.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Масло 4л + фильтр', NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(77, 'kit', 'Набор Liqui Moly для ТО', 'KIT002', '1 компл', '7800.00', 1, 0, 'Liqui Moly', 'uploads/products/696392655986c.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Масло 5л + фильтры + свечи', NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(78, 'kit', 'Комплект тормозной жидкости', 'KIT003', '1 компл', '1850.00', 1, 1, 'ATE', 'uploads/products/696392655986c.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Тормозная жидкость 1л + очиститель', NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(79, 'kit', 'Набор охлаждающей жидкости', 'KIT004', '1 компл', '2450.00', 0, 0, 'Motul', 'uploads/products/696392655986c.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Антифриз 5л + дистиллированная вода', NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(80, 'kit', 'Комплект трансмиссионного масла', 'KIT005', '1 компл', '3200.00', 1, 0, 'Liqui Moly', 'uploads/products/696392655986c.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Масло 2л + прокладка', NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(81, 'kit', 'Набор для ГУР', 'KIT006', '1 компл', '1980.00', 1, 1, 'Febi', 'uploads/products/696392655986c.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Жидкость ГУР 1л + очиститель', NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(82, 'kit', 'Комплект полного ТО', 'KIT007', '1 компл', '12500.00', 1, 1, 'Various', 'uploads/products/696392655986c.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Масло, фильтры, свечи, жидкости', NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(83, 'kit', 'Набор для замены АКПП', 'KIT008', '1 компл', '6800.00', 1, 0, 'Mobil', 'uploads/products/696392655986c.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Масло АКПП 4л + фильтр', NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(84, 'kit', 'Комплект зимнего ТО', 'KIT009', '1 компл', '4200.00', 1, 1, 'Various', 'uploads/products/696392655986c.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Омыватель, антифриз, свечи, жидкости', NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(85, 'kit', 'Набор для дизельного двигателя', 'KIT010', '1 компл', '8900.00', 1, 0, 'Liqui Moly', 'uploads/products/696392655986c.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Масло 5л + фильтры + присадка', NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(86, 'kit', 'Комплект замены масла Mobil', 'KIT011', '1 компл', '5800.00', 1, 0, 'Mobil', 'uploads/products/696392655986c.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Масло 4л + фильтр', NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(87, 'kit', 'Набор для бензинового двигателя', 'KIT012', '1 компл', '9500.00', 1, 1, 'Castrol', 'uploads/products/696392655986c.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Масло 5л + фильтры + свечи', NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(88, 'kit', 'Комплект тормозной системы', 'KIT013', '1 компл', '3200.00', 1, 0, 'Brembo', 'uploads/products/696392655986c.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Тормозная жидкость + колодки', NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(89, 'kit', 'Набор для кондиционера', 'KIT014', '1 компл', '2800.00', 0, 0, 'Various', 'uploads/products/696392655986c.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Фреон + очиститель', NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(90, 'kit', 'Комплект летнего ТО', 'KIT015', '1 компл', '3800.00', 1, 1, 'Various', 'uploads/products/696392655986c.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Омыватель, антифриз, фильтры', NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(91, 'transmission-oil', 'Castrol TRANSMAX 75W-90', 'TRANS001', '1 л', '1890.00', 1, 1, 'Castrol', 'uploads/products/696392655986c.png', 'Синтетическое', NULL, '75W-90', NULL, 'МКПП', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(92, 'transmission-oil', 'Mobilube 1 SHC 75W-90', 'MOB-TR001', '1 л', '2150.00', 1, 0, 'Mobil', 'uploads/products/696392655986c.png', 'Синтетическое', NULL, '75W-90', NULL, 'МКПП, АКПП', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(93, 'transmission-oil', 'Liqui Moly Hochleistungs-Getriebeoil 75W-90', 'LM-TR001', '1 л', '1980.00', 1, 1, 'Liqui Moly', 'uploads/products/696392655986c.png', 'Синтетическое', NULL, '75W-90', NULL, 'МКПП', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(94, 'transmission-oil', 'Motul Gear 300 75W-90', 'MOT-TR001', '1 л', '2450.00', 1, 0, 'Motul', 'uploads/products/696392655986c.png', 'Синтетическое', NULL, '75W-90', NULL, 'МКПП', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(95, 'transmission-oil', 'Shell Spirax S6 GX 80W-90', 'SHELL-TR01', '1 л', '1650.00', 1, 0, 'Shell', 'uploads/products/696392655986c.png', 'Минеральное', NULL, '80W-90', NULL, 'МКПП, редуктор', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(96, 'transmission-oil', 'Total Transmission FE 75W-80', 'TOTAL-TR01', '1 л', '1780.00', 0, 0, 'Total', 'uploads/products/696392655986c.png', 'Синтетическое', NULL, '75W-80', NULL, 'МКПП', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(97, 'transmission-oil', 'ZIC G-F Top 75W-85', 'ZIC-TR001', '1 л', '1520.00', 1, 1, 'ZIC', 'uploads/products/696392655986c.png', 'Синтетическое', NULL, '75W-85', NULL, 'МКПП', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(98, 'transmission-oil', 'ELF Tranself NFJ 75W-80', 'ELF-TR001', '1 л', '1920.00', 1, 0, 'ELF', 'uploads/products/696392655986c.png', 'Синтетическое', NULL, '75W-80', NULL, 'МКПП', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(99, 'transmission-oil', 'Ravenol MTF-2 75W-80', 'RAV-TR001', '1 л', '1680.00', 1, 0, 'Ravenol', 'uploads/products/696392655986c.png', 'Синтетическое', NULL, '75W-80', NULL, 'МКПП', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(100, 'transmission-oil', 'Febi Bilstein Getriebeoil 75W-90', 'FEBI-TR01', '1 л', '1350.00', 1, 0, 'Febi', 'uploads/products/696392655986c.png', 'Синтетическое', NULL, '75W-90', NULL, 'МКПП', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(101, 'transmission-oil', 'Castrol TRANSMAX 80W-90', 'TRANS002', '1 л', '1750.00', 1, 0, 'Castrol', 'uploads/products/696392655986c.png', 'Минеральное', NULL, '80W-90', NULL, 'МКПП, редуктор', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(102, 'transmission-oil', 'Mobilube GX 80W-90', 'MOB-TR002', '1 л', '1420.00', 1, 0, 'Mobil', 'uploads/products/696392655986c.png', 'Минеральное', NULL, '80W-90', NULL, 'МКПП, редуктор', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(103, 'transmission-oil', 'Liqui Moly Getriebeoil 75W-80', 'LM-TR002', '1 л', '1850.00', 1, 1, 'Liqui Moly', 'uploads/products/696392655986c.png', 'Синтетическое', NULL, '75W-80', NULL, 'МКПП', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(104, 'transmission-oil', 'Motul Multi ATF', 'MOT-TR002', '1 л', '1950.00', 1, 0, 'Motul', 'uploads/products/696392655986c.png', 'Синтетическое', NULL, 'ATF', NULL, 'АКПП', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(105, 'transmission-oil', 'Shell Spirax S4 ATF MD3', 'SHELL-TR02', '1 л', '1250.00', 1, 0, 'Shell', 'uploads/products/696392655986c.png', 'Синтетическое', NULL, 'ATF', NULL, 'АКПП', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(106, 'motor-oil', 'Castrol EDGE 5W-30', '15698E4', '4 л', '3890.00', 1, 1, 'Castrol', 'uploads/products/696392655986c.png', 'Синтетическое', NULL, '5W-30', NULL, NULL, NULL, NULL, NULL, NULL, 'SN/CF', 'A3/B4', '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(107, 'motor-oil', 'Mobil Super 3000 X1 5W-40', '152343', '4 л', '3450.00', 1, 0, 'Mobil', 'uploads/products/696392655986c.png', 'Синтетическое', NULL, '5W-40', NULL, NULL, NULL, NULL, NULL, NULL, 'SN', 'A3/B4', '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(108, 'motor-oil', 'Liqui Moly Special Tec AA 5W-30', '1123DE', '5 л', '4210.00', 1, 1, 'Liqui Moly', 'uploads/products/696392655986c.png', 'Синтетическое', NULL, '5W-30', NULL, NULL, NULL, NULL, NULL, NULL, 'SN/CF', 'A5/B5', '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(109, 'motor-oil', 'Shell Helix HX7 10W-40', '87654F', '4 л', '2890.00', 0, 0, 'Shell', 'uploads/products/696392655986c.png', 'Полусинтетическое', NULL, '10W-40', NULL, NULL, NULL, NULL, NULL, NULL, 'SN/CF', 'A3/B4', '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(110, 'motor-oil', 'Total Quartz 9000 5W-40', 'TQ9000', '5 л', '3650.00', 1, 0, 'Total', 'uploads/products/696392655986c.png', 'Синтетическое', NULL, '5W-40', NULL, NULL, NULL, NULL, NULL, NULL, 'SN', 'A3/B4', '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(111, 'motor-oil', 'Motul 8100 X-clean 5W-30', 'M8100', '5 л', '4890.00', 1, 1, 'Motul', 'uploads/products/696392655986c.png', 'Синтетическое', NULL, '5W-30', NULL, NULL, NULL, NULL, NULL, NULL, 'SN/CF', 'C3', '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(112, 'motor-oil', 'ZIC X9 5W-30', 'ZX9-5W30', '4 л', '2990.00', 1, 0, 'ZIC', 'uploads/products/696392655986c.png', 'Синтетическое', NULL, '5W-30', NULL, NULL, NULL, NULL, NULL, NULL, 'SN', 'A5/B5', '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(113, 'motor-oil', 'ELF Evolution 900 NF 5W-40', 'ELF900', '5 л', '3750.00', 1, 0, 'ELF', 'uploads/products/696392655986c.png', 'Синтетическое', NULL, '5W-40', NULL, NULL, NULL, NULL, NULL, NULL, 'SN/CF', 'A3/B4', '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(114, 'motor-oil', 'Castrol MAGNATEC 5W-30', 'CAST567', '4 л', '3250.00', 1, 1, 'Castrol', 'uploads/products/696392655986c.png', 'Синтетическое', NULL, '5W-30', NULL, NULL, NULL, NULL, NULL, NULL, 'SN', 'A1/B1', '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(115, 'motor-oil', 'Mobil 1 0W-40', 'MOB1-0W40', '4 л', '4450.00', 1, 0, 'Mobil', 'uploads/products/696392655986c.png', 'Синтетическое', NULL, '0W-40', NULL, NULL, NULL, NULL, NULL, NULL, 'SN', 'A3/B4', '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(116, 'motor-oil', 'Castrol EDGE 0W-20', '15698E5', '4 л', '3990.00', 1, 0, 'Castrol', 'uploads/products/696392655986c.png', 'Синтетическое', NULL, '0W-20', NULL, NULL, NULL, NULL, NULL, NULL, 'SN/CF', 'A1/B1', '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(117, 'motor-oil', 'Mobil Super 3000 5W-30', '152344', '4 л', '3550.00', 1, 0, 'Mobil', 'uploads/products/696392655986c.png', 'Синтетическое', NULL, '5W-30', NULL, NULL, NULL, NULL, NULL, NULL, 'SN', 'A3/B4', '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(118, 'motor-oil', 'Liqui Moly Molygen 5W-40', '1124DE', '5 л', '4510.00', 1, 1, 'Liqui Moly', 'uploads/products/696392655986c.png', 'Синтетическое', NULL, '5W-40', NULL, NULL, NULL, NULL, NULL, NULL, 'SN/CF', 'A3/B4', '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(119, 'motor-oil', 'Shell Helix Ultra 5W-30', '87655F', '4 л', '3290.00', 1, 0, 'Shell', 'uploads/products/696392655986c.png', 'Синтетическое', NULL, '5W-30', NULL, NULL, NULL, NULL, NULL, NULL, 'SN/CF', 'A5/B5', '2026-02-16 17:49:30', '2026-02-16 17:49:30'),
(120, 'motor-oil', 'Total Quartz 7000 10W-40', 'TQ7000', '4 л', '2750.00', 1, 0, 'Total', 'uploads/products/696392655986c.png', 'Полусинтетическое', NULL, '10W-40', NULL, NULL, NULL, NULL, NULL, NULL, 'SN', 'A3/B4', '2026-02-16 17:49:30', '2026-02-16 17:49:30');

-- --------------------------------------------------------

--
-- Структура таблицы `company_documents`
--

CREATE TABLE `company_documents` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `file_name` varchar(255) DEFAULT NULL,
  `file_size` varchar(20) DEFAULT NULL,
  `display_order` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `company_documents`
--

INSERT INTO `company_documents` (`id`, `title`, `description`, `file_name`, `file_size`, `display_order`) VALUES
(1, 'Устав компании', 'Учредительный документ ООО \"Лал-Авто\"', 'Устав_ООО_Лал-Авто.pdf', '2.3 MB', 1),
(2, 'Свидетельство ОГРН', 'Свидетельство о государственной регистрации', 'Свидетельство_ОГРН.pdf', '1.8 MB', 2),
(3, 'Свидетельство ИНН', 'Свидетельство о постановке на налоговый учет', 'Свидетельство_ИНН.pdf', '1.5 MB', 3);

-- --------------------------------------------------------

--
-- Структура таблицы `company_requisites`
--

CREATE TABLE `company_requisites` (
  `id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `copy_value` varchar(500) DEFAULT NULL,
  `display_order` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `company_requisites`
--

INSERT INTO `company_requisites` (`id`, `category`, `title`, `value`, `copy_value`, `display_order`) VALUES
(1, 'general', 'Полное наименование', 'Общество с ограниченной ответственностью \"Лал-Авто\"', 'Общество с ограниченной ответственностью \"Лал-Авто\"', 1),
(2, 'general', 'Сокращенное наименование', 'ООО \"Лал-Авто\"', 'ООО \"Лал-Авто\"', 2),
(3, 'general', 'ИНН', '3900000000', '3900000000', 3),
(4, 'general', 'КПП', '390001001', '390001001', 4),
(5, 'general', 'ОГРН', '1023900000000', '1023900000000', 5),
(6, 'general', 'ОКПО', '12345678', '12345678', 6),
(7, 'general', 'ОКВЭД', '45.32.1 Торговля автомобильными деталями, узлами и принадлежностями', '45.32.1 Торговля автомобильными деталями, узлами и принадлежностями', 7),
(8, 'bank', 'Расчетный счет', '40702810500000000001', '40702810500000000001', 1),
(9, 'bank', 'Банк', 'ПАО \"Сбербанк\"', 'ПАО \"Сбербанк\"', 2),
(10, 'bank', 'БИК', '044525225', '044525225', 3),
(11, 'bank', 'Корреспондентский счет', '30101810400000000225', '30101810400000000225', 4),
(12, 'bank', 'Юридический адрес банка', '117997, г. Москва, ул. Вавилова, д. 19', '117997, г. Москва, ул. Вавилова, д. 19', 5),
(13, 'address', 'Юридический адрес', '236000, г. Калининград, ул. Автомобильная, д. 12', '236000, г. Калининград, ул. Автомобильная, д. 12', 1),
(14, 'address', 'Фактический адрес', '236000, г. Калининград, ул. Автомобильная, д. 12', '236000, г. Калининград, ул. Автомобильная, д. 12', 2),
(15, 'address', 'Телефон', '+7 (4012) 65-65-65', '+74012656565', 3),
(16, 'address', 'Email', 'info@lal-auto.ru', 'info@lal-auto.ru', 4),
(17, 'address', 'Сайт', 'www.lal-auto.ru', 'www.lal-auto.ru', 5),
(18, 'management', 'Генеральный директор', 'Иванов Петр Сергеевич', 'Иванов Петр Сергеевич', 1),
(19, 'management', 'Главный бухгалтер', 'Смирнова Ольга Владимировна', 'Смирнова Ольга Владимировна', 2),
(20, 'management', 'Действует на основании', 'Устава', 'Устава', 3);

-- --------------------------------------------------------

--
-- Структура таблицы `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text,
  `author` varchar(100) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `published_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `news`
--

INSERT INTO `news` (`id`, `title`, `content`, `author`, `status`, `created_at`, `published_at`) VALUES
(1, 'Открытие нового магазина в Москве', 'Рады сообщить, что мы открыли новый магазин автозапчастей в центре Москвы! Теперь у наших клиентов есть еще одна удобная точка для покупки качественных запчастей и аксессуаров для автомобилей. В новом магазине представлен расширенный ассортимент товаров, а также работает профессиональная консультационная служба. Ждем вас по адресу: ул. Тверская, 25.', 'Администратор', 'published', '2026-01-10 07:30:00', '2026-01-10'),
(2, 'Новые поступления моторных масел', 'В нашем магазине появились новые виды моторных масел от ведущих производителей: Shell, Mobil, Castrol. Все масла соответствуют современным стандартам качества и подходят для различных типов двигателей. Специально для наших клиентов мы подготовили выгодные предложения при покупке от 5 литров. Акция действует до конца месяца.', 'Менеджер', 'published', '2026-01-11 11:20:00', '2026-01-17'),
(3, 'Скидки на тормозные системы', 'С 15 января по 15 февраля действуют специальные скидки на все комплектующие тормозных систем. Тормозные колодки, диски, суппорты - все со скидкой до 25%! Не упустите возможность обновить тормозную систему вашего автомобиля с выгодой. Гарантия на все товары - 12 месяцев.', 'Администратор', 'draft', '2026-01-12 06:15:00', NULL),
(4, 'Обновление сервисного центра', 'Завершилась модернизация нашего сервисного центра. Теперь мы предлагаем еще более качественный и быстрый сервис по ремонту и обслуживанию автомобилей. Установлено новое диагностическое оборудование, расширен штат специалистов. Записаться на обслуживание можно онлайн или по телефону.', 'Технический директор', 'draft', '2026-01-13 08:45:00', NULL),
(5, 'Работа в праздничные дни', 'Уважаемые клиенты! Сообщаем о графике работы в праздничные дни. 1-2 января - выходные дни. С 3 января магазины и сервисный центр работают в обычном режиме. Онлайн-заказы принимаются круглосуточно. С наступающим Новым годом!', 'Администратор', 'published', '2026-01-14 13:30:00', '2026-01-17'),
(6, 'Мастер-класс по замене фильтров', 'Приглашаем всех желающих на бесплатный мастер-класс \"Самостоятельная замена воздушного и салонного фильтров\", который состоится 20 января в 18:00 в нашем сервисном центре. Наши специалисты покажут, как правильно выполнить замену, и ответят на все вопросы. Количество мест ограничено, требуется предварительная регистрация.', 'Сервисный менеджер', 'published', '2026-01-15 05:20:00', '2026-01-15'),
(7, 'Расширение ассортимента аккумуляторов', 'Теперь в нашем магазине представлены аккумуляторы новых брендов: Varta, Bosch, Tudor. Все аккумуляторы проходят предпродажную проверку и имеют гарантию 24 месяца. Для постоянных клиентов - дополнительные скидки. Также доступна услуга профессиональной установки.', 'Менеджер по закупкам', 'published', '2026-01-16 10:10:00', '2026-01-16'),
(8, 'Система лояльности для клиентов', 'Запускаем новую программу лояльности! Теперь за каждую покупку вы получаете бонусные баллы, которые можно использовать для оплаты следующих покупок. Регистрируйтесь в нашей программе и получайте дополнительные преимущества: персональные скидки, приоритетное обслуживание, информацию о новинках.', 'Маркетолог', 'published', '2026-01-17 14:40:00', '2026-01-17');

-- --------------------------------------------------------

--
-- Структура таблицы `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `is_read`, `created_at`) VALUES
(1, 2, 'Новое поступление', 'Появились в наличии запчасти для Toyota Camry', 0, '2026-01-15 13:29:33'),
(2, 2, 'Заказ готов к выдаче', 'Ваш заказ #12345 готов к получению', 0, '2026-01-15 13:29:33'),
(3, 3, 'Скидка 15%', 'Специальное предложение для вас действует до конца недели', 0, '2026-01-15 13:29:33'),
(4, 4, 'Изменение графика работы', 'В праздничные дни магазин работает с 10:00 до 18:00', 1, '2026-01-15 13:29:33'),
(5, 5, 'Бонусные баллы', 'На ваш счет начислено 100 бонусных баллов', 1, '2026-01-15 13:29:33');

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `order_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `shipping_address` text,
  `phone` varchar(20) DEFAULT NULL,
  `notes` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `user_id`, `total_amount`, `status`, `order_date`, `shipping_address`, `phone`, `notes`) VALUES
(1, 'ORD-20260105-5A3C6597', 2, '41700.00', 'pending', '2026-01-05 15:00:31', '', '89113456789', ''),
(2, 'ORD-20260213-AAC10A70', 3, '23330.00', 'cancelled', '2026-02-13 16:27:06', '', '89114567891', '');

-- --------------------------------------------------------

--
-- Структура таблицы `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `price`, `quantity`) VALUES
(1, 1, 0, 'Генератор Audi A4 B9', '15600.00', 1),
(2, 1, 0, 'Ремень ГРМ BMW 7 series G11', '3200.00', 1),
(3, 1, 0, 'Аккумулятор BMW 5 series F10', '12500.00', 1),
(4, 1, 0, 'Тормозные колодки BMW 1 series F20', '5200.00', 2),
(5, 2, 98, 'Чехол на сиденье с подогревом', '6590.00', 1),
(6, 2, 56, 'ELF Evolution 900 NF 5W-40', '3750.00', 2),
(7, 2, 53, 'Total Quartz 9000 5W-40', '3650.00', 1),
(8, 2, 3, 'Свечи зажигания Audi Q5 2.0 TDI', '850.00', 2),
(9, 2, 2, 'Тормозные колодки Audi A6 C7', '3890.00', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `short_token` varchar(10) DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token`, `short_token`, `expires_at`, `used`, `created_at`) VALUES
(20, 4, 'aa261e316f214a318e91e660bd08ccc86a73def1d906eeb0fbc0e71d751b3d69', 'iZ6U6x', '2026-01-13 12:55:17', 1, '2026-01-13 08:55:17'),
(23, 2, 'b9e8ef21647e0966957767b7ca64426477d61895b924179635a936f99227134b', 'g2wk8b', '2026-02-12 21:52:07', 0, '2026-02-12 17:52:08');

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `category` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `old_price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT '0',
  `article` varchar(100) DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `badge` varchar(50) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `product_type` varchar(50) DEFAULT NULL COMMENT 'part - запчасть, oil - масло, accessory - аксессуар',
  `brand` varchar(100) DEFAULT NULL,
  `viscosity` varchar(50) DEFAULT NULL,
  `oil_type` varchar(50) DEFAULT NULL,
  `volume` varchar(20) DEFAULT NULL,
  `hit` tinyint(1) DEFAULT '0',
  `art` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `category`, `price`, `old_price`, `quantity`, `article`, `image`, `badge`, `status`, `created_at`, `updated_at`, `product_type`, `brand`, `viscosity`, `oil_type`, `volume`, `hit`, `art`) VALUES
(1, 'Фильтр масляный Audi A4 B8 2.0 TFSI', 'Качественный масляный фильтр для Audi A4 B8', 'фильтры', '1250.00', NULL, 25, 'AUDI-FILTER-001', 'uploads/products/696392655986c.png', 'Для Audi', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(2, 'Тормозные колодки Audi A6 C7', 'Передние тормозные колодки для Audi A6 C7', 'тормозная система', '3890.00', '4500.00', 15, 'AUDI-BRAKE-001', 'uploads/products/696392655986c.png', 'Для Audi', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(3, 'Свечи зажигания Audi Q5 2.0 TDI', 'Свечи зажигания для дизельного двигателя', 'двигатель', '850.00', NULL, 30, 'AUDI-SPARK-001', 'uploads/products/696392655986c.png', 'Для Audi', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(4, 'Сцепление Audi A3 8V', 'Комплект сцепления для Audi A3', 'трансмиссия', '12500.00', NULL, 8, 'AUDI-CLUTCH-001', 'uploads/products/696392655986c.png', 'Для Audi', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(5, 'Генератор Audi A4 B9', 'Генератор 150A для Audi A4 B9', 'электрика', '15600.00', NULL, 12, 'AUDI-GEN-001', 'uploads/products/696392655986c.png', 'Для Audi', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(6, 'Воздушный фильтр Audi Q7 4L', 'Воздушный фильтр салона', 'фильтры', '2100.00', NULL, 20, 'AUDI-AIR-001', 'uploads/products/696392655986c.png', 'Для Audi', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(7, 'Фара передняя BMW 3 series F30', 'Передняя фара левая', 'кузовные детали', '18700.00', NULL, 6, 'BMW-LIGHT-001', 'uploads/products/696392655986c.png', 'Для BMW', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(8, 'Тормозные диски BMW X5 E70', 'Вентилируемые тормозные диски', 'тормозная система', '8900.00', NULL, 10, 'BMW-DISC-001', 'uploads/products/696392655986c.png', 'Для BMW', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(9, 'Аккумулятор BMW 5 series F10', 'Аккумулятор 80Ah', 'электрика', '12500.00', NULL, 14, 'BMW-BATTERY-001', 'uploads/products/696392655986c.png', 'Для BMW', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(10, 'Ремень ГРМ BMW 7 series G11', 'Ремень газораспределительного механизма', 'двигатель', '3200.00', NULL, 18, 'BMW-TIMING-001', 'uploads/products/696392655986c.png', 'Для BMW', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(11, 'Масляный фильтр BMW X3 G01', 'Фильтр моторного масла', 'фильтры', '1450.00', NULL, 22, 'BMW-OILFILTER-001', 'uploads/products/696392655986c.png', 'Для BMW', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(12, 'Тормозные колодки BMW 1 series F20', 'Комплект передних колодок', 'тормозная система', '5200.00', NULL, 16, 'BMW-BRAKEPAD-001', 'uploads/products/696392655986c.png', 'Для BMW', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(13, 'Моторное масло 5W-40', 'Синтетическое моторное масло для всех типов двигателей', 'масла и жидкости', '2500.00', NULL, 50, 'OIL-5W40-001', 'uploads/products/696392655986c.png', 'Хит', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(14, 'Воздушный фильтр', 'Воздушный фильтр для легковых автомобилей', 'фильтры', '800.00', NULL, 40, 'FILTER-AIR-001', 'uploads/products/696392655986c.png', NULL, 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(15, 'Тормозные колодки', 'Передние тормозные колодки универсальные', 'тормозная система', '3200.00', NULL, 25, 'BRAKEPAD-UNIV-001', 'uploads/products/696392655986c.png', NULL, 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(16, 'Аккумулятор 60Ah', 'Свинцово-кислотный аккумулятор 60Ah', 'электрика', '5500.00', NULL, 18, 'BATTERY-60AH', 'uploads/products/696392655986c.png', NULL, 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(17, 'Аккумулятор Mercedes-Benz E-class W213', 'Аккумулятор 90Ah для Mercedes', 'электрика', '12500.00', NULL, 10, 'MB-BATTERY-001', 'uploads/products/696392655986c.png', 'Для Mercedes', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(18, 'Тормозные колодки Mercedes C-class W205', 'Передние тормозные колодки', 'тормозная система', '4500.00', NULL, 15, 'MB-BRAKE-001', 'uploads/products/696392655986c.png', 'Для Mercedes', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(19, 'Воздушный фильтр Mercedes GLC X253', 'Воздушный фильтр салона', 'фильтры', '1850.00', NULL, 20, 'MB-AIR-001', 'uploads/products/696392655986c.png', 'Для Mercedes', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(20, 'Свечи зажигания Mercedes E-class W212', 'Иридиевые свечи зажигания', 'двигатель', '1200.00', NULL, 25, 'MB-SPARK-001', 'uploads/products/696392655986c.png', 'Для Mercedes', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(21, 'Сцепление Mercedes A-class W176', 'Комплект сцепления', 'трансмиссия', '13800.00', NULL, 8, 'MB-CLUTCH-001', 'uploads/products/696392655986c.png', 'Для Mercedes', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(22, 'Ремень ГРМ Toyota Camry XV70', 'Ремень ГРМ с натяжителем', 'двигатель', '3200.00', NULL, 15, 'TOYOTA-TIMING-001', 'uploads/products/696392655986c.png', 'Для Toyota', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(23, 'Масляный фильтр Toyota RAV4 XA50', 'Масляный фильтр оригинальный', 'фильтры', '950.00', NULL, 22, 'TOYOTA-OIL-001', 'uploads/products/696392655986c.png', 'Для Toyota', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(24, 'Амортизатор Toyota Corolla E210', 'Амортизатор передний', 'ходовая часть', '3800.00', NULL, 12, 'TOYOTA-SHOCK-001', 'uploads/products/696392655986c.png', 'Для Toyota', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(25, 'Тормозные колодки Toyota Land Cruiser 200', 'Комплект передних колодок', 'тормозная система', '6700.00', NULL, 10, 'TOYOTA-BRAKE-001', 'uploads/products/696392655986c.png', 'Для Toyota', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(26, 'Стартер Toyota Prius XW30', 'Стартер для гибридной системы', 'электрика', '14200.00', NULL, 6, 'TOYOTA-STARTER-001', 'uploads/products/696392655986c.png', 'Для Toyota', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(27, 'Воздушный фильтр Ford Focus MK4', 'Воздушный фильтр салона', 'фильтры', '950.00', NULL, 18, 'FORD-AIR-001', 'uploads/products/696392655986c.png', 'Для Ford', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(28, 'Тормозные колодки Ford Kuga II', 'Передние тормозные колодки', 'тормозная система', '2900.00', NULL, 14, 'FORD-BRAKE-001', 'uploads/products/696392655986c.png', 'Для Ford', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(29, 'Бампер передний Ford Fiesta MK7', 'Бампер передний оригинальный', 'кузовные детали', '15600.00', NULL, 8, 'FORD-BUMPER-001', 'uploads/products/696392655986c.png', 'Для Ford', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(30, 'Турбина Ford Mondeo MK5', 'Турбокомпрессор 2.0 TDCi', 'двигатель', '23400.00', NULL, 5, 'FORD-TURBO-001', 'uploads/products/696392655986c.png', 'Для Ford', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(31, 'Генератор Ford Explorer U502', 'Генератор 180A', 'электрика', '16700.00', NULL, 7, 'FORD-GEN-001', 'uploads/products/696392655986c.png', 'Для Ford', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(32, 'Амортизатор Hyundai Solaris II', 'Амортизатор задний', 'ходовая часть', '3800.00', NULL, 16, 'HYUNDAI-SHOCK-001', 'uploads/products/696392655986c.png', 'Для Hyundai', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(33, 'Тормозные колодки Hyundai Tucson TL', 'Передние тормозные колодки', 'тормозная система', '2900.00', NULL, 18, 'HYUNDAI-BRAKE-001', 'uploads/products/696392655986c.png', 'Для Hyundai', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(34, 'Генератор Hyundai Santa Fe TM', 'Генератор 150A', 'электрика', '13400.00', NULL, 9, 'HYUNDAI-GEN-001', 'uploads/products/696392655986c.png', 'Для Hyundai', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(35, 'Топливный фильтр Hyundai Elantra MD', 'Топливный фильтр тонкой очистки', 'фильтры', '1250.00', NULL, 20, 'HYUNDAI-FUEL-001', 'uploads/products/696392655986c.png', 'Для Hyundai', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(36, 'Ремень ГРМ Hyundai Creta', 'Ремень ГРМ с роликами', 'двигатель', '2800.00', NULL, 15, 'HYUNDAI-TIMING-001', 'uploads/products/696392655986c.png', 'Для Hyundai', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(37, 'Коленчатый вал двигателя 2.0 TSI', 'Коленчатый вал оригинальный', 'двигатель', '18700.00', NULL, 4, 'CRANKSHAFT-001', 'uploads/products/696392655986c.png', 'Хит', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(38, 'Прокладки двигателя комплект V8', 'Полный комплект прокладок', 'двигатель', '4500.00', NULL, 8, 'GASKET-V8', 'uploads/products/696392655986c.png', 'Акция', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(39, 'Топливный насос высокого давления', 'ТНВД дизельный', 'двигатель', '8900.00', NULL, 6, 'FUEL-PUMP-001', 'uploads/products/696392655986c.png', 'Новинка', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(40, 'Распределительный вал 16V', 'Распредвал для 16-клапанного двигателя', 'двигатель', '12300.00', NULL, 5, 'CAMSHAFT-16V', 'uploads/products/696392655986c.png', 'Хит', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(41, 'Тормозной цилиндр главный', 'Главный тормозной цилиндр', 'тормозная система', '3400.00', NULL, 12, 'BRAKE-CYL-001', 'uploads/products/696392655986c.png', 'Акция', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(42, 'Тормозные колодки керамические', 'Керамические тормозные колодки', 'тормозная система', '5600.00', NULL, 10, 'CERAMIC-PADS', 'uploads/products/696392655986c.png', 'Хит', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(43, 'Стабилизатор поперечной устойчивости', 'Стойка стабилизатора', 'ходовая часть', '6700.00', NULL, 15, 'STABILIZER-001', 'uploads/products/696392655986c.png', 'Новинка', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(44, 'Тормозные суппорта передние', 'Суппорта тормозные ремонтные', 'тормозная система', '12800.00', NULL, 8, 'CALIPER-SET', 'uploads/products/696392655986c.png', 'Акция', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(45, 'Топливный фильтр тонкой очистки', 'Фильтр топливный тонкой очистки', 'фильтры', '2100.00', NULL, 20, 'FUEL-FILTER-FINE', 'uploads/products/696392655986c.png', 'Хит', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(46, 'Тормозные диски вентилируемые', 'Вентилируемые тормозные диски', 'тормозная система', '7800.00', NULL, 12, 'VENT-DISCS', 'uploads/products/696392655986c.png', 'Новинка', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(47, 'Цапфа поворотная', 'Поворотная цапфа передняя', 'ходовая часть', '4500.00', NULL, 14, 'KNUCKLE-001', 'uploads/products/696392655986c.png', 'Акция', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(48, 'Сальники коленвала комплект', 'Комплект сальников коленчатого вала', 'двигатель', '3200.00', NULL, 18, 'SEAL-KIT', 'uploads/products/696392655986c.png', 'Хит', 'available', '2026-02-08 16:31:56', '2026-02-08 16:31:56', 'part', NULL, NULL, NULL, NULL, 0, NULL),
(49, 'Castrol EDGE 5W-30', 'Моторное масло Castrol 5W-30, 4 л', 'Масла и технические жидкости', '3890.00', NULL, 50, '15698E4', 'uploads/products/696392655986c.png', '0', 'available', '2026-02-12 14:58:10', '2026-02-12 16:31:24', 'oil', 'Castrol', '5W-30', 'Синтетическое', '4 л', 1, NULL),
(50, 'Mobil Super 3000 X1 5W-40', 'Моторное масло Mobil 5W-40, 4 л', 'Масла и технические жидкости', '3450.00', NULL, 50, '152343', 'uploads/products/696392655986c.png', NULL, 'available', '2026-02-12 14:58:10', '2026-02-12 16:31:37', 'oil', 'Mobil', '5W-40', 'Синтетическое', '4 л', 0, NULL),
(51, 'Liqui Moly Special Tec AA 5W-30', 'Моторное масло Liqui Moly 5W-30, 5 л', 'Масла и технические жидкости', '4210.00', NULL, 50, '1123DE', 'uploads/products/696392655986c.png', '0', 'available', '2026-02-12 14:58:10', '2026-02-12 16:31:52', 'oil', 'Liqui Moly', '5W-30', 'Синтетическое', '5 л', 1, NULL),
(52, 'Shell Helix HX7 10W-40', 'Моторное масло Shell 10W-40, 4 л', 'Масла и технические жидкости', '2890.00', NULL, 0, '87654F', 'uploads/products/696392655986c.png', NULL, 'out_of_stock', '2026-02-12 14:58:10', '2026-02-12 16:32:01', 'oil', 'Shell', '10W-40', 'Полусинтетическое', '4 л', 0, NULL),
(53, 'Total Quartz 9000 5W-40', 'Моторное масло Total 5W-40, 5 л', 'Масла и технические жидкости', '3650.00', NULL, 50, 'TQ9000', 'uploads/products/696392655986c.png', NULL, 'available', '2026-02-12 14:58:10', '2026-02-12 16:32:11', 'oil', 'Total', '5W-40', 'Синтетическое', '5 л', 0, NULL),
(54, 'Motul 8100 X-clean 5W-30', 'Моторное масло Motul 5W-30, 5 л', 'Масла и технические жидкости', '4890.00', NULL, 50, 'M8100', 'uploads/products/696392655986c.png', '0', 'available', '2026-02-12 14:58:10', '2026-02-12 16:32:20', 'oil', 'Motul', '5W-30', 'Синтетическое', '5 л', 1, NULL),
(55, 'ZIC X9 5W-30', 'Моторное масло ZIC 5W-30, 4 л', 'Масла и технические жидкости', '2990.00', NULL, 50, 'ZX9-5W30', 'uploads/products/696392655986c.png', NULL, 'available', '2026-02-12 14:58:10', '2026-02-12 16:32:29', 'oil', 'ZIC', '5W-30', 'Синтетическое', '4 л', 0, NULL),
(56, 'ELF Evolution 900 NF 5W-40', 'Моторное масло ELF 5W-40, 5 л', 'Масла и технические жидкости', '3750.00', NULL, 50, 'ELF900', 'uploads/products/696392655986c.png', NULL, 'available', '2026-02-12 14:58:10', '2026-02-12 16:32:40', 'oil', 'ELF', '5W-40', 'Синтетическое', '5 л', 0, NULL),
(57, 'Castrol MAGNATEC 5W-30', 'Моторное масло Castrol 5W-30, 4 л', 'Масла и технические жидкости', '3250.00', NULL, 50, 'CAST567', 'uploads/products/696392655986c.png', '0', 'available', '2026-02-12 14:58:10', '2026-02-12 16:32:50', 'oil', 'Castrol', '5W-30', 'Синтетическое', '4 л', 1, NULL),
(58, 'Mobil 1 0W-40', 'Моторное масло Mobil 0W-40, 4 л', 'Масла и технические жидкости', '4450.00', NULL, 50, 'MOB1-0W40', 'uploads/products/696392655986c.png', NULL, 'available', '2026-02-12 14:58:10', '2026-02-12 16:33:00', 'oil', 'Mobil', '0W-40', 'Синтетическое', '4 л', 0, NULL),
(59, 'Liqui Moly Molygen 5W-40', 'Моторное масло Liqui Moly 5W-40, 5 л', 'Масла и технические жидкости', '5120.00', NULL, 50, 'LM-MOLY', 'uploads/products/696392655986c.png', '0', 'available', '2026-02-12 14:58:10', '2026-02-12 16:33:11', 'oil', 'Liqui Moly', '5W-40', 'Синтетическое', '5 л', 1, NULL),
(60, 'Shell Helix Ultra 5W-40', 'Моторное масло Shell 5W-40, 4 л', 'Масла и технические жидкости', '3980.00', NULL, 50, 'SHU-5W40', 'uploads/products/696392655986c.png', NULL, 'available', '2026-02-12 14:58:10', '2026-02-12 16:33:25', 'oil', 'Shell', '5W-40', 'Синтетическое', '4 л', 0, NULL),
(61, 'Total Quartz INEO ECS 5W-30', 'Моторное масло Total 5W-30, 5 л', 'Масла и технические жидкости', '4120.00', NULL, 0, 'TQ-ECS', 'uploads/products/696392655986c.png', NULL, 'out_of_stock', '2026-02-12 14:58:10', '2026-02-12 16:33:36', 'oil', 'Total', '5W-30', 'Синтетическое', '5 л', 0, NULL),
(62, 'Motul 8100 Eco-nergy 5W-30', 'Моторное масло Motul 5W-30, 5 л', 'Масла и технические жидкости', '4670.00', NULL, 50, 'MOT-ECO', 'uploads/products/696392655986c.png', '0', 'available', '2026-02-12 14:58:10', '2026-02-12 16:33:47', 'oil', 'Motul', '5W-30', 'Синтетическое', '5 л', 1, NULL),
(63, 'ZIC X7 10W-40', 'Моторное масло ZIC 10W-40, 4 л', 'Масла и технические жидкости', '2450.00', NULL, 50, 'ZX7-10W40', 'uploads/products/696392655986c.png', NULL, 'available', '2026-02-12 14:58:10', '2026-02-12 16:33:58', 'oil', 'ZIC', '10W-40', 'Полусинтетическое', '4 л', 0, NULL),
(64, 'ELF Evolution 700 STI 10W-40', 'Моторное масло ELF 10W-40, 4 л', 'Масла и технические жидкости', '2780.00', NULL, 50, 'ELF700', 'uploads/products/696392655986c.png', NULL, 'available', '2026-02-12 14:58:10', '2026-02-12 16:34:07', 'oil', 'ELF', '10W-40', 'Полусинтетическое', '4 л', 0, NULL),
(65, 'Castrol EDGE 0W-20', 'Моторное масло Castrol 0W-20, 4 л', 'Масла и технические жидкости', '4120.00', NULL, 50, 'CAST-0W20', 'uploads/products/696392655986c.png', '0', 'available', '2026-02-12 14:58:10', '2026-02-12 16:34:18', 'oil', 'Castrol', '0W-20', 'Синтетическое', '4 л', 1, NULL),
(66, 'Mobil Super 2000 10W-40', 'Моторное масло Mobil 10W-40, 4 л', 'Масла и технические жидкости', '2670.00', NULL, 50, 'MS2000', 'uploads/products/696392655986c.png', NULL, 'available', '2026-02-12 14:58:10', '2026-02-12 16:34:27', 'oil', 'Mobil', '10W-40', 'Полусинтетическое', '4 л', 0, NULL),
(67, 'Liqui Moly Leichtlauf 10W-40', 'Моторное масло Liqui Moly 10W-40, 5 л', 'Масла и технические жидкости', '3890.00', NULL, 50, 'LM-LEICHT', 'uploads/products/696392655986c.png', NULL, 'available', '2026-02-12 14:58:10', '2026-02-12 16:34:38', 'oil', 'Liqui Moly', '10W-40', 'Синтетическое', '5 л', 0, NULL),
(68, 'Shell Helix HX8 5W-30', 'Моторное масло Shell 5W-30, 4 л', 'Масла и технические жидкости', '3450.00', NULL, 50, 'SH-HX8', 'uploads/products/696392655986c.png', '0', 'available', '2026-02-12 14:58:10', '2026-02-12 16:34:49', 'oil', 'Shell', '5W-30', 'Синтетическое', '4 л', 1, NULL),
(69, 'Total Quartz 7000 10W-40', 'Моторное масло Total 10W-40, 4 л', 'Масла и технические жидкости', '2780.00', NULL, 50, 'TQ7000', 'uploads/products/696392655986c.png', NULL, 'available', '2026-02-12 14:58:10', '2026-02-12 16:35:00', 'oil', 'Total', '10W-40', 'Полусинтетическое', '4 л', 0, NULL),
(70, 'Motul 8100 X-clean+ 5W-30', 'Моторное масло Motul 5W-30, 5 л', 'Масла и технические жидкости', '5120.00', NULL, 50, 'MOT-CLEAN+', 'uploads/products/696392655986c.png', '0', 'available', '2026-02-12 14:58:10', '2026-02-12 16:35:12', 'oil', 'Motul', '5W-30', 'Синтетическое', '5 л', 1, NULL),
(71, 'ZIC X5 10W-40', 'Моторное масло ZIC 10W-40, 4 л', 'Масла и технические жидкости', '2230.00', NULL, 50, 'ZX5-10W40', 'uploads/products/696392655986c.png', NULL, 'available', '2026-02-12 14:58:10', '2026-02-12 16:35:22', 'oil', 'ZIC', '10W-40', 'Минеральное', '4 л', 0, NULL),
(72, 'ELF Evolution SXR 5W-30', 'Моторное масло ELF 5W-30, 5 л', 'Масла и технические жидкости', '3980.00', NULL, 50, 'ELF-SXR', 'uploads/products/696392655986c.png', NULL, 'available', '2026-02-12 14:58:10', '2026-02-12 16:35:32', 'oil', 'ELF', '5W-30', 'Синтетическое', '5 л', 0, NULL),
(73, 'Чехлы на сиденья Premium', 'Качественные автомобильные чехлы из экокожи', 'Для салона', '4290.00', '5050.00', 50, 'ACS-001', 'uploads/products/696392655986c.png', 'danger', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'AutoStyle', NULL, NULL, NULL, 1, NULL),
(74, 'Коврики в салон 3D', 'Трехмерные коврики для защиты салона', 'Для салона', '6790.00', '0.00', 45, 'ACS-002', 'uploads/products/696392655986c.png', 'success', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'WeatherTech', NULL, NULL, NULL, 1, NULL),
(75, 'Органайзер для багажника', 'Удобный органайзер для багажного отделения', 'Для салона', '3490.00', '0.00', 60, 'ACS-003', 'uploads/products/696392655986c.png', '', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'CarMate', NULL, NULL, NULL, 0, NULL),
(76, 'Ароматизатор CS-X3', 'Автомобильный ароматизатор с запахом свежести', 'Для салона', '790.00', '0.00', 100, 'ACS-004', 'uploads/products/696392655986c.png', 'info', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'Air Spencer', NULL, NULL, NULL, 1, NULL),
(77, 'Автохолодильник 12V', 'Портативный автомобильный холодильник', 'Для салона', '8990.00', '10500.00', 20, 'ACS-005', 'uploads/products/696392655986c.png', 'warning', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'CoolMaster', NULL, NULL, NULL, 0, NULL),
(78, 'Видеорегистратор 4K', 'Автомобильный видеорегистратор с записью 4K', 'Электроника', '12490.00', '0.00', 30, 'ACS-006', 'uploads/products/696392655986c.png', 'success', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'RoadEye', NULL, NULL, NULL, 1, NULL),
(79, 'Чехол на руль из кожи', 'Кожаный чехол для рулевого колеса', 'Для салона', '2190.00', '0.00', 70, 'ACS-007', 'uploads/products/696392655986c.png', 'info', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'SteeringPro', NULL, NULL, NULL, 1, NULL),
(80, 'Компрессор автомобильный', 'Автомобильный компрессор для подкачки шин', 'Электроника', '3590.00', '4490.00', 40, 'ACS-008', 'uploads/products/696392655986c.png', 'danger', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'AirForce', NULL, NULL, NULL, 0, NULL),
(81, 'Держатель магнитный', 'Магнитный держатель для телефона в авто', 'Электроника', '1290.00', '0.00', 80, 'ACS-009', 'uploads/products/696392655986c.png', '', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'PhoneMount', NULL, NULL, NULL, 0, NULL),
(82, 'Парктроник 8 датчиков', 'Парковочный радар с 8 датчиками', 'Электроника', '7890.00', '0.00', 25, 'ACS-010', 'uploads/products/696392655986c.png', 'success', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'ParkMaster', NULL, NULL, NULL, 1, NULL),
(83, 'Автоодеяло с подогревом', 'Одеяло для автомобиля с подогревом', 'Для салона', '5490.00', '0.00', 35, 'ACS-011', 'uploads/products/696392655986c.png', 'info', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'ComfortCar', NULL, NULL, NULL, 1, NULL),
(84, 'Набор автомобильных инструментов', 'Универсальный набор инструментов для авто', 'Для экстерьера', '6990.00', '8200.00', 30, 'ACS-012', 'uploads/products/696392655986c.png', 'warning', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'ToolPro', NULL, NULL, NULL, 0, NULL),
(85, 'Воск для полировки кузова', 'Профессиональный воск для полировки', 'Уход за авто', '1890.00', '0.00', 60, 'ACS-013', 'uploads/products/696392655986c.png', '', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'Meguire\'s', NULL, NULL, NULL, 0, NULL),
(86, 'Щетки стеклоочистителя', 'Комплект стеклоочистителей Bosch', 'Для экстерьера', '2490.00', '2990.00', 50, 'ACS-014', 'uploads/products/696392655986c.png', 'danger', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'Bosch', NULL, NULL, NULL, 0, NULL),
(87, 'Чехол на автомобиль', 'Защитный чехол для всего автомобиля', 'Для экстерьера', '8990.00', '0.00', 20, 'ACS-015', 'uploads/products/696392655986c.png', 'success', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'CoverKing', NULL, NULL, NULL, 1, NULL),
(88, 'Шумоизоляция салона', 'Комплект для шумоизоляции автомобиля', 'Для салона', '12990.00', '0.00', 25, 'ACS-016', 'uploads/products/696392655986c.png', 'info', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'NoiseGuard', NULL, NULL, NULL, 1, NULL),
(89, 'Автосканер OBD2', 'Диагностический сканер для автомобиля', 'Электроника', '4590.00', '0.00', 40, 'ACS-017', 'uploads/products/696392655986c.png', '', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'Launch', NULL, NULL, NULL, 0, NULL),
(90, 'Коврик багажника', 'Резиновый коврик для багажника', 'Для салона', '4290.00', '0.00', 55, 'ACS-018', 'uploads/products/696392655986c.png', '', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'WeatherTech', NULL, NULL, NULL, 0, NULL),
(91, 'Зарядное устройство USB', 'Быстрая зарядка для автомобиля', 'Электроника', '1590.00', '1990.00', 90, 'ACS-019', 'uploads/products/696392655986c.png', 'warning', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'Anker', NULL, NULL, NULL, 0, NULL),
(92, 'Очиститель кондиционера', 'Средство для очистки кондиционера', 'Уход за авто', '890.00', '0.00', 70, 'ACS-020', 'uploads/products/696392655986c.png', '', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'Wynn\'s', NULL, NULL, NULL, 0, NULL),
(93, 'Брелок с сигнализацией', 'Брелок сигнализации с автозапуском', 'Для экстерьера', '2990.00', '0.00', 45, 'ACS-021', 'uploads/products/696392655986c.png', '', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'KeySafe', NULL, NULL, NULL, 0, NULL),
(94, 'Насос для подкачки шин', 'Автомобильный насос с манометром', 'Для экстерьера', '3290.00', '0.00', 60, 'ACS-022', 'uploads/products/696392655986c.png', '', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'Michelin', NULL, NULL, NULL, 0, NULL),
(95, 'Чистящее средство для салона', 'Пена для чистки салона автомобиля', 'Уход за авто', '1290.00', '0.00', 80, 'ACS-023', 'uploads/products/696392655986c.png', '', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'Sonax', NULL, NULL, NULL, 0, NULL),
(96, 'Антидождь для стекол', 'Средство для защиты стекол от дождя', 'Уход за авто', '1490.00', '0.00', 85, 'ACS-024', 'uploads/products/696392655986c.png', 'info', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'RainX', NULL, NULL, NULL, 1, NULL),
(97, 'Коврики резиновые Universal', 'Универсальные резиновые коврики', 'Для салона', '1890.00', '2290.00', 75, 'ACS-025', 'uploads/products/696392655986c.png', 'danger', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'AutoPro', NULL, NULL, NULL, 0, NULL),
(98, 'Чехол на сиденье с подогревом', 'Автомобильный чехол с подогревом', 'Для салона', '6590.00', '0.00', 40, 'ACS-026', 'uploads/products/696392655986c.png', 'success', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'HotSeat', NULL, NULL, NULL, 1, NULL),
(99, 'Авто пылесос мощный', 'Портативный автомобильный пылесос', 'Для салона', '3290.00', '3990.00', 50, 'ACS-027', 'uploads/products/696392655986c.png', 'warning', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'Black+Decker', NULL, NULL, NULL, 0, NULL),
(100, 'Зеркало видеорегистратора', 'Зеркало заднего вида с видеорегистратором', 'Электроника', '8990.00', '0.00', 35, 'ACS-028', 'uploads/products/696392655986c.png', 'info', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'MirrorCam', NULL, NULL, NULL, 1, NULL),
(101, 'Навигатор 7 дюймов', 'GPS навигатор с экраном 7 дюймов', 'Электроника', '12990.00', '14990.00', 25, 'ACS-029', 'uploads/products/696392655986c.png', 'danger', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'Garmin', NULL, NULL, NULL, 0, NULL),
(102, 'Радар-детектор Pro', 'Радар-детектор с дальним обнаружением', 'Электроника', '7590.00', '0.00', 40, 'ACS-030', 'uploads/products/696392655986c.png', '', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'StreetStorm', NULL, NULL, NULL, 0, NULL),
(103, 'Автосигнализация с автозапуском', 'Современная автосигнализация', 'Электроника', '15990.00', '18990.00', 20, 'ACS-031', 'uploads/products/696392655986c.png', 'warning', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'StarLine', NULL, NULL, NULL, 1, NULL),
(104, 'Камера заднего вида', 'Камера заднего вида для автомобиля', 'Электроника', '4290.00', '0.00', 45, 'ACS-032', 'uploads/products/696392655986c.png', 'success', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'ParkMaster', NULL, NULL, NULL, 1, NULL),
(105, 'Фаркоп универсальный', 'Универсальный фаркоп для автомобиля', 'Для экстерьера', '8990.00', '0.00', 30, 'ACS-033', 'uploads/products/696392655986c.png', '', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'Bosch', NULL, NULL, NULL, 0, NULL),
(106, 'Дефлекторы окон', 'Ветровики для окон автомобиля', 'Для экстерьера', '3490.00', '0.00', 50, 'ACS-034', 'uploads/products/696392655986c.png', '', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'WeatherTech', NULL, NULL, NULL, 0, NULL),
(107, 'Спойлер задний', 'Декоративный задний спойлер', 'Для экстерьера', '7890.00', '8990.00', 25, 'ACS-035', 'uploads/products/696392655986c.png', 'danger', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'AutoStyle', NULL, NULL, NULL, 0, NULL),
(108, 'Накладки на пороги', 'Защитные накладки на пороги', 'Для экстерьера', '4590.00', '0.00', 40, 'ACS-036', 'uploads/products/696392655986c.png', 'info', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'SteelGuard', NULL, NULL, NULL, 1, NULL),
(109, 'Шумоизоляция дверей', 'Шумоизоляция для дверей автомобиля', 'Для салона', '6990.00', '0.00', 30, 'ACS-037', 'uploads/products/696392655986c.png', '', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'NoiseGuard', NULL, NULL, NULL, 0, NULL),
(110, 'Полироль для кузова', 'Полироль для восстановления цвета', 'Уход за авто', '1290.00', '1590.00', 70, 'ACS-038', 'uploads/products/696392655986c.png', 'warning', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'Turtle Wax', NULL, NULL, NULL, 0, NULL),
(111, 'Очиститель тормозных дисков', 'Специальное средство для тормозов', 'Уход за авто', '890.00', '0.00', 80, 'ACS-039', 'uploads/products/696392655986c.png', '', 'available', '2026-02-13 15:38:45', '2026-02-13 15:39:06', 'accessory', 'LIQUI MOLY', NULL, NULL, NULL, 0, NULL),
(112, 'Воск для шин', 'Воск для защиты и блеска шин', 'Уход за авто', '790.00', '0.00', 90, 'ACS-040', 'uploads/products/696392655986c.png', '', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'Sonax', NULL, NULL, NULL, 0, NULL),
(113, 'Щетка для снега', 'Автомобильная щетка для снега со скребком', 'Для экстерьера', '1590.00', '1990.00', 60, 'ACS-041', 'uploads/products/696392655986c.png', 'danger', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'SnowJoe', NULL, NULL, NULL, 0, NULL),
(114, 'Антизапотеватель стекол', 'Средство против запотевания стекол', 'Уход за авто', '490.00', '0.00', 100, 'ACS-042', 'uploads/products/696392655986c.png', '', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'GlassCare', NULL, NULL, NULL, 0, NULL),
(115, 'Домкрат гидравлический', 'Гидравлический домкрат 2 тонны', 'Для экстерьера', '3890.00', '0.00', 40, 'ACS-043', 'uploads/products/696392655986c.png', '', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'ForceFlex', NULL, NULL, NULL, 0, NULL),
(116, 'Знак аварийной остановки', 'Светоотражающий знак аварийной остановки', 'Для экстерьера', '590.00', '0.00', 120, 'ACS-044', 'uploads/products/696392655986c.png', '', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'AutoSafe', NULL, NULL, NULL, 0, NULL),
(117, 'Огнетушитель автомобильный', 'Компактный огнетушитель для авто', 'Для экстерьера', '1290.00', '0.00', 80, 'ACS-045', 'uploads/products/696392655986c.png', 'info', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'FireStop', NULL, NULL, NULL, 1, NULL),
(118, 'Аптечка первой помощи', 'Автомобильная аптечка ФЭСТ', 'Для экстерьера', '1890.00', '0.00', 70, 'ACS-046', 'uploads/products/696392655986c.png', '', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'MediKit', NULL, NULL, NULL, 0, NULL),
(119, 'Багажные ремни', 'Ремни для фиксации груза', 'Для салона', '1290.00', '0.00', 90, 'ACS-047', 'uploads/products/696392655986c.png', '', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'CargoTie', NULL, NULL, NULL, 0, NULL),
(120, 'Органайзер для бардачка', 'Органайзер для хранения вещей в бардачке', 'Для салона', '890.00', '0.00', 100, 'ACS-048', 'uploads/products/696392655986c.png', '', 'available', '2026-02-13 15:38:45', '2026-02-13 15:38:45', 'accessory', 'CarOrganizer', NULL, NULL, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `remember_tokens`
--

CREATE TABLE `remember_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `rating` int(11) NOT NULL,
  `text` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `reviews`
--

INSERT INTO `reviews` (`id`, `name`, `email`, `rating`, `text`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Иван Петров', 'ivan@mail.ru', 5, 'Отличный сервис! Быстро и качественно починили мой автомобиль. Персонал вежливый, цены адекватные. Рекомендую всем!', 'approved', '2024-01-15 07:30:00', '2025-11-11 16:36:39'),
(2, 'Мария Сидорова', 'maria@yandex.ru', 4, 'Хороший магазин автозапчастей. Большой выбор, консультанты помогли подобрать нужную деталь. Не хватило только скидочной системы для постоянных клиентов.', 'approved', '2024-01-20 11:45:00', '2025-11-11 16:36:39'),
(3, 'Алексей Козлов', 'alex@mail.ru', 5, 'Лучший автосервис в городе! Делали полное ТО, всё выполнили в срок, дали полезные советы по эксплуатации. Буду обращаться только сюда.', 'approved', '2024-02-01 06:15:00', '2026-01-07 18:31:54'),
(7, 'Наталья', 'email2@gmail.com', 4, 'Круто!', 'approved', '2026-01-07 18:33:14', '2026-01-07 18:35:20');

-- --------------------------------------------------------

--
-- Структура таблицы `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text,
  `duration` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `services`
--

INSERT INTO `services` (`id`, `name`, `category`, `price`, `description`, `duration`, `status`, `created_at`) VALUES
(1, 'Замена масла', 'Техническое обслуживание', '1500.00', 'Замена моторного масла и масляного фильтра', 30, 'active', '2026-01-06 17:15:45'),
(2, 'Диагностика двигателя', 'Диагностика', '3000.00', 'Комплексная диагностика двигателя', 60, 'active', '2026-01-06 17:15:45');

-- --------------------------------------------------------

--
-- Структура таблицы `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text,
  `setting_group` varchar(50) DEFAULT 'general',
  `setting_type` varchar(20) DEFAULT 'text',
  `label` varchar(200) DEFAULT NULL,
  `description` text,
  `options` text,
  `is_public` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'Лал-Авто', 'general', 'text', 'Название сайта', NULL, NULL, 0, '2026-01-11 12:15:06', '2026-01-11 14:18:20'),
(2, 'admin_email', 'admin@lal-auto.ru', 'general', 'email', 'Email администратора', NULL, NULL, 0, '2026-01-11 12:15:06', '2026-01-11 14:18:20'),
(3, 'support_phone', '+7 (999) 123-45-67', 'general', 'tel', 'Телефон поддержки', NULL, NULL, 0, '2026-01-11 12:15:06', '2026-01-11 14:18:20'),
(4, 'working_hours', 'Пн-Пт: 9:00-18:00, Сб: 10:00-16:00', 'general', 'text', 'Время работы', NULL, NULL, 0, '2026-01-11 12:15:06', '2026-01-11 14:18:20'),
(5, 'min_order_amount', '1000', 'store', 'number', 'Минимальная сумма заказа', NULL, NULL, 0, '2026-01-11 12:15:06', '2026-01-11 14:18:20'),
(6, 'vat_rate', '20', 'store', 'select', 'Ставка НДС', NULL, NULL, 0, '2026-01-11 12:15:06', '2026-01-11 14:18:20'),
(7, 'maintenance_mode', '0', 'maintenance', 'checkbox', 'Режим обслуживания', NULL, NULL, 0, '2026-01-11 12:15:06', '2026-01-11 14:22:40'),
(8, 'api_enabled', '1', 'api', 'checkbox', 'Включить API', NULL, NULL, 0, '2026-01-11 12:15:06', '2026-01-11 14:18:20'),
(9, 'system_version', '2.2.0', 'system', 'text', 'Версия системы', NULL, NULL, 0, '2026-01-11 12:15:06', '2026-01-11 14:22:40'),
(10, 'group', 'api', 'general', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:15:53', '2026-01-11 14:18:13'),
(11, 'site_description', 'Автозапчасти и автосервис - качественное обслуживание вашего автомобиля', 'general', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:15:53', '2026-01-11 14:18:20'),
(12, 'default_language', 'Русский', 'general', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:15:53', '2026-01-11 14:18:20'),
(13, 'currency', 'RUB', 'general', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:15:53', '2026-01-11 14:18:20'),
(14, 'email_new_orders', '1', 'notifications', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(15, 'email_payments', '1', 'notifications', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(16, 'email_low_stock', '1', 'notifications', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(17, 'sms_promo', '1', 'notifications', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(18, 'smtp_server', 'smtp.gmail.com', 'notifications', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(19, 'smtp_port', '587', 'notifications', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(20, 'bank_cards_enabled', '1', 'payment', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(21, 'yoomoney_enabled', '1', 'payment', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(22, 'cash_on_delivery', '1', 'payment', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(23, 'processing_fee', '2.5', 'payment', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(24, 'min_fee', '10', 'payment', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(25, 'courier_enabled', '1', 'shipping', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(26, 'courier_cost', '300', 'shipping', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(27, 'pickup_enabled', '1', 'shipping', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(28, 'russian_post_cost', '500', 'shipping', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(29, 'cdek_enabled', '1', 'shipping', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(30, 'cdek_cost', '450', 'shipping', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(31, 'free_shipping_min', '5000', 'shipping', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(32, 'delivery_days', '3', 'shipping', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(33, 'min_password_length', '8', 'security', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(34, 'password_expiry_days', '90', 'security', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(35, 'require_special_char', '1', 'security', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(36, 'require_numbers', '1', 'security', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(37, 'prevent_reuse', '1', 'security', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(38, 'max_login_attempts', '5', 'security', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(39, 'lockout_minutes', '30', 'security', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(40, 'usd_rate', '90.5', 'store', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(41, 'eur_rate', '99.8', 'store', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(42, 'low_stock_alert', '1', 'store', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(43, 'return_policy', 'Возврат товара возможен в течение 14 дней с момента покупки при сохранении товарного вида и упаковки.', 'store', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:03', '2026-01-11 14:18:20'),
(44, 'request_limit', '100', 'api', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:05', '2026-01-11 14:18:20'),
(45, 'webhook_url', '', 'api', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:05', '2026-01-11 14:18:13'),
(46, 'meta_title', 'Лал-Авто - Автозапчасти и автосервис', 'seo', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:05', '2026-01-11 14:18:20'),
(47, 'meta_description', 'Качественные автозапчасти и профессиональный автосервис. Широкий ассортимент, доступные цены, гарантия качества.', 'seo', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:05', '2026-01-11 14:18:20'),
(48, 'meta_keywords', 'автозапчасти, автосервис, автомобильные запчасти, ремонт авто', 'seo', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:05', '2026-01-11 14:18:20'),
(49, 'og_title', 'Лал-Авто', 'seo', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:05', '2026-01-11 14:18:20'),
(50, 'og_image', '', 'seo', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:05', '2026-01-11 14:18:13'),
(51, 'seo_friendly_urls', '1', 'seo', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:05', '2026-01-11 14:18:20'),
(52, 'generate_sitemap', '1', 'seo', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:05', '2026-01-11 14:18:20'),
(53, 'robots_txt', 'User-agent: *\nDisallow: /admin/\nDisallow: /cart/\nAllow: /public/\nSitemap: https://lal-auto.ru/sitemap.xml', 'seo', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:05', '2026-01-11 14:18:20'),
(54, 'maintenance_message', 'Сайт временно недоступен. Ведутся технические работы. Приносим извинения за неудобства.', 'maintenance', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:05', '2026-01-11 14:18:20'),
(55, 'allow_backorder', '0', 'store', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:10', '2026-01-11 14:18:20'),
(56, 'email_reviews', '0', 'notifications', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:10', '2026-01-11 14:18:20'),
(57, 'email_newsletter', '0', 'notifications', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:10', '2026-01-11 14:18:20'),
(58, 'sms_order_status', '0', 'notifications', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:10', '2026-01-11 14:18:20'),
(59, 'sms_delivery', '0', 'notifications', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:10', '2026-01-11 14:18:20'),
(60, 'require_upper_lower', '0', 'security', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:10', '2026-01-11 14:18:20'),
(61, 'enable_2fa_admin', '0', 'security', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:10', '2026-01-11 14:18:20'),
(62, 'enable_2fa_users', '0', 'security', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:10', '2026-01-11 14:18:20'),
(63, 'sberbank_enabled', '0', 'payment', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:10', '2026-01-11 14:18:20'),
(64, 'russian_post_enabled', '0', 'shipping', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:10', '2026-01-11 14:18:20'),
(65, 'graphql_enabled', '0', 'api', 'text', NULL, NULL, NULL, 0, '2026-01-11 12:16:10', '2026-01-11 14:18:20');

--
-- Триггеры `settings`
--
DELIMITER $$
CREATE TRIGGER `update_settings_timestamp` BEFORE UPDATE ON `settings` FOR EACH ROW BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Структура таблицы `shops`
--

CREATE TABLE `shops` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(20) DEFAULT 'branch',
  `region` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text NOT NULL,
  `area` decimal(10,2) DEFAULT '0.00',
  `employees` int(11) DEFAULT '0',
  `status` varchar(20) DEFAULT 'active',
  `description` text,
  `schedule` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `shops`
--

INSERT INTO `shops` (`id`, `name`, `type`, `region`, `phone`, `email`, `address`, `area`, `employees`, `status`, `description`, `schedule`, `created_at`) VALUES
(1, 'Лал-Авто Центр', 'main', 'Москва', '+7 (495) 123-45-67', 'center@lal-auto.ru', 'ул. Ленина, 123, бизнес-центр \"Северный\"', '250.50', 15, 'active', 'Основной магазин компании', '09:00 - 18:00', '2026-01-07 17:00:41'),
(2, 'Лал-Авто Север', 'branch', 'Санкт-Петербург', '+7 (812) 987-65-43', 'north@lal-auto.ru', 'пр. Мира, 45, торговый комплекс \"Европа\"', '180.00', 10, 'active', 'Филиал в Санкт-Петербурге', '10:00 - 19:00', '2026-01-07 17:00:41');

-- --------------------------------------------------------

--
-- Структура таблицы `system_versions`
--

CREATE TABLE `system_versions` (
  `id` int(11) NOT NULL,
  `version` varchar(20) NOT NULL,
  `installed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `notes` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `system_versions`
--

INSERT INTO `system_versions` (`id`, `version`, `installed_at`, `notes`) VALUES
(2, '2.2.0', '2026-01-11 12:47:28', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `update_logs`
--

CREATE TABLE `update_logs` (
  `id` int(11) NOT NULL,
  `old_version` varchar(20) NOT NULL,
  `new_version` varchar(20) NOT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `details` text,
  `error_message` text,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `update_logs`
--

INSERT INTO `update_logs` (`id`, `old_version`, `new_version`, `status`, `details`, `error_message`, `updated_at`) VALUES
(2, '2.1.0', '2.2.0', 'success', 'Системное обновление', NULL, '2026-01-11 12:47:28');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id_users` int(11) NOT NULL,
  `surname_users` varchar(255) DEFAULT NULL,
  `name_users` varchar(255) DEFAULT NULL,
  `patronymic_users` varchar(255) DEFAULT NULL,
  `login_users` varchar(255) DEFAULT NULL,
  `password_users` varchar(255) DEFAULT NULL,
  `email_users` varchar(255) DEFAULT NULL,
  `discountСardNumber_users` varchar(6) DEFAULT NULL,
  `region_users` varchar(255) DEFAULT NULL,
  `city_users` varchar(255) DEFAULT NULL,
  `address_users` varchar(255) DEFAULT NULL,
  `phone_users` bigint(12) DEFAULT NULL,
  `avatar_users` varchar(255) DEFAULT NULL,
  `TIN_users` bigint(10) DEFAULT NULL,
  `person_users` varchar(255) DEFAULT NULL,
  `organization_users` varchar(255) DEFAULT NULL,
  `organizationType_users` varchar(255) DEFAULT NULL,
  `user_type` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id_users`, `surname_users`, `name_users`, `patronymic_users`, `login_users`, `password_users`, `email_users`, `discountСardNumber_users`, `region_users`, `city_users`, `address_users`, `phone_users`, `avatar_users`, `TIN_users`, `person_users`, `organization_users`, `organizationType_users`, `user_type`) VALUES
(1, NULL, NULL, NULL, 'admin', 'admin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(2, 'Иванов', 'Иван', 'Иванович', 'user1', 'user1', 'user1@gmail.com', '223344', 'Калининградская область', 'Калининград', 'Малый переулок, 3', 89113456789, 'uploads/avatars/avatar_2_1758131749.jpg', NULL, NULL, NULL, NULL, 'physical'),
(3, NULL, NULL, NULL, 'user2', 'user2', 'user2@gmail.com', NULL, 'Калининградская область', 'Калининград', 'Уральская улица, 20', 89114567891, NULL, 2222455179, 'Наталья Евгеньевна Графарова', 'Дизель-мастер', 'ООО', 'legal'),
(4, NULL, NULL, NULL, 'user3', 'user3new', 'user3@gmail.com', '556677', 'Калининградская область', 'Балтийск', 'Киркенесская улица, 20', 89115678912, NULL, 5552431142, 'Иван Иванович Иванов', 'КлассикАвто', 'ЗАО', 'legal'),
(5, 'Рожков', 'Олег', 'Константинович', 'user4', 'user4', 'user4@gmail.com', NULL, 'Калининградская область', 'Черняховск', 'улица Советская, 5', 89116789123, NULL, NULL, NULL, NULL, NULL, 'physical');

-- --------------------------------------------------------

--
-- Структура таблицы `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_name`, `product_image`, `price`, `created_at`) VALUES
(3, 3, 'Свечи зажигания NGK BKR6E', 'img/no-image.png', '850.00', '2026-01-15 13:29:33'),
(4, 4, 'Тормозные колодки Brembo P85115', 'img/no-image.png', '3890.00', '2026-01-15 13:29:33'),
(5, 5, 'Фильтр масляный Mann W914/2', 'img/no-image.png', '1250.00', '2026-01-15 13:29:33'),
(13, 2, 'Фара передняя BMW 3 series F30', 'uploads/products/696392655986c.png', '18700.00', '2026-02-14 15:52:56'),
(14, 2, 'Тормозные колодки Audi A6 C7', 'uploads/products/696392655986c.png', '3890.00', '2026-02-14 15:53:36');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `action_logs`
--
ALTER TABLE `action_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `api_keys`
--
ALTER TABLE `api_keys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `api_key` (`api_key`),
  ADD KEY `idx_api_key` (`api_key`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- Индексы таблицы `backup_logs`
--
ALTER TABLE `backup_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_filename` (`filename`(100));

--
-- Индексы таблицы `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_cart_product` (`product_id`);

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `category_products`
--
ALTER TABLE `category_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category_type` (`category_type`),
  ADD KEY `idx_brand` (`brand`),
  ADD KEY `idx_price` (`price`);

--
-- Индексы таблицы `company_documents`
--
ALTER TABLE `company_documents`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `company_requisites`
--
ALTER TABLE `company_requisites`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Индексы таблицы `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_short_token` (`short_token`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_price` (`price`);

--
-- Индексы таблицы `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_token` (`token`);

--
-- Индексы таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_group` (`setting_group`),
  ADD KEY `idx_key` (`setting_key`);

--
-- Индексы таблицы `shops`
--
ALTER TABLE `shops`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_region` (`region`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_type` (`type`);

--
-- Индексы таблицы `system_versions`
--
ALTER TABLE `system_versions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_version` (`version`);

--
-- Индексы таблицы `update_logs`
--
ALTER TABLE `update_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_versions` (`old_version`,`new_version`),
  ADD KEY `idx_status` (`status`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_users`);

--
-- Индексы таблицы `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `action_logs`
--
ALTER TABLE `action_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `api_keys`
--
ALTER TABLE `api_keys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `backup_logs`
--
ALTER TABLE `backup_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `category_products`
--
ALTER TABLE `category_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT для таблицы `company_documents`
--
ALTER TABLE `company_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `company_requisites`
--
ALTER TABLE `company_requisites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT для таблицы `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT для таблицы `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT для таблицы `shops`
--
ALTER TABLE `shops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `system_versions`
--
ALTER TABLE `system_versions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `update_logs`
--
ALTER TABLE `update_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id_users` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `action_logs`
--
ALTER TABLE `action_logs`
  ADD CONSTRAINT `action_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_users`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_users`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_users`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_users`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_password_resets_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_users`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD CONSTRAINT `fk_remember_tokens_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_users`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_users`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
