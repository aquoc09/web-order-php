-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 12, 2025 at 02:03 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quanlynhahang`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE IF NOT EXISTS `cart` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `cartStatus` varchar(50) DEFAULT NULL,
  `createAt` datetime DEFAULT CURRENT_TIMESTAMP,
  `updateAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `cartStatus`, `createAt`, `updateAt`) VALUES
(1, 5, 'active', '2025-11-24 23:30:30', '2025-11-24 23:30:30'),
(2, 6, 'active', '2025-11-26 19:31:44', '2025-11-26 19:31:44'),
(3, 8, 'active', '2025-11-28 22:04:01', '2025-11-28 22:04:01'),
(4, 9, 'active', '2025-11-29 14:29:17', '2025-11-29 14:29:17'),
(5, 11, 'active', '2025-12-08 00:34:18', '2025-12-08 00:34:18'),
(6, 10, 'active', '2025-12-08 14:29:06', '2025-12-08 14:29:06');

-- --------------------------------------------------------

--
-- Table structure for table `cart_item`
--

DROP TABLE IF EXISTS `cart_item`;
CREATE TABLE IF NOT EXISTS `cart_item` (
  `cartId` bigint NOT NULL,
  `product_id` bigint NOT NULL,
  `quantity` int DEFAULT NULL,
  `totalMoney` float DEFAULT NULL,
  `note` text,
  PRIMARY KEY (`cartId`,`product_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cart_item`
--

INSERT INTO `cart_item` (`cartId`, `product_id`, `quantity`, `totalMoney`, `note`) VALUES
(1, 1, 1, NULL, NULL),
(2, 1, 1, NULL, NULL),
(4, 4, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `categoryCode` varchar(50) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`, `categoryCode`, `active`) VALUES
(1, 'RICE', 'RICE', 1),
(2, 'HOT UDON', 'UDON_HOT', 1),
(3, 'SALAD', 'SALAD', 1),
(4, 'RICE CURRY', 'RICE_CURRY', 1);

-- --------------------------------------------------------

--
-- Table structure for table `coupon`
--

DROP TABLE IF EXISTS `coupon`;
CREATE TABLE IF NOT EXISTS `coupon` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `code` varchar(50) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `discountAmount` decimal(10,2) DEFAULT NULL,
  `conditionAmount` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
CREATE TABLE IF NOT EXISTS `order` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `note` text,
  `orderDate` datetime DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `totalMoney` float DEFAULT NULL,
  `paymentMethod` varchar(50) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `createAt` datetime DEFAULT CURRENT_TIMESTAMP,
  `updateAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`id`, `user_id`, `address`, `note`, `orderDate`, `status`, `totalMoney`, `paymentMethod`, `active`, `createAt`, `updateAt`) VALUES
(1, 8, 'q8, hcm', '', '2025-11-28 22:04:18', 'accepted', 375000, 'cod', 1, '2025-11-28 22:04:18', '2025-11-28 23:29:03'),
(2, 10, '123', '', '2025-12-08 15:16:11', 'pending', 250000, 'cod', 1, '2025-12-08 15:16:11', '2025-12-08 15:16:11'),
(3, 10, '123', '', '2025-12-08 15:16:36', 'accepted', 250000, 'vnpay', 1, '2025-12-08 15:16:36', '2025-12-08 15:17:03'),
(4, 10, 'Test', '', '2025-12-08 15:20:13', 'pending', 125000, 'vnpay', 1, '2025-12-08 15:20:13', '2025-12-08 15:20:37'),
(5, 10, 'Test', '', '2025-12-08 15:21:15', 'pending', 125000, 'cod', 1, '2025-12-08 15:21:15', '2025-12-08 15:21:15'),
(6, 10, 'Test11', '', '2025-12-08 15:21:36', 'pending', 125000, 'vnpay', 1, '2025-12-08 15:21:36', '2025-12-08 15:21:56'),
(7, 10, '123', '', '2025-12-08 15:26:02', 'pending', 6666670, 'cod', 1, '2025-12-08 15:26:02', '2025-12-08 15:26:02'),
(8, 10, '123 HCM', '', '2025-12-08 15:27:15', 'pending', 125000, 'vnpay', 1, '2025-12-08 15:27:15', '2025-12-08 15:27:43'),
(9, 10, '123123', '', '2025-12-08 15:29:16', 'pending', 125000, 'cod', 1, '2025-12-08 15:29:16', '2025-12-08 15:29:16'),
(10, 10, '12343', '', '2025-12-08 15:29:33', 'pending', 125000, 'vnpay', 1, '2025-12-08 15:29:33', '2025-12-08 15:29:54'),
(11, 10, '123 HCM', '', '2025-12-08 15:38:02', 'pending', 1125000, 'vnpay', 1, '2025-12-08 15:38:02', '2025-12-08 15:41:44'),
(12, 10, '123 HCM', '', '2025-12-08 15:39:19', 'pending', 1125000, 'vnpay', 1, '2025-12-08 15:39:19', '2025-12-08 15:39:42'),
(13, 11, '123', '', '2025-12-08 15:45:15', 'pending', 125000, 'vnpay', 1, '2025-12-08 15:45:15', '2025-12-08 15:46:28'),
(14, 11, '123', '', '2025-12-08 15:46:41', 'pending', 125000, 'vnpay', 1, '2025-12-08 15:46:41', '2025-12-08 15:46:41'),
(15, 11, '123', '', '2025-12-08 16:16:30', 'pending', 125000, 'vnpay', 1, '2025-12-08 16:16:30', '2025-12-08 16:16:30'),
(16, 11, '123', '', '2025-12-08 16:22:09', 'pending', 125000, 'cod', 1, '2025-12-08 16:22:09', '2025-12-08 16:22:09'),
(17, 11, '123', '', '2025-12-09 00:24:23', 'pending', 125000, 'vnpay', 1, '2025-12-09 00:24:23', '2025-12-09 00:24:23'),
(18, 11, '123', '', '2025-12-09 00:27:35', 'pending', 125000, 'vnpay', 1, '2025-12-09 00:27:35', '2025-12-09 00:27:35');

-- --------------------------------------------------------

--
-- Table structure for table `order_detail`
--

DROP TABLE IF EXISTS `order_detail`;
CREATE TABLE IF NOT EXISTS `order_detail` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `order_id` bigint DEFAULT NULL,
  `product_id` bigint DEFAULT NULL,
  `numOfProducts` int DEFAULT NULL,
  `totalMoney` float DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_detail`
--

INSERT INTO `order_detail` (`id`, `order_id`, `product_id`, `numOfProducts`, `totalMoney`) VALUES
(1, 1, 1, 2, 250000),
(2, 1, 4, 1, 125000),
(3, 2, 1, 1, 125000),
(4, 2, 2, 1, 125000),
(5, 3, 1, 1, 125000),
(6, 3, 2, 1, 125000),
(7, 4, 2, 1, 125000),
(8, 5, 4, 1, 125000),
(9, 6, 4, 1, 125000),
(10, 7, 10, 1, 6666670),
(11, 8, 2, 1, 125000),
(12, 9, 2, 1, 125000),
(13, 10, 2, 1, 125000),
(14, 11, 1, 5, 625000),
(15, 11, 2, 4, 500000),
(16, 12, 1, 5, 625000),
(17, 12, 2, 4, 500000),
(18, 13, 2, 1, 125000),
(19, 14, 1, 1, 125000),
(20, 15, 1, 1, 125000),
(21, 16, 1, 1, 125000),
(22, 17, 1, 1, 125000),
(23, 18, 1, 1, 125000);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `productCode` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `productImage` varchar(255) DEFAULT NULL,
  `description` text,
  `category_id` bigint DEFAULT NULL,
  `inStock` tinyint(1) DEFAULT '1',
  `inPopular` tinyint(1) DEFAULT '0',
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `name`, `productCode`, `price`, `productImage`, `description`, `category_id`, `inStock`, `inPopular`, `active`) VALUES
(1, 'Udon Bò Trứng Onsen', 'UDON_NIKU_ONSEN', 125000.00, 'udon_niku_onsen_1765128405.jpg', 'Mì bò trứng onsen', 2, 1, 0, 1),
(2, 'Butadon', 'RICE_BUTADON', 125000.00, 'rice_butadon_1764254039.png', 'Cơm thịt heo xông khói ăn cùng sốt mayonise kiểu Nhật', 1, 1, 0, 1),
(4, 'Udon Tôm + Rong', 'UDON_EBI_WAKAME', 125000.00, 'udon_ebi_wakame_1765128411.jpg', 'Mì udon tôm tempura và rong biển', 2, 1, 1, 1),
(5, 'T1', 'RICE_BUTADON', 9999.00, 'rice_butadon_1765127186.png', 'tttt', 1, 1, 0, 1),
(6, 'AAA', 'RICE_BUTADON', 200000.00, 'rice_butadon_1765127255.png', '', 1, 1, 0, 1),
(7, 'Téadada', 'UDON_NIKU_ONSEN', 10000000.00, 'udon_niku_onsen_1765128445.jpg', 'ádasdaadd', 2, 1, 0, 1),
(8, 'ádaafdf', 'RICE_BUTADON', 99999999.99, 'rice_butadon_1765127314.png', '', 1, 1, 0, 1),
(9, 'dsadsa', 'sadsd', 200000.00, 'sadsd_1765128190.png', 'addfds', 1, 1, 1, 1),
(10, 'dsfdsgf', 'RICE_BUTADON', 6666666.00, 'rice_butadon_1765128211.png', 'dsgdghghj', 1, 1, 0, 1),
(11, 'đàgfg', 'hfgfhgf', 2222222.00, 'hfgfhgf_1765128229.png', 'jgjhjfgd', 1, 1, 1, 1),
(12, 'sdad', 'UDON_NIKU_ONSEN', 200000.00, 'udon_niku_onsen_1765128368.png', 'sdasgsdfd', 2, 1, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `revenue`
--

DROP TABLE IF EXISTS `revenue`;
CREATE TABLE IF NOT EXISTS `revenue` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `totalMoney` float DEFAULT NULL,
  `totalOrder` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `fullName` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `userImage` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `facebookAccountId` text,
  `googleAccountId` text,
  `address` varchar(255) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `fullName`, `username`, `password`, `userImage`, `email`, `phone`, `active`, `facebookAccountId`, `googleAccountId`, `address`, `role`, `createdAt`, `updatedAt`) VALUES
(5, 'Nguyễn Võ Anh', 'aquoc01', '$2y$12$yCkK1GaHkBgCTxnrbOjmHeaaUeBDYLV7pzTRoJj0agGN4xs.ktrxC', 'aquoc01_1764271350.jpg', 'kenjiprovip@gmail.com', '0909123456', 1, NULL, NULL, 'Q8, HCM', 'admin', '2025-11-22 13:14:23', '2025-11-28 20:37:50'),
(6, 'Nguyễn Võ Anh Quốc 01A324', '', NULL, '_1764271308.jpg', 'anhquoc7a4@gmail.com', '', 1, NULL, '101220335148052205716', '', 'user', '2025-11-26 19:31:02', '2025-11-28 02:21:48'),
(7, 'Nguyễn Văn Anh', 'user0001', '$2y$12$2EDfvesUYY3Ry2ymLPDBd.LoU6bHE0zFrkMLnSu0zWYej4VOAxgWG', 'user0001_1764271270.jpg', '', '0909123456', 1, NULL, NULL, 'q8, hcm', 'admin', '2025-11-28 01:29:14', '2025-12-02 21:52:25'),
(8, 'Nguyễn Văn B', 'user0002', '$2y$12$HKe/oRqqNkzCNbgnJZ08BuzIGBv9.9AYzVNaCBCL.6Re2XCCm9KxS', 'user0002_1764268337.jpg', 'example@gmail.com', '0909123456', 1, NULL, NULL, 'q8, hcm', 'user', '2025-11-28 01:32:17', '2025-11-28 01:32:17'),
(9, 'Quốc Nguyễn (Kenji)', NULL, NULL, NULL, 'anhquoc6a4@gmail.com', NULL, 1, NULL, '108866701826669752032', NULL, 'user', '2025-11-29 14:29:03', '2025-11-29 14:29:03'),
(10, 'Nguyen Quoc Huy', 'quochuy', '$2y$10$WGzLbABjynJfP0EviAODSuqn.bys/Sbm5OrvoQ5Cl40ZaL/2GnyY6', NULL, 'quochuy@gmail.com', '0381234567', 1, NULL, NULL, NULL, 'user', '2025-12-02 22:04:46', '2025-12-08 00:04:33'),
(11, 'Admin Huy', 'admin123', '$2y$10$MMd91mRsAyS2QRZtNorX0eHQVg0yFWL8VzMA/L.yAi4mEV2ad7l1O', NULL, 'admin@gmail.com', '0381234588', 1, NULL, NULL, NULL, 'admin', '2025-12-02 22:05:55', '2025-12-02 22:06:18');

-- --------------------------------------------------------

--
-- Table structure for table `user_tokens`
--

DROP TABLE IF EXISTS `user_tokens`;
CREATE TABLE IF NOT EXISTS `user_tokens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `refresh_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_tokens`
--

INSERT INTO `user_tokens` (`id`, `user_id`, `token`, `expires_at`, `created_at`, `refresh_time`) VALUES
(10, 3, '361ca18350c8c65879ba58cbbe15997506fd2a0de2cbf20898873fbfda2a8e12', '2025-11-23 06:15:56', '2025-11-22 13:15:56', '2025-11-24 06:15:56'),
(15, 5, '82f946a8cd9f92d65a08ddd960cc54ee6d00f751a48911e6e52ad6d2a4ef47ce', '2025-11-27 14:45:11', '2025-11-26 21:45:11', '2025-11-28 14:45:11'),
(21, 5, 'b3b592c986e8c132a7df128a09a675a4923d48bf82eed77576da0cbab59fa476', '2025-11-28 19:03:24', '2025-11-28 02:03:24', '2025-11-29 19:03:24'),
(28, 7, 'ce8e86646547db32b0da152917364e160a2f470115951cbd9e9d9655c3e3b344', '2025-12-03 14:52:06', '2025-12-02 21:52:06', '2025-12-04 14:52:06'),
(30, 11, 'b4f6ad3835cdd100f07805cd489b5fbf695dadf3b0e995591ef800afde668cdb', '2025-12-03 15:06:27', '2025-12-02 22:06:27', '2025-12-04 15:06:27'),
(38, 11, '71533833690f09231c5a41a9b4cae283e54de043adbc4ccf15ddfe64599c20c1', '2025-12-09 08:42:29', '2025-12-08 15:42:29', '2025-12-10 08:42:29'),
(35, 10, '450e5bf9d77dbddd3c910ba6dd99b1060d36852180573bcb027a1e14198d4fac', '2025-12-09 07:33:50', '2025-12-08 14:33:50', '2025-12-10 07:33:50'),
(39, 11, '2d61a7598e80409e5e01b94c3ebb1e7d9806387a94eae26f6454bee11b0cf048', '2025-12-13 14:02:22', '2025-12-12 21:02:22', '2025-12-14 14:02:22');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cart_item`
--
ALTER TABLE `cart_item`
  ADD CONSTRAINT `cart_item_ibfk_1` FOREIGN KEY (`cartId`) REFERENCES `cart` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cart_item_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD CONSTRAINT `order_detail_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_detail_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- --------------------------------------------------------

--
-- Table structure for table `order_coupon`
--

DROP TABLE IF EXISTS `order_coupon`;
CREATE TABLE IF NOT EXISTS `order_coupon` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `order_id` bigint DEFAULT NULL,
  `coupon_code` varchar(50) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

ALTER TABLE `order_coupon`
  ADD CONSTRAINT `order_coupon_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;