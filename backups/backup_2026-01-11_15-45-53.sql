-- AutoShop Database Backup
-- Date: 2026-01-11 15:45:53
-- PHP Version: 8.0.22
-- MySQL Version: 50739

SET FOREIGN_KEY_CHECKS=0;

--
-- Структура таблицы `action_logs`
--

DROP TABLE IF EXISTS `action_logs`;
CREATE TABLE `action_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `action_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_users`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Структура таблицы `api_keys`
--

DROP TABLE IF EXISTS `api_keys`;
CREATE TABLE `api_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `api_key` varchar(100) NOT NULL,
  `secret_key` varchar(100) NOT NULL,
  `status` varchar(20) DEFAULT 'active',
  `permissions` text,
  `last_used` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NULL DEFAULT NULL,
  `revoked_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `api_key` (`api_key`),
  KEY `idx_api_key` (`api_key`),
  KEY `idx_status` (`status`),
  KEY `idx_expires` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `api_keys`
-- Всего записей: 1
--

INSERT INTO `api_keys` (`id`, `name`, `api_key`, `secret_key`, `status`, `permissions`, `last_used`, `created_at`, `expires_at`, `revoked_at`) VALUES 
('1', 'api_keys', 'sk_live_9f91e34cecf030985f13e1eeae02e6b3', 'sk_305308cd266a815f26aebb00613a6e2e9d9028b9946fe003', 'active', 'read,write', NULL, '2026-01-11 15:27:36', '2027-01-11 15:27:36', NULL);

--
-- Структура таблицы `backup_logs`
--

DROP TABLE IF EXISTS `backup_logs`;
CREATE TABLE `backup_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `source` varchar(50) DEFAULT 'manual',
  `action` varchar(50) DEFAULT 'backup',
  `error_message` text,
  `downloads` int(11) DEFAULT '0',
  `last_download` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_created` (`created_at`),
  KEY `idx_filename` (`filename`(100))
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

--
-- Структура таблицы `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE `cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_image` varchar(255) DEFAULT 'no-image.png',
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_users`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `cart`
-- Всего записей: 4
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `product_name`, `product_image`, `price`, `quantity`, `created_at`, `updated_at`) VALUES 
('20', '3', '0', 'Фильтр масляный Audi A4 B8 2.0 TFSI', '../img/no-image.png', '1250.00', '1', '2026-01-05 18:19:41', '2026-01-05 18:19:41'),
('21', '3', '0', 'Тормозные колодки Audi A6 C7', '../img/no-image.png', '3890.00', '1', '2026-01-05 18:19:42', '2026-01-05 18:19:42'),
('22', '3', '0', 'Свечи зажигания Audi Q5 2.0 TDI', '../img/no-image.png', '850.00', '1', '2026-01-05 18:19:43', '2026-01-05 18:19:43'),
('23', '3', '0', 'Сцепление Audi A3 8V', '../img/no-image.png', '12500.00', '2', '2026-01-05 18:19:43', '2026-01-05 18:19:50');

--
-- Структура таблицы `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `categories`
-- Всего записей: 3
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES 
('1', 'Запчасти', 'Автомобильные запчасти', '2026-01-06 20:15:45'),
('2', 'Масла', 'Моторные и трансмиссионные масла', '2026-01-06 20:15:45'),
('3', 'Аксессуары', 'Аксессуары для автомобилей', '2026-01-06 20:15:45');

--
-- Структура таблицы `news`
--

DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text,
  `author` varchar(100) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `published_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Структура таблицы `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `order_items`
-- Всего записей: 4
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `price`, `quantity`) VALUES 
('1', '1', '0', 'Генератор Audi A4 B9', '15600.00', '1'),
('2', '1', '0', 'Ремень ГРМ BMW 7 series G11', '3200.00', '1'),
('3', '1', '0', 'Аккумулятор BMW 5 series F10', '12500.00', '1'),
('4', '1', '0', 'Тормозные колодки BMW 1 series F20', '5200.00', '2');

