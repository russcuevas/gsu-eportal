-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 23, 2024 at 12:48 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gsudatabase`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `id` int(11) UNSIGNED NOT NULL,
  `profile_image` varchar(255) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_admin`
--

INSERT INTO `tbl_admin` (`id`, `profile_image`, `fullname`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(2, 'asdas', 'Admin Deans', 'admindeans@gmail.com', 'f7c3bc1d808e04732adf679965ccc34ca7ae3441', 'deans', '2024-12-14 10:33:16', '2024-12-22 04:28:48'),
(24, 'asdsa', 'Admin OSDS', 'adminosds@gmail.com', 'f7c3bc1d808e04732adf679965ccc34ca7ae3441', 'osds', '2024-12-15 07:14:15', '2024-12-22 04:29:02'),
(26, 'asdasdas', 'Admin Cashier', 'admincashier@gmail.com', 'f7c3bc1d808e04732adf679965ccc34ca7ae3441', 'cashier', '2024-12-17 14:28:38', '2024-12-22 04:29:35'),
(28, 'asdasdas', 'Admin Clinic', 'adminclinic@gmail.com', 'f7c3bc1d808e04732adf679965ccc34ca7ae3441', 'clinic', '2024-12-21 12:44:36', '2024-12-22 04:29:48'),
(30, 'asdas', 'Admin Registrar', 'adminregistrar@gmail.com', 'f7c3bc1d808e04732adf679965ccc34ca7ae3441', 'registrar', '2024-12-22 04:30:09', '2024-12-22 04:30:09');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_clinic_request`
--

CREATE TABLE `tbl_clinic_request` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `request_number` varchar(255) DEFAULT NULL,
  `laboratory_request` varchar(255) DEFAULT NULL,
  `with_med_cert` varchar(3) DEFAULT NULL,
  `med_cert_picture` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `appointed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_clinic_request`
--

INSERT INTO `tbl_clinic_request` (`id`, `user_id`, `request_number`, `laboratory_request`, `with_med_cert`, `med_cert_picture`, `status`, `requested_at`, `appointed_at`) VALUES
(21, 2, '257808', 'Proceed to clinic', 'Yes', NULL, 'Accepted', '2024-12-23 11:47:05', '2024-12-23 19:47:00');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_deans_post_class_schedules`
--

CREATE TABLE `tbl_deans_post_class_schedules` (
  `id` int(11) NOT NULL,
  `deans_id` int(11) UNSIGNED NOT NULL,
  `school_year` varchar(50) NOT NULL,
  `semester` varchar(50) DEFAULT NULL,
  `department` varchar(100) NOT NULL,
  `year` varchar(50) NOT NULL,
  `section` varchar(255) DEFAULT NULL,
  `course` varchar(100) NOT NULL,
  `schedule_upload` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_deans_post_class_schedules`
--

INSERT INTO `tbl_deans_post_class_schedules` (`id`, `deans_id`, `school_year`, `semester`, `department`, `year`, `section`, `course`, `schedule_upload`, `created_at`, `updated_at`) VALUES
(15, 2, '2024-2025', '1st semester', 'COLLEGE OF CRIMINAL JUSTICE EDUCATION', 'I', 'A', 'BACHELOR OF SCIENCE IN CRIMINOLOGY', 'CRIM-1A 1.pdf', '2024-12-15 12:19:51', '2024-12-16 12:54:05'),
(16, 2, '2024-2025', '1st semester', 'COLLEGE OF CRIMINAL JUSTICE EDUCATION', 'II', 'A', 'BACHELOR OF SCIENCE IN CRIMINOLOGY', 'CRIM-2A 1.pdf', '2024-12-15 12:20:03', '2024-12-15 14:53:27'),
(17, 2, '2024-2025', '1st semester', 'COLLEGE OF CRIMINAL JUSTICE EDUCATION', 'III', 'A', 'BACHELOR OF SCIENCE IN CRIMINOLOGY', 'CRIM-3A 1.pdf', '2024-12-15 12:20:15', '2024-12-15 14:56:36'),
(18, 2, '2024-2025', '1st semester', 'COLLEGE OF CRIMINAL JUSTICE EDUCATION', 'IV', 'A', 'BACHELOR OF SCIENCE IN CRIMINOLOGY', 'CRIM-4A 1.pdf', '2024-12-15 12:20:28', '2024-12-15 14:56:41'),
(19, 2, '2024-2025', '1st semester', 'COLLEGE OF BUSINESS MANAGEMENT', 'I', 'A', 'BACHELOR OF SCIENCE IN BUSINESS ADMINISTRATION MAJOR IN FINANCIAL MANAGEMENT', 'BSBA-FM-1A.pdf', '2024-12-15 12:24:30', '2024-12-15 14:56:46'),
(20, 2, '2024-2025', '1st semester', 'COLLEGE OF BUSINESS MANAGEMENT', 'II', 'A', 'BACHELOR OF SCIENCE IN BUSINESS ADMINISTRATION MAJOR IN FINANCIAL MANAGEMENT', 'BSBA-FM-2A.pdf', '2024-12-15 12:24:45', '2024-12-15 14:56:53'),
(21, 2, '2024-2025', '1st semester', 'COLLEGE OF BUSINESS MANAGEMENT', 'III', 'A', 'BACHELOR OF SCIENCE IN BUSINESS ADMINISTRATION MAJOR IN FINANCIAL MANAGEMENT', 'BSBA-FM-3A.pdf', '2024-12-15 12:24:57', '2024-12-15 14:56:58'),
(22, 2, '2024-2025', '1st semester', 'COLLEGE OF BUSINESS MANAGEMENT', 'IV', 'A', 'BACHELOR OF SCIENCE IN BUSINESS ADMINISTRATION MAJOR IN FINANCIAL MANAGEMENT', 'BSBA-FM-4A.pdf', '2024-12-15 12:25:10', '2024-12-15 14:57:04'),
(23, 2, '2024-2025', '1st semester', 'COLLEGE OF ARTS AND SCIENCE', 'I', 'A', 'BACHELOR OF ARTS IN ENGLISH LANGUAGE STUDIES', 'BAEL 1A(1).pdf', '2024-12-15 12:26:12', '2024-12-15 14:57:09'),
(24, 2, '2024-2025', '1st semester', 'COLLEGE OF ARTS AND SCIENCE', 'II', 'A', 'BACHELOR OF ARTS IN ENGLISH LANGUAGE STUDIES', 'BAEL 2A.pdf', '2024-12-15 12:26:35', '2024-12-15 14:57:15'),
(25, 2, '2024-2025', '1st semester', 'COLLEGE OF ARTS AND SCIENCE', 'III', 'A', 'BACHELOR OF ARTS IN ENGLISH LANGUAGE STUDIES', 'BAEL 3A.pdf', '2024-12-15 12:26:51', '2024-12-15 14:57:21'),
(26, 2, '2024-2025', '1st semester', 'COLLEGE OF ARTS AND SCIENCE', 'IV', 'A', 'BACHELOR OF ARTS IN ENGLISH LANGUAGE STUDIES', 'BAEL 4A.pdf', '2024-12-15 12:27:04', '2024-12-15 14:57:27'),
(27, 2, '2024-2025', '1st semester', 'COLLEGE OF TEACHER EDUCATION', 'I', 'A', 'BACHELOR OF ELEMENTARY EDUCATION', 'BEED-GE-1A.pdf', '2024-12-15 12:27:47', '2024-12-15 14:57:33'),
(28, 2, '2024-2025', '1st semester', 'COLLEGE OF TEACHER EDUCATION', 'II', 'A', 'BACHELOR OF ELEMENTARY EDUCATION', 'BEED-GE-2A.pdf', '2024-12-15 12:28:06', '2024-12-15 14:57:38'),
(29, 2, '2024-2025', '1st semester', 'COLLEGE OF TEACHER EDUCATION', 'III', 'A', 'BACHELOR OF ELEMENTARY EDUCATION', 'BEED-GE-3A.pdf', '2024-12-15 12:28:24', '2024-12-15 14:57:43'),
(30, 2, '2024-2025', '1st semester', 'COLLEGE OF TEACHER EDUCATION', 'IV', 'A', 'BACHELOR OF ELEMENTARY EDUCATION', 'BEED-GE-4A 1.pdf', '2024-12-15 12:28:37', '2024-12-15 14:57:48'),
(31, 2, '2024-2025', '1st semester', 'COLLEGE OF INDUSTRIAL ENGINEERING', 'I', 'A', 'BACHELOR OF INDUSTRIAL TECHNOLOGY MAJOR IN AUTOMOTIVE TECHNOLOGY', 'BIT-AT 1A.pdf', '2024-12-16 12:43:32', '2024-12-16 12:43:32'),
(32, 2, '2024-2025', '1st semester', 'COLLEGE OF INDUSTRIAL ENGINEERING', 'II', 'A', 'BACHELOR OF INDUSTRIAL TECHNOLOGY MAJOR IN AUTOMOTIVE TECHNOLOGY', 'BIT-AT 2A.pdf', '2024-12-16 12:44:05', '2024-12-16 12:44:05'),
(33, 2, '2024-2025', '1st semester', 'COLLEGE OF INDUSTRIAL ENGINEERING', 'III', 'A', 'BACHELOR OF INDUSTRIAL TECHNOLOGY MAJOR IN AUTOMOTIVE TECHNOLOGY', 'BIT-AT 3A.pdf', '2024-12-16 12:44:33', '2024-12-16 12:44:33'),
(34, 2, '2024-2025', '1st semester', 'COLLEGE OF INDUSTRIAL ENGINEERING', 'IV', 'A', 'BACHELOR OF INDUSTRIAL TECHNOLOGY MAJOR IN AUTOMOTIVE TECHNOLOGY', 'BIT-AT 4A.pdf', '2024-12-16 12:44:48', '2024-12-16 12:44:48'),
(35, 2, '2024-2025', '1st semester', 'COLLEGE OF AGRICULTURE SCIENCES', 'I', 'A', 'BACHELOR OF SCIENCE IN AGRICULTURE', 'BSA 1A.pdf', '2024-12-16 12:45:16', '2024-12-16 12:45:16'),
(36, 2, '2024-2025', '1st semester', 'COLLEGE OF AGRICULTURE SCIENCES', 'II', 'A', 'BACHELOR OF SCIENCE IN AGRICULTURE', 'BSA 2A.pdf', '2024-12-16 12:45:31', '2024-12-16 12:45:31'),
(37, 2, '2024-2025', '1st semester', 'COLLEGE OF AGRICULTURE SCIENCES', 'III', 'A', 'BACHELOR OF SCIENCE IN AGRICULTURE', 'BSA 3A.pdf', '2024-12-16 12:45:47', '2024-12-16 12:45:47'),
(38, 2, '2024-2025', '1st semester', 'COLLEGE OF AGRICULTURE SCIENCES', 'IV', 'A', 'BACHELOR OF SCIENCE IN AGRICULTURE', 'BSA 4A.pdf', '2024-12-16 12:46:24', '2024-12-16 12:46:24');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_deans_users_issuance`
--

CREATE TABLE `tbl_deans_users_issuance` (
  `id` int(11) NOT NULL,
  `deans_id` int(11) UNSIGNED NOT NULL,
  `school_year` varchar(20) DEFAULT NULL,
  `semester` varchar(50) DEFAULT NULL,
  `schedule_upload` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_deans_users_issuance`
--

INSERT INTO `tbl_deans_users_issuance` (`id`, `deans_id`, `school_year`, `semester`, `schedule_upload`, `created_at`, `updated_at`) VALUES
(6, 2, '2024-2025', '1st semester', 'Schedule of Enrollment .pdf', '2024-12-22 04:27:02', '2024-12-22 04:27:12');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_documents`
--

CREATE TABLE `tbl_documents` (
  `id` int(11) NOT NULL,
  `type_of_documents` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_documents`
--

INSERT INTO `tbl_documents` (`id`, `type_of_documents`, `price`, `created_at`, `updated_at`) VALUES
(7, 'RF', 25.00, '2024-12-18 17:49:56', '2024-12-18 17:50:17'),
(9, 'GRADES', 25.00, '2024-12-18 17:52:45', '2024-12-18 17:52:45'),
(11, 'CAV-G', 250.00, '2024-12-18 17:52:56', '2024-12-20 05:12:02'),
(12, 'CAV-UG', 100.00, '2024-12-18 17:53:02', '2024-12-18 17:53:02'),
(13, 'Permit to cross enroll', 150.00, '2024-12-18 17:53:10', '2024-12-18 17:53:10'),
(14, 'TOR', 100.00, '2024-12-20 05:12:13', '2024-12-20 05:12:13');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_document_reports`
--

CREATE TABLE `tbl_document_reports` (
  `id` int(11) NOT NULL,
  `student_id` varchar(100) DEFAULT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `year` varchar(100) DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `gender` varchar(100) DEFAULT NULL,
  `request_number` varchar(100) DEFAULT NULL,
  `type_of_documents` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `number_of_copies` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(100) DEFAULT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `gcash_reference_number` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_document_reports`
--

INSERT INTO `tbl_document_reports` (`id`, `student_id`, `fullname`, `email`, `year`, `course`, `gender`, `request_number`, `type_of_documents`, `price`, `number_of_copies`, `total_price`, `payment_method`, `payment_proof`, `gcash_reference_number`, `status`, `created_at`, `updated_at`) VALUES
(6, '2420580', 'Russel Vincent C. Cuevas', 'russelcuevas0@gmail.com', '1', 'BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY', 'male', 'REQ1734938672', 'RF', 25.00, 2, 50.00, 'GCash', 'analytics.jpeg', '123456789', 'paid', '2024-12-23 07:24:32', '2024-12-23 07:33:47'),
(7, '2420580', 'Russel Vincent C. Cuevas', 'russelcuevas0@gmail.com', '1', 'BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY', 'male', 'REQ1734938672', 'CAV-G', 250.00, 1, 250.00, 'GCash', 'analytics.jpeg', '123456789', 'paid', '2024-12-23 07:24:32', '2024-12-23 07:33:47');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_document_request`
--

CREATE TABLE `tbl_document_request` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `documents_id` int(11) DEFAULT NULL,
  `request_number` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `student_id` varchar(100) DEFAULT NULL,
  `number_of_copies` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `gcash_reference_number` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_document_request`
--

INSERT INTO `tbl_document_request` (`id`, `user_id`, `documents_id`, `request_number`, `status`, `fullname`, `student_id`, `number_of_copies`, `total_price`, `payment_method`, `payment_proof`, `gcash_reference_number`, `created_at`, `updated_at`) VALUES
(33, 2, 7, 'REQ1734938672', 'paid', 'Russel Vincent C. Cuevas', '2420580', 2, 50.00, 'GCash', 'analytics.jpeg', '123456789', '2024-12-23 07:24:32', '2024-12-23 07:33:47'),
(34, 2, 11, 'REQ1734938672', 'paid', 'Russel Vincent C. Cuevas', '2420580', 1, 250.00, 'GCash', 'analytics.jpeg', '123456789', '2024-12-23 07:24:32', '2024-12-23 07:33:47');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_osds_post_requirements`
--

CREATE TABLE `tbl_osds_post_requirements` (
  `id` int(11) NOT NULL,
  `osds_id` int(10) UNSIGNED NOT NULL,
  `requirements_description` text NOT NULL,
  `requirements_upload` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_osds_post_requirements`
--

INSERT INTO `tbl_osds_post_requirements` (`id`, `osds_id`, `requirements_description`, `requirements_upload`, `created_at`, `updated_at`) VALUES
(4, 24, 'Admission Requirements', 'Addmission requirments osds.pdf', '2024-12-15 12:31:13', '2024-12-15 12:31:13'),
(5, 24, 'Permit for Accreditation and Re Osds', 'Issuing Permit for Accreditation and Re 0sds.pdf', '2024-12-15 12:31:54', '2024-12-15 12:31:54'),
(6, 24, 'Permit for Campus Student Osds', 'Issuing permit for in campus Student osds.pdf', '2024-12-15 12:32:19', '2024-12-15 12:32:19'),
(7, 24, 'Application for financial assistance osds', 'Application for financial assistance osds.pdf', '2024-12-15 12:32:44', '2024-12-15 12:32:44'),
(8, 24, 'Permit for Conducting Local off Campus Activities OSDS', 'Permit for Conducting Local off Campus Activities osds.pdf', '2024-12-15 12:33:23', '2024-12-15 12:33:23');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `id` int(11) NOT NULL,
  `student_id` varchar(100) DEFAULT NULL,
  `fullname` varchar(255) NOT NULL,
  `age` int(10) UNSIGNED DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `year` varchar(255) DEFAULT NULL,
  `course` varchar(255) DEFAULT NULL,
  `gender` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`id`, `student_id`, `fullname`, `age`, `email`, `password`, `year`, `course`, `gender`, `status`, `created_at`, `updated_at`) VALUES
(2, '2420580', 'Russel Vincent C. Cuevas', 22, 'russelcuevas0@gmail.com', 'f7c3bc1d808e04732adf679965ccc34ca7ae3441', '1', 'BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY', 'male', 'graduate', '2024-12-18 18:24:49', '2024-12-18 18:24:49'),
(3, '2420580', 'Russel Pogi', 22, 'russelcuevas01@gmail.com', 'f7c3bc1d808e04732adf679965ccc34ca7ae3441', '3', 'BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY', 'male', 'old', '2024-12-20 19:54:34', '2024-12-20 19:54:34'),
(8, '134561', 'Russel Cuevas', 22, 'russelgraduate1@gmail.com', 'f7c3bc1d808e04732adf679965ccc34ca7ae3441', 'graduate', 'BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY', 'male', 'graduate', '2024-12-21 08:26:00', '2024-12-21 08:26:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `tbl_clinic_request`
--
ALTER TABLE `tbl_clinic_request`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tbl_deans_post_class_schedules`
--
ALTER TABLE `tbl_deans_post_class_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deans_id` (`deans_id`);

--
-- Indexes for table `tbl_deans_users_issuance`
--
ALTER TABLE `tbl_deans_users_issuance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deans_id` (`deans_id`);

--
-- Indexes for table `tbl_documents`
--
ALTER TABLE `tbl_documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_document_reports`
--
ALTER TABLE `tbl_document_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_document_request`
--
ALTER TABLE `tbl_document_request`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `documents_id` (`documents_id`);

--
-- Indexes for table `tbl_osds_post_requirements`
--
ALTER TABLE `tbl_osds_post_requirements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `osds_id` (`osds_id`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `tbl_clinic_request`
--
ALTER TABLE `tbl_clinic_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tbl_deans_post_class_schedules`
--
ALTER TABLE `tbl_deans_post_class_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `tbl_deans_users_issuance`
--
ALTER TABLE `tbl_deans_users_issuance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_documents`
--
ALTER TABLE `tbl_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tbl_document_reports`
--
ALTER TABLE `tbl_document_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_document_request`
--
ALTER TABLE `tbl_document_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `tbl_osds_post_requirements`
--
ALTER TABLE `tbl_osds_post_requirements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_clinic_request`
--
ALTER TABLE `tbl_clinic_request`
  ADD CONSTRAINT `tbl_clinic_request_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`id`);

--
-- Constraints for table `tbl_deans_post_class_schedules`
--
ALTER TABLE `tbl_deans_post_class_schedules`
  ADD CONSTRAINT `tbl_deans_post_class_schedules_ibfk_1` FOREIGN KEY (`deans_id`) REFERENCES `tbl_admin` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_deans_users_issuance`
--
ALTER TABLE `tbl_deans_users_issuance`
  ADD CONSTRAINT `tbl_deans_users_issuance_ibfk_1` FOREIGN KEY (`deans_id`) REFERENCES `tbl_admin` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_document_request`
--
ALTER TABLE `tbl_document_request`
  ADD CONSTRAINT `tbl_document_request_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_document_request_ibfk_2` FOREIGN KEY (`documents_id`) REFERENCES `tbl_documents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_osds_post_requirements`
--
ALTER TABLE `tbl_osds_post_requirements`
  ADD CONSTRAINT `tbl_osds_post_requirements_ibfk_1` FOREIGN KEY (`osds_id`) REFERENCES `tbl_admin` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
