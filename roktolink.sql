-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 04, 2026 at 06:03 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `roktolink`
--
CREATE DATABASE IF NOT EXISTS `roktolink` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `roktolink`;

-- --------------------------------------------------------

--
-- Table structure for table `donor_profiles`
--

CREATE TABLE `donor_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `blood_group` varchar(5) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `dob` date NOT NULL,
  `address` text NOT NULL,
  `last_donation` date DEFAULT NULL,
  `availability` enum('Yes','No') DEFAULT 'Yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donor_profiles`
--

INSERT INTO `donor_profiles` (`id`, `user_id`, `full_name`, `profile_image`, `blood_group`, `phone`, `dob`, `address`, `last_donation`, `availability`) VALUES
(1, 3, 'SomeRandomNameV2', NULL, 'AB-', '01984876967', '2002-10-30', 'Avengers Tower,NY', '2020-01-01', 'Yes'),
(2, 4, 'dsadas', NULL, 'AB-', '01984876969', '2018-07-18', 'sdadasda', '2022-03-17', 'Yes');

-- --------------------------------------------------------

--
-- Table structure for table `patient_profiles`
--

CREATE TABLE `patient_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `blood_group` varchar(5) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `hospital` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `blood_units` int(11) NOT NULL,
  `required_date` date NOT NULL,
  `urgency` varchar(50) NOT NULL,
  `medical_info` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_profiles`
--

INSERT INTO `patient_profiles` (`id`, `user_id`, `full_name`, `profile_image`, `blood_group`, `phone`, `email`, `dob`, `hospital`, `address`, `blood_units`, `required_date`, `urgency`, `medical_info`) VALUES
(1, 3, 'SomeRandomName', NULL, 'AB-', '01984876969', 'test1@gmail.com', '2003-11-02', 'X hospital', 'X street,Under Dr. doom\'s mansion,NY', 5, '2026-09-11', 'Urgent', 'Probably gonna die if no blood'),
(2, 4, 'dsadsa', NULL, 'A+', '01229341123', 'test5@gmail.com', '2023-01-31', 'dsad', 'sdadsadsadad', 1, '2026-09-16', 'Urgent', 'dsadsadasd'),
(3, 4, 'dsadsa', NULL, 'A+', '01823732311', 'dsadasdadas@gmail.com', '2023-03-01', 'dsadsa', 'dsadasda', 2, '2026-09-24', 'Normal', 'dsadsadasdsad');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `nid` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `nid`, `password`) VALUES
(1, 'Tanzimul Islam', 'tanzimul3010@gmail.com', '1111222233', '$2y$10$HSuQ5DjZHbHbK54ApYRDZeWi8g5kIJUv/IxoWVIVBAMqOJpU27tgm'),
(2, 'Adnan Akib', 'akku42069@gmail.com', '6969696969', '$2y$10$gBtLPf54wvVQJ2Q4o1sZ.OJJ35SkKsqGPnpxN3yW65KER2/9QOH/i'),
(3, 'test', 'test@gmail.com', '1234567890', '$2y$10$/eCc/V4OReicblwNAVm1EOa5x2HJyUvlDooqmi1EJlSSHktxYDBY6'),
(4, 'testSub2', 'test2@gmail.com', '1234567890', '$2y$10$oe0/WabadC73Rrlqgdjd2eLPDy9U9LRq8KX4uVk/nNzUUAxbp87Am'),
(5, 'Admin', 'admin@roktolink.com', '1234567898', '$2y$10$z1sUckqY6k6QCZwUGod.OeYcWX3qrbvOngWDWoU0uyR0IZsu81itK');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `donor_profiles`
--
ALTER TABLE `donor_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `patient_profiles`
--
ALTER TABLE `patient_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `donor_profiles`
--
ALTER TABLE `donor_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `patient_profiles`
--
ALTER TABLE `patient_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `donor_profiles`
--
ALTER TABLE `donor_profiles`
  ADD CONSTRAINT `donor_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patient_profiles`
--
ALTER TABLE `patient_profiles`
  ADD CONSTRAINT `patient_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
