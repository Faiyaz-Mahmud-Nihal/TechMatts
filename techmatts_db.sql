-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 21, 2025 at 09:55 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `techmatts_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` varchar(10) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `size` varchar(50) DEFAULT NULL,
  `added_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_number` varchar(20) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `payment_method` enum('bkash','sslcommerz','cod') NOT NULL,
  `payment_status` enum('pending','paid','failed') NOT NULL DEFAULT 'pending',
  `shipping_address` text NOT NULL,
  `shipping_district` varchar(50) NOT NULL,
  `shipping_postcode` varchar(20) DEFAULT NULL,
  `shipping_phone` varchar(20) NOT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_number`, `order_date`, `total_amount`, `status`, `payment_method`, `payment_status`, `shipping_address`, `shipping_district`, `shipping_postcode`, `shipping_phone`, `notes`) VALUES
(1, 1, 'ORD-1755205976', '2025-08-15 03:12:56', 1100.00, 'pending', 'bkash', 'paid', 'House66, uttara ,dhaka, Dhaka', 'Dhaka', '1230', '01757575757', 'Deliver it as soon as possible '),
(2, 1, 'ORD-1755206989', '2025-08-15 03:29:49', 850.00, 'pending', 'bkash', 'paid', '66 malibagh, Dhaka', 'Dhaka', '1234', '01657474747', '1234'),
(3, 1, 'ORD-1755207150', '2025-08-15 03:32:30', 850.00, 'pending', 'bkash', 'paid', 'uttara, Dhaka', 'Dhaka', '1234', '01657575757', 'gg'),
(4, 1, 'ORD-1755207320', '2025-08-15 03:35:20', 850.00, 'pending', 'bkash', 'paid', 'house 66, Uttara .Dhaka, Dhaka', 'Dhaka', '1234', '01757575757', '1234'),
(5, 1, 'ORD-1755209421', '2025-08-15 04:10:21', 6050.00, 'pending', 'bkash', 'paid', '22,sector 10, Uttara, Dhaka', 'Dhaka', '1234', '01747474747', '1234'),
(6, 6, 'ORD-1755209949', '2025-08-15 04:19:09', 850.00, 'pending', 'bkash', 'paid', '66,uttara, Dhaka', 'Dhaka', '1234', '01521687878', 'deliver it soon'),
(7, 6, 'ORD-1755211002-9742', '2025-08-15 04:36:42', 850.00, 'pending', 'bkash', 'paid', '20, sector 10, Dhaka', 'Dhaka', '12345', '01850504848', 'quick deliver'),
(8, 7, 'ORD-1755211779', '2025-08-15 04:49:39', 1700.00, 'pending', 'bkash', 'paid', 'Uttara, Dhaka', 'Dhaka', '12345', '01836366636', 'ff'),
(9, 7, 'ORD-1755211927', '2025-08-15 04:52:07', 1700.00, 'pending', 'bkash', 'paid', 'House No:38,Khilgaon Bagicha ., Dhaka', 'Dhaka', '1217', '01747474747', 'ff'),
(10, 7, 'ORD-1755211933', '2025-08-15 04:52:13', 1700.00, 'pending', 'bkash', 'paid', 'House No:38,Khilgaon Bagicha ., Dhaka', 'Dhaka', '1217', '01747474747', 'ff'),
(11, 6, 'ORD-1755221287', '2025-08-15 07:28:07', 850.00, 'pending', 'bkash', 'paid', 'uttara, Dhaka', 'Dhaka', '123', '01750605040', ''),
(12, 6, 'ORD-1755221732', '2025-08-15 07:35:32', 850.00, 'pending', 'bkash', 'paid', 'uttara, Dhaka', 'Dhaka', '123', '01750605040', ''),
(13, 6, 'ORD-1755221788', '2025-08-15 07:36:28', 1700.00, 'pending', 'bkash', 'paid', 'mirpur, Dhaka', 'Dhaka', '1234', '01757575757', ''),
(14, 1, 'ORD-1755221890', '2025-08-15 07:38:10', 850.00, 'cancelled', 'bkash', 'paid', 'khilgaon, Dhaka', 'Dhaka', '12344', '01757575757', ''),
(15, 1, 'ORD-1755222103', '2025-08-15 07:41:43', 850.00, 'cancelled', 'bkash', 'paid', 'khilgaon, Dhaka', 'Dhaka', '12344', '01757575757', ''),
(16, 1, 'ORD-1755222742', '2025-08-15 07:52:22', 850.00, 'cancelled', 'bkash', 'paid', 'uttara, Dhaka', 'Dhaka', '1234', '01750506060', ''),
(17, 6, 'ORD-1755224044', '2025-08-15 08:14:04', 163500.00, 'delivered', 'bkash', 'paid', 'uttara, Dhaka', 'Dhaka', '1234', '01754757575', ''),
(18, 7, 'ORD-1755294707', '2025-08-16 03:51:47', 850.00, 'shipped', 'bkash', 'paid', '75, Dhaka', 'Dhaka', '12345', '01757573753', 'jf'),
(19, 1, 'ORD-1755302293', '2025-08-16 05:58:13', 670000.00, 'shipped', 'bkash', 'paid', 'Khilgaon , Dhaka', 'Dhaka', '1234', '016567345838', 'Deliver it as soon as possible '),
(20, 8, 'ORD-1755314333', '2025-08-16 09:18:53', 89000.00, 'delivered', 'bkash', 'paid', 'Uttara sector 11, Road20, Dhaka, Dhaka', 'Dhaka', '1230', '01757569462', 'GG'),
(21, 1, 'ORD-1755623416', '2025-08-19 23:10:16', 1100.00, 'shipped', 'bkash', 'paid', 'ff, Dhaka', 'Dhaka', '1234', '01757568362', '123444'),
(22, 1, 'ORD-1755793437', '2025-08-21 22:23:57', 850.00, 'processing', 'bkash', 'paid', 'khilgaon, Dhaka', 'Dhaka', '1234', '017576374758', 'fast delivery ');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` varchar(10) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `size` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `unit_price`, `size`) VALUES
(1, 1, 'mp-003', 1, 1100.00, NULL),
(2, 2, 'mp-001', 1, 850.00, NULL),
(3, 3, 'mp-004', 1, 850.00, NULL),
(4, 4, 'mp-005', 1, 850.00, NULL),
(5, 5, 'mp-002', 1, 5200.00, '700 X 300 X 4mm'),
(6, 5, 'mp-007', 1, 850.00, NULL),
(7, 6, 'mp-009', 1, 850.00, NULL),
(8, 7, 'mp-012', 1, 850.00, NULL),
(9, 8, 'mp-013', 1, 850.00, NULL),
(10, 8, 'mp-014', 1, 850.00, NULL),
(11, 9, 'mp-013', 1, 850.00, NULL),
(12, 9, 'mp-014', 1, 850.00, NULL),
(13, 10, 'mp-013', 1, 850.00, NULL),
(14, 10, 'mp-014', 1, 850.00, NULL),
(15, 11, 'mp-009', 1, 850.00, NULL),
(16, 12, 'mp-009', 1, 850.00, NULL),
(17, 13, 'mp-009', 1, 850.00, NULL),
(18, 13, 'mp-001', 1, 850.00, NULL),
(19, 14, 'mp-001', 1, 850.00, NULL),
(20, 15, 'mp-001', 1, 850.00, NULL),
(21, 16, 'mp-006', 1, 850.00, NULL),
(22, 17, 'pc-001', 1, 163500.00, NULL),
(23, 18, 'mp-004', 1, 850.00, NULL),
(24, 19, 'pc-005', 1, 670000.00, NULL),
(25, 20, 'pc-006', 1, 89000.00, NULL),
(26, 21, 'mp-003', 1, 1100.00, '900 X 400 X 4mm'),
(27, 22, 'mp-013', 1, 850.00, '700 X 300 X 4mm');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('mousepad','pcbuild') NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `price_range` varchar(50) DEFAULT NULL,
  `added_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `category`, `type`, `price`, `price_range`, `added_by`, `created_at`, `updated_at`, `is_active`) VALUES
('mp-001', 'Street Vibes Mousepad', 'Premium street-themed mousepad with vibrant design', 'mousepad', 'Control', 850.00, '850.00৳', 1, '2025-08-11 00:17:58', NULL, 1),
('mp-002', 'KenseiMat', 'High-quality gaming mousepad with smooth surface', 'mousepad', 'Speed', 5200.00, '5200.00৳', 1, '2025-08-11 00:17:58', NULL, 1),
('mp-003', 'Red Samurai Mousepad', 'Samurai-themed mousepad with red accents', 'mousepad', 'Speed', 1100.00, '1100.00৳', 1, '2025-08-11 00:17:58', NULL, 1),
('mp-004', 'Ukiru Mousepad', 'Artistic house design mousepad', 'mousepad', 'Speed', 850.00, '850.00৳', 1, '2025-08-11 00:17:58', NULL, 1),
('mp-005', 'Mozaic Matrix Mousepad', 'Matrix-style mosaic design mousepad', 'mousepad', 'Control', 850.00, '850.00৳', 1, '2025-08-11 00:17:58', NULL, 1),
('mp-006', 'Lines of Dhaka Mousepad', 'Dhaka city map design mousepad', 'mousepad', 'Control', 850.00, '850.00৳', 1, '2025-08-11 00:17:58', NULL, 1),
('mp-007', 'Rickshaw Matts', 'Traditional rickshaw design mousepad', 'mousepad', 'Control', 850.00, '850.00৳', 1, '2025-08-11 00:17:58', '2025-08-16 07:55:27', 1),
('mp-008', 'RGB Mousepad', 'Colorful wave design RGB mousepad', 'mousepad', 'Speed', 850.00, '850.00৳', 1, '2025-08-11 00:17:58', NULL, 1),
('mp-009', 'Russet Potato Mousepad', 'Unique potato-themed mousepad', 'mousepad', 'Speed', 850.00, '850.00৳', 1, '2025-08-11 00:17:58', NULL, 1),
('mp-010', 'Retro Pink Mousepad', 'Retro-style pink gaming mousepad', 'mousepad', 'Speed', 1100.00, '850.00৳', 1, '2025-08-11 00:17:58', NULL, 1),
('mp-011', 'Adrift Space Mousepad', 'Space-themed astronaut design mousepad', 'mousepad', 'Speed', 850.00, '850.00৳', 1, '2025-08-11 00:17:58', NULL, 1),
('mp-012', 'PulseMatrix Mouse Pad', 'Dynamic pulse matrix design mousepad', 'mousepad', 'Speed', 850.00, '850.00', 1, '2025-08-11 00:17:58', '2025-08-16 20:16:11', 1),
('mp-013', 'Astro Dreams', 'Astro girl blue space-themed mousepad', 'mousepad', 'Control', 850.00, '850.00৳', 1, '2025-08-11 00:17:58', '2025-08-16 20:15:48', 1),
('mp-014', 'Black And Red Mousepad', 'Sleek black and red design mousepad', 'mousepad', 'Control', 850.00, '850.00৳', 1, '2025-08-11 00:17:58', NULL, 1),
('mp-015', 'Black Mousepad', 'Minimalist full black mousepad', 'mousepad', 'Speed', 850.00, '850.00৳', 1, '2025-08-11 00:17:58', NULL, 1),
('mp-016', 'Timeline Mousepad', 'Tech timeline design mousepad', 'mousepad', 'Speed', 850.00, '850.00৳', 1, '2025-08-11 00:17:58', NULL, 1),
('mp-500', 'New Product ', 'new Item', 'mousepad', 'speed', 800.00, '800.00৳', 8, '2025-08-16 21:52:33', '2025-08-21 22:15:05', 1),
('pc-001', '168k Gaming PC', 'High-end gaming PC with RTX 5070', 'pcbuild', 'Gaming', 163500.00, '163,500.00৳', 1, '2025-08-11 00:19:07', NULL, 1),
('pc-002', '77k Gaming PC', 'Mid-range gaming PC with RTX 4060', 'pcbuild', 'Gaming', 77000.00, '77,000.00৳', 1, '2025-08-11 00:19:07', NULL, 1),
('pc-003', 'Solo Leveling 65K Gaming PC', 'Entry-level gaming PC with RTX 4060', 'pcbuild', 'Gaming', 65000.00, '65,000.00৳', 1, '2025-08-11 00:19:07', NULL, 1),
('pc-004', 'Gaming PC | AMD 9070 XT', 'Premium gaming PC with AMD 9070 XT', 'pcbuild', 'Gaming', 199100.00, '199,100.00৳', 1, '2025-08-11 00:19:07', NULL, 1),
('pc-005', 'Pro 670k PC Build', 'Extreme performance PC with RTX 5090', 'pcbuild', 'Extreme Gaming', 670000.00, '670,000.00৳', 1, '2025-08-11 00:19:07', NULL, 1),
('pc-006', 'Premium 89k PC Build', 'Mid-range DDR5 gaming PC', 'pcbuild', 'Gaming', 89000.00, '89,000.00৳', 1, '2025-08-11 00:19:07', NULL, 1),
('pc-007', 'Core 69k PC Build', 'Entry-level gaming PC with RGB RAM', 'pcbuild', 'Gaming', 69000.00, '69,000.00৳', 1, '2025-08-11 00:19:07', NULL, 1),
('pc-008', 'Lite 42k Gaming PC', 'Budget gaming PC with RX 580', 'pcbuild', 'Budget Gaming', 50000.00, '50000.00৳', 1, '2025-08-11 00:19:07', '2025-08-16 20:41:09', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `category_id` int(11) NOT NULL,
  `product_id` varchar(10) NOT NULL,
  `category_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`category_id`, `product_id`, `category_name`) VALUES
