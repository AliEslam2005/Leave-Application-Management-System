-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 26, 2025 at 06:49 PM
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
-- Database: `leave_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `timestamp`) VALUES
(51, 2, 'Logged Out', '2025-06-26 13:18:28'),
(52, 2, 'Logged in', '2025-06-26 13:18:39'),
(53, 2, 'Viewed Leave Applications', '2025-06-26 13:18:55'),
(54, 2, 'Viewed Reports', '2025-06-26 13:19:25'),
(55, 2, 'Viewed Leave Applications', '2025-06-26 13:44:45'),
(56, 2, 'Rejected leave request ID 3', '2025-06-26 13:46:07'),
(57, 2, 'Back to menu from leave_requests', '2025-06-26 13:46:25'),
(58, 2, 'Viewed Reports', '2025-06-26 13:46:31'),
(59, 2, 'Logged Out', '2025-06-26 13:46:49'),
(60, 3, 'Logged in', '2025-06-26 14:57:17'),
(61, 3, 'Viewed Profile', '2025-06-26 14:57:19'),
(62, 3, 'Updated profile', '2025-06-26 15:01:34'),
(63, 3, 'Updated profile', '2025-06-26 15:01:43'),
(64, 3, 'Back to menu from profile', '2025-06-26 15:02:34'),
(65, 3, 'Viewed Profile', '2025-06-26 15:02:39'),
(66, 3, 'Updated profile', '2025-06-26 15:03:41'),
(67, 3, 'Back to menu from profile', '2025-06-26 15:03:44'),
(68, 3, 'Viewed Profile', '2025-06-26 15:04:05'),
(69, 3, 'Back to menu from profile', '2025-06-26 15:04:10'),
(70, 3, 'Logged Out', '2025-06-26 15:04:14'),
(71, 1, 'Logged in', '2025-06-26 15:41:20'),
(72, 1, 'Logged in', '2025-06-26 15:42:20'),
(73, 1, 'Accessed Announcements Manager', '2025-06-26 15:42:24'),
(74, 1, 'Accessed Announcements Manager', '2025-06-26 15:43:10'),
(75, 1, 'Accessed Announcements Manager', '2025-06-26 15:43:12'),
(76, 1, 'Accessed Announcements Manager', '2025-06-26 15:43:47'),
(77, 1, 'Accessed Announcements Manager', '2025-06-26 15:44:05'),
(78, 1, 'Logged Out', '2025-06-26 15:59:58'),
(79, 1, 'Logged in', '2025-06-26 16:00:10'),
(80, 1, 'Accessed Announcements Manager', '2025-06-26 16:00:34'),
(81, 1, 'Accessed Announcements Manager', '2025-06-26 16:08:03'),
(82, 1, 'Accessed Announcements Manager', '2025-06-26 16:21:44'),
(83, 1, 'Logged Out', '2025-06-26 16:22:37');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_applications`
--

CREATE TABLE `leave_applications` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `manager_comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_applications`
--

INSERT INTO `leave_applications` (`id`, `staff_id`, `leave_type_id`, `from_date`, `to_date`, `reason`, `status`, `manager_comment`, `created_at`) VALUES
(1, 3, 2, '2025-06-24', '2025-06-29', 'Because I can', 'approved', 'Pipe down lil bro', '2025-06-24 10:26:18'),
(2, 3, 3, '2025-06-27', '2025-06-30', 'I\'m going to mars', 'rejected', 'nuh uh', '2025-06-24 11:21:56'),
(3, 3, 1, '2025-06-24', '2025-06-25', 'I almost sneezed but I didn\'t. I still almost sneezed', 'rejected', 'crazy', '2025-06-24 11:29:38');

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

CREATE TABLE `leave_types` (
  `id` int(11) NOT NULL,
  `type_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_types`
--

INSERT INTO `leave_types` (`id`, `type_name`) VALUES
(1, 'Sick Leave'),
(2, 'Because Leave'),
(3, 'Vacation Leave');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('admin','manager','staff') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `name`, `email`, `role`) VALUES
(1, 'admin1', '32250170a0dca92d53ec9624f336ca24', 'Ali', 'admin@gmail.com', 'admin'),
(2, 'manager1', '32250170a0dca92d53ec9624f336ca24', 'Lily', 'manager@gmail.com', 'manager'),
(3, 'staff1', '32250170a0dca92d53ec9624f336ca24', 'Bob', 'staff@gmail.com', 'staff');

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `user_id` int(11) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `gender` enum('male','female') DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`user_id`, `phone`, `address`, `department`, `profile_picture`, `gender`, `date_of_birth`) VALUES
(1, '0123456789', 'Admin Address', 'IT', NULL, NULL, NULL),
(2, '0135792468', 'Manager Address', 'HR', NULL, NULL, NULL),
(3, '0987654321', 'Staff Address', 'Development', './assets/test.jpg', 'male', '1997-05-05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leave_applications`
--
ALTER TABLE `leave_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `leave_type_id` (`leave_type_id`);

--
-- Indexes for table `leave_types`
--
ALTER TABLE `leave_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `leave_applications`
--
ALTER TABLE `leave_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `leave_applications`
--
ALTER TABLE `leave_applications`
  ADD CONSTRAINT `leave_applications_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `leave_applications_ibfk_2` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`);

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
