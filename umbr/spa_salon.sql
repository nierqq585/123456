-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Хост: MySQL-8.2
-- Время создания: Мар 18 2026 г., 12:15
-- Версия сервера: 8.2.0
-- Версия PHP: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `spa_salon`
--

-- --------------------------------------------------------

--
-- Структура таблицы `appointments`
--

CREATE TABLE `appointments` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `program` varchar(255) DEFAULT NULL,
  `message` text,
  `status` enum('new','processed','completed','cancelled') DEFAULT 'new',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `appointments`
--

INSERT INTO `appointments` (`id`, `name`, `phone`, `email`, `program`, `message`, `status`, `created_at`) VALUES
(1, 'hfdfgdf', '79961069644', 'ssss@rr.ru', 'Детоксикация', '', 'new', '2026-03-13 05:40:06'),
(2, '123', '891754343443', '123@gmail.com', 'Антистресс', '', 'new', '2026-03-18 07:13:51');

-- --------------------------------------------------------

--
-- Структура таблицы `programs`
--

CREATE TABLE `programs` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `duration` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `programs`
--

INSERT INTO `programs` (`id`, `title`, `description`, `duration`, `price`, `old_price`, `image`, `category`, `is_active`, `created_at`) VALUES
(1, 'Антистресс', 'Комплексные программы стресс-протекции позволяют психологически восстановиться, повысить стрессоустойчивость.', '1-14 дней', 7000.00, NULL, NULL, NULL, 1, '2026-03-13 05:36:11'),
(2, 'Детоксикация', 'Идеальный способ очиститься от накопившихся токсинов, восстановить энергию.', '3-10 дней', 13800.00, NULL, NULL, NULL, 1, '2026-03-13 05:36:11'),
(3, 'Снижение веса', 'Авторская программа снижения веса с медицинским сопровождением.', '7-21 дней', 18500.00, NULL, NULL, NULL, 1, '2026-03-13 05:36:11'),
(4, 'Лечебное голодание', 'Классическая программа лечебного голодания с подготовкой и правильным выходом.', '5-14 дней', 21000.00, NULL, NULL, NULL, 1, '2026-03-13 05:36:11'),
(5, 'SPA-омоложение', 'Комплекс омолаживающих процедур: обертывания, массажи, кислородные коктейли.', '3-7 дней', 15900.00, NULL, NULL, NULL, 1, '2026-03-13 05:36:11'),
(6, 'Здоровый сон', 'Специализированная программа для тех, кто страдает бессонницей.', '5-10 дней', 12500.00, NULL, NULL, NULL, 1, '2026-03-13 05:36:11');

-- --------------------------------------------------------

--
-- Структура таблицы `reviews`
--

CREATE TABLE `reviews` (
  `id` int NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `review_text` text,
  `rating` int DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ;

-- --------------------------------------------------------

--
-- Структура таблицы `settings`
--

CREATE TABLE `settings` (
  `id` int NOT NULL,
  `setting_key` varchar(100) DEFAULT NULL,
  `setting_value` text,
  `setting_type` varchar(50) DEFAULT 'text'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_type`) VALUES
(1, 'site_title', 'SpaGold Life', 'text'),
(2, 'site_phone', '+7 (901) 718 88 99', 'text'),
(3, 'site_email', 'info@spagold.ru', 'text'),
(4, 'site_address', 'г. Москва, ул. Тверская, д. 15', 'text'),
(5, 'working_hours', 'Ежедневно с 9:00 до 21:00', 'text');

-- --------------------------------------------------------

--
-- Структура таблицы `team`
--

CREATE TABLE `team` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `position` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `bio` text,
  `order_num` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `team`
--

INSERT INTO `team` (`id`, `name`, `position`, `photo`, `bio`, `order_num`, `is_active`) VALUES
(1, 'Загитов Даниял', 'Главный врач, терапевт', NULL, NULL, 1, 1),
(2, 'Мосин Оскар', 'Врач-диетолог', NULL, NULL, 2, 1),
(3, 'Кузнецов Кирилл', 'СПА-терапевт', NULL, NULL, 3, 1),
(4, 'Ахмадаллин Влад', 'Психолог', NULL, NULL, 4, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','manager') DEFAULT 'manager',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', 'admin123', 'admin', '2026-03-13 05:36:11');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Индексы таблицы `team`
--
ALTER TABLE `team`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `team`
--
ALTER TABLE `team`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
