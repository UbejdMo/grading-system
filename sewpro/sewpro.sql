-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 12, 2025 at 09:34 PM
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
-- Database: `sewpro`
--

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL,
  `class_name` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_id`, `class_name`, `created_at`) VALUES
(5, 'Klasa 3/1', '2025-02-07 23:50:14'),
(6, 'Klasa 3/2', '2025-02-07 23:50:28'),
(7, 'Klasa 4/1', '2025-02-07 23:50:36'),
(8, 'Klasa 5/1', '2025-02-07 23:50:43');

-- --------------------------------------------------------

--
-- Table structure for table `final_grades`
--

CREATE TABLE `final_grades` (
  `grade_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `grade_date` date DEFAULT NULL,
  `semester` int(11) NOT NULL,
  `final_grade` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `final_grades`
--

INSERT INTO `final_grades` (`grade_id`, `student_id`, `subject_id`, `grade_date`, `semester`, `final_grade`) VALUES
(78, 60, 6, '2025-02-08', 0, 0.00),
(79, 61, 6, '2025-02-08', 0, 0.00),
(80, 58, 6, '2025-02-08', 0, 0.00),
(81, 57, 6, '2025-03-15', 0, 0.00),
(82, 59, 6, '2025-02-08', 0, 0.00),
(83, 58, 5, '2025-02-08', 0, 0.00),
(84, 60, 5, '2025-02-08', 0, 0.00),
(85, 61, 5, '2025-02-08', 0, 0.00),
(86, 57, 5, '2025-02-08', 0, 0.00),
(87, 59, 5, '2025-02-08', 0, 0.00),
(88, 60, 10, '2025-02-08', 0, 0.00),
(89, 58, 10, '2025-02-08', 0, 0.00),
(90, 59, 10, '2025-02-08', 0, 0.00),
(91, 61, 10, '2025-02-08', 0, 0.00),
(92, 57, 10, '2025-02-08', 0, 0.00),
(93, 59, 12, '2025-02-08', 0, 0.00),
(94, 61, 12, '2025-02-08', 0, 0.00),
(95, 57, 12, '2025-02-08', 0, 0.00),
(96, 58, 12, '2025-02-08', 0, 0.00),
(97, 60, 12, '2025-02-08', 0, 0.00),
(98, 59, 2, '2025-02-08', 0, 0.00),
(99, 61, 2, '2025-02-08', 0, 0.00),
(100, 57, 2, '2025-02-08', 0, 0.00),
(101, 58, 2, '2025-02-08', 0, 0.00),
(102, 60, 2, '2025-02-08', 0, 0.00),
(103, 58, 3, '2025-02-08', 0, 0.00),
(104, 59, 3, '2025-02-08', 0, 0.00),
(105, 61, 3, '2025-02-08', 0, 0.00),
(106, 57, 3, '2025-02-08', 0, 0.00),
(107, 60, 3, '2025-02-08', 0, 0.00),
(108, 61, 8, '2025-02-08', 0, 0.00),
(109, 58, 8, '2025-02-08', 0, 0.00),
(110, 60, 8, '2025-02-08', 0, 0.00),
(111, 57, 8, '2025-02-08', 0, 0.00),
(112, 59, 8, '2025-02-08', 0, 0.00),
(113, 57, 1, '2025-02-08', 0, 0.00),
(114, 61, 1, '2025-02-08', 0, 0.00),
(115, 59, 1, '2025-02-08', 0, 0.00),
(116, 60, 1, '2025-02-08', 0, 0.00),
(117, 58, 1, '2025-02-08', 0, 0.00),
(118, 57, 7, '2025-02-08', 0, 0.00),
(119, 58, 7, '2025-02-08', 0, 0.00),
(120, 61, 7, '2025-02-08', 0, 0.00),
(121, 59, 7, '2025-02-08', 0, 0.00),
(122, 60, 7, '2025-02-08', 0, 0.00),
(123, 58, 11, '2025-02-08', 0, 0.00),
(124, 57, 11, '2025-02-08', 0, 0.00),
(125, 59, 11, '2025-02-08', 0, 0.00),
(126, 60, 11, '2025-02-08', 0, 0.00),
(127, 61, 11, '2025-02-08', 0, 0.00),
(128, 57, 4, '2025-02-08', 0, 0.00),
(129, 59, 4, '2025-02-08', 0, 0.00),
(130, 60, 4, '2025-02-08', 0, 0.00),
(131, 58, 4, '2025-02-08', 0, 0.00),
(132, 61, 4, '2025-02-08', 0, 0.00),
(133, 59, 9, '2025-02-08', 0, 0.00),
(134, 60, 9, '2025-02-08', 0, 0.00),
(135, 58, 9, '2025-02-08', 0, 0.00),
(136, 57, 9, '2025-02-08', 0, 0.00),
(137, 61, 9, '2025-02-08', 0, 0.00),
(138, 62, 6, '2025-02-08', 0, 0.00),
(139, 64, 6, '2025-02-08', 0, 0.00),
(140, 65, 6, '2025-02-08', 0, 0.00),
(141, 63, 6, '2025-02-08', 0, 0.00),
(142, 65, 5, '2025-02-08', 0, 0.00),
(143, 62, 5, '2025-02-08', 0, 0.00),
(144, 63, 5, '2025-02-08', 0, 0.00),
(145, 64, 5, '2025-02-08', 0, 0.00),
(146, 62, 10, '2025-02-08', 0, 0.00),
(147, 63, 10, '2025-02-08', 0, 0.00),
(148, 64, 10, '2025-02-08', 0, 0.00),
(149, 65, 10, '2025-02-08', 0, 0.00),
(150, 63, 12, '2025-02-08', 0, 0.00),
(151, 64, 12, '2025-02-08', 0, 0.00),
(152, 65, 12, '2025-02-08', 0, 0.00),
(153, 62, 12, '2025-02-08', 0, 0.00),
(154, 63, 2, '2025-02-08', 0, 0.00),
(155, 64, 2, '2025-02-08', 0, 0.00),
(156, 65, 2, '2025-02-08', 0, 0.00),
(157, 62, 2, '2025-02-08', 0, 0.00),
(158, 62, 3, '2025-02-08', 0, 0.00),
(159, 63, 3, '2025-02-08', 0, 0.00),
(160, 64, 3, '2025-02-08', 0, 0.00),
(161, 65, 3, '2025-02-08', 0, 0.00),
(162, 62, 8, '2025-02-08', 0, 0.00),
(163, 63, 8, '2025-02-08', 0, 0.00),
(164, 64, 8, '2025-02-08', 0, 0.00),
(165, 65, 8, '2025-02-08', 0, 0.00),
(166, 62, 1, '2025-02-08', 0, 0.00),
(167, 63, 1, '2025-02-08', 0, 0.00),
(168, 64, 1, '2025-02-08', 0, 0.00),
(169, 65, 1, '2025-02-08', 0, 0.00),
(170, 62, 7, '2025-02-08', 0, 0.00),
(171, 65, 7, '2025-02-08', 0, 0.00),
(172, 64, 7, '2025-02-08', 0, 0.00),
(173, 63, 7, '2025-02-08', 0, 0.00),
(174, 62, 11, '2025-02-08', 0, 0.00),
(175, 65, 11, '2025-02-08', 0, 0.00),
(176, 63, 11, '2025-02-08', 0, 0.00),
(177, 64, 11, '2025-02-08', 0, 0.00),
(178, 65, 4, '2025-02-08', 0, 0.00),
(179, 63, 4, '2025-02-08', 0, 0.00),
(180, 64, 4, '2025-02-08', 0, 0.00),
(181, 62, 4, '2025-02-08', 0, 0.00),
(182, 65, 9, '2025-02-08', 0, 0.00),
(183, 63, 9, '2025-02-08', 0, 0.00),
(184, 64, 9, '2025-02-08', 0, 0.00),
(185, 62, 9, '2025-02-08', 0, 0.00),
(186, 68, 6, '2025-02-08', 0, 0.00),
(187, 66, 6, '2025-02-08', 0, 0.00),
(188, 67, 6, '2025-02-08', 0, 0.00),
(189, 69, 6, '2025-02-08', 0, 0.00),
(190, 66, 5, '2025-02-08', 0, 0.00),
(191, 68, 5, '2025-02-08', 0, 0.00),
(192, 69, 5, '2025-02-08', 0, 0.00),
(193, 67, 5, '2025-02-08', 0, 0.00),
(194, 66, 10, '2025-02-08', 0, 0.00),
(195, 68, 10, '2025-02-08', 0, 0.00),
(196, 67, 10, '2025-02-08', 0, 0.00),
(197, 69, 10, '2025-02-08', 0, 0.00),
(198, 66, 12, '2025-02-08', 0, 0.00),
(199, 68, 12, '2025-02-08', 0, 0.00),
(200, 67, 12, '2025-02-08', 0, 0.00),
(201, 69, 12, '2025-02-08', 0, 0.00),
(202, 66, 2, '2025-02-08', 0, 0.00),
(203, 68, 2, '2025-02-08', 0, 0.00),
(204, 67, 2, '2025-02-08', 0, 0.00),
(205, 69, 2, '2025-02-08', 0, 0.00),
(206, 66, 3, '2025-02-08', 0, 0.00),
(207, 68, 3, '2025-02-08', 0, 0.00),
(208, 67, 3, '2025-02-08', 0, 0.00),
(209, 69, 3, '2025-02-08', 0, 0.00),
(210, 66, 8, '2025-02-08', 0, 0.00),
(211, 68, 8, '2025-02-08', 0, 0.00),
(212, 69, 8, '2025-02-08', 0, 0.00),
(213, 67, 8, '2025-02-08', 0, 0.00),
(214, 68, 1, '2025-02-08', 0, 0.00),
(215, 67, 1, '2025-02-08', 0, 0.00),
(216, 69, 1, '2025-02-08', 0, 0.00),
(217, 68, 7, '2025-02-08', 0, 0.00),
(218, 67, 7, '2025-02-08', 0, 0.00),
(219, 69, 7, '2025-02-08', 0, 0.00),
(220, 66, 7, '2025-02-08', 0, 0.00),
(221, 66, 11, '2025-02-08', 0, 0.00),
(222, 68, 11, '2025-02-08', 0, 0.00),
(223, 67, 11, '2025-02-08', 0, 0.00),
(224, 69, 11, '2025-02-08', 0, 0.00),
(225, 66, 4, '2025-02-08', 0, 0.00),
(226, 67, 4, '2025-02-08', 0, 0.00),
(227, 68, 4, '2025-02-08', 0, 0.00),
(228, 69, 4, '2025-02-08', 0, 0.00),
(229, 66, 9, '2025-02-08', 0, 0.00),
(230, 67, 9, '2025-02-08', 0, 0.00),
(231, 68, 9, '2025-02-08', 0, 0.00),
(232, 69, 9, '2025-02-08', 0, 0.00),
(233, 71, 6, '2025-02-08', 0, 0.00),
(234, 70, 6, '2025-02-08', 0, 0.00),
(236, 73, 6, '2025-02-08', 0, 0.00),
(237, 70, 5, '2025-02-08', 0, 0.00),
(238, 71, 5, '2025-02-08', 0, 0.00),
(239, 73, 5, '2025-02-08', 0, 0.00),
(241, 71, 10, '2025-02-08', 0, 0.00),
(242, 73, 10, '2025-02-08', 0, 0.00),
(244, 70, 10, '2025-02-08', 0, 0.00),
(245, 71, 12, '2025-02-08', 0, 0.00),
(247, 73, 12, '2025-02-08', 0, 0.00),
(248, 70, 12, '2025-02-08', 0, 0.00),
(249, 71, 2, '2025-02-08', 0, 0.00),
(251, 73, 2, '2025-02-08', 0, 0.00),
(252, 70, 2, '2025-02-08', 0, 0.00),
(253, 71, 3, '2025-02-08', 0, 0.00),
(255, 73, 3, '2025-02-08', 0, 0.00),
(256, 70, 3, '2025-02-08', 0, 0.00),
(257, 71, 8, '2025-02-08', 0, 0.00),
(259, 73, 8, '2025-02-08', 0, 0.00),
(260, 70, 8, '2025-02-08', 0, 0.00),
(261, 71, 1, '2025-02-08', 0, 0.00),
(262, 73, 1, '2025-02-08', 0, 0.00),
(264, 70, 1, '2025-02-08', 0, 0.00),
(265, 71, 7, '2025-02-08', 0, 0.00),
(266, 73, 7, '2025-02-08', 0, 0.00),
(267, 70, 7, '2025-02-08', 0, 0.00),
(269, 73, 11, '2025-02-08', 0, 0.00),
(271, 70, 11, '2025-02-08', 0, 0.00),
(272, 71, 11, '2025-02-08', 0, 0.00),
(273, 73, 4, '2025-02-08', 0, 0.00),
(275, 70, 4, '2025-02-08', 0, 0.00),
(276, 71, 4, '2025-02-08', 0, 0.00),
(277, 73, 9, '2025-02-08', 0, 0.00),
(278, 70, 9, '2025-02-08', 0, 0.00),
(280, 71, 9, '2025-02-08', 0, 0.00),
(283, 83, 6, '2025-02-08', 0, 0.00),
(284, 83, 5, '2025-02-08', 0, 0.00),
(285, 83, 10, '2025-02-08', 0, 0.00),
(286, 83, 12, '2025-02-08', 0, 0.00),
(287, 83, 2, '2025-02-08', 0, 0.00),
(288, 83, 3, '2025-02-08', 0, 0.00),
(289, 83, 8, '2025-02-08', 0, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `monthly_grades`
--

CREATE TABLE `monthly_grades` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `average_grade` float NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parent_student`
--

CREATE TABLE `parent_student` (
  `parent_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parent_student`
--

INSERT INTO `parent_student` (`parent_id`, `student_id`) VALUES
(74, 58),
(74, 65),
(75, 59),
(75, 60),
(76, 57),
(76, 61),
(77, 63),
(78, 66),
(81, 64),
(81, 68),
(81, 73);

-- --------------------------------------------------------

--
-- Table structure for table `remember_tokens`
--

CREATE TABLE `remember_tokens` (
  `token_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expiration` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `remember_tokens`
--

INSERT INTO `remember_tokens` (`token_id`, `user_id`, `token`, `expiration`) VALUES
(34, 1, 'd8d73c67609294b41872b7d040408160', '2025-01-11 23:07:05'),
(35, 1, '099a0e89b16d9ac5f1ebc9f4c0a606cf', '2025-01-11 23:07:07'),
(36, 1, '170aeb649218d0af8b1a958efcf046e4', '2025-01-11 23:09:08'),
(37, 1, '86673c2bb3ce91edf5d8748e891d7321', '2025-01-11 23:10:40'),
(38, 1, 'ade3ac3675af6427b2946184398826e3', '2025-01-11 23:10:43'),
(39, 1, '434574307b745fecbbc984cc40e72320', '2025-01-11 23:10:45'),
(40, 1, '54ec061de0811835ae03c7cfbf78d9ad', '2025-01-11 23:11:41'),
(41, 1, '931a4a2b8e68738ea82ad3b1e196a1e5', '2025-01-11 23:12:41'),
(42, 1, '71e71ca6deff598d3ec5a4f4f9ebf708', '2025-01-11 23:12:44'),
(43, 1, '3ff38e442e055cd6b59167af78047656', '2025-01-11 23:12:50'),
(44, 1, '825ee88120e29ec9bcb4d467fc69b4b2', '2025-01-11 23:13:47'),
(45, 1, '06e375d00bb4a0e2ded23e310c4e0b18', '2025-01-11 23:13:52'),
(80, 1, '7fa648f4767aa9983d8533b07b45e4d8', '2025-01-30 21:45:32'),
(110, 1, '347114a28547dba8af4a73425251507b', '2025-02-25 00:00:34'),
(112, 1, '313665032eda0062cdbe6d8d8eec7e18', '2025-02-25 00:00:37'),
(113, 1, '150f3f197ae261ba4a71a5dab1fbc1cb', '2025-02-25 00:00:40'),
(114, 1, '74e197cbc820a5c01dbff4f6da8828a9', '2025-02-25 00:00:52'),
(115, 1, 'fd8867cc694929ebfd22cf58db14d33c', '2025-02-25 00:00:53'),
(116, 1, '598a8f8b600e308024cd1d7b23ed670a', '2025-02-25 00:00:54');

-- --------------------------------------------------------

--
-- Table structure for table `self_grades`
--

CREATE TABLE `self_grades` (
  `grade_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `grade_date` date NOT NULL,
  `grade` enum('A','B','C','D','E') NOT NULL,
  `month` int(11) DEFAULT 1,
  `year` int(11) DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `self_grades`
--

INSERT INTO `self_grades` (`grade_id`, `student_id`, `subject_id`, `grade_date`, `grade`, `month`, `year`, `is_approved`) VALUES
(502, 58, 6, '2025-02-08', 'B', 1, NULL, 1),
(503, 58, 5, '2025-02-08', 'A', 1, NULL, 1),
(504, 58, 10, '2025-02-08', 'B', 1, NULL, 1),
(505, 58, 12, '2025-02-08', 'C', 1, NULL, 1),
(506, 58, 2, '2025-02-08', 'D', 1, NULL, 1),
(507, 58, 3, '2025-02-08', 'A', 1, NULL, 1),
(508, 58, 8, '2025-02-08', 'B', 1, NULL, 1),
(509, 58, 1, '2025-02-08', 'E', 1, NULL, 1),
(510, 58, 7, '2025-02-08', 'A', 1, NULL, 1),
(511, 58, 11, '2025-02-08', 'A', 1, NULL, 1),
(512, 58, 4, '2025-02-08', 'B', 1, NULL, 1),
(513, 58, 9, '2025-02-08', 'B', 1, NULL, 1),
(514, 57, 6, '2025-02-08', 'C', 2, NULL, 1),
(515, 57, 5, '2025-02-08', 'B', 1, NULL, 1),
(516, 57, 10, '2025-02-08', 'D', 1, NULL, 1),
(517, 57, 12, '2025-02-08', 'C', 1, NULL, 1),
(518, 57, 2, '2025-02-08', 'D', 1, NULL, 1),
(519, 57, 3, '2025-02-08', 'B', 1, NULL, 1),
(520, 57, 8, '2025-02-08', 'C', 1, NULL, 1),
(521, 57, 1, '2025-02-08', 'A', 1, NULL, 1),
(522, 57, 7, '2025-02-08', 'A', 1, NULL, 1),
(523, 57, 11, '2025-02-08', 'B', 1, NULL, 1),
(524, 57, 4, '2025-02-08', 'A', 1, NULL, 1),
(525, 57, 9, '2025-02-08', 'C', 1, NULL, 1),
(526, 59, 6, '2025-02-08', 'D', 1, NULL, 1),
(527, 59, 5, '2025-02-08', 'E', 1, NULL, 1),
(528, 59, 10, '2025-02-08', 'B', 1, NULL, 1),
(529, 59, 12, '2025-02-08', 'B', 1, NULL, 1),
(530, 59, 2, '2025-02-08', 'A', 1, NULL, 1),
(531, 59, 3, '2025-02-08', 'A', 1, NULL, 1),
(532, 59, 8, '2025-02-08', 'C', 1, NULL, 1),
(533, 59, 1, '2025-02-08', 'B', 1, NULL, 1),
(534, 59, 7, '2025-02-08', 'D', 1, NULL, 1),
(535, 59, 11, '2025-02-08', 'B', 1, NULL, 1),
(536, 59, 4, '2025-02-08', 'A', 1, NULL, 1),
(537, 59, 9, '2025-02-08', 'A', 1, NULL, 1),
(538, 60, 6, '2025-02-08', 'A', 1, NULL, 1),
(539, 60, 5, '2025-02-08', 'A', 1, NULL, 1),
(540, 60, 10, '2025-02-08', 'A', 1, NULL, 1),
(541, 60, 12, '2025-02-08', 'E', 1, NULL, 1),
(542, 60, 2, '2025-02-08', 'E', 1, NULL, 1),
(543, 60, 3, '2025-02-08', 'D', 1, NULL, 1),
(544, 60, 8, '2025-02-08', 'B', 1, NULL, 1),
(545, 60, 1, '2025-02-08', 'C', 1, NULL, 1),
(546, 60, 7, '2025-02-08', 'E', 1, NULL, 1),
(547, 60, 11, '2025-02-08', 'B', 1, NULL, 1),
(548, 60, 4, '2025-02-08', 'A', 1, NULL, 1),
(549, 60, 9, '2025-02-08', 'A', 1, NULL, 1),
(550, 61, 6, '2025-02-08', 'A', 1, NULL, 1),
(551, 61, 5, '2025-02-08', 'A', 1, NULL, 1),
(552, 61, 10, '2025-02-08', 'B', 1, NULL, 1),
(553, 61, 12, '2025-02-08', 'B', 1, NULL, 1),
(554, 61, 2, '2025-02-08', 'B', 1, NULL, 1),
(555, 61, 3, '2025-02-08', 'A', 1, NULL, 1),
(556, 61, 8, '2025-02-08', 'A', 1, NULL, 1),
(557, 61, 1, '2025-02-08', 'A', 1, NULL, 1),
(558, 61, 7, '2025-02-08', 'C', 1, NULL, 1),
(559, 61, 11, '2025-02-08', 'D', 1, NULL, 1),
(560, 61, 4, '2025-02-08', 'C', 1, NULL, 1),
(561, 61, 9, '2025-02-08', 'E', 1, NULL, 1),
(562, 62, 6, '2025-02-08', 'B', 1, NULL, 1),
(563, 62, 5, '2025-02-08', 'B', 1, NULL, 1),
(564, 62, 10, '2025-02-08', 'B', 1, NULL, 1),
(565, 62, 12, '2025-02-08', 'B', 1, NULL, 1),
(566, 62, 2, '2025-02-08', 'B', 1, NULL, 1),
(567, 62, 3, '2025-02-08', 'A', 1, NULL, 1),
(568, 62, 8, '2025-02-08', 'A', 1, NULL, 1),
(569, 62, 1, '2025-02-08', 'A', 1, NULL, 1),
(570, 62, 7, '2025-02-08', 'A', 1, NULL, 1),
(571, 62, 11, '2025-02-08', 'A', 1, NULL, 1),
(572, 62, 4, '2025-02-08', 'C', 1, NULL, 1),
(573, 62, 9, '2025-02-08', 'C', 1, NULL, 1),
(574, 63, 6, '2025-02-08', 'C', 1, NULL, 1),
(575, 63, 5, '2025-02-08', 'B', 1, NULL, 1),
(576, 63, 10, '2025-02-08', 'B', 1, NULL, 1),
(577, 63, 12, '2025-02-08', 'A', 1, NULL, 1),
(578, 63, 2, '2025-02-08', 'A', 1, NULL, 1),
(579, 63, 3, '2025-02-08', 'A', 1, NULL, 1),
(580, 63, 8, '2025-02-08', 'A', 1, NULL, 1),
(581, 63, 1, '2025-02-08', 'A', 1, NULL, 1),
(582, 63, 7, '2025-02-08', 'D', 1, NULL, 1),
(583, 63, 11, '2025-02-08', 'B', 1, NULL, 1),
(584, 63, 4, '2025-02-08', 'B', 1, NULL, 1),
(585, 63, 9, '2025-02-08', 'B', 1, NULL, 1),
(586, 64, 6, '2025-02-08', 'B', 1, NULL, 1),
(587, 64, 5, '2025-02-08', 'B', 1, NULL, 1),
(588, 64, 10, '2025-02-08', 'C', 1, NULL, 1),
(589, 64, 12, '2025-02-08', 'A', 1, NULL, 1),
(590, 64, 2, '2025-02-08', 'A', 1, NULL, 1),
(591, 64, 3, '2025-02-08', 'A', 1, NULL, 1),
(592, 64, 8, '2025-02-08', 'A', 1, NULL, 1),
(593, 64, 1, '2025-02-08', 'A', 1, NULL, 1),
(594, 64, 7, '2025-02-08', 'B', 1, NULL, 1),
(595, 64, 11, '2025-02-08', 'C', 1, NULL, 1),
(596, 64, 4, '2025-02-08', 'B', 1, NULL, 1),
(597, 64, 9, '2025-02-08', 'B', 1, NULL, 1),
(598, 65, 6, '2025-02-08', 'B', 1, NULL, 1),
(599, 65, 5, '2025-02-08', 'A', 1, NULL, 1),
(600, 65, 10, '2025-02-08', 'C', 1, NULL, 1),
(601, 65, 12, '2025-02-08', 'A', 1, NULL, 1),
(602, 65, 2, '2025-02-08', 'A', 1, NULL, 1),
(603, 65, 3, '2025-02-08', 'A', 1, NULL, 1),
(604, 65, 8, '2025-02-08', 'A', 1, NULL, 1),
(605, 65, 1, '2025-02-08', 'A', 1, NULL, 1),
(606, 65, 7, '2025-02-08', 'A', 1, NULL, 1),
(607, 65, 11, '2025-02-08', 'A', 1, NULL, 1),
(608, 65, 4, '2025-02-08', 'A', 1, NULL, 1),
(609, 65, 9, '2025-02-08', 'A', 1, NULL, 1),
(610, 66, 6, '2025-02-08', 'C', 1, NULL, 1),
(611, 66, 5, '2025-02-08', 'A', 1, NULL, 1),
(612, 66, 10, '2025-02-08', 'A', 1, NULL, 1),
(613, 66, 12, '2025-02-08', 'A', 1, NULL, 1),
(614, 66, 2, '2025-02-08', 'A', 1, NULL, 1),
(615, 66, 3, '2025-02-08', 'A', 1, NULL, 1),
(616, 66, 8, '2025-02-08', 'A', 1, NULL, 1),
(617, 66, 7, '2025-02-08', 'D', 1, NULL, 1),
(618, 66, 11, '2025-02-08', 'A', 1, NULL, 1),
(619, 66, 4, '2025-02-08', 'A', 1, NULL, 1),
(620, 66, 9, '2025-02-08', 'A', 1, NULL, 1),
(621, 67, 6, '2025-02-08', 'C', 1, NULL, 1),
(622, 67, 5, '2025-02-08', 'C', 1, NULL, 1),
(623, 67, 10, '2025-02-08', 'C', 1, NULL, 1),
(624, 67, 12, '2025-02-08', 'B', 1, NULL, 1),
(625, 67, 2, '2025-02-08', 'B', 1, NULL, 1),
(626, 67, 3, '2025-02-08', 'B', 1, NULL, 1),
(627, 67, 8, '2025-02-08', 'B', 1, NULL, 1),
(628, 67, 1, '2025-02-08', 'B', 1, NULL, 1),
(629, 67, 7, '2025-02-08', 'B', 1, NULL, 1),
(630, 67, 11, '2025-02-08', 'B', 1, NULL, 1),
(631, 67, 4, '2025-02-08', 'A', 1, NULL, 1),
(632, 67, 9, '2025-02-08', 'A', 1, NULL, 1),
(633, 68, 6, '2025-02-08', 'A', 1, NULL, 1),
(634, 68, 5, '2025-02-08', 'A', 1, NULL, 1),
(635, 68, 10, '2025-02-08', 'A', 1, NULL, 1),
(636, 68, 12, '2025-02-08', 'A', 1, NULL, 1),
(637, 68, 2, '2025-02-08', 'A', 1, NULL, 1),
(638, 68, 3, '2025-02-08', 'A', 1, NULL, 1),
(639, 68, 8, '2025-02-08', 'A', 1, NULL, 1),
(640, 68, 1, '2025-02-08', 'A', 1, NULL, 1),
(641, 68, 7, '2025-02-08', 'A', 1, NULL, 1),
(642, 68, 11, '2025-02-08', 'A', 1, NULL, 1),
(643, 68, 4, '2025-02-08', 'A', 1, NULL, 1),
(644, 68, 9, '2025-02-08', 'A', 1, NULL, 1),
(645, 69, 6, '2025-02-08', 'C', 1, NULL, 1),
(646, 69, 5, '2025-02-08', 'B', 1, NULL, 1),
(647, 69, 10, '2025-02-08', 'D', 1, NULL, 1),
(648, 69, 12, '2025-02-08', 'E', 1, NULL, 1),
(649, 69, 2, '2025-02-08', 'B', 1, NULL, 1),
(650, 69, 3, '2025-02-08', 'C', 1, NULL, 1),
(651, 69, 8, '2025-02-08', 'A', 1, NULL, 1),
(652, 69, 1, '2025-02-08', 'B', 1, NULL, 1),
(653, 69, 7, '2025-02-08', 'C', 1, NULL, 1),
(654, 69, 11, '2025-02-08', 'D', 1, NULL, 1),
(655, 69, 4, '2025-02-08', 'C', 1, NULL, 1),
(656, 69, 9, '2025-02-08', 'A', 1, NULL, 1),
(657, 70, 6, '2025-02-08', 'C', 1, NULL, 1),
(658, 70, 5, '2025-02-08', 'B', 1, NULL, 1),
(659, 70, 10, '2025-02-08', 'E', 1, NULL, 1),
(660, 70, 12, '2025-02-08', 'E', 1, NULL, 1),
(661, 70, 2, '2025-02-08', 'E', 1, NULL, 1),
(662, 70, 3, '2025-02-08', 'D', 1, NULL, 1),
(663, 70, 8, '2025-02-08', 'C', 1, NULL, 1),
(664, 70, 1, '2025-02-08', 'C', 1, NULL, 1),
(665, 70, 7, '2025-02-08', 'C', 1, NULL, 1),
(666, 70, 11, '2025-02-08', 'C', 1, NULL, 1),
(667, 70, 4, '2025-02-08', 'C', 1, NULL, 1),
(668, 70, 9, '2025-02-08', 'B', 1, NULL, 1),
(669, 71, 6, '2025-02-08', 'B', 1, NULL, 1),
(670, 71, 5, '2025-02-08', 'B', 1, NULL, 1),
(671, 71, 10, '2025-02-08', 'B', 1, NULL, 1),
(672, 71, 12, '2025-02-08', 'B', 1, NULL, 1),
(673, 71, 2, '2025-02-08', 'A', 1, NULL, 1),
(674, 71, 3, '2025-02-08', 'A', 1, NULL, 1),
(675, 71, 8, '2025-02-08', 'A', 1, NULL, 1),
(676, 71, 1, '2025-02-08', 'A', 1, NULL, 1),
(677, 71, 7, '2025-02-08', 'A', 1, NULL, 1),
(678, 71, 11, '2025-02-08', 'C', 1, NULL, 1),
(679, 71, 4, '2025-02-08', 'C', 1, NULL, 1),
(680, 71, 9, '2025-02-08', 'C', 1, NULL, 1),
(693, 73, 6, '2025-02-08', 'D', 1, NULL, 1),
(694, 73, 5, '2025-02-08', 'C', 1, NULL, 1),
(695, 73, 10, '2025-02-08', 'B', 1, NULL, 1),
(696, 73, 12, '2025-02-08', 'B', 1, NULL, 1),
(697, 73, 2, '2025-02-08', 'A', 1, NULL, 1),
(698, 73, 3, '2025-02-08', 'A', 1, NULL, 1),
(699, 73, 8, '2025-02-08', 'A', 1, NULL, 1),
(700, 73, 1, '2025-02-08', 'A', 1, NULL, 1),
(701, 73, 7, '2025-02-08', 'A', 1, NULL, 1),
(702, 73, 11, '2025-02-08', 'A', 1, NULL, 1),
(703, 73, 4, '2025-02-08', 'A', 1, NULL, 1),
(704, 73, 9, '2025-02-08', 'A', 1, NULL, 1),
(705, 57, 1, '2025-02-08', 'A', 2, 0, 1),
(706, 57, 2, '2025-02-08', 'B', 2, 0, 1),
(707, 57, 3, '2025-02-08', 'A', 2, 0, 1),
(708, 57, 4, '2025-02-08', 'C', 2, 0, 1),
(709, 57, 5, '2025-02-08', 'B', 2, 0, 1),
(710, 57, 6, '2025-02-08', 'A', 3, 0, 1),
(711, 57, 7, '2025-02-08', 'D', 2, 0, 1),
(712, 57, 8, '2025-02-08', 'E', 2, 0, 1),
(713, 57, 9, '2025-02-08', 'A', 2, 0, 1),
(714, 57, 10, '2025-02-08', 'A', 2, 0, 1),
(715, 57, 11, '2025-02-08', 'B', 2, 0, 1),
(716, 57, 12, '2025-02-08', 'C', 2, 0, 1),
(717, 57, 1, '2025-02-08', 'A', 3, 0, 1),
(718, 57, 2, '2025-02-08', 'A', 3, 0, 1),
(719, 57, 3, '2025-02-08', 'C', 3, 0, 1),
(720, 57, 4, '2025-02-08', 'D', 3, 0, 1),
(721, 57, 5, '2025-02-08', 'A', 3, 0, 1),
(722, 57, 6, '2025-02-08', 'E', 4, 0, 1),
(723, 57, 7, '2025-02-08', 'A', 3, 0, 1),
(724, 57, 8, '2025-02-08', 'A', 3, 0, 1),
(725, 57, 9, '2025-02-08', 'B', 3, 0, 1),
(726, 57, 10, '2025-02-08', 'A', 3, 0, 1),
(727, 57, 11, '2025-02-08', 'A', 3, 0, 1),
(728, 57, 12, '2025-02-08', 'B', 3, 0, 1),
(729, 57, 1, '2025-02-08', 'A', 4, 0, 1),
(730, 57, 2, '2025-02-08', 'C', 4, 0, 1),
(731, 57, 3, '2025-02-08', 'A', 4, 0, 1),
(732, 57, 4, '2025-02-08', 'A', 4, 0, 1),
(733, 57, 5, '2025-02-08', 'E', 4, 0, 1),
(734, 57, 6, '2025-02-08', 'D', 5, 0, 1),
(735, 57, 7, '2025-02-08', 'B', 4, 0, 1),
(736, 57, 8, '2025-02-08', 'A', 4, 0, 1),
(737, 57, 9, '2025-02-08', 'A', 4, 0, 1),
(738, 57, 10, '2025-02-08', 'C', 4, 0, 1),
(739, 57, 11, '2025-02-08', 'A', 4, 0, 1),
(740, 57, 12, '2025-02-08', 'B', 4, 0, 1),
(741, 57, 1, '2025-02-08', 'A', 5, 0, 1),
(742, 57, 2, '2025-02-08', 'B', 5, 0, 1),
(743, 57, 3, '2025-02-08', 'E', 5, 0, 1),
(744, 57, 4, '2025-02-08', 'B', 5, 0, 1),
(745, 57, 5, '2025-02-08', 'C', 5, 0, 1),
(746, 57, 6, '2025-02-08', 'A', 6, 0, 1),
(747, 57, 7, '2025-02-08', 'E', 5, 0, 1),
(748, 57, 8, '2025-02-08', 'C', 5, 0, 1),
(749, 57, 9, '2025-02-08', 'E', 5, 0, 1),
(750, 57, 10, '2025-02-08', 'B', 5, 0, 1),
(751, 57, 11, '2025-02-08', 'C', 5, 0, 1),
(752, 57, 12, '2025-02-08', 'A', 5, 0, 1),
(753, 57, 1, '2025-02-08', 'B', 6, 0, 1),
(754, 57, 2, '2025-02-08', 'A', 6, 0, 1),
(755, 57, 3, '2025-02-08', 'D', 6, 0, 1),
(756, 57, 4, '2025-02-08', 'D', 6, 0, 1),
(757, 57, 5, '2025-02-08', 'C', 6, 0, 1),
(758, 57, 6, '2025-02-08', 'A', 7, 0, 1),
(759, 57, 7, '2025-02-08', 'B', 6, 0, 1),
(760, 57, 8, '2025-02-08', 'B', 6, 0, 1),
(761, 57, 9, '2025-02-08', 'B', 6, 0, 1),
(762, 57, 10, '2025-02-08', 'D', 6, 0, 1),
(763, 57, 11, '2025-02-08', 'D', 6, 0, 1),
(764, 57, 12, '2025-02-08', 'E', 6, 0, 1),
(765, 57, 1, '2025-02-08', 'A', 7, 0, 1),
(766, 57, 2, '2025-02-08', 'A', 7, 0, 1),
(767, 57, 3, '2025-02-08', 'A', 7, 0, 1),
(768, 57, 4, '2025-02-08', 'B', 7, 0, 1),
(769, 57, 5, '2025-02-08', 'A', 7, 0, 1),
(770, 57, 6, '2025-02-08', 'C', 8, 0, 1),
(771, 57, 7, '2025-02-08', 'D', 7, 0, 1),
(772, 57, 8, '2025-02-08', 'B', 7, 0, 1),
(773, 57, 9, '2025-02-08', 'E', 7, 0, 1),
(774, 57, 10, '2025-02-08', 'E', 7, 0, 1),
(775, 57, 11, '2025-02-08', 'B', 7, 0, 1),
(776, 57, 12, '2025-02-08', 'D', 7, 0, 1),
(777, 57, 1, '2025-02-08', 'A', 8, 0, 1),
(778, 57, 2, '2025-02-08', 'C', 8, 0, 1),
(779, 57, 3, '2025-02-08', 'E', 8, 0, 1),
(780, 57, 4, '2025-02-08', 'A', 8, 0, 1),
(781, 57, 5, '2025-02-08', 'C', 8, 0, 1),
(782, 57, 6, '2025-02-08', 'D', 9, 0, 1),
(783, 57, 7, '2025-02-08', 'A', 8, 0, 1),
(784, 57, 8, '2025-02-08', 'D', 8, 0, 1),
(785, 57, 9, '2025-02-08', 'C', 8, 0, 1),
(786, 57, 10, '2025-02-08', 'A', 8, 0, 1),
(787, 57, 11, '2025-02-08', 'A', 8, 0, 1),
(788, 57, 12, '2025-02-08', 'A', 8, 0, 1),
(789, 57, 1, '2025-02-08', 'B', 9, 0, 1),
(790, 57, 2, '2025-02-08', 'E', 9, 0, 1),
(791, 57, 3, '2025-02-08', 'D', 9, 0, 1),
(792, 57, 4, '2025-02-08', 'E', 9, 0, 1),
(793, 57, 5, '2025-02-08', 'E', 9, 0, 1),
(794, 57, 6, '2025-02-08', 'D', 10, 0, 1),
(795, 57, 7, '2025-02-08', 'D', 9, 0, 1),
(796, 57, 8, '2025-02-08', 'C', 9, 0, 1),
(797, 57, 9, '2025-02-08', 'E', 9, 0, 1),
(798, 57, 10, '2025-02-08', 'D', 9, 0, 1),
(799, 57, 11, '2025-02-08', 'B', 9, 0, 1),
(800, 57, 12, '2025-02-08', 'A', 9, 0, 1),
(801, 57, 1, '2025-02-08', 'C', 10, 0, 1),
(802, 57, 2, '2025-02-08', 'B', 10, 0, 1),
(803, 57, 3, '2025-02-08', 'D', 10, 0, 1),
(804, 57, 4, '2025-02-08', 'B', 10, 0, 1),
(805, 57, 5, '2025-02-08', 'B', 10, 0, 1),
(806, 57, 6, '2025-02-08', 'B', 11, 0, 1),
(807, 57, 7, '2025-02-08', 'C', 10, 0, 1),
(808, 57, 8, '2025-02-08', 'C', 10, 0, 1),
(809, 57, 9, '2025-02-08', 'C', 10, 0, 1),
(810, 57, 10, '2025-02-08', 'C', 10, 0, 1),
(811, 57, 11, '2025-02-08', 'C', 10, 0, 1),
(812, 57, 12, '2025-02-08', 'D', 10, 0, 1),
(813, 57, 6, '2025-02-08', 'A', 1, 0, 1),
(814, 83, 6, '2025-02-08', 'A', 1, NULL, 1),
(815, 83, 5, '2025-02-08', 'B', 1, NULL, 1),
(816, 83, 10, '2025-02-08', 'A', 1, NULL, 1),
(817, 83, 12, '2025-02-08', 'A', 1, NULL, 1),
(818, 83, 2, '2025-02-08', 'A', 1, NULL, 1),
(819, 83, 3, '2025-02-08', 'A', 1, NULL, 1),
(820, 83, 8, '2025-02-08', 'A', 1, NULL, 1);

--
-- Triggers `self_grades`
--
DELIMITER $$
CREATE TRIGGER `before_insert_self_grades` BEFORE INSERT ON `self_grades` FOR EACH ROW BEGIN
    DECLARE last_month INT;

    -- Get the last month that was used for the student's subject
    SELECT MAX(month) INTO last_month
    FROM self_grades
    WHERE student_id = NEW.student_id
      AND subject_id = NEW.subject_id;

    -- If no grades exist, start with month 1
    IF last_month IS NULL THEN
        SET NEW.month = 1;
    ELSE
        -- Otherwise, increment the month by 1, but make sure it doesn't exceed 12
        IF last_month < 12 THEN
            SET NEW.month = last_month + 1;
        ELSE
            SET NEW.month = 12;
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `student_class`
--

CREATE TABLE `student_class` (
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_class`
--

INSERT INTO `student_class` (`student_id`, `class_id`, `first_name`, `last_name`) VALUES
(57, 5, NULL, NULL),
(58, 5, NULL, NULL),
(59, 5, NULL, NULL),
(60, 5, NULL, NULL),
(61, 5, NULL, NULL),
(62, 6, NULL, NULL),
(63, 6, NULL, NULL),
(64, 6, NULL, NULL),
(65, 6, NULL, NULL),
(66, 7, NULL, NULL),
(67, 7, NULL, NULL),
(68, 7, NULL, NULL),
(69, 7, NULL, NULL),
(70, 8, NULL, NULL),
(71, 8, NULL, NULL),
(73, 8, NULL, NULL),
(83, 5, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `subject_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `subject_name`) VALUES
(6, 'Disiplina ime'),
(5, 'Edukatë figurative'),
(10, 'Edukatë Fizike'),
(12, 'Edukatë muzikore'),
(2, 'Gjuhë Angleze'),
(3, 'Gjuhë Shqipe'),
(8, 'Lëndë zgjedhore'),
(1, 'Matematikë'),
(7, 'Mjetet e punës'),
(11, 'Njeriu dhe Natyra'),
(4, 'Shkathtësi  për jetë'),
(9, 'Shoqëria dhe mjedisi');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_class`
--

CREATE TABLE `teacher_class` (
  `teacher_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_class`
--

INSERT INTO `teacher_class` (`teacher_id`, `class_id`) VALUES
(52, 5),
(54, 6),
(55, 7),
(56, 8);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` enum('admin','teacher','student','parent') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', '123', 'admin', '2024-12-09 23:01:42'),
(52, 'Qemajl', '123', 'teacher', '2025-02-07 23:54:22'),
(54, 'Hysen', '123', 'teacher', '2025-02-07 23:55:14'),
(55, 'Halit', '123', 'teacher', '2025-02-07 23:55:27'),
(56, 'Mejreme', '123', 'teacher', '2025-02-07 23:55:44'),
(57, 'arber', '123', 'student', '2025-02-07 23:56:44'),
(58, 'dren', '123', 'student', '2025-02-07 23:56:52'),
(59, 'erald', '123', 'student', '2025-02-07 23:57:02'),
(60, 'erina', '123', 'student', '2025-02-07 23:57:20'),
(61, 'blerina', '123', 'student', '2025-02-07 23:57:28'),
(62, 'klea', '123', 'student', '2025-02-07 23:57:44'),
(63, 'fisnik', '123', 'student', '2025-02-07 23:57:56'),
(64, 'eris', '123', 'student', '2025-02-07 23:58:07'),
(65, 'donika', '123', 'student', '2025-02-07 23:58:17'),
(66, 'albion', '123', 'student', '2025-02-07 23:58:41'),
(67, 'altea', '123', 'student', '2025-02-07 23:58:51'),
(68, 'jara', '123', 'student', '2025-02-07 23:58:59'),
(69, 'altin', '123', 'student', '2025-02-07 23:59:10'),
(70, 'endrit', '123', 'student', '2025-02-07 23:59:53'),
(71, 'flakron', '123', 'student', '2025-02-08 00:00:07'),
(73, 'rina', '123', 'student', '2025-02-08 00:00:34'),
(74, 'Ibrahim', '123', 'parent', '2025-02-08 00:01:18'),
(75, 'Nexhmi', '123', 'parent', '2025-02-08 00:02:33'),
(76, 'Shaban', '123', 'parent', '2025-02-08 00:02:49'),
(77, 'Fatime', '123', 'parent', '2025-02-08 00:03:13'),
(78, 'Zylfie', '123', 'parent', '2025-02-08 00:04:01'),
(81, 'Shaip', '123', 'parent', '2025-02-08 00:06:55'),
(83, 'test', '123', 'student', '2025-02-08 16:21:43');

-- --------------------------------------------------------

--
-- Table structure for table `user_time_limits`
--

CREATE TABLE `user_time_limits` (
  `limit_id` int(11) NOT NULL,
  `role` enum('admin','teacher','student','parent') NOT NULL,
  `days` varchar(255) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_time_limits`
--

INSERT INTO `user_time_limits` (`limit_id`, `role`, `days`, `start_time`, `end_time`) VALUES
(25, 'admin', 'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday', '00:00:00', '23:59:00'),
(26, 'teacher', 'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday', '00:00:00', '23:59:00'),
(27, 'student', 'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday', '00:00:00', '23:59:00'),
(28, 'parent', 'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday', '00:00:00', '23:59:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`),
  ADD UNIQUE KEY `class_name` (`class_name`);

--
-- Indexes for table `final_grades`
--
ALTER TABLE `final_grades`
  ADD PRIMARY KEY (`grade_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `monthly_grades`
--
ALTER TABLE `monthly_grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `parent_student`
--
ALTER TABLE `parent_student`
  ADD PRIMARY KEY (`parent_id`,`student_id`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- Indexes for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD PRIMARY KEY (`token_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `self_grades`
--
ALTER TABLE `self_grades`
  ADD PRIMARY KEY (`grade_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `student_class`
--
ALTER TABLE `student_class`
  ADD PRIMARY KEY (`student_id`,`class_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`),
  ADD UNIQUE KEY `subject_name` (`subject_name`);

--
-- Indexes for table `teacher_class`
--
ALTER TABLE `teacher_class`
  ADD PRIMARY KEY (`teacher_id`,`class_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_time_limits`
--
ALTER TABLE `user_time_limits`
  ADD PRIMARY KEY (`limit_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `final_grades`
--
ALTER TABLE `final_grades`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=290;

--
-- AUTO_INCREMENT for table `monthly_grades`
--
ALTER TABLE `monthly_grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `token_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT for table `self_grades`
--
ALTER TABLE `self_grades`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=821;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `user_time_limits`
--
ALTER TABLE `user_time_limits`
  MODIFY `limit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `final_grades`
--
ALTER TABLE `final_grades`
  ADD CONSTRAINT `final_grades_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `final_grades_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`);

--
-- Constraints for table `monthly_grades`
--
ALTER TABLE `monthly_grades`
  ADD CONSTRAINT `monthly_grades_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `monthly_grades_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`);

--
-- Constraints for table `parent_student`
--
ALTER TABLE `parent_student`
  ADD CONSTRAINT `parent_student_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `parent_student_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD CONSTRAINT `remember_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `self_grades`
--
ALTER TABLE `self_grades`
  ADD CONSTRAINT `self_grades_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `self_grades_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_class`
--
ALTER TABLE `student_class`
  ADD CONSTRAINT `student_class_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_class_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE;

--
-- Constraints for table `teacher_class`
--
ALTER TABLE `teacher_class`
  ADD CONSTRAINT `teacher_class_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teacher_class_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