(1, 'mp-001', 'Mousepad'),
(2, 'mp-001', 'Pre Order'),
(3, 'mp-001', 'Speed'),
(4, 'mp-002', 'Mousepad'),
(5, 'mp-002', 'Pre Order'),
(6, 'mp-002', 'Speed'),
(7, 'mp-003', 'Mousepad'),
(8, 'mp-003', 'Pre Order'),
(9, 'mp-003', 'Speed'),
(10, 'mp-004', 'Mousepad'),
(11, 'mp-004', 'Pre Order'),
(12, 'mp-004', 'Speed'),
(13, 'mp-005', 'Mousepad'),
(14, 'mp-005', 'Pre Order'),
(15, 'mp-005', 'Control'),
(16, 'mp-006', 'Mousepad'),
(17, 'mp-006', 'Pre Order'),
(18, 'mp-006', 'Speed'),
(22, 'mp-008', 'Mousepad'),
(23, 'mp-008', 'Pre Order'),
(24, 'mp-008', 'Speed'),
(25, 'mp-009', 'Mousepad'),
(26, 'mp-009', 'Pre Order'),
(27, 'mp-009', 'Speed'),
(28, 'mp-010', 'Mousepad'),
(29, 'mp-010', 'Pre Order'),
(30, 'mp-010', 'Speed'),
(31, 'mp-011', 'Mousepad'),
(32, 'mp-011', 'Pre Order'),
(33, 'mp-011', 'Speed'),
(34, 'mp-012', 'Mousepad'),
(35, 'mp-012', 'Pre Order'),
(36, 'mp-012', 'Speed'),
(37, 'mp-013', 'Mousepad'),
(38, 'mp-013', 'Pre Order'),
(39, 'mp-013', 'Control'),
(40, 'mp-014', 'Mousepad'),
(41, 'mp-014', 'Pre Order'),
(42, 'mp-014', 'Control'),
(43, 'mp-015', 'Mousepad'),
(44, 'mp-015', 'Pre Order'),
(45, 'mp-015', 'Speed'),
(46, 'mp-016', 'Mousepad'),
(47, 'mp-016', 'Pre Order'),
(48, 'mp-016', 'Speed'),
(49, 'pc-001', 'PC Build'),
(50, 'pc-001', 'Gaming'),
(51, 'pc-002', 'PC Build'),
(52, 'pc-002', 'Gaming'),
(53, 'pc-003', 'PC Build'),
(54, 'pc-003', 'Gaming'),
(55, 'pc-004', 'PC Build'),
(56, 'pc-004', 'Gaming'),
(57, 'pc-005', 'PC Build'),
(58, 'pc-005', 'Extreme Gaming'),
(59, 'pc-006', 'PC Build'),
(60, 'pc-006', 'Gaming'),
(61, 'pc-007', 'PC Build'),
(62, 'pc-007', 'Gaming'),
(80, 'mp-007', 'Mousepad'),
(81, 'mp-007', 'Pre Order'),
(82, 'mp-007', 'Speed'),
(87, 'pc-008', 'PC Build'),
(88, 'pc-008', 'Budget Gaming'),
(92, 'mp-500', 'Pre -Order');

