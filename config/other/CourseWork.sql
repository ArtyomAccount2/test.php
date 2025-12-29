-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Дек 29 2025 г., 19:54
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
-- Структура таблицы `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
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

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `product_name`, `product_image`, `price`, `quantity`, `created_at`, `updated_at`) VALUES
(6, 2, 0, 'Фильтр масляный Audi A4 B8 2.0 TFSI', '../img/no-image.png', '1250.00', 1, '2025-12-29 16:30:35', '2025-12-29 16:30:35'),
(7, 2, 0, 'Тормозные колодки Audi A6 C7', '../img/no-image.png', '3890.00', 1, '2025-12-29 16:30:51', '2025-12-29 16:30:51'),
(8, 2, 0, 'Свечи зажигания Audi Q5 2.0 TDI', '../img/no-image.png', '850.00', 2, '2025-12-29 16:30:53', '2025-12-29 16:33:55'),
(9, 2, 0, 'Сцепление Audi A3 8V', '../img/no-image.png', '12500.00', 1, '2025-12-29 16:31:09', '2025-12-29 16:31:09');

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
(3, 'Алексей Козлов', 'alex@mail.ru', 5, 'Лучший автосервис в городе! Делали полное ТО, всё выполнили в срок, дали полезные советы по эксплуатации. Буду обращаться только сюда.', 'approved', '2024-02-01 06:15:00', '2025-11-11 16:36:39'),
(6, 'Наталья', 'email7@gmail.com', 4, 'Круто!', 'approved', '2025-11-12 17:13:10', '2025-11-12 17:13:52');

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
  `organizationType_users` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id_users`, `surname_users`, `name_users`, `patronymic_users`, `login_users`, `password_users`, `email_users`, `discountСardNumber_users`, `region_users`, `city_users`, `address_users`, `phone_users`, `avatar_users`, `TIN_users`, `person_users`, `organization_users`, `organizationType_users`) VALUES
(1, NULL, NULL, NULL, 'admin', 'admin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'Иванов', 'Иван', 'Иванович', 'user1', 'user1', 'user1@gmail.com', '223344', 'Калининградская область', 'Калининград', 'Малый переулок, 3', 89113456789, 'uploads/avatars/avatar_2_1758131749.jpg', NULL, NULL, NULL, NULL),
(3, NULL, NULL, NULL, 'user2', 'user2', 'user2@gmail.com', NULL, 'Калининградская область', 'Калининград', 'Уральская улица, 20', 89114567891, NULL, 2222455179, 'Наталья Евгеньевна Графарова', 'Дизель-мастер', 'ООО'),
(4, NULL, NULL, NULL, 'user3', 'user3', 'user3@gmail.com', '556677', 'Калининградская область', 'Балтийск', 'Киркенесская улица, 20', 89115678912, NULL, 5552431142, 'Иван Иванович Иванов', 'КлассикАвто', 'ЗАО'),
(5, 'Рожков', 'Олег', 'Константинович', 'user4', 'user4', 'user4@gmail.com', NULL, 'Калининградская область', 'Черняховск', 'улица Советская, 5', 89116789123, NULL, NULL, NULL, NULL, NULL);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `cart`
--
ALTER TABLE `cart`
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
-- Индексы таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_users`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id_users` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_users`) ON DELETE CASCADE;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