--
-- Структура таблицы `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `order_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `shipping_address` text,
  `phone` varchar(20) DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_users`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `orders`
-- Всего записей: 1
--

INSERT INTO `orders` (`id`, `order_number`, `user_id`, `total_amount`, `status`, `order_date`, `shipping_address`, `phone`, `notes`) VALUES 
('1', 'ORD-20260105-5A3C6597', '2', '41700.00', 'pending', '2026-01-05 18:00:31', '', '89113456789', '');

--
-- Структура таблицы `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `category` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `quantity` int(11) DEFAULT '0',
  `article` varchar(100) DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`),
  KEY `idx_status` (`status`),
  KEY `idx_price` (`price`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `products`
-- Всего записей: 4
--

INSERT INTO `products` (`id`, `name`, `description`, `category`, `price`, `quantity`, `article`, `image`, `status`, `created_at`, `updated_at`) VALUES 
('1', 'Моторное масло 5W-40', 'Синтетическое моторное масло для всех типов двигателей', 'Масла', '2500.00', '45', 'MO-5W40-001', 'uploads/products/696392655986c.png', 'available', '2026-01-07 20:51:40', '2026-01-11 15:08:27'),
('2', 'Воздушный фильтр', 'Воздушный фильтр для легковых автомобилей', 'Запчасти', '800.00', '23', 'AF-001', 'uploads/products/696392655986c.png', 'low', '2026-01-07 20:51:40', '2026-01-11 15:08:17'),
('3', 'Тормозные колодки', 'Передние тормозные колодки', 'Запчасти', '3200.00', '15', 'TB-001', 'uploads/products/696392655986c.png', 'available', '2026-01-07 20:51:40', '2026-01-11 15:08:09'),
('4', 'Аккумулятор 60Ah', 'Свинцово-кислотный аккумулятор 60Ah', 'Аксессуары', '5500.00', '8', 'BAT-60', 'uploads/products/696392655986c.png', 'available', '2026-01-07 20:51:40', '2026-01-11 15:08:01');

--
-- Структура таблицы `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `rating` int(11) NOT NULL,
  `text` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `reviews`
-- Всего записей: 4
--

INSERT INTO `reviews` (`id`, `name`, `email`, `rating`, `text`, `status`, `created_at`, `updated_at`) VALUES 
('1', 'Иван Петров', 'ivan@mail.ru', '5', 'Отличный сервис! Быстро и качественно починили мой автомобиль. Персонал вежливый, цены адекватные. Рекомендую всем!', 'approved', '2024-01-15 10:30:00', '2025-11-11 19:36:39'),
('2', 'Мария Сидорова', 'maria@yandex.ru', '4', 'Хороший магазин автозапчастей. Большой выбор, консультанты помогли подобрать нужную деталь. Не хватило только скидочной системы для постоянных клиентов.', 'approved', '2024-01-20 14:45:00', '2025-11-11 19:36:39'),
('3', 'Алексей Козлов', 'alex@mail.ru', '5', 'Лучший автосервис в городе! Делали полное ТО, всё выполнили в срок, дали полезные советы по эксплуатации. Буду обращаться только сюда.', 'approved', '2024-02-01 09:15:00', '2026-01-07 21:31:54'),
('7', 'Наталья', 'email2@gmail.com', '4', 'Круто!', 'approved', '2026-01-07 21:33:14', '2026-01-07 21:35:20');

--
-- Структура таблицы `services`
--

DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text,
  `duration` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `services`
-- Всего записей: 2
--

INSERT INTO `services` (`id`, `name`, `category`, `price`, `description`, `duration`, `status`, `created_at`) VALUES 
('1', 'Замена масла', 'Техническое обслуживание', '1500.00', 'Замена моторного масла и масляного фильтра', '30', 'active', '2026-01-06 20:15:45'),
('2', 'Диагностика двигателя', 'Диагностика', '3000.00', 'Комплексная диагностика двигателя', '60', 'active', '2026-01-06 20:15:45');

