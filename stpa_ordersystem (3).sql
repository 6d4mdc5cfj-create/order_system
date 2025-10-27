-- phpMyAdmin SQL Dump
-- version 5.2.1-1.el8.remi
-- https://www.phpmyadmin.net/
--
-- ホスト: localhost
-- 生成日時: 2025 年 10 月 27 日 11:58
-- サーバのバージョン： 10.5.22-MariaDB-log
-- PHP のバージョン: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `stpa_ordersystem`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `furigana` varchar(100) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `zipcode` varchar(10) DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `customers`
--

INSERT INTO `customers` (`id`, `name`, `furigana`, `phone`, `email`, `zipcode`, `address1`, `address2`, `created_at`, `updated_at`) VALUES
(1, '渡辺開史', 'わたなべはるちか', '09086418762', 'watanabe@photoartisan.jp', '1690072', '東京都新宿区大久保', '2-11-17砂森ビルB1', '2025-10-24 18:05:55', '2025-10-27 08:58:10'),
(2, 'ああああ', NULL, '09086418763', NULL, '1690072', 'あああ', '', '2025-10-24 18:44:52', '2025-10-24 18:44:52'),
(3, 'あああああああ', '', '09086418760', '', '1690052', '東京都新宿区戸山', '1-5-90', '2025-10-24 18:48:51', '2025-10-24 19:39:31'),
(6, '', '', '', '', '', '', '', '2025-10-24 20:06:09', '2025-10-24 20:06:09');

-- --------------------------------------------------------

--
-- テーブルの構造 `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `group_name` varchar(100) DEFAULT NULL COMMENT '予約グループ名（同行者共有）',
  `visit_date` date DEFAULT NULL,
  `visit_time` time DEFAULT NULL,
  `departure_time` time DEFAULT NULL,
  `service_photo` tinyint(1) DEFAULT 0,
  `service_rental` tinyint(1) DEFAULT 0,
  `service_kitsuke` tinyint(1) DEFAULT 0,
  `service_hairmake` tinyint(1) DEFAULT 0,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `reservations`
--

INSERT INTO `reservations` (`id`, `customer_id`, `group_name`, `visit_date`, `visit_time`, `departure_time`, `service_photo`, `service_rental`, `service_kitsuke`, `service_hairmake`, `updated_at`, `created_at`) VALUES
(4, 2, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, '2025-10-24 18:44:52'),
(5, 3, NULL, '2025-10-25', '20:00:00', '00:00:00', 1, 0, 0, 0, '2025-10-24 18:57:16', '2025-10-24 18:48:51'),
(7, 3, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2025-10-24 19:37:16', '2025-10-24 19:37:16'),
(22, 1, NULL, '2025-10-15', '00:00:00', '00:00:00', 0, 0, 0, 0, '2025-10-26 18:48:17', '2025-10-26 18:48:17'),
(23, 1, NULL, '2025-10-23', NULL, NULL, 1, 0, 0, 0, '2025-10-26 19:17:10', '2025-10-26 19:17:10'),
(24, 1, NULL, '2025-10-31', '10:00:00', NULL, 1, 0, 0, 0, '2025-10-26 19:19:34', '2025-10-26 19:19:34'),
(29, 1, NULL, '2025-10-31', '12:00:00', NULL, 1, 0, 0, 0, '2025-10-27 08:47:30', '2025-10-26 19:23:13');

-- --------------------------------------------------------

--
-- テーブルの構造 `reservation_people`
--

CREATE TABLE `reservation_people` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `person_no` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `furigana` varchar(100) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `age` varchar(10) DEFAULT NULL,
  `rental_kitsuke` varchar(50) DEFAULT NULL COMMENT 'レンタル・着付（無し／レンタル&着付／レンタルのみ／持ち込み着付）',
  `rental_out` varchar(50) DEFAULT NULL COMMENT '持ち出しレンタル（持ち出し有り／スタジオ内のみ／未定）',
  `height` float DEFAULT NULL COMMENT '身長',
  `foot_size` float DEFAULT NULL COMMENT '足サイズ',
  `hairmake` varchar(50) DEFAULT NULL COMMENT 'ヘアメイク（無し／ヘアセット／ヘアセット&メイク）',
  `request` text DEFAULT NULL,
  `note` text DEFAULT NULL COMMENT '備考',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `reservation_people`
--

INSERT INTO `reservation_people` (`id`, `reservation_id`, `person_no`, `name`, `furigana`, `gender`, `age`, `rental_kitsuke`, `rental_out`, `height`, `foot_size`, `hairmake`, `request`, `note`, `created_at`, `updated_at`) VALUES
(51, 22, 1, '', '', '', '0', NULL, NULL, 0, 0, '無し', '', NULL, '2025-10-26 18:48:17', '2025-10-26 18:48:17'),
(52, 23, 1, '', '', '', '0', NULL, NULL, 0, 0, '無し', '', NULL, '2025-10-26 19:17:10', '2025-10-26 19:17:10'),
(53, 24, 1, '', '', '', '0', NULL, NULL, 0, 0, '無し', '', NULL, '2025-10-26 19:19:34', '2025-10-26 19:19:34'),
(64, 29, 1, '', '', '', '0', NULL, NULL, 0, 0, '無し', '', NULL, '2025-10-27 08:47:30', '2025-10-27 08:47:30');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD KEY `idx_phone` (`phone`);

--
-- テーブルのインデックス `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_customer` (`customer_id`);

--
-- テーブルのインデックス `reservation_people`
--
ALTER TABLE `reservation_people`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reservation` (`reservation_id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- テーブルの AUTO_INCREMENT `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- テーブルの AUTO_INCREMENT `reservation_people`
--
ALTER TABLE `reservation_people`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- テーブルの制約 `reservation_people`
--
ALTER TABLE `reservation_people`
  ADD CONSTRAINT `reservation_people_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
