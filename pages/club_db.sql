-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 30, 2026 at 05:35 PM
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
-- Table structure for table `event_images`
--

CREATE TABLE `event_images` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_images`
--

INSERT INTO `event_images` (`id`, `event_id`, `image_path`) VALUES
(1, 1, 'assets/images/gallery/1.jpg'),
(2, 1, 'assets/images/gallery/2.jpg'),
(3, 1, 'assets/images/gallery/3.jpg'),
(4, 1, 'assets/images/gallery/4.jpg'),
(5, 1, 'assets/images/gallery/5.jpg'),
(6, 1, 'assets/images/gallery/6.jpg'),
(7, 1, 'assets/images/gallery/7.jpg'),
(8, 1, 'assets/images/gallery/8.jpg'),
(9, 1, 'assets/images/gallery/9.jpg'),
(10, 1, 'assets/images/gallery/10.jpg'),
(11, 1, 'assets/images/gallery/11.jpg'),
(12, 1, 'assets/images/gallery/12.jpg'),
(13, 1, 'assets/images/gallery/13.jpg'),
(14, 1, 'assets/images/gallery/14.jpg'),
(15, 1, 'assets/images/gallery/15.jpg'),
(16, 1, 'assets/images/gallery/16.jpg'),
(17, 1, 'assets/images/gallery/17.jpg'),
(18, 1, 'assets/images/gallery/18.jpg'),
(19, 1, 'assets/images/gallery/19.jpg'),
(20, 1, 'assets/images/gallery/20.jpg'),
(21, 1, 'assets/images/gallery/21.jpg'),
(22, 1, 'assets/images/gallery/22.jpg'),
(23, 1, 'assets/images/gallery/23.jpg'),
(24, 1, 'assets/images/gallery/24.jpg'),
(25, 1, 'assets/images/gallery/25.jpg'),
(26, 2, 'assets/images/gallery/1.jpg'),
(27, 2, 'assets/images/gallery/2.jpg'),
(28, 2, 'assets/images/gallery/3.jpg'),
(29, 2, 'assets/images/gallery/4.jpg'),
(30, 2, 'assets/images/gallery/5.jpg'),
(31, 2, 'assets/images/gallery/6.jpg'),
(32, 2, 'assets/images/gallery/7.jpg'),
(33, 2, 'assets/images/gallery/8.jpg'),
(34, 2, 'assets/images/gallery/9.jpg'),
(35, 2, 'assets/images/gallery/10.jpg'),
(36, 2, 'assets/images/gallery/11.jpg'),
(37, 2, 'assets/images/gallery/12.jpg'),
(38, 2, 'assets/images/gallery/13.jpg'),
(39, 2, 'assets/images/gallery/14.jpg'),
(40, 2, 'assets/images/gallery/15.jpg'),
(41, 2, 'assets/images/gallery/16.jpg'),
(42, 2, 'assets/images/gallery/17.jpg'),
(43, 2, 'assets/images/gallery/18.jpg'),
(44, 2, 'assets/images/gallery/19.jpg'),
(45, 2, 'assets/images/gallery/20.jpg'),
(46, 2, 'assets/images/gallery/21.jpg'),
(47, 2, 'assets/images/gallery/22.jpg'),
(48, 2, 'assets/images/gallery/23.jpg'),
(49, 2, 'assets/images/gallery/24.jpg'),
(50, 2, 'assets/images/gallery/25.jpg'),
(51, 3, 'assets/images/gallery/1.jpg'),
(52, 3, 'assets/images/gallery/2.jpg'),
(53, 3, 'assets/images/gallery/3.jpg'),
(54, 3, 'assets/images/gallery/4.jpg'),
(55, 3, 'assets/images/gallery/5.jpg'),
(56, 3, 'assets/images/gallery/6.jpg'),
(57, 3, 'assets/images/gallery/7.jpg'),
(58, 3, 'assets/images/gallery/8.jpg'),
(59, 3, 'assets/images/gallery/9.jpg'),
(60, 3, 'assets/images/gallery/10.jpg'),
(61, 3, 'assets/images/gallery/11.jpg'),
(62, 3, 'assets/images/gallery/12.jpg'),
(63, 3, 'assets/images/gallery/13.jpg'),
(64, 3, 'assets/images/gallery/14.jpg'),
(65, 3, 'assets/images/gallery/15.jpg'),
(66, 3, 'assets/images/gallery/16.jpg'),
(67, 3, 'assets/images/gallery/17.jpg'),
(68, 3, 'assets/images/gallery/18.jpg'),
(69, 3, 'assets/images/gallery/19.jpg'),
(70, 3, 'assets/images/gallery/20.jpg'),
(71, 3, 'assets/images/gallery/21.jpg'),
(72, 3, 'assets/images/gallery/22.jpg'),
(73, 3, 'assets/images/gallery/23.jpg'),
(74, 3, 'assets/images/gallery/24.jpg'),
(75, 3, 'assets/images/gallery/25.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `event_list`
--

CREATE TABLE `event_list` (
  `id` int(11) NOT NULL,
  `event_name` varchar(150) NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_list`
--

INSERT INTO `event_list` (`id`, `event_name`, `date`, `start_time`, `end_time`, `description`, `created_at`) VALUES
(1, 'Cultural Festival 2026', '2026-08-15', '10:00:00', '17:00:00', 'An all-day exhibition featuring traditional foods, performances, and art from over 15 different regions. General admission is free for students.', '2026-05-29 07:57:01'),
(2, 'Tech & AI Summit', '2026-09-10', '13:00:00', '17:00:00', 'A professional gathering of software engineers and industry leaders discussing the future of artificial intelligence and progressive web applications.', '2026-05-29 07:57:01'),
(3, 'University Open House', '2026-10-05', '10:00:00', '17:00:00', 'Welcome to the campus! Explore the facilities, meet the faculty, and learn about the Information System and Software Engineering programs.', '2026-05-29 07:57:01');

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
-- Table structure for table `invitation_codes`
--