--
-- Структура таблицы `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text,
  `setting_group` varchar(50) DEFAULT 'general',
  `setting_type` varchar(20) DEFAULT 'text',
  `label` varchar(200) DEFAULT NULL,
  `description` text,
  `options` text,
  `is_public` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`),
  KEY `idx_group` (`setting_group`),
  KEY `idx_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `settings`
-- Всего записей: 65
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES 
('1', 'site_name', 'Лал-Авто', 'general', 'text', 'Название сайта', NULL, NULL, '0', '2026-01-11 15:15:06', '2026-01-11 15:29:02'),
('2', 'admin_email', 'admin@lal-auto.ru', 'general', 'email', 'Email администратора', NULL, NULL, '0', '2026-01-11 15:15:06', '2026-01-11 15:29:02'),
('3', 'support_phone', '+7 (999) 123-45-67', 'general', 'tel', 'Телефон поддержки', NULL, NULL, '0', '2026-01-11 15:15:06', '2026-01-11 15:29:02'),
('4', 'working_hours', 'Пн-Пт: 9:00-18:00, Сб: 10:00-16:00', 'general', 'text', 'Время работы', NULL, NULL, '0', '2026-01-11 15:15:06', '2026-01-11 15:29:02'),
('5', 'min_order_amount', '1000', 'store', 'number', 'Минимальная сумма заказа', NULL, NULL, '0', '2026-01-11 15:15:06', '2026-01-11 15:29:02'),
('6', 'vat_rate', '20', 'store', 'select', 'Ставка НДС', NULL, NULL, '0', '2026-01-11 15:15:06', '2026-01-11 15:29:02'),
('7', 'maintenance_mode', '0', 'maintenance', 'checkbox', 'Режим обслуживания', NULL, NULL, '0', '2026-01-11 15:15:06', '2026-01-11 15:29:03'),
('8', 'api_enabled', '1', 'api', 'checkbox', 'Включить API', NULL, NULL, '0', '2026-01-11 15:15:06', '2026-01-11 15:29:03'),
('9', 'system_version', '2.2.0', 'system', 'text', 'Версия системы', NULL, NULL, '0', '2026-01-11 15:15:06', '2026-01-11 15:28:42'),
('10', 'group', 'general', 'general', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:15:53', '2026-01-11 15:28:56'),
('11', 'site_description', 'Автозапчасти и автосервис - качественное обслуживание вашего автомобиля', 'general', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:15:53', '2026-01-11 15:29:02'),
('12', 'default_language', 'Русский', 'general', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:15:53', '2026-01-11 15:29:02'),
('13', 'currency', 'RUB', 'general', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:15:53', '2026-01-11 15:29:02'),
('14', 'email_new_orders', '1', 'notifications', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:02'),
('15', 'email_payments', '1', 'notifications', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:02'),
('16', 'email_low_stock', '1', 'notifications', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:02'),
('17', 'sms_promo', '1', 'notifications', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03'),
('18', 'smtp_server', 'smtp.gmail.com', 'notifications', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03'),
('19', 'smtp_port', '587', 'notifications', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03'),
('20', 'bank_cards_enabled', '1', 'payment', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03'),
('21', 'yoomoney_enabled', '1', 'payment', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03'),
('22', 'cash_on_delivery', '1', 'payment', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03'),
('23', 'processing_fee', '2.5', 'payment', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03'),
('24', 'min_fee', '10', 'payment', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03'),
('25', 'courier_enabled', '1', 'shipping', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03'),
('26', 'courier_cost', '300', 'shipping', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03'),
('27', 'pickup_enabled', '1', 'shipping', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03'),
('28', 'russian_post_cost', '500', 'shipping', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03'),
('29', 'cdek_enabled', '1', 'shipping', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03'),
('30', 'cdek_cost', '450', 'shipping', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03'),
('31', 'free_shipping_min', '5000', 'shipping', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03'),
('32', 'delivery_days', '3', 'shipping', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03'),
('33', 'min_password_length', '8', 'security', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03'),
('34', 'password_expiry_days', '90', 'security', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03'),
('35', 'require_special_char', '1', 'security', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03'),
('36', 'require_numbers', '1', 'security', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03'),
('37', 'prevent_reuse', '1', 'security', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03'),
('38', 'max_login_attempts', '5', 'security', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03'),
('39', 'lockout_minutes', '30', 'security', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03'),
('40', 'usd_rate', '90.5', 'store', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:02'),
('41', 'eur_rate', '99.8', 'store', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:02'),
('42', 'low_stock_alert', '1', 'store', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:02'),
('43', 'return_policy', 'Возврат товара возможен в течение 14 дней с момента покупки при сохранении товарного вида и упаковки.', 'store', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:02'),
('44', 'request_limit', '100', 'api', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:05', '2026-01-11 15:29:03'),
('45', 'webhook_url', '', 'api', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:05', '2026-01-11 15:16:05'),
('46', 'meta_title', 'Лал-Авто - Автозапчасти и автосервис', 'seo', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:05', '2026-01-11 15:29:03'),
('47', 'meta_description', 'Качественные автозапчасти и профессиональный автосервис. Широкий ассортимент, доступные цены, гарантия качества.', 'seo', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:05', '2026-01-11 15:29:03'),
('48', 'meta_keywords', 'автозапчасти, автосервис, автомобильные запчасти, ремонт авто', 'seo', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:05', '2026-01-11 15:29:03'),
('49', 'og_title', 'Лал-Авто', 'seo', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:05', '2026-01-11 15:29:03'),
('50', 'og_image', '', 'seo', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:05', '2026-01-11 15:16:05'),
('51', 'seo_friendly_urls', '1', 'seo', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:05', '2026-01-11 15:29:03'),
('52', 'generate_sitemap', '1', 'seo', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:05', '2026-01-11 15:29:03'),
('53', 'robots_txt', 'User-agent: *\nDisallow: /admin/\nDisallow: /cart/\nAllow: /public/\nSitemap: https://lal-auto.ru/sitemap.xml', 'seo', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:05', '2026-01-11 15:29:03'),
('54', 'maintenance_message', 'Сайт временно недоступен. Ведутся технические работы. Приносим извинения за неудобства.', 'maintenance', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:05', '2026-01-11 15:29:03'),
('55', 'allow_backorder', '0', 'store', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:10', '2026-01-11 15:29:02'),
('56', 'email_reviews', '0', 'notifications', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:10', '2026-01-11 15:29:02'),
('57', 'email_newsletter', '0', 'notifications', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:10', '2026-01-11 15:29:02'),
('58', 'sms_order_status', '0', 'notifications', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:10', '2026-01-11 15:29:02'),
('59', 'sms_delivery', '0', 'notifications', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:10', '2026-01-11 15:29:03'),
('60', 'require_upper_lower', '0', 'security', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:10', '2026-01-11 15:29:03'),
('61', 'enable_2fa_admin', '0', 'security', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:10', '2026-01-11 15:29:03'),
('62', 'enable_2fa_users', '0', 'security', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:10', '2026-01-11 15:29:03'),
('63', 'sberbank_enabled', '0', 'payment', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:10', '2026-01-11 15:29:03'),
('64', 'russian_post_enabled', '0', 'shipping', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:10', '2026-01-11 15:29:03'),
('65', 'graphql_enabled', '0', 'api', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:10', '2026-01-11 15:29:03');

--
-- Структура таблицы `shops`
--

DROP TABLE IF EXISTS `shops`;
CREATE TABLE `shops` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_region` (`region`),
  KEY `idx_status` (`status`),
  KEY `idx_type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `shops`
-- Всего записей: 2
--

INSERT INTO `shops` (`id`, `name`, `type`, `region`, `phone`, `email`, `address`, `area`, `employees`, `status`, `description`, `schedule`, `created_at`) VALUES 
('1', 'Лал-Авто Центр', 'main', 'Москва', '+7 (495) 123-45-67', 'center@lal-auto.ru', 'ул. Ленина, 123, бизнес-центр \"Северный\"', '250.50', '15', 'active', 'Основной магазин компании', '09:00 - 18:00', '2026-01-07 20:00:41'),
('2', 'Лал-Авто Север', 'branch', 'Санкт-Петербург', '+7 (812) 987-65-43', 'north@lal-auto.ru', 'пр. Мира, 45, торговый комплекс \"Европа\"', '180.00', '10', 'active', 'Филиал в Санкт-Петербурге', '10:00 - 19:00', '2026-01-07 20:00:41');

--
-- Структура таблицы `system_versions`
--

DROP TABLE IF EXISTS `system_versions`;
CREATE TABLE `system_versions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(20) NOT NULL,
  `installed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `notes` text,
  PRIMARY KEY (`id`),
  KEY `idx_version` (`version`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `system_versions`
-- Всего записей: 1
--

INSERT INTO `system_versions` (`id`, `version`, `installed_at`, `notes`) VALUES 
('1', '2.2.0', '2026-01-11 15:28:42', NULL);

--
-- Структура таблицы `update_logs`
--

DROP TABLE IF EXISTS `update_logs`;
CREATE TABLE `update_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `old_version` varchar(20) NOT NULL,
  `new_version` varchar(20) NOT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `details` text,
  `error_message` text,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_versions` (`old_version`,`new_version`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `update_logs`
-- Всего записей: 1
--

INSERT INTO `update_logs` (`id`, `old_version`, `new_version`, `status`, `details`, `error_message`, `updated_at`) VALUES 
('1', '2.1.0', '2.2.0', 'success', 'Системное обновление', NULL, '2026-01-11 15:28:42');

--
-- Структура таблицы `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id_users` int(11) NOT NULL AUTO_INCREMENT,
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
  `user_type` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_users`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users`
-- Всего записей: 5
--

INSERT INTO `users` (`id_users`, `surname_users`, `name_users`, `patronymic_users`, `login_users`, `password_users`, `email_users`, `discountСardNumber_users`, `region_users`, `city_users`, `address_users`, `phone_users`, `avatar_users`, `TIN_users`, `person_users`, `organization_users`, `organizationType_users`, `user_type`) VALUES 
('1', NULL, NULL, NULL, 'admin', 'admin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
('2', 'Иванов', 'Иван', 'Иванович', 'user1', 'user1', 'user1@gmail.com', '223344', 'Калининградская область', 'Калининград', 'Малый переулок, 3', '89113456789', 'uploads/avatars/avatar_2_1758131749.jpg', NULL, NULL, NULL, NULL, 'physical'),
('3', NULL, NULL, NULL, 'user2', 'user2', 'user2@gmail.com', NULL, 'Калининградская область', 'Калининград', 'Уральская улица, 20', '89114567891', NULL, '2222455179', 'Наталья Евгеньевна Графарова', 'Дизель-мастер', 'ООО', 'legal'),
('4', NULL, NULL, NULL, 'user3', 'user3', 'user3@gmail.com', '556677', 'Калининградская область', 'Балтийск', 'Киркенесская улица, 20', '89115678912', NULL, '5552431142', 'Иван Иванович Иванов', 'КлассикАвто', 'ЗАО', 'legal'),
('5', 'Рожков', 'Олег', 'Константинович', 'user4', 'user4', 'user4@gmail.com', NULL, 'Калининградская область', 'Черняховск', 'улица Советская, 5', '89116789123', NULL, NULL, NULL, NULL, NULL, 'physical');

SET FOREIGN_KEY_CHECKS=1;
-- End of backup
