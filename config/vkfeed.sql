-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost:3306
-- Время создания: Фев 14 2024 г., 14:41
-- Версия сервера: 10.1.48-MariaDB-0+deb9u2
-- Версия PHP: 7.1.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `vkfeed`
--

-- --------------------------------------------------------

--
-- Структура таблицы `good`
--

CREATE TABLE `good` (
  `id` int(11) NOT NULL,
  `good_id` varchar(20) DEFAULT NULL,
  `available` tinyint(4) NOT NULL,
  `url` varchar(50) NOT NULL,
  `price` float NOT NULL,
  `old_price` float NOT NULL DEFAULT '0',
  `categoryId` int(11) NOT NULL,
  `picture` text NOT NULL,
  `store` tinyint(4) NOT NULL,
  `pickup` tinyint(4) NOT NULL,
  `name` varchar(200) NOT NULL,
  `vendor` varchar(100) DEFAULT NULL,
  `color` varchar(100) NOT NULL,
  `size` varchar(30) NOT NULL,
  `need_update` int(11) NOT NULL DEFAULT '0',
  `need_delete` int(11) NOT NULL DEFAULT '0',
  `shop_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `error` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `offsets`
--

CREATE TABLE `offsets` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `offset` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `task`
--

CREATE TABLE `task` (
  `id` int(11) NOT NULL,
  `task` varchar(20) NOT NULL,
  `shop_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `updated_good`
--

CREATE TABLE `updated_good` (
  `id` int(11) NOT NULL,
  `good_id` varchar(20) DEFAULT NULL,
  `available` tinyint(4) NOT NULL,
  `url` varchar(50) NOT NULL,
  `price` float NOT NULL,
  `old_price` float NOT NULL DEFAULT '0',
  `categoryId` int(11) NOT NULL,
  `picture` text NOT NULL,
  `store` tinyint(4) NOT NULL,
  `pickup` tinyint(4) NOT NULL,
  `name` varchar(200) NOT NULL,
  `vendor` varchar(100) DEFAULT NULL,
  `shop_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `good`
--
ALTER TABLE `good`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shop_id` (`shop_id`);

--
-- Индексы таблицы `offsets`
--
ALTER TABLE `offsets`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `updated_good`
--
ALTER TABLE `updated_good`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shop_id` (`shop_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `good`
--
ALTER TABLE `good`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `offsets`
--
ALTER TABLE `offsets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `task`
--
ALTER TABLE `task`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `updated_good`
--
ALTER TABLE `updated_good`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
