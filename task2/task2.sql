-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Авг 28 2018 г., 20:04
-- Версия сервера: 5.6.38
-- Версия PHP: 7.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `medialab`
--

-- --------------------------------------------------------

--
-- Структура таблицы `strings`
--

CREATE TABLE `strings` (
  `hash` char(40) NOT NULL,
  `length` int(10) UNSIGNED NOT NULL,
  `string` mediumblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8
PARTITION BY RANGE ( length)
(
PARTITION p0 VALUES LESS THAN (10240) ENGINE=InnoDB,
PARTITION p1 VALUES LESS THAN (102400) ENGINE=InnoDB,
PARTITION p2 VALUES LESS THAN (524288) ENGINE=InnoDB,
PARTITION p3 VALUES LESS THAN (1048576) ENGINE=InnoDB,
PARTITION p4 VALUES LESS THAN (5242880) ENGINE=InnoDB,
PARTITION p5 VALUES LESS THAN MAXVALUE ENGINE=InnoDB
);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `strings`
--
ALTER TABLE `strings`
  ADD UNIQUE KEY `u_hash` (`hash`,`length`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
