-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Дек 03 2025 г., 18:46
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
-- AUTO_INCREMENT для таблицы `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id_users` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
