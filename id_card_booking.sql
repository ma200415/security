-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2022 年 09 月 08 日 02:03
-- 伺服器版本： 10.4.24-MariaDB
-- PHP 版本： 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `id_card_booking`
--

-- --------------------------------------------------------

--
-- 資料表結構 `booking`
--

CREATE TABLE `booking` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `occupation` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `photo` mediumtext NOT NULL,
  `engName` varchar(255) NOT NULL,
  `chiName` varchar(255) NOT NULL,
  `idNo` varchar(255) NOT NULL,
  `birthday` varchar(255) NOT NULL,
  `birthPlace` varchar(255) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `reservationDate` date NOT NULL,
  `reservationTime` varchar(5) NOT NULL,
  `redemptionPlace` varchar(255) NOT NULL,
  `iv` varchar(255) NOT NULL,
  `cdate` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT NULL,
  `sdate` datetime DEFAULT NULL,
  `sby` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 資料表結構 `misc`
--

CREATE TABLE `misc` (
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `remark` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `misc`
--

INSERT INTO `misc` (`name`, `value`, `remark`) VALUES
('cipher', 'aes-256-cbc', ''),
('cipherKey', 'e8f6d3564f8b6b2c1caebefba607d8e84e897cd43ed35bbe127daccd8a62f96061543177bc477468640fc8ec5bd9f44d6b74', ''),
('smtpPassword', 'hmigchjhmtldyrws', '$mail->Password'),
('smtpUsername', 'ma20200415@gmail.com', '$mail->Username');

-- --------------------------------------------------------

--
-- 資料表結構 `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(10) NOT NULL,
  `cdate` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `user`
--

INSERT INTO `user` (`id`, `email`, `password`, `role`, `cdate`) VALUES
(21, 'admin@gmail.com', '$2y$10$F3fgWRv2Bsi7nemvB3v1meCCEgw8mP405.gRt3bTfH5mWVcaULrde', 'admin', '2022-09-03 19:03:41');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user` (`user`),
  ADD KEY `sby` (`sby`);

--
-- 資料表索引 `misc`
--
ALTER TABLE `misc`
  ADD UNIQUE KEY `name` (`name`);

--
-- 資料表索引 `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `booking`
--
ALTER TABLE `booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`sby`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
