-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Янв 23 2026 г., 21:33
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
(2, 'secret', 'sk_live_0530805657771205c63ddf970a3b4365', 'sk_3d6b3db0920b13298822604a87e2e7ea83be75a7d0d363ea', 'active', 'read,write', NULL, '2026-01-11 14:24:16', '2027-01-11 14:24:16', NULL);

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
(20, 3, 0, 'Фильтр масляный Audi A4 B8 2.0 TFSI', '../img/no-image.png', '1250.00', 1, '2026-01-05 15:19:41', '2026-01-05 15:19:41'),
(21, 3, 0, 'Тормозные колодки Audi A6 C7', '../img/no-image.png', '3890.00', 1, '2026-01-05 15:19:42', '2026-01-05 15:19:42'),
(22, 3, 0, 'Свечи зажигания Audi Q5 2.0 TDI', '../img/no-image.png', '850.00', 1, '2026-01-05 15:19:43', '2026-01-05 15:19:43'),
(23, 3, 0, 'Сцепление Audi A3 8V', '../img/no-image.png', '12500.00', 2, '2026-01-05 15:19:43', '2026-01-05 15:19:50'),
(39, 2, 0, 'Тормозные колодки Audi A6 C7', '../img/no-image.png', '3890.00', 1, '2026-01-23 18:12:11', '2026-01-23 18:12:11'),
(40, 2, 0, 'Сцепление Audi A3 8V', '../img/no-image.png', '12500.00', 2, '2026-01-23 18:12:14', '2026-01-23 18:12:16'),
(41, 2, 6, 'Motul 8100 X-clean 5W-30', '../img/no-image.png', '4890.00', 1, '2026-01-23 18:12:30', '2026-01-23 18:12:30'),
(42, 2, 3, 'Органайзер для багажника', '../img/no-image.png', '3490.00', 1, '2026-01-23 18:12:56', '2026-01-23 18:12:56');

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
(1, 'ORD-20260105-5A3C6597', 2, '41700.00', 'pending', '2026-01-05 15:00:31', '', '89113456789', '');

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
(4, 1, 0, 'Тормозные колодки BMW 1 series F20', '5200.00', 2);

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
(20, 4, 'aa261e316f214a318e91e660bd08ccc86a73def1d906eeb0fbc0e71d751b3d69', 'iZ6U6x', '2026-01-13 12:55:17', 1, '2026-01-13 08:55:17');

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
  `quantity` int(11) DEFAULT '0',
  `article` varchar(100) DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `category`, `price`, `quantity`, `article`, `image`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Моторное масло 5W-40', 'Синтетическое моторное масло для всех типов двигателей', 'Масла', '2500.00', 45, 'MO-5W40-001', 'uploads/products/696392655986c.png', 'available', '2026-01-07 17:51:40', '2026-01-11 12:08:27'),
(2, 'Воздушный фильтр', 'Воздушный фильтр для легковых автомобилей', 'Запчасти', '800.00', 23, 'AF-001', 'uploads/products/696392655986c.png', 'low', '2026-01-07 17:51:40', '2026-01-17 17:49:31'),
(3, 'Тормозные колодки', 'Передние тормозные колодки', 'Запчасти', '3200.00', 15, 'TB-001', 'uploads/products/696392655986c.png', 'available', '2026-01-07 17:51:40', '2026-01-11 12:08:09'),
(4, 'Аккумулятор 60Ah', 'Свинцово-кислотный аккумулятор 60Ah', 'Аксессуары', '5500.00', 8, 'BAT-60', 'uploads/products/696392655986c.png', 'available', '2026-01-07 17:51:40', '2026-01-11 12:08:01');

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
(1, 2, 'Моторное масло Castrol 5W-40', 'img/no-image.png', '3450.00', '2026-01-15 13:29:33'),
(2, 2, 'Воздушный фильтр Mann', 'img/no-image.png', '1890.00', '2026-01-15 13:29:33'),
(3, 3, 'Свечи зажигания NGK BKR6E', 'img/no-image.png', '850.00', '2026-01-15 13:29:33'),
(4, 4, 'Тормозные колодки Brembo P85115', 'img/no-image.png', '3890.00', '2026-01-15 13:29:33'),
(5, 5, 'Фильтр масляный Mann W914/2', 'img/no-image.png', '1250.00', '2026-01-15 13:29:33');

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
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `api_keys`
--
ALTER TABLE `api_keys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `backup_logs`
--
ALTER TABLE `backup_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_users`) ON DELETE CASCADE;

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
