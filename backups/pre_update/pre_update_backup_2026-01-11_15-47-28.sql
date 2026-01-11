-- Pre-update backup
-- Created: 2026-01-11 15:47:28

SET FOREIGN_KEY_CHECKS=0;

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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

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

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

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

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('1', 'site_name', 'Лал-Авто', 'general', 'text', 'Название сайта', NULL, NULL, '0', '2026-01-11 15:15:06', '2026-01-11 15:29:02');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('2', 'admin_email', 'admin@lal-auto.ru', 'general', 'email', 'Email администратора', NULL, NULL, '0', '2026-01-11 15:15:06', '2026-01-11 15:29:02');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('3', 'support_phone', '+7 (999) 123-45-67', 'general', 'tel', 'Телефон поддержки', NULL, NULL, '0', '2026-01-11 15:15:06', '2026-01-11 15:29:02');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('4', 'working_hours', 'Пн-Пт: 9:00-18:00, Сб: 10:00-16:00', 'general', 'text', 'Время работы', NULL, NULL, '0', '2026-01-11 15:15:06', '2026-01-11 15:29:02');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('5', 'min_order_amount', '1000', 'store', 'number', 'Минимальная сумма заказа', NULL, NULL, '0', '2026-01-11 15:15:06', '2026-01-11 15:29:02');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('6', 'vat_rate', '20', 'store', 'select', 'Ставка НДС', NULL, NULL, '0', '2026-01-11 15:15:06', '2026-01-11 15:29:02');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('7', 'maintenance_mode', '0', 'maintenance', 'checkbox', 'Режим обслуживания', NULL, NULL, '0', '2026-01-11 15:15:06', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('8', 'api_enabled', '1', 'api', 'checkbox', 'Включить API', NULL, NULL, '0', '2026-01-11 15:15:06', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('9', 'system_version', '2.2.0', 'system', 'text', 'Версия системы', NULL, NULL, '0', '2026-01-11 15:15:06', '2026-01-11 15:28:42');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('10', 'group', 'general', 'general', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:15:53', '2026-01-11 15:28:56');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('11', 'site_description', 'Автозапчасти и автосервис - качественное обслуживание вашего автомобиля', 'general', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:15:53', '2026-01-11 15:29:02');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('12', 'default_language', 'Русский', 'general', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:15:53', '2026-01-11 15:29:02');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('13', 'currency', 'RUB', 'general', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:15:53', '2026-01-11 15:29:02');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('14', 'email_new_orders', '1', 'notifications', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:02');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('15', 'email_payments', '1', 'notifications', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:02');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('16', 'email_low_stock', '1', 'notifications', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:02');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('17', 'sms_promo', '1', 'notifications', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('18', 'smtp_server', 'smtp.gmail.com', 'notifications', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('19', 'smtp_port', '587', 'notifications', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('20', 'bank_cards_enabled', '1', 'payment', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('21', 'yoomoney_enabled', '1', 'payment', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('22', 'cash_on_delivery', '1', 'payment', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('23', 'processing_fee', '2.5', 'payment', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('24', 'min_fee', '10', 'payment', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('25', 'courier_enabled', '1', 'shipping', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('26', 'courier_cost', '300', 'shipping', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('27', 'pickup_enabled', '1', 'shipping', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('28', 'russian_post_cost', '500', 'shipping', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('29', 'cdek_enabled', '1', 'shipping', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('30', 'cdek_cost', '450', 'shipping', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('31', 'free_shipping_min', '5000', 'shipping', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('32', 'delivery_days', '3', 'shipping', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('33', 'min_password_length', '8', 'security', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('34', 'password_expiry_days', '90', 'security', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('35', 'require_special_char', '1', 'security', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('36', 'require_numbers', '1', 'security', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('37', 'prevent_reuse', '1', 'security', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('38', 'max_login_attempts', '5', 'security', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('39', 'lockout_minutes', '30', 'security', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('40', 'usd_rate', '90.5', 'store', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:02');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('41', 'eur_rate', '99.8', 'store', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:02');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('42', 'low_stock_alert', '1', 'store', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:02');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('43', 'return_policy', 'Возврат товара возможен в течение 14 дней с момента покупки при сохранении товарного вида и упаковки.', 'store', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:03', '2026-01-11 15:29:02');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('44', 'request_limit', '100', 'api', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:05', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('45', 'webhook_url', '', 'api', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:05', '2026-01-11 15:16:05');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('46', 'meta_title', 'Лал-Авто - Автозапчасти и автосервис', 'seo', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:05', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('47', 'meta_description', 'Качественные автозапчасти и профессиональный автосервис. Широкий ассортимент, доступные цены, гарантия качества.', 'seo', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:05', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('48', 'meta_keywords', 'автозапчасти, автосервис, автомобильные запчасти, ремонт авто', 'seo', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:05', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('49', 'og_title', 'Лал-Авто', 'seo', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:05', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('50', 'og_image', '', 'seo', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:05', '2026-01-11 15:16:05');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('51', 'seo_friendly_urls', '1', 'seo', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:05', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('52', 'generate_sitemap', '1', 'seo', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:05', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('53', 'robots_txt', 'User-agent: *\nDisallow: /admin/\nDisallow: /cart/\nAllow: /public/\nSitemap: https://lal-auto.ru/sitemap.xml', 'seo', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:05', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('54', 'maintenance_message', 'Сайт временно недоступен. Ведутся технические работы. Приносим извинения за неудобства.', 'maintenance', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:05', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('55', 'allow_backorder', '0', 'store', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:10', '2026-01-11 15:29:02');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('56', 'email_reviews', '0', 'notifications', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:10', '2026-01-11 15:29:02');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('57', 'email_newsletter', '0', 'notifications', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:10', '2026-01-11 15:29:02');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('58', 'sms_order_status', '0', 'notifications', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:10', '2026-01-11 15:29:02');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('59', 'sms_delivery', '0', 'notifications', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:10', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('60', 'require_upper_lower', '0', 'security', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:10', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('61', 'enable_2fa_admin', '0', 'security', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:10', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('62', 'enable_2fa_users', '0', 'security', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:10', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('63', 'sberbank_enabled', '0', 'payment', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:10', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('64', 'russian_post_enabled', '0', 'shipping', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:10', '2026-01-11 15:29:03');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `options`, `is_public`, `created_at`, `updated_at`) VALUES ('65', 'graphql_enabled', '0', 'api', 'text', NULL, NULL, NULL, '0', '2026-01-11 15:16:10', '2026-01-11 15:29:03');

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

DROP TABLE IF EXISTS `system_versions`;
CREATE TABLE `system_versions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(20) NOT NULL,
  `installed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `notes` text,
  PRIMARY KEY (`id`),
  KEY `idx_version` (`version`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

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

INSERT INTO `users` (`id_users`, `surname_users`, `name_users`, `patronymic_users`, `login_users`, `password_users`, `email_users`, `discountСardNumber_users`, `region_users`, `city_users`, `address_users`, `phone_users`, `avatar_users`, `TIN_users`, `person_users`, `organization_users`, `organizationType_users`, `user_type`) VALUES ('1', NULL, NULL, NULL, 'admin', 'admin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '');
INSERT INTO `users` (`id_users`, `surname_users`, `name_users`, `patronymic_users`, `login_users`, `password_users`, `email_users`, `discountСardNumber_users`, `region_users`, `city_users`, `address_users`, `phone_users`, `avatar_users`, `TIN_users`, `person_users`, `organization_users`, `organizationType_users`, `user_type`) VALUES ('2', 'Иванов', 'Иван', 'Иванович', 'user1', 'user1', 'user1@gmail.com', '223344', 'Калининградская область', 'Калининград', 'Малый переулок, 3', '89113456789', 'uploads/avatars/avatar_2_1758131749.jpg', NULL, NULL, NULL, NULL, 'physical');
INSERT INTO `users` (`id_users`, `surname_users`, `name_users`, `patronymic_users`, `login_users`, `password_users`, `email_users`, `discountСardNumber_users`, `region_users`, `city_users`, `address_users`, `phone_users`, `avatar_users`, `TIN_users`, `person_users`, `organization_users`, `organizationType_users`, `user_type`) VALUES ('3', NULL, NULL, NULL, 'user2', 'user2', 'user2@gmail.com', NULL, 'Калининградская область', 'Калининград', 'Уральская улица, 20', '89114567891', NULL, '2222455179', 'Наталья Евгеньевна Графарова', 'Дизель-мастер', 'ООО', 'legal');
INSERT INTO `users` (`id_users`, `surname_users`, `name_users`, `patronymic_users`, `login_users`, `password_users`, `email_users`, `discountСardNumber_users`, `region_users`, `city_users`, `address_users`, `phone_users`, `avatar_users`, `TIN_users`, `person_users`, `organization_users`, `organizationType_users`, `user_type`) VALUES ('4', NULL, NULL, NULL, 'user3', 'user3', 'user3@gmail.com', '556677', 'Калининградская область', 'Балтийск', 'Киркенесская улица, 20', '89115678912', NULL, '5552431142', 'Иван Иванович Иванов', 'КлассикАвто', 'ЗАО', 'legal');
INSERT INTO `users` (`id_users`, `surname_users`, `name_users`, `patronymic_users`, `login_users`, `password_users`, `email_users`, `discountСardNumber_users`, `region_users`, `city_users`, `address_users`, `phone_users`, `avatar_users`, `TIN_users`, `person_users`, `organization_users`, `organizationType_users`, `user_type`) VALUES ('5', 'Рожков', 'Олег', 'Константинович', 'user4', 'user4', 'user4@gmail.com', NULL, 'Калининградская область', 'Черняховск', 'улица Советская, 5', '89116789123', NULL, NULL, NULL, NULL, NULL, 'physical');

SET FOREIGN_KEY_CHECKS=1;
