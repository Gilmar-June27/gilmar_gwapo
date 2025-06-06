-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 05, 2025 at 04:45 PM
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
-- Database: `junkshop`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_notification`
--

CREATE TABLE `admin_notification` (
  `id` int(11) NOT NULL,
  `loan_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL COMMENT '''0=unread, 1=read''',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `collector_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_notification`
--

INSERT INTO `admin_notification` (`id`, `loan_id`, `admin_id`, `message`, `status`, `created_at`, `collector_id`) VALUES
(242, 63, 3, 'Your loan has been confirmed.', 0, '2025-05-30 02:39:55', 36),
(243, 63, 3, 'Your loan has been released.', 0, '2025-05-30 02:40:02', 36),
(244, 63, 3, 'Your loan has been marked as completed.', 0, '2025-05-30 02:40:04', 36),
(245, 64, 3, 'Your loan has been confirmed.', 0, '2025-05-30 06:00:31', 10),
(246, 64, 3, 'Your loan has been released.', 0, '2025-05-30 06:00:34', 10),
(247, 64, 3, 'Your loan has been marked as completed.', 0, '2025-05-30 06:00:37', 10),
(248, 0, 0, 'Your pickup request has been marked as completed.', 0, '2025-06-02 12:24:06', 43),
(249, 0, 0, 'Your pickup request has been marked as completed.', 0, '2025-06-02 12:24:33', 43),
(250, 79, 3, 'Your loan has been confirmed.', 0, '2025-06-02 16:08:11', 36),
(251, 79, 3, 'Your loan has been released.', 0, '2025-06-02 16:08:14', 36),
(252, 79, 3, 'Your loan has been marked as completed.', 0, '2025-06-02 16:08:16', 36),
(253, 81, 3, 'Your loan has been confirmed.', 0, '2025-06-04 01:10:39', 36),
(254, 81, 3, 'Your loan has been released.', 0, '2025-06-04 01:11:26', 36),
(255, 81, 3, 'Your loan has been marked as completed.', 0, '2025-06-04 01:11:31', 36),
(256, 78, 3, 'Your loan has been confirmed.', 0, '2025-06-05 07:40:33', 32),
(257, 78, 3, 'Your loan has been released.', 0, '2025-06-05 07:40:40', 32),
(258, 82, 3, 'Your loan has been confirmed.', 0, '2025-06-05 07:46:15', 32),
(259, 82, 3, 'Your loan has been released.', 0, '2025-06-05 07:46:58', 32),
(260, 82, 3, 'Your loan has been marked as completed.', 0, '2025-06-05 07:47:11', 32),
(261, 83, 3, 'Your loan has been confirmed.', 0, '2025-06-05 07:48:32', 35),
(262, 83, 3, 'Your loan has been released.', 0, '2025-06-05 07:48:37', 35),
(263, 83, 3, 'Your loan has been marked as completed.', 0, '2025-06-05 07:48:43', 35),
(264, 84, 3, 'Your loan has been confirmed.', 0, '2025-06-05 08:13:40', 36),
(265, 87, 3, 'Your loan has been confirmed.', 0, '2025-06-05 09:30:23', 32),
(266, 87, 3, 'Your loan has been released.', 0, '2025-06-05 13:11:46', 32),
(267, 90, 3, 'Your loan has been confirmed.', 0, '2025-06-05 13:53:26', 35),
(268, 90, 3, 'Your loan has been released.', 0, '2025-06-05 13:53:35', 35),
(269, 90, 3, 'Your loan has been marked as completed.', 0, '2025-06-05 13:53:42', 35);

-- --------------------------------------------------------

--
-- Table structure for table `approved_collectors`
--

CREATE TABLE `approved_collectors` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `collector_id` int(11) NOT NULL,
  `approved_at` datetime NOT NULL DEFAULT current_timestamp(),
  `completed_at` datetime NOT NULL DEFAULT current_timestamp(),
  `declined_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `borrower`
--

CREATE TABLE `borrower` (
  `borrower_id` int(11) NOT NULL,
  `collector_id` int(11) NOT NULL,
  `tax_id` varchar(255) NOT NULL,
  `admin_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrower`
--

INSERT INTO `borrower` (`borrower_id`, `collector_id`, `tax_id`, `admin_id`) VALUES
(20, 32, '1231313', 0),
(21, 35, '12', 0),
(23, 1, '123123', 0),
(25, 10, '1232313', 0),
(27, 35, '3243424', 0),
(28, 1, '[op', 0),
(29, 35, '123', 0),
(30, 1, '1', 0),
(31, 32, '21323', 0),
(32, 1, '000000000', 0),
(33, 1, 'PPPPP', 0),
(34, 36, '312113', 0),
(35, 0, '12332132', 3),
(36, 43, '1234213', 0);

-- --------------------------------------------------------

--
-- Table structure for table `collector_notification`
--

CREATE TABLE `collector_notification` (
  `id` int(11) NOT NULL,
  `loan_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL COMMENT '''0=unread, 1=read''',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `customer_id` int(11) NOT NULL,
  `pickup_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `collector_notification`
--

INSERT INTO `collector_notification` (`id`, `loan_id`, `admin_id`, `message`, `status`, `created_at`, `customer_id`, `pickup_id`) VALUES
(166, 0, 0, 'Your pickup request has been approved.', 1, '2025-05-29 21:35:32', 41, 0),
(167, 0, 0, 'Your pickup request has been approved.', 1, '2025-05-29 21:35:36', 41, 0),
(168, 0, 0, 'Your pickup request has been marked as completed.', 1, '2025-05-30 07:40:57', 41, 0),
(169, 0, 0, 'Your pickup request has been marked as completed.', 0, '2025-05-30 09:29:47', 46, 0),
(170, 0, 0, 'Your pickup request has been approved.', 1, '2025-06-02 21:18:18', 41, 0),
(171, 0, 0, 'Your pickup request has been marked as completed.', 1, '2025-06-02 21:18:38', 41, 0),
(172, 0, 0, 'Your pickup request has been marked as completed.', 1, '2025-06-02 21:19:18', 41, 0),
(173, 0, 0, 'Your pickup request has been approved.', 1, '2025-06-03 21:56:32', 47, 0),
(174, 0, 0, 'Your pickup request has been approved.', 0, '2025-06-03 23:06:44', 47, 0),
(175, 0, 0, 'Your pickup request has been marked as completed.', 0, '2025-06-05 12:09:54', 51, 0);

-- --------------------------------------------------------

--
-- Table structure for table `customer_notification`
--

CREATE TABLE `customer_notification` (
  `id` int(11) NOT NULL,
  `loan_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL COMMENT '''0=unread, 1=read''',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `collector_id` int(11) NOT NULL,
  `pickup_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_notification`
--

