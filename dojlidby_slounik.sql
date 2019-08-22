-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 15, 2019 at 10:45 PM
-- Server version: 5.7.27-cll-lve
-- PHP Version: 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dojlidby_slounik`
--
CREATE DATABASE IF NOT EXISTS `dojlidby_slounik` DEFAULT CHARACTER SET cp1251 COLLATE cp1251_general_ci;
USE `dojlidby_slounik`;

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
CREATE TABLE `images` (
  `id` int(15) NOT NULL,
  `word_id` int(100) NOT NULL,
  `image` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `images`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nick` varchar(30) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `words`
--

DROP TABLE IF EXISTS `words`;
CREATE TABLE `words` (
  `id` int(100) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `words`
--

INSERT INTO `words` (`id`, `created`, `modified`) VALUES
(29, '2017-04-11 14:06:19', '2017-06-26 00:27:14');

-- --------------------------------------------------------

--
-- Table structure for table `words_translations`
--

DROP TABLE IF EXISTS `words_translations`;
CREATE TABLE `words_translations` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `word_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  `lang` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `words_translations`
--

INSERT INTO `words_translations` (`id`, `name`, `description`, `word_id`, `created`, `modified`, `lang`) VALUES
(1, 'др', 'Пфываfgfgsdfgdsfфываривети ', 29, '2017-04-11 14:06:19', '2017-06-26 00:27:14', 'by'),
(2, 'день рождения', 'День рождения', 29, '2017-07-09 09:43:21', '2017-07-09 09:43:21', 'ru'),
(3, 'birthday', 'day when you get birth', 29, '2017-07-09 09:43:43', '2017-07-09 09:43:43', 'en'),
(4, 'test', 'test', 30, '2019-05-15 03:33:05', '2019-05-15 03:33:05', 'en'),
(5, 'test', 'test', 30, '2019-05-15 03:33:15', '2019-05-15 03:33:15', 'ru');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nick` (`nick`);

--
-- Indexes for table `words`
--
ALTER TABLE `words`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `words_translations`
--
ALTER TABLE `words_translations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `words_translations_name_IDX` (`name`),
  ADD KEY `words_translations_word_id_IDX` (`word_id`);