CREATE TABLE `invitation_codes` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `generated_by` int(20) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invitation_codes`
--

INSERT INTO `invitation_codes` (`id`, `code`, `generated_by`, `created_at`) VALUES
(1, 'QWER1', 1, '2026-05-30 06:37:03'),
(2, 'QWER2', 1, '2026-05-30 06:37:03'),
(6, 'FHA59YGRYX', 1, '2026-05-30 14:29:26'),
(7, 'C6WJ4CBHPE', 11, '2026-05-30 15:10:08');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int(20) NOT NULL,
  `role` enum('Admin','Staff') NOT NULL DEFAULT 'Staff',
  `user_id` int(20) UNSIGNED NOT NULL,
  `event_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `role`, `user_id`, `event_id`) VALUES
(1, 'Admin', 1, NULL),
(2, 'Staff', 2, 1),
(3, 'Staff', 3, 1),
(4, 'Staff', 11, 2);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(20) UNSIGNED NOT NULL,
  `name` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `password` varchar(300) NOT NULL,
  `profile_pic` varchar(255) NOT NULL DEFAULT 'avatar1.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `password`, `profile_pic`) VALUES
(1, 'Stella', 'admin1@mail.com', '$2y$10$AlxrLTKeAfGoPqG69otN8.RM3CrpmmdfZYjJs9QviDQ1qmLEmGClS', 'avatar4.jpg'),
(2, 'Manager', 'manager@gmail.com', '$2y$10$GjngPUS44OP8NzA55M955uHRU1PGZDhL3chsBc31WNMj/AtOF4Wni', 'avatar1.jpg'),
(3, 'lilian', 'lilian123@gmail.com', '$2y$10$9wv/x937AraDlVK7riorK.NBvUW0w4ymdvMvtdvPw.3LLuCjsAZs2', 'avatar2.jpg'),
(4, 'Alvino', 'vino@gmail.com', '$2y$10$l6cX2mhWHN52oJMgvPb.iOz22nhifK6qv3bn1A89grZNjWnqeXpWe', 'avatar3.jpg'),
(5, 'shandy', 'shandy@gmail.com', '$2y$10$kuQbRnM/Z5eqirDEhmyO5.BruLmwGgGROt53L/hXDlX4vqrnDrsk.', 'avatar4.jpg'),
(6, 'raf', 'raf@gmail.com', '$2y$10$AK9lpd2EpUkxFjhMrmgRl.ME6/pCw4kI1RS05P8DWXcDky7YdYD3q', 'avatar3.jpg'),
(7, 'jj', 'jj@gmail.com', '$2y$10$M8NCrWyxC9DNbPVzs/ujweNE8LBtMMw3/4TJjs8y1oJDsnNYtVL9y', 'avatar3.jpg'),
(8, 'kenny', 'kenny@gmail.com', '$2y$10$5wOezxdUD6YBGcYcbDCzJuN3knFEEQq7YY33eWaCToAMMvTWzpbwG', 'avatar2.jpg'),
(9, 'Kenny', 'kenny@mail.com', '$2y$10$Ts2XTc9pvI.dRw4mCSQE5OBpcMHL/AG1HFmkmneJJ4Av5ZEmvg1ci', 'avatar2.jpg'),
(10, 'Nicholas Kenny', 'kenny1@mail.com', '$2y$10$HCdAC6OW.8MgugHRSxONzup1lWACAKdkOhNPbuxl0fUU0nkZ2dbtO', 'avatar4.jpg'),
(11, 'Stella Gunawan', 'stella@gmail.com', '$2y$10$VB2fR3okH1ids9CMPbKTceEVr3yaX9CKy4KuqjV5xlDto9Sw1r4T6', 'avatar1.jpg');

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
(1, 'notstella', 'stellaaa@gmail.com', 1, 'where is the noodle stall', NULL, 'Pending', '2026-05-30 15:17:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `event_images`
--
ALTER TABLE `event_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `event_list`
--
ALTER TABLE `event_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faq_chatbot`
--
ALTER TABLE `faq_chatbot`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invitation_codes`
--
ALTER TABLE `invitation_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `generated_by` (`generated_by`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FOREIGN KEY` (`user_id`),
  ADD KEY `fk_role_event` (`event_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQUE` (`email`);

--
-- Indexes for table `visitor_inquiries`
--
ALTER TABLE `visitor_inquiries`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `event_images`
--
ALTER TABLE `event_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `event_list`
--
ALTER TABLE `event_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `faq_chatbot`
--
ALTER TABLE `faq_chatbot`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `invitation_codes`
--
ALTER TABLE `invitation_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `visitor_inquiries`
--
ALTER TABLE `visitor_inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `event_images`
--
ALTER TABLE `event_images`
  ADD CONSTRAINT `event_images_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invitation_codes`
--
ALTER TABLE `invitation_codes`
  ADD CONSTRAINT `invitation_codes_ibfk_2` FOREIGN KEY (`generated_by`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role`
--
ALTER TABLE `role`
  ADD CONSTRAINT `fk_role_event` FOREIGN KEY (`event_id`) REFERENCES `event_list` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_role` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