-- --------------------------------------------------------

--
-- Table structure for table `product_features`
--

CREATE TABLE `product_features` (
  `feature_id` int(11) NOT NULL,
  `product_id` varchar(10) NOT NULL,
  `feature` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_features`
--

INSERT INTO `product_features` (`feature_id`, `product_id`, `feature`) VALUES
(1, 'mp-001', 'Watersplash Proof'),
(2, 'mp-001', 'Washable'),
(3, 'mp-001', 'Natural Rubber'),
(4, 'mp-001', 'All Weather Ready'),
(5, 'mp-001', 'Glass Cloth'),
(6, 'mp-001', 'Glass'),
(7, 'mp-002', 'Watersplash Proof'),
(8, 'mp-002', 'Washable'),
(9, 'mp-002', 'Natural Rubber'),
(10, 'mp-002', 'All Weather Ready'),
(11, 'mp-002', 'Glass Cloth'),
(12, 'mp-002', 'Glass'),
(13, 'mp-003', 'Watersplash Proof'),
(14, 'mp-003', 'Washable'),
(15, 'mp-003', 'Natural Rubber'),
(16, 'mp-003', 'All Weather Ready'),
(17, 'mp-003', 'Glass Cloth'),
(18, 'mp-003', 'Glass'),
(19, 'mp-004', 'Watersplash Proof'),
(20, 'mp-004', 'Washable'),
(21, 'mp-004', 'Natural Rubber'),
(22, 'mp-004', 'All Weather Ready'),
(23, 'mp-004', 'Glass Cloth'),
(24, 'mp-004', 'Glass'),
(25, 'mp-005', 'Watersplash Proof'),
(26, 'mp-005', 'Washable'),
(27, 'mp-005', 'Natural Rubber'),
(28, 'mp-005', 'All Weather Ready'),
(29, 'mp-005', 'Glass Cloth'),
(30, 'mp-005', 'Glass'),
(31, 'mp-006', 'Watersplash Proof'),
(32, 'mp-006', 'Washable'),
(33, 'mp-006', 'Natural Rubber'),
(34, 'mp-006', 'All Weather Ready'),
(35, 'mp-006', 'Glass Cloth'),
(36, 'mp-006', 'Glass'),
(43, 'mp-008', 'Watersplash Proof'),
(44, 'mp-008', 'Washable'),
(45, 'mp-008', 'Natural Rubber'),
(46, 'mp-008', 'All Weather Ready'),
(47, 'mp-008', 'Glass Cloth'),
(48, 'mp-008', 'Glass'),
(49, 'mp-009', 'Watersplash Proof'),
(50, 'mp-009', 'Washable'),
(51, 'mp-009', 'Natural Rubber'),
(52, 'mp-009', 'All Weather Ready'),
(53, 'mp-009', 'Glass Cloth'),
(54, 'mp-009', 'Glass'),
(55, 'mp-010', 'Watersplash Proof'),
(56, 'mp-010', 'Washable'),
(57, 'mp-010', 'Natural Rubber'),
(58, 'mp-010', 'All Weather Ready'),
(59, 'mp-010', 'Glass Cloth'),
(60, 'mp-010', 'Glass'),
(61, 'mp-011', 'Watersplash Proof'),
(62, 'mp-011', 'Washable'),
(63, 'mp-011', 'Natural Rubber'),
(64, 'mp-011', 'All Weather Ready'),
(65, 'mp-011', 'Glass Cloth'),
(66, 'mp-011', 'Glass'),
(67, 'mp-012', 'Watersplash Proof'),
(68, 'mp-012', 'Washable'),
(69, 'mp-012', 'Natural Rubber'),
(70, 'mp-012', 'All Weather Ready'),
(71, 'mp-012', 'Glass Cloth'),
(72, 'mp-012', 'Glass'),
(73, 'mp-013', 'Watersplash Proof'),
(74, 'mp-013', 'Washable'),
(75, 'mp-013', 'Natural Rubber'),
(76, 'mp-013', 'All Weather Ready'),
(77, 'mp-013', 'Glass Cloth'),
(78, 'mp-013', 'Glass'),
(79, 'mp-014', 'Watersplash Proof'),
(80, 'mp-014', 'Washable'),
(81, 'mp-014', 'Natural Rubber'),
(82, 'mp-014', 'All Weather Ready'),
(83, 'mp-014', 'Glass Cloth'),
(84, 'mp-014', 'Glass'),
(85, 'mp-015', 'Watersplash Proof'),
(86, 'mp-015', 'Washable'),
(87, 'mp-015', 'Natural Rubber'),
(88, 'mp-015', 'All Weather Ready'),
(89, 'mp-015', 'Glass Cloth'),
(90, 'mp-015', 'Glass'),
(91, 'mp-016', 'Watersplash Proof'),
(92, 'mp-016', 'Washable'),
(93, 'mp-016', 'Natural Rubber'),
(94, 'mp-016', 'All Weather Ready'),
(95, 'mp-016', 'Glass Cloth'),
(96, 'mp-016', 'Glass'),
(97, 'pc-001', 'High Performance'),
(98, 'pc-001', 'Gaming Ready'),
(99, 'pc-001', 'RGB Lighting'),
(100, 'pc-002', 'Mid-range Gaming'),
(101, 'pc-002', 'Value for Money'),
(102, 'pc-003', 'Entry-level Gaming'),
(103, 'pc-003', 'RGB Lighting'),
(104, 'pc-004', 'High-end Gaming'),
(105, 'pc-004', 'RGB Lighting'),
(106, 'pc-004', 'Premium Components'),
(107, 'pc-005', 'Extreme Performance'),
(108, 'pc-005', 'Liquid Cooling'),
(109, 'pc-005', 'RGB Lighting'),
(110, 'pc-006', 'Mid-range Performance'),
(111, 'pc-006', 'DDR5 Memory'),
(112, 'pc-007', 'Entry-level Gaming'),
(113, 'pc-007', 'RGB RAM'),
(121, 'mp-007', 'Watersplash Proof'),
(122, 'mp-007', 'Washable'),
(123, 'mp-007', 'Natural Rubber'),
(124, 'mp-007', 'All Weather Ready'),
(125, 'mp-007', 'Glass Cloth'),
(126, 'mp-007', 'Glass'),
(130, 'mp-500', 'waterprrof');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `image_id` int(11) NOT NULL,
  `product_id` varchar(10) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `is_thumbnail` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`image_id`, `product_id`, `image_url`, `is_thumbnail`) VALUES
(3, 'mp-001', 'media/Mousepad/New folder/rickshaw-in-street.webp', 1),
(4, 'mp-001', 'media/Mousepad/New folder/dsc_8495.webp', 0),
(5, 'mp-001', 'media/Mousepad/New folder/dsc_8500.webp', 0),
(6, 'mp-001', 'media/Mousepad/New folder/dsc_8501.webp', 0),
(7, 'mp-002', 'media/Mousepad/kenseiMat/1.webp', 1),
(8, 'mp-002', 'media/Mousepad/kenseiMat/4.webp', 0),
(9, 'mp-002', 'media/Mousepad/kenseiMat/5.webp', 0),
(10, 'mp-002', 'media/Mousepad/kenseiMat/6.webp', 0),
(11, 'mp-003', 'media/Mousepad/Red Samurai/red-samurai.webp', 1),
(12, 'mp-003', 'media/Mousepad/Red Samurai/dsc_8488.webp', 0),
(13, 'mp-003', 'media/Mousepad/Red Samurai/dsc_8492.webp', 0),
(14, 'mp-003', 'media/Mousepad/Red Samurai/dsc_8494.webp', 0),
(15, 'mp-004', 'media/Mousepad/Ukiru/house-art-design.webp', 1),
(16, 'mp-004', 'media/Mousepad/Ukiru/dsc_8460.webp', 0),
(17, 'mp-004', 'media/Mousepad/Ukiru/dsc_8462.webp', 0),
(18, 'mp-004', 'media/Mousepad/Ukiru/dsc_8469.webp', 0),
(19, 'mp-005', 'media/Mousepad/Mozaic Matrix/pcbbd-typogrpahy.webp', 1),
(20, 'mp-005', 'media/Mousepad/Mozaic Matrix/dsc_8415-scaled.webp', 0),
(21, 'mp-005', 'media/Mousepad/Mozaic Matrix/dsc_8420-scaled.webp', 0),
(22, 'mp-005', 'media/Mousepad/Mozaic Matrix/dsc_8422-scaled.webp', 0),
(23, 'mp-006', 'media/Mousepad/Lines of Dhaka/dhaka-map.webp', 1),
(24, 'mp-006', 'media/Mousepad/Lines of Dhaka/dsc_8503-scaled.webp', 0),
(25, 'mp-006', 'media/Mousepad/Lines of Dhaka/dsc_8504-scaled.webp', 0),
(26, 'mp-006', 'media/Mousepad/Lines of Dhaka/dsc_8510-scaled.webp', 0),
(27, 'mp-007', 'media/Mousepad/Rickshaw Matts/rickshaw-design.webp', 1),
(28, 'mp-007', 'media/Mousepad/Rickshaw Matts/dsc_8444-scaled.webp', 0),
(29, 'mp-007', 'media/Mousepad/Rickshaw Matts/dsc_8449-scaled.webp', 0),
(30, 'mp-007', 'media/Mousepad/Rickshaw Matts/dsc_8446-scaled.webp', 0),
(31, 'mp-008', 'media/Mousepad/rgb/colorful-wave-design.webp', 1),
(32, 'mp-008', 'media/Mousepad/rgb/dsc_8430.webp', 0),
(33, 'mp-008', 'media/Mousepad/rgb/dsc_8428.webp', 0),
(34, 'mp-008', 'media/Mousepad/rgb/dsc_8427.webp', 0),
(35, 'mp-009', 'media/Mousepad/Russet Potato/potato.webp', 1),
(36, 'mp-009', 'media/Mousepad/Russet Potato/dsc_8470.webp', 0),
(37, 'mp-009', 'media/Mousepad/Russet Potato/dsc_8471.webp', 0),
(38, 'mp-009', 'media/Mousepad/Russet Potato/dsc_8472.webp', 0),
(39, 'mp-010', 'media/Mousepad/Retro Pink/pink-gaming-mousepad.webp', 1),
(40, 'mp-010', 'media/Mousepad/Retro Pink/dsc_8436.webp', 0),
(41, 'mp-010', 'media/Mousepad/Retro Pink/dsc_8433.webp', 0),
(42, 'mp-010', 'media/Mousepad/Retro Pink/dsc_8438.webp', 0),
(43, 'mp-011', 'media/Mousepad/Adrift Space/white-red-astronaut.webp', 1),
(44, 'mp-011', 'media/Mousepad/Adrift Space/dsc_8480.webp', 0),
(45, 'mp-011', 'media/Mousepad/Adrift Space/dsc_8482.webp', 0),
(46, 'mp-011', 'media/Mousepad/Adrift Space/dsc_8485.webp', 0),
(47, 'mp-012', 'media/Mousepad/PulseMatrix Mouse Pad/DSC_7498-scaled.webp', 1),
(48, 'mp-012', 'media/Mousepad/PulseMatrix Mouse Pad/DSC_7499-scaled.webp', 0),
(49, 'mp-012', 'media/Mousepad/PulseMatrix Mouse Pad/DSC_7509-scaled.webp', 0),
(50, 'mp-012', 'media/Mousepad/PulseMatrix Mouse Pad/DSC_7507-scaled.webp', 0),
(51, 'mp-013', 'media/Mousepad/Astro Dreams/astro-girl-blue.webp', 1),
(52, 'mp-013', 'media/Mousepad/Astro Dreams/astro-girl-3.webp', 0),
(53, 'mp-013', 'media/Mousepad/Astro Dreams/astro-girl-4.webp', 0),
(54, 'mp-013', 'media/Mousepad/Astro Dreams/astro-girl-2.webp', 0),
(55, 'mp-014', 'media/Mousepad/Black And red/Mousepad-02-900x400mm-WebP-scaled.webp', 1),
(56, 'mp-014', 'media/Mousepad/Black And red/DSC_7516-scaled.webp', 0),
(57, 'mp-014', 'media/Mousepad/Black And red/DSC_7520-scaled.webp', 0),
(58, 'mp-014', 'media/Mousepad/Black And red/DSC_7512-scaled.webp', 0),
(59, 'mp-015', 'media/Mousepad/Black/full-black-mousepad_bb.webp', 1),
(60, 'mp-015', 'media/Mousepad/Black/dsc_8514.webp', 0),
(61, 'mp-015', 'media/Mousepad/Black/dsc_8512.webp', 0),
(62, 'mp-015', 'media/Mousepad/Black/dsc_8511.webp', 0),
(63, 'mp-016', 'media/Mousepad/Timeline/pcbbd-timeline.webp', 1),
(64, 'mp-016', 'media/Mousepad/Timeline/dsc_8523.webp', 0),
(65, 'mp-016', 'media/Mousepad/Timeline/dsc_8520.webp', 0),
(66, 'mp-016', 'media/Mousepad/Timeline/dsc_8519.webp', 0),
(67, 'pc-001', 'media/Custom PC/Solo Leveling 65K Gaming PC/untitled-210-x-297-mm.png', 1),
(68, 'pc-001', 'media/Custom PC/Solo Leveling 65K Gaming PC/11.png', 0),
(69, 'pc-001', 'media/Custom PC/Solo Leveling 65K Gaming PC/12.png', 0),
(70, 'pc-001', 'media/Custom PC/Solo Leveling 65K Gaming PC/13.png', 0),
(71, 'pc-002', 'media/Custom PC/77k Gaming PC/16.png', 1),
(72, 'pc-002', 'media/Custom PC/77k Gaming PC/17.png', 0),
(73, 'pc-002', 'media/Custom PC/77k Gaming PC/18.png', 0),
(74, 'pc-003', 'media/Custom PC/All Red Gaming PC  AMD 9070 XT & 7800X3D/untitled-210-x-297-mm-1.png', 1),
(75, 'pc-003', 'media/Custom PC/All Red Gaming PC  AMD 9070 XT & 7800X3D/6.png', 0),
(76, 'pc-003', 'media/Custom PC/All Red Gaming PC  AMD 9070 XT & 7800X3D/7.png', 0),
(77, 'pc-003', 'media/Custom PC/All Red Gaming PC  AMD 9070 XT & 7800X3D/8.png', 0),
(78, 'pc-004', 'media/Custom PC/Lite 42k PC For lite gaming/1-1.svg', 1),
(79, 'pc-005', 'media/Custom PC/Pro 670k PC Build/4-1.svg', 1),
(80, 'pc-006', 'media/Custom PC/Premium 89k PC Build/3-2.svg', 1),
(81, 'pc-007', 'media/Custom PC/Core 69k PC Build/2-2.svg', 1),
(82, 'pc-008', 'media/Custom PC/77k Gaming PC/16.png', 1),
(93, 'mp-500', 'media/68a0a941d7eff.webp', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_sizes`
--

CREATE TABLE `product_sizes` (
  `size_id` int(11) NOT NULL,
  `product_id` varchar(10) NOT NULL,
  `dimensions` varchar(50) NOT NULL,
  `sku` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_sizes`
--

INSERT INTO `product_sizes` (`size_id`, `product_id`, `dimensions`, `sku`) VALUES
(1, 'mp-001', '900 X 400 X 4mm', 'street-vibes-900-400-1'),
(2, 'mp-001', '700 X 300 X 4mm', 'street-vibes-700-300-1'),
(3, 'mp-002', '900 X 400 X 4mm', 'street-vibes-900-400-1'),
(4, 'mp-002', '700 X 300 X 4mm', 'street-vibes-700-300-1'),
(5, 'mp-003', '900 X 400 X 4mm', 'street-vibes-900-400-1'),
(6, 'mp-003', '700 X 300 X 4mm', 'street-vibes-700-300-1'),
(7, 'mp-004', '900 X 400 X 4mm', 'street-vibes-900-400-1'),
(8, 'mp-004', '700 X 300 X 4mm', 'street-vibes-700-300-1'),
(9, 'mp-005', '900 X 400 X 4mm', 'street-vibes-900-400-1'),
(10, 'mp-005', '700 X 300 X 4mm', 'street-vibes-700-300-1'),
(11, 'mp-006', '900 X 400 X 4mm', 'street-vibes-900-400-1'),
(12, 'mp-006', '700 X 300 X 4mm', 'street-vibes-700-300-1'),
(15, 'mp-008', '900 X 400 X 4mm', 'street-vibes-900-400-1'),
(16, 'mp-008', '700 X 300 X 4mm', 'street-vibes-700-300-1'),
(17, 'mp-009', '900 X 400 X 4mm', 'street-vibes-900-400-1'),
(18, 'mp-009', '700 X 300 X 4mm', 'street-vibes-700-300-1'),
(19, 'mp-010', '900 X 400 X 4mm', 'street-vibes-900-400-1'),
(20, 'mp-010', '700 X 300 X 4mm', 'street-vibes-700-300-1'),
(21, 'mp-011', '900 X 400 X 4mm', 'street-vibes-900-400-1'),
(22, 'mp-011', '700 X 300 X 4mm', 'street-vibes-700-300-1'),
(23, 'mp-012', '900 X 400 X 4mm', 'street-vibes-900-400-1'),
(24, 'mp-012', '700 X 300 X 4mm', 'street-vibes-700-300-1'),
(25, 'mp-013', '900 X 400 X 4mm', 'street-vibes-900-400-1'),
(26, 'mp-013', '700 X 300 X 4mm', 'street-vibes-700-300-1'),
(27, 'mp-014', '900 X 400 X 4mm', 'street-vibes-900-400-1'),
(28, 'mp-014', '700 X 300 X 4mm', 'street-vibes-700-300-1'),
(29, 'mp-015', '900 X 400 X 4mm', 'street-vibes-900-400-1'),
(30, 'mp-015', '700 X 300 X 4mm', 'street-vibes-700-300-1'),
(31, 'mp-016', '900 X 400 X 4mm', 'street-vibes-900-400-1'),
(32, 'mp-016', '700 X 300 X 4mm', 'street-vibes-700-300-1'),
(38, 'mp-007', '900 X 400 X 4mm', 'street-vibes-900-400-1'),
(39, 'mp-007', '700 X 300 X 4mm', 'street-vibes-700-300-1');

-- --------------------------------------------------------

--
-- Table structure for table `product_specs`
--

CREATE TABLE `product_specs` (
  `spec_id` int(11) NOT NULL,
  `product_id` varchar(10) NOT NULL,
  `spec` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_specs`
--

INSERT INTO `product_specs` (`spec_id`, `product_id`, `spec`) VALUES
(1, 'pc-001', 'AMD Ryzen 7 7700'),
(2, 'pc-001', 'GIGABYTE B650M GAMING PLUS WIFI'),
(3, 'pc-001', 'Uphere uk2p6 CPU Cooler'),
(4, 'pc-001', 'Kingston KC3000 1TB M.2 NVMe SSD'),
(5, 'pc-001', 'Kingston FURY Beast 16GB 6000Mhz RAM'),
(6, 'pc-001', 'The 1STPLAYER Go6 Case'),
(7, 'pc-001', '1STPLAYER ACK Silver 850W PSU'),
(8, 'pc-001', 'Zotac Gaming GeForce RTX 5070 SOLID OC'),
(9, 'pc-002', 'AMD Ryzen 7 5700X'),
(10, 'pc-002', 'Biostar B550-5 E PRO'),
(11, 'pc-002', 'Uphere uk2p6 CPU Cooler'),
(12, 'pc-002', 'Kimtigo TP5000 SSD'),
(13, 'pc-002', '2x Kingston Fury Beast RAM'),
(14, 'pc-002', 'Valuetop Nubia 20F5 Case'),
(15, 'pc-002', '1st Player DK Premium PSU'),
(16, 'pc-002', 'GIGABYTE GeForce RTX 4060 EAGLE OC'),
(17, 'pc-003', 'AMD Ryzen 5 5600'),
(18, 'pc-003', 'GIGABYTE B450M K Motherboard'),
(19, 'pc-003', 'Kimtigo TP3000 512GB NVMe SSD'),
(20, 'pc-003', 'Kingston FURY Beast 16GB (2x8GB) 3200MHz RGB RAM'),
(21, 'pc-003', '1STPLAYER DK5.0 500W PSU'),
(22, 'pc-003', 'Maxsun GeForce RTX 4060 Terminator W 8GB'),
(23, 'pc-003', 'Value-Top NUBIA20F5W Case'),
(24, 'pc-004', 'Ryzen 7 7800X3D'),
(25, 'pc-004', 'Thermalright perless 120 RGB Cooler'),
(26, 'pc-004', 'Gigabyte B650M GAMING PLUS WIFI'),
(27, 'pc-004', 'Kingston Fury 32GB (16x2) RAM'),
(28, 'pc-004', 'Gigabyte G440E500G 500GB SSD'),
(29, 'pc-004', 'GIGABYTE C102 Glass Case'),
(30, 'pc-004', 'Power color REAPER 9070 XT GPU'),
(31, 'pc-004', '1st player steampunk 850W PSU'),
(32, 'pc-005', 'AMD Ryzen 7 9800X3D'),
(33, 'pc-005', 'NZXT KRAKEN ELITE 360 RGB AIO Liquid Cooler'),
(34, 'pc-005', 'GIGABYTE X870E AORUS MASTER Motherboard'),
(35, 'pc-005', 'Corsair Vengeance RGB 32GB DDR5 6000MHz'),
(36, 'pc-005', 'Kingston KC3000 2TB PCIe 4.0 NVMe SSD'),
(37, 'pc-005', 'Gigabyte AORUS GeForce RTX 5090 MASTER'),
(38, 'pc-005', '1STPLAYER NGDP ATX 3.1 1000W PSU'),
(39, 'pc-005', 'Antec Flux Pro E-ATX Case'),
(40, 'pc-005', 'NZXT F360 RGB Core Fans'),
(41, 'pc-006', 'Ryzen 5 7500F'),
(42, 'pc-006', 'Deepcool AK400 Digital Cooler'),
(43, 'pc-006', 'MSI Pro B650-M Motherboard'),
(44, 'pc-006', 'Kingston FURY Beast 16GB 6000Mhz DDR5'),
(45, 'pc-006', 'Kingston KC3000 512GB SSD'),
(46, 'pc-006', 'GUNNIR Intel Arc B580 GPU'),
(47, 'pc-006', 'FSP Hydro K PRO 750W PSU'),
(48, 'pc-006', 'Gamdias E3 Talos E3 Mesh Case'),
(49, 'pc-007', 'Ryzen 5 5600x'),
(50, 'pc-007', 'Arctic Freezer 7x Air Cooler'),
(51, 'pc-007', 'Asus Prime B450M-k II Motherboard'),
(52, 'pc-007', 'OCPC X3 8GB RGB 3200MHz RAM'),
(53, 'pc-007', 'Acer FA100 512GB Nvme SSD'),
(54, 'pc-007', 'Sparkle Intel Arc A750 ROC Luna OC White GPU'),
(55, 'pc-007', 'Acer AC 650W PSU'),
(56, 'pc-007', 'Acer U351W Micro ATX Case'),
(130, 'pc-008', 'Ryzen 5 5500'),
(131, 'pc-008', 'MSI B450M-A Pro Max II Motherboard'),
(132, 'pc-008', 'Apache NOX 8GB 3200Mhz RAM'),
(133, 'pc-008', 'XOC G300 512GB SSD'),
(134, 'pc-008', 'PELADN RX 580 8G 256Bit GPU'),
(135, 'pc-008', '1STPLAYER DK 5.0 500W PSU'),
(136, 'pc-008', 'MaxGreen S275-22 Case');

-- --------------------------------------------------------

--
-- Table structure for table `product_tags`
--

CREATE TABLE `product_tags` (
  `tag_id` int(11) NOT NULL,
  `product_id` varchar(10) NOT NULL,
  `tag_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_tags`
--

INSERT INTO `product_tags` (`tag_id`, `product_id`, `tag_name`) VALUES
(1, 'mp-001', 'deal'),
(2, 'mp-001', 'design'),
(3, 'mp-001', 'mousepad'),
(4, 'mp-001', 'street'),
(5, 'mp-001', 'streetvibes'),
(6, 'mp-002', 'deal'),
(7, 'mp-002', 'design'),
(8, 'mp-002', 'mousepad'),
(9, 'mp-002', 'street'),
(10, 'mp-002', 'streetvibes'),
(11, 'mp-003', 'deal'),
(12, 'mp-003', 'design'),
(13, 'mp-003', 'mousepad'),
(14, 'mp-003', 'samurai'),
(15, 'mp-003', 'red'),
(16, 'mp-004', 'deal'),
(17, 'mp-004', 'design'),
(18, 'mp-004', 'mousepad'),
(19, 'mp-004', 'art'),
(20, 'mp-004', 'house'),
(21, 'mp-005', 'deal'),
(22, 'mp-005', 'design'),
(23, 'mp-005', 'mousepad'),
(24, 'mp-005', 'matrix'),
(25, 'mp-005', 'control'),
(26, 'mp-006', 'deal'),
(27, 'mp-006', 'design'),
(28, 'mp-006', 'mousepad'),
(29, 'mp-006', 'dhaka'),
(30, 'mp-006', 'map'),
(36, 'mp-008', 'deal'),
(37, 'mp-008', 'design'),
(38, 'mp-008', 'mousepad'),
(39, 'mp-008', 'rgb'),
(40, 'mp-008', 'colorful'),
(41, 'mp-009', 'deal'),
(42, 'mp-009', 'design'),
(43, 'mp-009', 'mousepad'),
(44, 'mp-009', 'potato'),
(45, 'mp-009', 'fun'),
(46, 'mp-010', 'deal'),
(47, 'mp-010', 'design'),
(48, 'mp-010', 'mousepad'),
(49, 'mp-010', 'retro'),
(50, 'mp-010', 'pink'),
(51, 'mp-011', 'deal'),
(52, 'mp-011', 'design'),
(53, 'mp-011', 'mousepad'),
(54, 'mp-011', 'space'),
(55, 'mp-011', 'astronaut'),
(56, 'mp-012', 'deal'),
(57, 'mp-012', 'design'),
(58, 'mp-012', 'mousepad'),
(59, 'mp-012', 'pulse'),
(60, 'mp-012', 'matrix'),
(61, 'mp-013', 'deal'),
(62, 'mp-013', 'design'),
(63, 'mp-013', 'mousepad'),
(64, 'mp-013', 'astro'),
(65, 'mp-013', 'dreams'),
(66, 'mp-014', 'deal'),
(67, 'mp-014', 'design'),
(68, 'mp-014', 'mousepad'),
(69, 'mp-014', 'black'),
(70, 'mp-014', 'red'),
(71, 'mp-015', 'deal'),
(72, 'mp-015', 'design'),
(73, 'mp-015', 'mousepad'),
(74, 'mp-015', 'black'),
(75, 'mp-015', 'minimal'),
(76, 'mp-016', 'deal'),
(77, 'mp-016', 'design'),
(78, 'mp-016', 'mousepad'),
(79, 'mp-016', 'timeline'),
(80, 'mp-016', 'tech'),
(81, 'pc-001', 'gaming'),
(82, 'pc-001', 'high-end'),
(83, 'pc-001', 'amd'),
(84, 'pc-001', 'nvidia'),
(85, 'pc-002', 'gaming'),
(86, 'pc-002', 'mid-range'),
(87, 'pc-002', 'amd'),
(88, 'pc-002', 'nvidia'),
(89, 'pc-003', 'gaming'),
(90, 'pc-003', 'entry-level'),
(91, 'pc-003', 'amd'),
(92, 'pc-003', 'nvidia'),
(93, 'pc-004', 'gaming'),
(94, 'pc-004', 'high-end'),
(95, 'pc-004', 'amd'),
(96, 'pc-004', 'premium'),
(97, 'pc-005', 'extreme'),
(98, 'pc-005', 'liquid-cooling'),
(99, 'pc-005', 'high-end'),
(100, 'pc-005', 'nvidia'),
(101, 'pc-006', 'gaming'),
(102, 'pc-006', 'mid-range'),
(103, 'pc-006', 'ddr5'),
(104, 'pc-006', 'intel'),
(105, 'pc-007', 'gaming'),
(106, 'pc-007', 'entry-level'),
(107, 'pc-007', 'rgb'),
(108, 'pc-007', 'intel'),
(136, 'mp-007', 'deal'),
(137, 'mp-007', 'design'),
(138, 'mp-007', 'mousepad'),
(139, 'mp-007', 'rickshaw'),
(140, 'mp-007', 'bangladesh'),
(149, 'pc-008', 'gaming'),
(150, 'pc-008', 'budget'),
(151, 'pc-008', 'entry-level'),
(152, 'pc-008', 'amd'),
(156, 'mp-500', 'New');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `supplier_since` date DEFAULT NULL,
  `bank_account` varchar(50) DEFAULT NULL,
  `tax_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `company_name`, `contact_person`, `supplier_since`, `bank_account`, `tax_id`) VALUES
(8, 'Pc Builder Bangladesh', 'Nihal', '2025-08-01', 'Ab Bank', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('customer','supplier','admin') NOT NULL DEFAULT 'customer',
  `registration_date` datetime NOT NULL DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `address` text DEFAULT NULL,
  `district` varchar(50) DEFAULT NULL,
  `postcode` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `phone`, `password_hash`, `role`, `registration_date`, `last_login`, `is_active`, `address`, `district`, `postcode`) VALUES
(1, 'Mahir', '', 'mahir@gmail.com', '01757575757', '$2y$10$ozVQA4wmCQdu8bfRoDhNuepCSBfYFgSZBbZi9xVnRg7Ie8RYiIwhi', 'customer', '2025-08-10 05:04:47', '2025-08-21 22:23:18', 1, '', '', ''),
(5, 'Admin', ' ', 'admin@techmatts.com', '01757569462', '$2y$10$jK9I3jBZYxdfCWtQltcdROoMYpyioHe4KSEJx/pkb5vqkG0rHWeGO', 'admin', '2025-08-10 06:44:37', '2025-08-21 22:24:21', 1, 'Uttara', 'Dhaka', '1230'),
(6, 'Faiyaz', 'Mahmud', 'faiyaz@gmail.com', '01756757437', '$2y$10$PK91gJ6EFwWSO2VAG3YxUuA1zI2j4FVL2Yvbq2NMYZ0pdsSwz/g/K', 'customer', '2025-08-10 08:11:40', '2025-08-21 23:59:44', 1, '', '', ''),
(7, 'hamza', 'Ahmed', 'hamza@gmail.com', '01747474747', '$2y$10$7u.wDodI7kMaGxP3rvh2re6hR3PKplAprkAucmOSCYmiXcaVne9YC', 'customer', '2025-08-15 03:27:20', '2025-08-16 09:10:54', 1, 'Mirpur,', 'Dhaka', '1230'),
(8, 'Faiyaz ', 'Mahmud', 'faiyazmahmud99@gamil.com', '01757569462', '$2y$10$rEDmApTW46wSqcgiTMkfq.tWiRgp/z8H1VzcTu5ZIeUglDHzaWbuu', 'supplier', '2025-08-16 09:13:04', '2025-08-21 22:14:59', 1, 'Uttara Sector 11', 'Dhaka', '1230');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `added_by` (`added_by`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`category_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_features`
--
ALTER TABLE `product_features`
  ADD PRIMARY KEY (`feature_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD PRIMARY KEY (`size_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_specs`
--
ALTER TABLE `product_specs`
  ADD PRIMARY KEY (`spec_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_tags`
--
ALTER TABLE `product_tags`
  ADD PRIMARY KEY (`tag_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `product_features`
--
ALTER TABLE `product_features`
  MODIFY `feature_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `product_sizes`
--
ALTER TABLE `product_sizes`
  MODIFY `size_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `product_specs`
--
ALTER TABLE `product_specs`
  MODIFY `spec_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- AUTO_INCREMENT for table `product_tags`
--
ALTER TABLE `product_tags`
  MODIFY `tag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD CONSTRAINT `product_categories_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_features`
--
ALTER TABLE `product_features`
  ADD CONSTRAINT `product_features_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD CONSTRAINT `product_sizes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_specs`
--
ALTER TABLE `product_specs`
  ADD CONSTRAINT `product_specs_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_tags`
--
ALTER TABLE `product_tags`
  ADD CONSTRAINT `product_tags_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD CONSTRAINT `suppliers_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
