-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: May 28, 2026 at 07:11 AM
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
-- Database: `club_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `user_id`, `user_name`, `action`, `details`, `created_at`) VALUES
(1, 2, 'Manager', 'Update Attendance', 'Updated attendance for Meeting ID: 1', '2025-12-18 00:35:37'),
(2, 2, 'Manager', 'Login', 'User logged into the system.', '2025-12-18 00:42:38'),
(3, 3, 'lilian', 'Login', 'User logged into the system.', '2025-12-18 00:43:05'),
(4, 1, 'SuperAdmin', 'Login', 'User logged into the system.', '2025-12-18 00:43:16'),
(5, 1, 'SuperAdmin', 'Login', 'User logged into the system.', '2025-12-18 01:10:25'),
(6, 1, 'SuperAdmin', 'Login', 'User logged into the system.', '2025-12-18 01:51:38'),
(7, 1, 'SuperAdmin', 'Create Meeting', 'Created: Baking Club', '2025-12-18 01:57:17'),
(8, 1, 'SuperAdmin', 'Update Attendance', 'Updated attendance for Meeting ID: 6', '2025-12-18 01:57:49'),
(9, 1, 'SuperAdmin', 'Update Attendance', 'Updated attendance for Meeting ID: 4', '2025-12-18 02:10:59'),
(10, 1, 'SuperAdmin', 'Export', 'Downloaded full database.', '2025-12-18 02:20:51'),
(11, 1, 'Stella', 'Login', 'User logged in.', '2025-12-18 03:09:17'),
(12, 6, 'raf', 'Register', 'User registered new account.', '2025-12-18 03:13:17'),
(13, 6, 'raf', 'Login', 'User logged in.', '2025-12-18 03:14:00'),
(14, 1, 'Stella', 'Login', 'User logged in.', '2025-12-18 03:14:23'),
(15, 1, 'Stella', 'Export', 'Downloaded full database.', '2025-12-18 03:16:04'),
(16, 2, 'Manager', 'Login', 'User logged in.', '2025-12-18 03:16:36'),
(17, 7, 'jj', 'Register', 'User registered new account.', '2025-12-18 03:17:21'),
(18, 8, 'kenny', 'Register', 'User registered new account.', '2025-12-18 03:17:57'),
(19, 1, 'Stella', 'Login', 'User logged in.', '2025-12-18 03:18:06'),
(20, 1, 'Stella', 'Update Attendance', 'Updated attendance for Meeting ID: 3', '2025-12-18 03:19:37'),
(21, 1, 'Stella', 'Export', 'Downloaded full database.', '2025-12-18 03:20:32'),
(22, 1, 'Stella', 'Export', 'Downloaded full database.', '2025-12-18 03:20:49'),
(23, 2, 'Manager', 'Login', 'User logged in.', '2025-12-18 03:21:28'),
(24, 2, 'Manager', 'Update Attendance', 'Updated attendance for Meeting ID: 2', '2025-12-18 03:25:36'),
(25, 2, 'Manager', 'Update Attendance', 'Updated attendance for Meeting ID: 2', '2025-12-18 03:26:18'),
(26, 1, 'Stella', 'Login', 'User logged in.', '2025-12-18 09:04:49'),
(27, 2, 'Manager', 'Login', 'User logged in.', '2025-12-18 21:54:20'),
(28, 1, 'Stella', 'Login', 'User logged in.', '2025-12-18 21:55:26'),
(29, 1, 'Stella', 'Export', 'Downloaded full database.', '2025-12-18 21:56:09'),
(30, 1, 'Stella', 'Login', 'User logged in.', '2026-01-17 14:24:05'),
(31, 2, 'Manager', 'Login', 'User logged in.', '2026-01-17 14:26:02'),
(32, 2, 'Manager', 'Login', 'User logged in.', '2026-01-17 14:31:33'),
(33, 1, 'Stella', 'Login', 'User logged in.', '2026-01-21 18:49:50'),
(34, 1, 'Stella', 'Create Meeting', 'Created: Eating Club', '2026-01-21 18:51:24'),
(35, 4, 'Alvino', 'Login', 'User logged in.', '2026-01-21 18:54:23'),
(36, 4, 'Alvino', 'Login', 'User logged in.', '2026-01-21 18:56:00'),
(37, 1, 'Stella', 'Login', 'User logged in.', '2026-01-21 19:24:48'),
(38, 2, 'Manager', 'Login', 'User logged in.', '2026-01-21 19:27:43'),
(39, 2, 'Manager', 'Update Attendance', 'Updated attendance for Meeting ID: 5', '2026-01-21 19:33:27'),
(40, 1, 'Stella', 'Login', 'User logged in.', '2026-01-21 20:19:18'),
(41, 1, 'Stella', 'Login', 'User logged in.', '2026-01-21 20:20:05'),
(42, 1, 'Stella', 'Create Meeting', 'Created: IT Club', '2026-01-21 20:22:47'),
(43, 2, 'Manager', 'Login', 'User logged in.', '2026-01-21 20:23:29'),
(44, 4, 'Alvino', 'Login', 'User logged in.', '2026-01-21 21:30:14'),
(45, 2, 'Manager', 'Login', 'User logged in.', '2026-01-21 21:47:18'),
(46, 4, 'Alvino', 'Login', 'User logged in.', '2026-01-21 21:59:17'),
(47, 2, 'Manager', 'Login', 'User logged in.', '2026-01-21 21:59:56'),
(48, 4, 'Alvino', 'Login', 'User logged in.', '2026-01-21 22:19:47'),
(49, 2, 'Manager', 'Login', 'User logged in.', '2026-01-21 22:22:34'),
(50, 4, 'Alvino', 'Login', 'User logged in.', '2026-01-21 22:29:06'),
(51, 2, 'Manager', 'Login', 'User logged in.', '2026-01-21 22:30:16'),
(52, 1, 'Stella', 'Login', 'User logged in.', '2026-01-21 22:47:00'),
(53, 1, 'Stella', 'Export', 'Downloaded full database.', '2026-01-21 22:49:03'),
(54, 2, 'Manager', 'Login', 'User logged in.', '2026-01-23 14:36:05');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(20) UNSIGNED NOT NULL,
  `user_id` int(20) UNSIGNED NOT NULL,
  `status` enum('Present','Absent','Pending','Rejected') DEFAULT NULL,
  `schedule_id` int(10) UNSIGNED NOT NULL,
  `reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `user_id`, `status`, `schedule_id`, `reason`) VALUES