INSERT INTO `customer_notification` (`id`, `loan_id`, `admin_id`, `message`, `status`, `created_at`, `collector_id`, `pickup_id`, `customer_id`) VALUES
(121, 0, 0, 'Pickup request from customer.', 0, '2025-06-02 20:53:37', 10, 0, 41),
(122, 0, 0, 'Pickup request from customer.', 0, '2025-06-02 20:55:40', 1, 0, 41),
(123, 0, 0, 'Pickup request from customer.', 0, '2025-06-02 20:57:17', 10, 0, 41),
(124, 0, 0, 'Pickup request from customer.', 0, '2025-06-02 20:57:41', 36, 0, 41),
(125, 0, 0, 'Pickup request from customer.', 0, '2025-06-02 21:05:55', 36, 0, 41),
(126, 0, 0, 'Pickup request from customer.', 0, '2025-06-02 21:06:06', 10, 0, 41),
(127, 0, 0, 'Pickup request from customer.', 0, '2025-06-02 21:07:50', 10, 0, 41),
(128, 0, 0, 'Pickup request from customer.', 0, '2025-06-02 21:09:33', 32, 0, 41),
(129, 0, 0, 'Pickup request from customer.', 0, '2025-06-02 21:09:37', 32, 0, 41),
(130, 0, 0, 'Pickup request from customer.', 0, '2025-06-02 21:10:17', 1, 0, 41),
(131, 0, 0, 'Pickup request from customer.', 0, '2025-06-02 21:11:27', 1, 0, 41),
(132, 0, 0, 'Pickup request from customer.', 0, '2025-06-02 21:11:30', 1, 0, 41),
(133, 0, 0, 'Pickup request from customer.', 0, '2025-06-02 21:11:51', 1, 0, 41),
(134, 0, 0, 'Pickup request from customer.', 0, '2025-06-02 21:12:05', 1, 0, 41),
(135, 0, 0, 'Pickup request from customer.', 0, '2025-06-02 21:12:09', 1, 0, 41),
(136, 0, 0, 'Pickup request from customer.', 0, '2025-06-02 21:12:21', 1, 0, 41),
(137, 0, 0, 'Pickup request from customer.', 0, '2025-06-02 21:12:31', 36, 0, 41),
(138, 0, 0, 'Pickup request from customer.', 0, '2025-06-02 21:17:09', 10, 0, 41),
(139, 0, 0, 'Pickup request from customer.', 0, '2025-06-02 21:17:36', 36, 0, 41),
(140, 0, 0, 'Pickup request from customer.', 0, '2025-06-02 21:17:41', 36, 0, 41),
(141, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 21:28:49', 1, 0, 0),
(142, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 21:29:32', 1, 0, 0),
(143, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 21:29:54', 1, 0, 0),
(144, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 21:33:26', 1, 0, 0),
(145, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 21:33:59', 1, 0, 0),
(146, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 21:36:40', 1, 0, 0),
(147, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 21:38:00', 1, 0, 0),
(148, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 21:38:44', 1, 0, 0),
(149, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 21:40:20', 1, 0, 0),
(150, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 21:42:16', 1, 0, 0),
(151, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 21:42:51', 1, 0, 0),
(152, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 21:43:33', 1, 0, 0),
(153, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 21:48:06', 1, 0, 0),
(154, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 21:48:30', 1, 0, 0),
(155, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 21:48:43', 1, 0, 0),
(156, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 21:49:20', 1, 0, 0),
(157, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 21:54:11', 1, 0, 0),
(158, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 21:54:50', 1, 0, 0),
(159, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 21:55:49', 1, 0, 0),
(160, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 21:56:19', 36, 0, 47),
(161, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 22:30:45', 36, 0, 47),
(162, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 22:33:30', 10, 0, 47),
(163, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 22:34:15', 10, 0, 47),
(164, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 22:34:34', 10, 0, 47),
(165, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 22:34:50', 10, 0, 47),
(166, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 22:35:33', 10, 0, 47),
(167, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 22:35:45', 36, 0, 47),
(168, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 22:36:10', 36, 0, 47),
(169, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 22:36:46', 36, 0, 47),
(170, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 22:37:07', 36, 0, 47),
(171, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 22:38:37', 36, 0, 47),
(172, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 22:39:06', 36, 0, 47),
(173, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 22:39:12', 36, 0, 47),
(174, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 22:39:59', 44, 0, 47),
(175, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 22:40:43', 44, 0, 47),
(176, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 22:40:56', 44, 0, 47),
(177, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 22:41:05', 44, 0, 47),
(178, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 22:41:52', 44, 0, 47),
(179, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 22:45:32', 44, 0, 47),
(180, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 22:45:52', 44, 0, 47),
(181, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 22:49:00', 44, 0, 47),
(182, 0, 0, 'Pickup request from customer.', 0, '2025-06-03 23:02:00', 36, 0, 47),
(183, 0, 0, 'Pickup request from customer.', 0, '2025-06-04 06:41:44', 10, 0, 47),
(184, 0, 0, 'Pickup request from customer.', 0, '2025-06-04 07:58:30', 36, 0, 47),
(185, 0, 0, 'Pickup request from customer.', 0, '2025-06-04 18:51:10', 36, 0, 47),
(186, 0, 0, 'Pickup request from customer.', 0, '2025-06-04 20:49:19', 10, 0, 47),
(187, 0, 0, 'Pickup request from customer.', 0, '2025-06-05 10:47:08', 44, 0, 41),
(188, 0, 0, 'Pickup request from customer.', 0, '2025-06-05 10:52:49', 43, 0, 41),
(189, 0, 0, 'Pickup request from customer.', 0, '2025-06-05 10:53:13', 1, 0, 41),
(190, 0, 0, 'Pickup request from customer.', 0, '2025-06-05 10:58:21', 35, 0, 47),
(191, 0, 0, 'Pickup request from customer.', 0, '2025-06-05 11:23:20', 36, 0, 48),
(192, 0, 0, 'Pickup request from customer.', 0, '2025-06-05 11:36:44', 36, 0, 49),
(193, 0, 0, 'Pickup request from customer.', 0, '2025-06-05 11:43:35', 36, 0, 51),
(194, 0, 0, 'Pickup request from customer.', 0, '2025-06-05 22:07:46', 44, 0, 52);

-- --------------------------------------------------------

--
-- Table structure for table `documentation`
--

CREATE TABLE `documentation` (
  `id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `review` varchar(255) NOT NULL,
  `image` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `collector_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `pickup_id` int(11) NOT NULL COMMENT 'completed',
  `admin_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documentation`
--

INSERT INTO `documentation` (`id`, `description`, `review`, `image`, `created_at`, `collector_id`, `customer_id`, `pickup_id`, `admin_id`) VALUES
(67, '34', 'ewr', '1748429016_Screenshot (11).png', '2025-05-28 10:43:19', 35, 41, 0, 0),
(68, '', '', '', '2025-05-28 13:59:53', 36, 41, 0, 0),
(69, '767657', 'rtyry', '1748562016_6f6190df-adb3-4f74-b2d8-e28baad3dc57_removalai_preview (1) (1).png', '2025-05-29 23:39:05', 36, 41, 0, 0),
(70, '', '', '', '2025-05-30 01:29:51', 36, 46, 0, 0),
(71, 'asd', 'fds', '1748757370_214567b57d343f38ba73be9cca40a1ef (1) (1).jpg', '2025-06-01 05:56:10', 10, 0, 0, 3),
(72, 'qwdqw', 'asdasd', '1748793712_Gemini_Generated_Image_2u5u6k2u5u6k2u5u.png', '2025-06-01 16:01:52', 32, 0, 0, 3),
(73, 'estf', 'wqd', '1748874528_6f6190df-adb3-4f74-b2d8-e28baad3dc57_removalai_preview (1) (1).png', '2025-06-01 16:27:12', 43, 0, 0, 3),
(74, '', '', '', '2025-06-02 13:18:43', 36, 41, 0, 0),
(75, '', '', '', '2025-06-02 13:19:22', 36, 41, 0, 0),
(78, 'estf', 'asdasd', '1749015034_jeffrey.png', '2025-06-04 05:30:34', 44, 0, 0, 3),
(83, '100000000000000000', 'sdfwsf', '1749016296_GCash-092431-23032025082315.PNG.jpg', '2025-06-04 05:51:36', 37, 0, 0, 3),
(84, '', '', '', '2025-06-05 04:26:28', 36, 49, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `junk_price`
--

CREATE TABLE `junk_price` (
  `id` int(11) NOT NULL,
  `junk_type` varchar(255) NOT NULL DEFAULT 'metal',
  `image` varchar(255) NOT NULL,
  `garbage_price` varchar(255) NOT NULL,
  `kl` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `collector_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `junk_price`
--

INSERT INTO `junk_price` (`id`, `junk_type`, `image`, `garbage_price`, `kl`, `created_at`, `collector_id`, `admin_id`) VALUES
(1, 'Plastic', '', '645', '1kg', '2025-03-26 14:18:46', 0, 0),
(2, 'Plastic', '', '645', '1kg', '2025-03-26 14:19:08', 0, 0),
(3, 'Metal', '', '4', '1kg', '2025-03-26 14:19:19', 0, 0),
(4, 'Wood', '1000016186.jpg', '22', '1kg', '2025-03-27 13:26:58', 0, 0),
(5, 'Glass', '', '32', '1kg', '2025-04-10 11:24:09', 0, 0),
(6, 'Paper', '', '231', '1kg', '2025-04-10 11:24:52', 0, 0),
(7, 'Paper', '', '231', '1kg', '2025-04-10 11:25:16', 0, 0),
(8, 'Paper', '', '231', '1kg', '2025-04-10 11:25:59', 0, 0),
(9, 'Paper', '', '231', '1kg', '2025-04-10 11:27:17', 0, 0),
(10, 'Paper', '', '231', '1kg', '2025-04-10 11:34:03', 0, 0),
(11, 'Paper', '', '32', '1kg', '2025-04-10 11:51:07', 0, 0),
(12, 'Paper', '', '32', '1kg', '2025-04-10 11:52:37', 0, 0),
(13, 'Metal', '', '32', '1kg', '2025-04-10 11:52:49', 0, 0),
(14, 'Metal', 'Screenshot 2024-02-09 085547.png', '32', '1kg', '2025-04-10 12:01:44', 44, 0),
(15, 'Metal', 'promo2.jpg', '32', '1kg', '2025-04-10 12:04:33', 7, 0),
(16, 'Paper', 'Screenshot 2024-07-17 142046.jpg', '000', '5kg', '2025-04-10 12:04:59', 7, 0),
(17, 'Paper', 'Screenshot 2024-07-17 142046.jpg', '000', '5kg', '2025-04-10 12:07:11', 7, 0),
(18, 'Paper', 'Screenshot 2024-07-17 142046.jpg', '000', '5kg', '2025-04-10 12:07:50', 7, 0),
(19, 'Copper', 'Screenshot 2024-07-17 142046.jpg', '2133', '1kg', '2025-04-10 12:27:45', 7, 0),
(20, 'Electronics', 'Screenshot 2024-03-06 035022.png', '222', '1kg', '2025-04-10 12:44:56', 7, 0),
(21, 'Fabric', 'Screenshot 2024-07-14 134159.jpg', '4', '10kg', '2025-04-10 12:45:11', 7, 0),
(35, 'Wood', 'Screenshot (3).png', '5443', '1kg', '2025-04-10 13:53:06', 9, 0),
(37, 'Copper', 'Screenshot (14).png', '32', '1kg', '2025-04-10 14:20:08', 9, 0),
(38, 'Plastic', '2d1a896a-01c0-4a52-ae6f-a95a6ef57c0d.jpg', '23', '1kg', '2025-04-15 11:42:50', 10, 0),
(45, 'Metal', 'Gemini_Generated_Image_2u5u6k2u5u6k2u5u.png', '4', '1kg', '2025-05-13 13:43:57', 43, 0),
(46, 'Paper', 'jeffrey.png', '-3', '2kg', '2025-05-13 13:44:22', 43, 0),
(47, 'Metal', 'Gemini_Generated_Image_2u5u6k2u5u6k2u5u.png', '3', '1kg', '2025-05-13 13:56:25', 43, 0),
(48, 'Metal', 'Coding workshop-bro (1).png', '1', '1kg', '2025-05-13 13:56:38', 43, 0),
(49, 'Metal', 'Gemini_Generated_Image_2u5u6k2u5u6k2u5u.png', '223', '1kg', '2025-05-13 13:56:47', 43, 0),
(50, 'Metal', 'Gemini_Generated_Image_2u5u6k2u5u6k2u5u.png', '231', '1kg', '2025-05-13 13:56:55', 43, 0),
(51, 'Glass', 'Gemini_Generated_Image_2u5u6k2u5u6k2u5u.png', '2', '1kg', '2025-05-13 13:58:39', 43, 0),
(52, 'Glass', '214567b57d343f38ba73be9cca40a1ef (1) (1).jpg', '111', '1kg', '2025-05-14 10:23:13', 35, 0),
(53, 'Metal', 'Screenshot (2).png', '111', '1kg', '2025-05-14 10:24:59', 43, 0),
(54, 'Rubber', 'Metal-Steel.webp', '111', '1kg', '2025-05-14 10:25:17', 43, 0),
(55, 'Metal', '1000016186.jpg', '11', '1kg', '2025-05-16 06:59:57', 43, 0),
(56, 'Paper', 'Screenshot 2025-03-22 165610.png', '4', '1kg', '2025-05-18 08:13:02', 36, 0),
(58, 'Paper', 'Screenshot 2025-03-23 122003.png', '4234', '1kg', '2025-05-28 00:14:22', 0, 3),
(59, 'Plastic', 'Screenshot (2).png', '2321', '2kg', '2025-05-28 14:09:12', 44, 0);

-- --------------------------------------------------------

--
-- Table structure for table `loan`
--

CREATE TABLE `loan` (
  `loan_id` int(11) NOT NULL,
  `ref_no` varchar(255) NOT NULL,
  `ltype_id` int(11) NOT NULL,
  `borrower_id` int(11) NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `amount` int(11) NOT NULL,
  `lplan_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL COMMENT '0=request, 1=confirmed, 2=released, 3=completed, 4=denied',
  `date_released` datetime NOT NULL,
  `date_created` datetime NOT NULL,
  `collector_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `is_paid` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan`
--

INSERT INTO `loan` (`loan_id`, `ref_no`, `ltype_id`, `borrower_id`, `purpose`, `amount`, `lplan_id`, `status`, `date_released`, `date_created`, `collector_id`, `admin_id`, `is_paid`) VALUES
(85, '469345', 0, 20, 'esfdewf', 23421, 1, 0, '2025-06-05 11:13:30', '2025-06-05 11:13:30', 32, 3, 0),
(86, '072027', 0, 20, 'esfdewf', 23421, 1, 0, '2025-06-05 11:13:30', '2025-06-05 11:13:30', 32, 3, 0),
(87, '467731', 0, 20, 'esfdewf', 23421, 1, 2, '2025-06-05 11:13:31', '2025-06-05 11:13:31', 32, 3, 0),
(88, '235506', 0, 20, 'eswfdew', 1112, 1, 0, '2025-06-05 15:28:17', '2025-06-05 15:28:17', 32, 3, 0),
(89, '574745', 20, 20, 'ytf', 10, 1, 0, '2025-06-05 15:30:57', '2025-06-05 15:30:57', 32, 3, 0),
(90, '193246', 21, 21, 'wewqerwq', 10, 1, 3, '2025-06-05 15:38:16', '2025-06-05 15:38:16', 35, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `loan_plan`
--

CREATE TABLE `loan_plan` (
  `lplan_id` int(11) NOT NULL,
  `lplan_month` int(11) NOT NULL,
  `lplan_interest` float NOT NULL,
  `lplan_penalty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan_plan`
--

INSERT INTO `loan_plan` (`lplan_id`, `lplan_month`, `lplan_interest`, `lplan_penalty`) VALUES
(1, 1, 1, 1),
(2, 1, 1, 1),
(3, 1, 1, 1),
(4, 2, 2, 2),
(5, 2, 2, 2),
(7, 33, 33, 33);

-- --------------------------------------------------------

--
-- Table structure for table `loan_schedule`
--

CREATE TABLE `loan_schedule` (
  `loan_sched_id` int(11) NOT NULL,
  `loan_id` int(11) NOT NULL,
  `due_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_type`
--

CREATE TABLE `loan_type` (
  `ltype_id` int(11) NOT NULL,
  `ltype_name` varchar(255) NOT NULL,
  `ltype_desc` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan_type`
--

INSERT INTO `loan_type` (`ltype_id`, `ltype_name`, `ltype_desc`) VALUES
(20, 'wqdsq', 'dwqqd'),
(21, 'asdwqaed', 'qwedwq'),
(24, 'qwerqwr', 'awsfrdewrfew'),
(25, 'ewfewfrw', 'wefrew'),
(26, '7', 'THYTRYRTYR');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `loan_id` int(11) NOT NULL,
  `payee` varchar(255) NOT NULL,
  `pay_amount` float NOT NULL,
  `penalty` float NOT NULL,
  `overdue` tinyint(4) NOT NULL COMMENT '0=no, 1=yes',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `is_paid` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_id`, `loan_id`, `payee`, `pay_amount`, `penalty`, `overdue`, `date_created`, `is_paid`) VALUES
(64, 61, '', 20200, 1, 0, '2025-05-29 21:11:55', 1),
(65, 64, '', 3990, 33, 0, '2025-05-30 14:00:43', 1),
(66, 79, '', 202, 1, 0, '2025-06-03 00:08:27', 1),
(67, 81, '', 34.34, 1, 0, '2025-06-04 13:23:57', 1),
(68, 83, '', 21555.4, 1, 0, '2025-06-05 15:48:47', 1),
(69, 90, '', 10.1, 1, 0, '2025-06-05 22:45:15', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pay_customer`
--

CREATE TABLE `pay_customer` (
  `id` int(11) NOT NULL,
  `money` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pickup_requests`
--

CREATE TABLE `pickup_requests` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `junk_type` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `preferred_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `customer_id` int(11) NOT NULL,
  `collector_id` int(11) NOT NULL,
  `paid` varchar(255) NOT NULL,
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `kl` varchar(255) NOT NULL,
  `admin_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pickup_requests`
--

INSERT INTO `pickup_requests` (`id`, `name`, `address`, `contact_number`, `junk_type`, `description`, `preferred_date`, `status`, `created_at`, `customer_id`, `collector_id`, `paid`, `paid_at`, `kl`, `admin_id`) VALUES
(71, '', 'anonang inabanga bohol', '', 'Metal', '', NULL, 'Completed', '2025-06-02 14:59:11', 0, 10, '213', '2025-06-02 14:59:11', '2kl', 3),
(72, '', 'Guindulman, Bohol', '', 'Metal', '', NULL, 'Completed', '2025-06-04 04:48:19', 0, 32, '200', '2025-06-04 04:48:19', '1kl', 3),
(73, '', 'tagbilaran city', '', 'Metal', '', NULL, 'Completed', '2025-06-02 14:18:35', 0, 43, '3424', '2025-06-04 01:47:13', '1kl', 3),
(77, '', 'tagbilaran city', '', 'Plastic', '', NULL, 'Completed', '2025-06-04 05:32:59', 0, 43, '32', '2025-06-04 05:32:59', '1k', 3),
(83, '', 'CTU Main - ICT Building, Cebu City, Cebu, Philippines', '', 'Glass', '', NULL, 'Completed', '2025-06-04 05:52:03', 0, 37, '3', '2025-06-04 07:48:19', '3kl', 3),
(194, '22', 'CTU Main - ICT Building, Cebu City, Cebu, Philippines', '076865757467', 'Metal', 'cvbhngcvbn', '2025-06-02', 'Pending', '2025-06-02 13:17:09', 41, 10, 'Unpaid', '2025-06-02 13:17:09', '1kg', 0),
(195, '22', 'CTU Main - ICT Building, Cebu City, Cebu, Philippines', '076865757467', 'Plastic', 'fdgd', '2025-06-02', 'Completed', '2025-06-02 13:17:36', 41, 36, '3454', '2025-06-02 13:19:18', '1kl', 0),
(196, '22', 'CTU Main - ICT Building, Cebu City, Cebu, Philippines', '076865757467', 'Plastic', 'fdgd', '2025-06-02', 'Completed', '2025-06-02 13:17:41', 41, 36, '543', '2025-06-02 13:18:38', '1kl', 0),
(197, '', '', '', 'Plastic', 'qweq', '2025-06-03', 'Pending', '2025-06-03 13:28:49', 0, 1, 'Unpaid', '2025-06-03 13:28:49', '1kg', 0),
(198, '', '', '', 'Plastic', 'qweq', '2025-06-03', 'Pending', '2025-06-03 13:29:32', 0, 1, 'Unpaid', '2025-06-03 13:29:32', '1kg', 0),
(199, '', '', '', 'Plastic', 'qweq', '2025-06-03', 'Pending', '2025-06-03 13:29:54', 0, 1, 'Unpaid', '2025-06-03 13:29:54', '1kg', 0),
(200, '', '', '', 'Plastic', 'qweq', '2025-06-03', 'Pending', '2025-06-03 13:33:26', 0, 1, 'Unpaid', '2025-06-03 13:33:26', '1kg', 0),
(201, '', '', '', 'Plastic', 'qweq', '2025-06-03', 'Pending', '2025-06-03 13:33:59', 0, 1, 'Unpaid', '2025-06-03 13:33:59', '1kg', 0),
(202, '', '', '', 'Plastic', 'qweq', '2025-06-03', 'Pending', '2025-06-03 13:36:40', 0, 1, 'Unpaid', '2025-06-03 13:36:40', '1kg', 0),
(203, '', '', '', 'Plastic', 'qweq', '2025-06-03', 'Pending', '2025-06-03 13:38:00', 0, 1, 'Unpaid', '2025-06-03 13:38:00', '1kg', 0),
(204, '', '', '', 'Plastic', 'qweq', '2025-06-03', 'Pending', '2025-06-03 13:38:44', 0, 1, 'Unpaid', '2025-06-03 13:38:44', '1kg', 0),
(205, '', '', '', 'Plastic', 'qweq', '2025-06-03', 'Pending', '2025-06-03 13:40:20', 0, 1, 'Unpaid', '2025-06-03 13:40:20', '1kg', 0),
(206, '', '', '', 'Plastic', 'qweq', '2025-06-03', 'Pending', '2025-06-03 13:42:16', 0, 1, 'Unpaid', '2025-06-03 13:42:16', '1kg', 0),
(207, '', '', '', 'Plastic', 'qweq', '2025-06-03', 'Pending', '2025-06-03 13:42:51', 0, 1, 'Unpaid', '2025-06-03 13:42:51', '1kg', 0),
(208, '', '', '', 'Plastic', 'qweq', '2025-06-03', 'Pending', '2025-06-03 13:43:33', 0, 1, 'Unpaid', '2025-06-03 13:43:33', '1kg', 0),
(209, '', '', '', 'Plastic', 'qweq', '2025-06-03', 'Pending', '2025-06-03 13:48:06', 0, 1, 'Unpaid', '2025-06-03 13:48:06', '1kg', 0),
(210, '', '', '', 'Plastic', 'qweq', '2025-06-03', 'Pending', '2025-06-03 13:48:30', 0, 1, 'Unpaid', '2025-06-03 13:48:30', '1kg', 0),
(211, '', '', '', 'Plastic', 'qweq', '2025-06-03', 'Pending', '2025-06-03 13:48:43', 0, 1, 'Unpaid', '2025-06-03 13:48:43', '1kg', 0),
(212, '', '', '', 'Plastic', 'qweq', '2025-06-03', 'Pending', '2025-06-03 13:49:20', 0, 1, 'Unpaid', '2025-06-03 13:49:20', '1kg', 0),
(213, '', '', '', 'Plastic', 'qweq', '2025-06-03', 'Pending', '2025-06-03 13:54:11', 0, 1, 'Unpaid', '2025-06-03 13:54:11', '1kg', 0),
(214, '', '', '', 'Plastic', 'qweq', '2025-06-03', 'Pending', '2025-06-03 13:54:50', 0, 1, 'Unpaid', '2025-06-03 13:54:50', '1kg', 0),
(215, '', '', '', 'Plastic', 'qweq', '2025-06-03', 'Pending', '2025-06-03 13:55:49', 0, 1, 'Unpaid', '2025-06-03 13:55:49', '1kg', 0),
(216, 'sdfgfdhg fdhdfhfdhfdhfd', 'Hunan, Buenavista, Bohol', '076865757467', 'Plastic', 'werff', '2025-06-03', 'Approved', '2025-06-03 13:56:19', 47, 36, 'Unpaid', '2025-06-03 13:56:19', '1kg', 0),
(217, 'sdfgfdhg fdhdfhfdhfdhfd', 'Poblascion, Buenavista, Bohol', '076865757467', 'Plastic', 'wdasd', '2025-06-03', 'Pending', '2025-06-03 14:30:45', 47, 36, 'Unpaid', '2025-06-03 14:30:45', '1kg', 0),
(218, 'sdfgfdhg fdhdfhfdhfdhfd', 'Poblascion, Buenavista, Bohol', '076865757467', 'Plastic', 'hgjhgj', '2025-06-03', 'Pending', '2025-06-03 14:33:30', 47, 10, 'Unpaid', '2025-06-03 14:33:30', '1kg', 0),
(219, 'sdfgfdhg fdhdfhfdhfdhfd', 'Poblascion, Buenavista, Bohol', '076865757467', 'Plastic', 'hgjhgj', '2025-06-03', 'Pending', '2025-06-03 14:34:15', 47, 10, 'Unpaid', '2025-06-03 14:34:15', '1kg', 0),
(220, 'sdfgfdhg fdhdfhfdhfdhfd', 'Poblascion, Buenavista, Bohol', '076865757467', 'Plastic', 'hgjhgj', '2025-06-03', 'Pending', '2025-06-03 14:34:34', 47, 10, 'Unpaid', '2025-06-03 14:34:34', '1kg', 0),
(221, 'sdfgfdhg fdhdfhfdhfdhfd', 'Poblascion, Buenavista, Bohol', '076865757467', 'Plastic', 'hgjhgj', '2025-06-03', 'Pending', '2025-06-03 14:34:50', 47, 10, 'Unpaid', '2025-06-03 14:34:50', '1kg', 0),
(222, 'sdfgfdhg fdhdfhfdhfdhfd', 'Poblascion, Buenavista, Bohol', '076865757467', 'Plastic', 'hgjhgj', '2025-06-03', 'Pending', '2025-06-03 14:35:33', 47, 10, 'Unpaid', '2025-06-03 14:35:33', '1kg', 0),
(223, 'sdfgfdhg fdhdfhfdhfdhfd', 'Poblascion, Buenavista, Bohol', '076865757467', 'Plastic', 'aDXasd', '2025-06-03', 'Pending', '2025-06-03 14:35:45', 47, 36, 'Unpaid', '2025-06-03 14:35:45', '1kg', 0),
(224, 'sdfgfdhg fdhdfhfdhfdhfd', 'Poblascion, Buenavista, Bohol', '076865757467', 'Plastic', 'aDXasd', '2025-06-03', 'Pending', '2025-06-03 14:36:10', 47, 36, 'Unpaid', '2025-06-03 14:36:10', '1kg', 0),
(225, 'sdfgfdhg fdhdfhfdhfdhfd', 'Poblascion, Buenavista, Bohol', '076865757467', 'Plastic', 'aDXasd', '2025-06-03', 'Pending', '2025-06-03 14:36:46', 47, 36, 'Unpaid', '2025-06-03 14:36:46', '1kg', 0),
(226, 'sdfgfdhg fdhdfhfdhfdhfd', 'Poblascion, Buenavista, Bohol', '076865757467', 'Plastic', 'aDXasd', '2025-06-03', 'Pending', '2025-06-03 14:37:07', 47, 36, 'Unpaid', '2025-06-03 14:37:07', '1kg', 0),
(227, 'sdfgfdhg fdhdfhfdhfdhfd', 'Poblascion, Buenavista, Bohol', '076865757467', 'Plastic', 'aDXasd', '2025-06-03', 'Pending', '2025-06-03 14:38:37', 47, 36, 'Unpaid', '2025-06-03 14:38:37', '1kg', 0),
(228, 'sdfgfdhg fdhdfhfdhfdhfd', 'Poblascion, Buenavista, Bohol', '076865757467', 'Plastic', 'aDXasd', '2025-06-03', 'Pending', '2025-06-03 14:39:06', 47, 36, 'Unpaid', '2025-06-03 14:39:06', '1kg', 0),
(229, 'sdfgfdhg fdhdfhfdhfdhfd', 'Poblascion, Buenavista, Bohol', '076865757467', 'Plastic', 'aDXasd', '2025-06-03', 'Pending', '2025-06-03 14:39:12', 47, 36, 'Unpaid', '2025-06-03 14:39:12', '1kg', 0),
(230, 'sdfgfdhg fdhdfhfdhfdhfd', 'Poblascion, Buenavista, Bohol', '076865757467', 'Plastic', 'reteyre', '2025-06-03', 'Pending', '2025-06-03 14:39:59', 47, 44, 'Unpaid', '2025-06-03 14:39:59', '1kg', 0),
(231, 'sdfgfdhg fdhdfhfdhfdhfd', 'Poblascion, Buenavista, Bohol', '076865757467', 'Plastic', 'reteyre', '2025-06-03', 'Pending', '2025-06-03 14:40:43', 47, 44, 'Unpaid', '2025-06-03 14:40:43', '1kg', 0),
(232, 'sdfgfdhg fdhdfhfdhfdhfd', 'Poblascion, Buenavista, Bohol', '076865757467', 'Plastic', 'reteyre', '2025-06-03', 'Pending', '2025-06-03 14:40:56', 47, 44, 'Unpaid', '2025-06-03 14:40:56', '1kg', 0),
(233, 'sdfgfdhg fdhdfhfdhfdhfd', 'Poblascion, Buenavista, Bohol', '076865757467', 'Plastic', 'reteyre', '2025-06-03', 'Pending', '2025-06-03 14:41:05', 47, 44, 'Unpaid', '2025-06-03 14:41:05', '1kg', 0),
(234, 'sdfgfdhg fdhdfhfdhfdhfd', 'Poblascion, Buenavista, Bohol', '076865757467', 'Plastic', 'reteyre', '2025-06-03', 'Pending', '2025-06-03 14:41:52', 47, 44, 'Unpaid', '2025-06-03 14:41:52', '1kg', 0),
(235, 'sdfgfdhg fdhdfhfdhfdhfd', 'Poblascion, Buenavista, Bohol', '076865757467', 'Plastic', 'reteyre', '2025-06-03', 'Pending', '2025-06-03 14:45:32', 47, 44, 'Unpaid', '2025-06-03 14:45:32', '1kg', 0),
(236, 'sdfgfdhg fdhdfhfdhfdhfd', 'Poblascion, Buenavista, Bohol', '076865757467', 'Plastic', 'reteyre', '2025-06-03', 'Pending', '2025-06-03 14:45:52', 47, 44, 'Unpaid', '2025-06-03 14:45:52', '1kg', 0),
(237, 'sdfgfdhg fdhdfhfdhfdhfd', 'Poblascion, Buenavista, Bohol', '076865757467', 'Plastic', 'reteyre', '2025-06-03', 'Pending', '2025-06-03 14:49:00', 47, 44, 'Unpaid', '2025-06-03 14:49:00', '1kg', 0),
(238, 'sdfgfdhg fdhdfhfdhfdhfd', 'Poblascion, Buenavista, Bohol', '076865757467', 'Plastic', 'asdfsadf', '2025-06-03', 'Approved', '2025-06-03 15:02:00', 47, 36, 'Unpaid', '2025-06-03 15:02:00', '1kg', 0),
(239, 'sdfgfdhg fdhdfhfdhfdhfd', 'Poblascion, Buenavista, Bohol', '076865757467', 'Plastic', 'wer', '2025-06-05', 'Pending', '2025-06-03 22:41:44', 47, 10, 'Unpaid', '2025-06-03 22:41:44', '1kg', 0),
(240, 'sdfgfdhg fdhdfhfdhfdhfd', 'Poblascion, Buenavista, Bohol', '076865757467', 'Plastic', 'fgfd', '2025-06-04', 'Pending', '2025-06-03 23:58:30', 47, 36, 'Unpaid', '2025-06-03 23:58:30', '1kg', 0),
(241, '', 'tagbilaran city', '', 'Metal', '', NULL, 'Completed', '2025-06-04 01:46:57', 0, 0, '200', '2025-06-04 01:46:57', '1kl', 3),
(242, 'sdfgfdhg fdhdfhfdhfdhfd', 'Poblascion, Buenavista, Bohol', '076865757467', 'Plastic', 'dsf', '2025-06-04', 'Pending', '2025-06-04 10:51:10', 47, 36, 'Unpaid', '2025-06-04 10:51:10', '1kg', 0),
(243, 'sdfgfdhg fdhdfhfdhfdhfd', 'Poblascion, Buenavista, Bohol', '076865757467', 'Metal', 'dsfs', '2025-06-04', 'Pending', '2025-06-04 12:49:19', 47, 10, 'Unpaid', '2025-06-04 12:49:19', '1kg', 0),
(244, '223', 'CTU Main - ICT Building, Cebu City, Cebu, Philippines', '076865757467', 'Plastic', 'fdgd', '2025-06-05', 'Pending', '2025-06-05 02:47:08', 41, 44, 'Unpaid', '2025-06-05 02:47:08', '1kg', 0),
(245, '223', 'CTU Main - ICT Building, Cebu City, Cebu, Philippines', '076865757467', 'Plastic', 'sdfsdf', '2025-06-05', 'Pending', '2025-06-05 02:52:49', 41, 43, 'Unpaid', '2025-06-05 02:52:49', '1kg', 0),
(246, '223', 'CTU Main - ICT Building, Cebu City, Cebu, Philippines', '076865757467', 'Plastic', 'dsfgdfg', '2025-07-12', 'Pending', '2025-06-05 02:53:13', 41, 1, 'Unpaid', '2025-06-05 02:53:13', '1kg', 0),
(247, 'sdfgfdhg fdhdfhfdhfdhfd', 'Poblascion, Buenavista, Bohol', '076865757467', 'Plastic', 'sdgf', '2025-06-05', 'Pending', '2025-06-05 02:58:21', 47, 35, 'Unpaid', '2025-06-05 02:58:21', '1kg', 0),
(248, 'dd', 'Sweetland Basketball Court, P. Estoce Street, Poblacion, Buenavista, Bohol, Central Visayas, 6333, Philippines', '076865757467', 'Plastic', 'efsdef', '2025-06-05', 'Pending', '2025-06-05 03:23:20', 48, 36, 'Unpaid', '2025-06-05 03:23:20', '1kg', 0),
(249, 'dwqedweq', 'Bugaong, Buenavista, Bohol', '076865757467', 'Plastic', 'dfds', '2025-06-05', 'Pending', '2025-06-05 03:36:44', 49, 36, 'Unpaid', '2025-06-05 03:36:44', '1kg', 0),
(250, 'sdfsdf', 'Baluarte, Buenavista, Bohol', '09463478938', 'Plastic', 'asdfsaf', '2025-06-05', 'Completed', '2025-06-05 03:43:35', 51, 36, '300', '2025-06-05 04:09:54', '1kl', 0),
(251, 'www', 'Anonang, Buenavista, Bohol', '076865757467', 'Plastic', 'gfhgf', '2025-06-05', 'Pending', '2025-06-05 14:07:46', 52, 44, 'Unpaid', '2025-06-05 14:07:46', '1kg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `total_money`
--

CREATE TABLE `total_money` (
  `id` int(11) NOT NULL,
  `capital_money` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `collector_id` int(11) NOT NULL,
  `deduction_of_capital_money` varchar(255) NOT NULL,
  `total_money` varchar(255) NOT NULL,
  `admin_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `total_money`
--

INSERT INTO `total_money` (`id`, `capital_money`, `created_at`, `collector_id`, `deduction_of_capital_money`, `total_money`, `admin_id`) VALUES
(38, '500', '2025-06-02 13:18:38', 36, '543', '-43', 0),
(39, '500', '2025-06-02 13:19:18', 36, '3454', '-2954', 0),
(41, '500', '2025-06-05 04:09:54', 36, '300', '200', 0),
(42, '100000000', '2025-06-05 07:45:59', 0, '0', '', 3),
(43, '100000000', '2025-06-05 07:46:12', 32, '12121', '99987879', 3),
(44, '100000000', '2025-06-05 07:48:26', 35, '33463', '99966537', 3),
(45, '100000000', '2025-06-05 08:13:36', 36, '33475', '99966525', 3),
(46, '100000000', '2025-06-05 09:13:30', 32, '35817', '99964183', 3),
(47, '100000000', '2025-06-05 09:13:30', 32, '38159', '99961841', 3),
(48, '100000000', '2025-06-05 09:13:31', 32, '40501', '99959499', 3),
(49, '100000000', '2025-06-05 13:28:17', 32, '40612', '99959388', 3),
(50, '100000000', '2025-06-05 13:30:57', 32, '40825', '99959175', 3),
(51, '100000000', '2025-06-05 13:38:16', 35, '44038', '99955962', 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `number` varchar(255) NOT NULL,
  `user_type` text NOT NULL DEFAULT 'collector',
  `image` varchar(255) NOT NULL DEFAULT '1.png',
  `password` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'deactivate',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `address`, `number`, `user_type`, `image`, `password`, `status`, `created_at`) VALUES
(1, 'hellosdd888', 'qwdq', 'lo@gmail.com', 'Poblacion Buenavista Bohol', '09463478938', 'collector', 'IMG_20250325_101226_332.jpg', '698d51a19d8a121ce581499d7b701668', 'deactivate', '2025-03-26 05:31:55'),
(2, 'regtress', 'ertgre', 'loret@gmail.com', 'Poblacion Buenavista Bohol', '09463478938', 'customer', 'IMG_20250325_101226_332.jpg', '698d51a19d8a121ce581499d7b701668', 'deactivate', '2025-03-26 05:31:55'),
(3, 'junkshop', 'owner', 'admin@gmail.com', '0809976868', '09463478938', 'admin', 'GCash-092431-23032025082315.PNG.jpg', '111', 'activate', '2025-03-26 05:31:55'),
(7, 'char', 'gwapa', 'gwapa111@gmail.com', 'Lapacan Norte Buenavista Bohol', '076865757467', 'customer', '', '698d51a19d8a121ce581499d7b701668', 'activate', '2025-03-26 12:11:38'),
(9, 'levin', 'cfgdg', 'levinssssssS6111@gmail.com', 'Guindulman, Bohol', '076865757467', 'customer', '214567b57d343f38ba73be9cca40a1ef (1) (1).jpg', 'd41d8cd98f00b204e9800998ecf8427e', 'activate', '2025-03-27 15:01:37'),
(10, 'livens', 'hah', 'l11@gmail.com', 'anonang inabanga bohol', '56745', 'collector', '', '698d51a19d8a121ce581499d7b701668', 'deactivate', '2025-04-11 01:49:11'),
(11, 'makoy', 'sayson', 'makoy@gmail.com', 'tugas getafe bohol', '09933957419', 'customer', '2d1a896a-01c0-4a52-ae6f-a95a6ef57c0d.jpg', '698d51a19d8a121ce581499d7b701668', 'activate', '2025-04-11 05:15:26'),
(12, 'usertest', 'jj', 'user@gmail.com', 'tagbilran city', '09933957419', 'customer', '2d1a896a-01c0-4a52-ae6f-a95a6ef57c0d.jpg', 'd41d8cd98f00b204e9800998ecf8427e', 'activate', '2025-04-15 06:57:08'),
(13, 'tae', 'daf', 'taefhsdfhf@gmail.com', 'jb', '09933957419', 'customer', '0a7a3cfa-5597-4ca5-9350-68106b2c9b96.jpg', 'd41d8cd98f00b204e9800998ecf8427e', 'activate', '2025-04-15 07:14:09'),
(14, 'ohaha', 'Frace', 'tasdfhf@gmail.com', 'tagbilran city', '09933957419', '', '', '698d51a19d8a121ce581499d7b701668', 'activate', '2025-04-15 07:23:58'),
(32, 'aica', 'gwpa', 'dano@gmail.com', 'Guindulman, Bohol', '076865757467', 'collector', '1.png', '698d51a19d8a121ce581499d7b701668', 'deactivate', '2025-04-20 01:26:10'),
(33, 'danio', 'sdgfdsfgd', 'dano34@gmail.com', 'CTU Main - ICT Building, Cebu City, Cebu, Philippines', '076865757467', 'customer', '1.png', 'd41d8cd98f00b204e9800998ecf8427e', 'deactivate', '2025-04-20 01:32:27'),
(34, 'dfvsdfsdf', 'sdfsd', 'dano34654@gmail.com', 'CTU Main - ICT Building, Cebu City, Cebu, Philippines', '076865757467', 'customer', '0a7a3cfa-5597-4ca5-9350-68106b2c9b96.jpg', '698d51a19d8a121ce581499d7b701668', 'deactivate', '2025-04-20 01:34:35'),
(35, '0890008', '890', '111222@gmail.com', 'Guindulman, Bohol', '076865757467', 'collector', '1.png', '111', 'deactivate', '2025-04-21 01:50:30'),
(36, 'cooll', 'cooll', 'cooll@gmail.com', 'sweetland buenavista bohol', '076865757467', 'collector', 'Screenshot (14).png', '111', 'deactivate', '2025-04-23 05:12:35'),
(37, 'dfgertgertgr]]]]]]', 'dfgertgertgr]]]]]]', 'dfgertgertgr@gmail.com', 'CTU Main - ICT Building, Cebu City, Cebu, Philippines', '076865757467', 'collector', '1.png', '698d51a19d8a121ce581499d7b701668', 'deactivate', '2025-04-23 05:13:21'),
(38, 'huhay', 'huhu', 'huhu@gmail.com', 'tagbilaran city', '076865757467', 'customer', '', '698d51a19d8a121ce581499d7b701668', 'deactivate', '2025-05-09 04:51:21'),
(39, 'sf', 'gfdfgdgdg', '42332432@gmail.com', 'anonang inabanga bohol', '076865757467', 'customer', '1.png', '111', 'activate', '2025-05-09 05:40:01'),
(40, 'dsfdsf', 'sdfsfsf', 'sdfsdf@gmail.com', 'sweetland buenavista bohol', '076865757467', 'customer', '', '111', 'deactivate', '2025-05-09 07:33:30'),
(41, '223', 'fdhgfhfghfg3456434', '122324@gmail.com', 'CTU Main - ICT Building, Cebu City, Cebu, Philippines', '076865757467', 'customer', 'GCash-092431-23032025082315.PNG.jpg', '111', 'activate', '2025-05-09 07:34:23'),
(42, 'dasdsada', 'asdasdsadsad', 'admin@gmail.com', 'CTU Main - ICT Building, Cebu City, Cebu, Philippines', '076865757467', 'collector', '1.png', '698d51a19d8a121ce581499d7b701668', 'deactivate', '2025-05-09 07:35:52'),
(43, '435345', '34535353', '00000@gmail.com', 'tagbilaran city', '076865757467', 'collector', 'Screenshot 2025-03-30 190335.png', '111', 'deactivate', '2025-05-09 07:36:40'),
(44, 'sdfsdfgsd', 'asdfsdfsd', '123@gmail.com', 'sweetland buenavista bohol', '078766', 'collector', '1.png', '111', 'deactivate', '2025-05-09 11:36:52'),
(45, '363636', '363636', '363636@gmail.com', 'tugas getafe bohol', '076865757467', 'customer', '', '111', 'deactivate', '2025-05-14 11:21:11'),
(46, 'mama', 'papa', 'papa@gmail.com', 'brgy.sweetland buenavista bohol', '078766', 'customer', 'jeffrey.png', '111', 'activate', '2025-05-30 01:28:58'),
(47, 'sdfgfdhg fdhdfhfdhfdhfd', 'dfh', '1111@gmail.com', 'Poblascion, Buenavista, Bohol', '076865757467', 'customer', '', 'sdfgfdhg fdhdfhfdhfdhfd', 'deactivate', '2025-06-03 09:14:09'),
(48, 'dd', 'qawsdqaswde', '22@gmail.com', 'Sweetland Basketball Court, P. Estoce Street, Poblacion, Buenavista, Bohol, Central Visayas, 6333, Philippines', '076865757467', 'customer', '', '111', 'deactivate', '2025-06-05 03:22:42'),
(49, 'dwqedweq', 'dssqn', '22222@gmail.com', 'Bugaong, Buenavista, Bohol', '076865757467', 'customer', '', '111', 'deactivate', '2025-06-05 03:36:21'),
(50, 'dfgd', 'ftgd', '8@gmail.com', 'Poblacion, Buenavista, Bohol', '09999222333', 'customer', '', '111', 'deactivate', '2025-06-05 03:38:17'),
(51, 'sdfsdf', 'sdfsd', '56@gmail.com', 'Baluarte, Buenavista, Bohol', '09463478938', 'customer', '', '111', 'deactivate', '2025-06-05 03:43:15'),
(52, 'www', 'www', 'www@gmail.com', 'Anonang, Buenavista, Bohol', '076865757467', 'customer', '', '111', 'activate', '2025-06-05 13:55:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_notification`
--
ALTER TABLE `admin_notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `approved_collectors`
--
ALTER TABLE `approved_collectors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `borrower`
--
ALTER TABLE `borrower`
  ADD PRIMARY KEY (`borrower_id`);

--
-- Indexes for table `collector_notification`
--
ALTER TABLE `collector_notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_notification`
--
ALTER TABLE `customer_notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `documentation`
--
ALTER TABLE `documentation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `junk_price`
--
ALTER TABLE `junk_price`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan`
--
ALTER TABLE `loan`
  ADD PRIMARY KEY (`loan_id`);

--
-- Indexes for table `loan_plan`
--
ALTER TABLE `loan_plan`
  ADD PRIMARY KEY (`lplan_id`);

--
-- Indexes for table `loan_type`
--
ALTER TABLE `loan_type`
  ADD PRIMARY KEY (`ltype_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `pay_customer`
--
ALTER TABLE `pay_customer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pickup_requests`
--
ALTER TABLE `pickup_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `total_money`
--
ALTER TABLE `total_money`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_notification`
--
ALTER TABLE `admin_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=270;

--
-- AUTO_INCREMENT for table `approved_collectors`
--
ALTER TABLE `approved_collectors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `borrower`
--
ALTER TABLE `borrower`
  MODIFY `borrower_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `collector_notification`
--
ALTER TABLE `collector_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=176;

--
-- AUTO_INCREMENT for table `customer_notification`
--
ALTER TABLE `customer_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=195;

--
-- AUTO_INCREMENT for table `documentation`
--
ALTER TABLE `documentation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `junk_price`
--
ALTER TABLE `junk_price`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `loan`
--
ALTER TABLE `loan`
  MODIFY `loan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `loan_plan`
--
ALTER TABLE `loan_plan`
  MODIFY `lplan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `loan_type`
--
ALTER TABLE `loan_type`
  MODIFY `ltype_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `pay_customer`
--
ALTER TABLE `pay_customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pickup_requests`
--
ALTER TABLE `pickup_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=252;

--
-- AUTO_INCREMENT for table `total_money`
--
ALTER TABLE `total_money`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