(2, 4, 'Present', 5, NULL),
(3, 7, 'Present', 5, NULL),
(4, 8, 'Present', 5, NULL),
(5, 3, 'Present', 5, NULL),
(6, 2, 'Present', 5, NULL),
(7, 6, 'Present', 5, NULL),
(8, 5, 'Present', 5, NULL),
(9, 1, 'Present', 5, NULL),
(10, 4, 'Absent', 8, 'sickk'),
(12, 9, 'Absent', 9, 'help'),
(13, 9, 'Rejected', 10, 'Lazy to come'),
(14, 10, 'Absent', 10, 'I am sick');

-- --------------------------------------------------------

--
-- Table structure for table `dummy_events`
--

CREATE TABLE `dummy_events` (
  `id` int(11) NOT NULL,
  `event_name` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dummy_events`
--

INSERT INTO `dummy_events` (`id`, `event_name`, `created_at`) VALUES
(1, 'Cultural Festival 2026', '2026-05-28 03:12:22'),
(2, 'Tech & AI Summit', '2026-05-28 03:12:22'),
(3, 'University Open House', '2026-05-28 03:12:22');

-- --------------------------------------------------------

--
-- Table structure for table `faq_chatbot`
--

CREATE TABLE `faq_chatbot` (
  `id` int(11) NOT NULL,
  `category` varchar(100) NOT NULL,
  `keywords` text NOT NULL,
  `bot_response` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faq_chatbot`
--

INSERT INTO `faq_chatbot` (`id`, `category`, `keywords`, `bot_response`, `created_at`) VALUES
(1, 'Invitation Codes', 'code, invitation, pin, access, login, join, register', 'Your random invitation code is issued directly by an event PIC or staff member via email or WhatsApp. This code serves as your universal pass—enter it on the login screen to access the full portal!', '2026-05-24 12:33:17'),
(2, 'Event Duration', 'duration, time, long, schedule, hours, open, close, calendar, date, past, present', 'Each event card on the dashboard displays its specific date and active duration hours. You can browse live active showcases as well as past cultural exhibitions.', '2026-05-24 12:33:17'),
(3, 'Requesting Extensions', 'extend, extension, longer, late, change time, delay, staff, add time', 'While events have predefined hours, an event PIC or staff member can trigger an extension request if visitor traffic is high. Approved extensions update the event closing times live!', '2026-05-24 12:33:17'),
(4, 'Photo Showcase', 'photo, picture, image, gallery, showcase, media, display, collection', 'Every single past and present cultural event features a dedicated showcase section holding a gallery of at least 25 and up to 75 high-quality documentation photos.', '2026-05-24 12:33:17'),
(5, 'Admission Fees', 'ticket, price, cost, buy, free, fee, admission, payment', 'General access to the platform and standard entry to all individual exhibitions is completely free for invited visitors utilizing a valid invitation code.', '2026-05-24 12:33:17'),
(6, 'Ticket Info & Price', 'ticket, price, cost, buy, free, fee, admission, enter, entrance', 'Entry to the Cultural Festival is completely free for all active university students and staff! For public visitors, entry passes can be purchased directly at the main registration gate for IDR 50,000.', '2026-05-24 12:33:17'),
(7, 'Festival Schedule', 'duration, time, long, hours, schedule, open, start, close, clock', 'The main cultural exhibition opens daily at 09:00 AM and concludes its regular schedule at 05:00 PM. Special evening cultural performances run from 07:00 PM to 09:30 PM.', '2026-05-24 12:33:17'),
(8, 'Schedule Extension', 'extend, extension, longer, late, delay, request, change time', 'If a booth PIC or staff member needs to request an event duration extension for special performances or high visitor volume, they can submit an \'Extension Application\' form through the Staff Dashboard panel. Visitors will receive a live notification popup in this app if an extension is approved!', '2026-05-24 12:33:17'),
(9, 'Dress Code', 'wear, dress, clothes, batik, outfit, custom, costume', 'We highly encourage all visitors and participants to wear traditional attire, regional costumes, or smart casual clothing to complement the cultural showcase atmosphere!', '2026-05-24 12:33:17'),
(10, 'Food & Beverages', 'food, drink, eat, beverage, culinary, booth, lunch', 'Yes! The event features a dedicated Cultural Culinary Zone showcasing traditional authentic foods and beverages from over 15 different regions. Outside food is not permitted inside the main exhibition hall.', '2026-05-24 12:33:17');

-- --------------------------------------------------------

--
-- Table structure for table `polls`
--

CREATE TABLE `polls` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `club_name` enum('Media Club','IT Club','Baking Club','Art Club','Dance Club') NOT NULL,
  `end_time` datetime NOT NULL,
  `type` enum('Leadership','Event') NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_visible` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `polls`
--

INSERT INTO `polls` (`id`, `title`, `club_name`, `end_time`, `type`, `description`, `created_at`, `is_visible`) VALUES
(3, 'Leadership election', 'IT Club', '2026-01-22 09:00:00', 'Leadership', NULL, '2026-01-21 14:07:36', 1),
(5, 'Valentine Booth', 'Art Club', '2026-01-24 09:47:00', 'Event', 'Volunteer for booth preparation', '2026-01-21 14:48:16', 1);

-- --------------------------------------------------------

--
-- Table structure for table `poll_options`
--

CREATE TABLE `poll_options` (
  `id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `option_name` varchar(255) NOT NULL,
  `category` enum('President','Vice President','Timeslot') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `poll_options`
--

INSERT INTO `poll_options` (`id`, `poll_id`, `option_name`, `category`) VALUES
(16, 3, 'lilian', 'President'),
(17, 3, 'stella', 'President'),
(18, 3, 'vino', 'Vice President'),
(19, 3, 'shandy', 'Vice President'),
(20, 5, '1pm', 'Timeslot'),
(21, 5, '3pm - 4pm', 'Timeslot');

-- --------------------------------------------------------

--
-- Table structure for table `poll_votes`
--

CREATE TABLE `poll_votes` (
  `id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `user_id` int(20) UNSIGNED NOT NULL,
  `president_choice` varchar(255) DEFAULT NULL,
  `vp_choice` varchar(255) DEFAULT NULL,
  `timeslot_choice` varchar(255) DEFAULT NULL,
  `voted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `poll_votes`
--

INSERT INTO `poll_votes` (`id`, `poll_id`, `user_id`, `president_choice`, `vp_choice`, `timeslot_choice`, `voted_at`) VALUES
(1, 5, 4, NULL, NULL, '3pm - 4pm', '2026-01-21 15:29:14'),
(2, 3, 4, 'stella', 'shandy', NULL, '2026-01-21 15:29:21');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int(20) NOT NULL,
  `role` enum('Member','Admin','Manager') DEFAULT 'Member',
  `user_id` int(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `role`, `user_id`) VALUES
(1, 'Admin', 1),
(2, 'Manager', 2),
(3, 'Manager', 3),
(4, 'Member', 4),
(5, 'Member', 5),
(6, 'Member', 6),
(7, 'Member', 7),
(8, 'Member', 8),
(9, 'Member', 9),
(10, 'Member', 10);

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `id` int(20) UNSIGNED NOT NULL,
  `meeting_name` enum('Media Club','IT Club','Baking Club','Art Club','Dance Club') NOT NULL,
  `meeting_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`id`, `meeting_name`, `meeting_time`) VALUES
(1, 'Media Club', '2025-12-20 14:00:00'),
(2, 'Art Club', '2025-12-22 09:30:00'),
(5, 'Dance Club', '2026-01-05 16:00:00'),
(8, 'IT Club', '2026-03-12 08:30:00'),
(9, 'IT Club', '2026-05-13 14:52:18'),
(10, 'Baking Club', '2026-05-18 12:43:00');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(20) UNSIGNED NOT NULL,
  `name` varchar(250) NOT NULL,
  `major` varchar(150) NOT NULL,
  `email` varchar(250) NOT NULL,
  `password` varchar(300) NOT NULL,
  `profile_pic` varchar(255) NOT NULL DEFAULT 'avatar1.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `major`, `email`, `password`, `profile_pic`) VALUES
(1, 'Stella', 'software engineering', 'admin1@mail.com', '$2y$10$AlxrLTKeAfGoPqG69otN8.RM3CrpmmdfZYjJs9QviDQ1qmLEmGClS', 'avatar4.jpg'),
(2, 'Manager', 'data science', 'manager@gmail.com', '$2y$10$GjngPUS44OP8NzA55M955uHRU1PGZDhL3chsBc31WNMj/AtOF4Wni', 'avatar1.jpg'),
(3, 'lilian', 'software engineering', 'lilian123@gmail.com', '$2y$10$9wv/x937AraDlVK7riorK.NBvUW0w4ymdvMvtdvPw.3LLuCjsAZs2', 'avatar2.jpg'),
(4, 'Alvino', 'information technology', 'vino@gmail.com', '$2y$10$l6cX2mhWHN52oJMgvPb.iOz22nhifK6qv3bn1A89grZNjWnqeXpWe', 'avatar3.jpg'),
(5, 'shandy', 'information technology', 'shandy@gmail.com', '$2y$10$kuQbRnM/Z5eqirDEhmyO5.BruLmwGgGROt53L/hXDlX4vqrnDrsk.', 'avatar4.jpg'),
(6, 'raf', 'accounting', 'raf@gmail.com', '$2y$10$AK9lpd2EpUkxFjhMrmgRl.ME6/pCw4kI1RS05P8DWXcDky7YdYD3q', 'avatar3.jpg'),
(7, 'jj', 'data science', 'jj@gmail.com', '$2y$10$M8NCrWyxC9DNbPVzs/ujweNE8LBtMMw3/4TJjs8y1oJDsnNYtVL9y', 'avatar3.jpg'),
(8, 'kenny', 'information technology', 'kenny@gmail.com', '$2y$10$5wOezxdUD6YBGcYcbDCzJuN3knFEEQq7YY33eWaCToAMMvTWzpbwG', 'avatar2.jpg'),
(9, 'Kenny', 'Information Technology', 'kenny@mail.com', '$2y$10$Ts2XTc9pvI.dRw4mCSQE5OBpcMHL/AG1HFmkmneJJ4Av5ZEmvg1ci', 'avatar2.jpg'),
(10, 'Nicholas Kenny', 'Information Technology', 'kenny1@mail.com', '$2y$10$HCdAC6OW.8MgugHRSxONzup1lWACAKdkOhNPbuxl0fUU0nkZ2dbtO', 'avatar4.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `venue`
--

CREATE TABLE `venue` (
  `id` int(100) NOT NULL,
  `schedule_id` int(20) UNSIGNED NOT NULL,
  `room_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `venue`
--

INSERT INTO `venue` (`id`, `schedule_id`, `room_name`) VALUES
(1, 1, '103'),
(2, 2, '506'),
(5, 5, '702'),
(8, 8, '606'),
(9, 10, '10.14');

-- --------------------------------------------------------

--
-- Table structure for table `visitor_inquiries`
--

CREATE TABLE `visitor_inquiries` (
  `id` int(11) NOT NULL,
  `visitor_name` varchar(100) NOT NULL,
  `visitor_email` varchar(100) NOT NULL,
  `event_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `admin_reply` text DEFAULT NULL,
  `status` enum('Pending','Answered') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visitor_inquiries`
--

INSERT INTO `visitor_inquiries` (`id`, `visitor_name`, `visitor_email`, `event_id`, `message`, `admin_reply`, `status`, `created_at`) VALUES
(1, 'nicholas', 'nicholas@gmail.com', 2, 'Help me', NULL, 'Pending', '2026-05-28 03:44:27'),
(2, 'dede', 'dwhwihdi@gmai', 2, 'wodwodwd', NULL, 'Pending', '2026-05-28 04:59:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FOREIGN KEY` (`schedule_id`),
  ADD KEY `attendance_user` (`user_id`);

--
-- Indexes for table `dummy_events`
--
ALTER TABLE `dummy_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faq_chatbot`
--
ALTER TABLE `faq_chatbot`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `polls`
--
ALTER TABLE `polls`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `poll_options`
--
ALTER TABLE `poll_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `poll_id` (`poll_id`);

--
-- Indexes for table `poll_votes`
--
ALTER TABLE `poll_votes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_poll_ref` (`poll_id`),
  ADD KEY `fk_pv_user_connection` (`user_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FOREIGN KEY` (`user_id`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQUE` (`email`);

--
-- Indexes for table `venue`
--
ALTER TABLE `venue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schedule_id` (`schedule_id`);

--
-- Indexes for table `visitor_inquiries`
--
ALTER TABLE `visitor_inquiries`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `dummy_events`
--
ALTER TABLE `dummy_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `faq_chatbot`
--
ALTER TABLE `faq_chatbot`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `polls`
--
ALTER TABLE `polls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `poll_options`
--
ALTER TABLE `poll_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `poll_votes`
--
ALTER TABLE `poll_votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `venue`
--
ALTER TABLE `venue`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `visitor_inquiries`
--
ALTER TABLE `visitor_inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `schedule_attendance` FOREIGN KEY (`schedule_id`) REFERENCES `schedule` (`id`);

--
-- Constraints for table `poll_options`
--
ALTER TABLE `poll_options`
  ADD CONSTRAINT `poll_options_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `poll_votes`
--
ALTER TABLE `poll_votes`
  ADD CONSTRAINT `fk_poll_ref` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pv_user_connection` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_votes_poll` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role`
--
ALTER TABLE `role`
  ADD CONSTRAINT `user_role` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `venue`
--
ALTER TABLE `venue`
  ADD CONSTRAINT `venue_ibfk_1` FOREIGN KEY (`schedule_id`) REFERENCES `schedule` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
