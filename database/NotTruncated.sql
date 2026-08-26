-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 26, 2026 at 12:41 PM
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
-- Database: `loan_management_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` bigint(20) UNSIGNED NOT NULL,
  `account_name` varchar(150) NOT NULL,
  `account_type` varchar(50) DEFAULT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `business_id`, `account_name`, `account_type`, `balance`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(4, 1, 'Cash', 'asset', 19000.00, 'active', 2, '2026-08-26 07:37:04', '2026-08-26 07:39:02'),
(5, 1, 'Maribank', 'asset', 2781.00, 'active', 2, '2026-08-26 07:37:28', '2026-08-26 07:39:11'),
(6, 1, 'Maribank Time Deposit', 'asset', 9500.00, 'active', 2, '2026-08-26 07:37:41', '2026-08-26 07:39:13'),
(7, 1, 'Coin Box', 'asset', 65.00, 'active', 2, '2026-08-26 07:37:52', '2026-08-26 07:39:07'),
(8, 1, 'She - Maribank', 'asset', 17017.00, 'active', 2, '2026-08-26 07:39:32', '2026-08-26 07:39:32'),
(9, 1, 'Gcash', 'asset', 3721.00, 'active', 2, '2026-08-26 07:39:45', '2026-08-26 07:39:45'),
(10, 1, 'Starting Loan Balance', 'asset', 61862.34, 'active', 2, '2026-08-26 08:25:34', '2026-08-26 10:05:09');

-- --------------------------------------------------------

--
-- Table structure for table `borrowers`
--

CREATE TABLE `borrowers` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `borrower_code` varchar(50) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `occupation` varchar(150) DEFAULT NULL,
  `employer` varchar(150) DEFAULT NULL,
  `monthly_income` decimal(15,2) DEFAULT 0.00,
  `status` enum('active','inactive','blacklisted') NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `borrowers`
--

INSERT INTO `borrowers` (`id`, `business_id`, `borrower_code`, `first_name`, `middle_name`, `last_name`, `email`, `phone`, `date_of_birth`, `gender`, `address`, `city`, `province`, `postal_code`, `occupation`, `employer`, `monthly_income`, `status`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(3, 1, 'BRW-20260826-DA248F', 'Janice', '', 'Olan-olan', '', '', NULL, 'female', '', '', '', '', 'V.A', '', 50000.00, 'active', '', 2, '2026-08-26 08:23:30', '2026-08-26 08:23:30'),
(4, 1, 'BRW-20260826-B90C3D', 'Marldohn', 'Codizar', 'Rubinos', 'marldohncrubinos11@gmail.com', '09061941138', '2002-11-04', 'male', 'Jakosalem Street', 'Cebu City', 'Cebu', '6000', 'IT Staff', 'Azpired Inc.', 16000.00, 'active', '', 2, '2026-08-26 10:04:32', '2026-08-26 10:04:32');

-- --------------------------------------------------------

--
-- Table structure for table `businesses`
--

CREATE TABLE `businesses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','suspended','pending') NOT NULL DEFAULT 'pending',
  `currency` varchar(10) NOT NULL DEFAULT 'PHP',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `businesses`
--

INSERT INTO `businesses` (`id`, `name`, `slug`, `email`, `phone`, `address`, `logo`, `status`, `currency`, `created_at`, `updated_at`) VALUES
(1, 'ShelDohns Financialss', 'sheldohns-financial', NULL, NULL, NULL, NULL, 'active', 'PHP', '2026-08-21 00:28:50', '2026-08-25 20:55:09'),
(2, 'Secret', 'secret', NULL, NULL, NULL, NULL, 'active', 'PHP', '2026-08-21 01:19:55', '2026-08-21 01:19:55'),
(3, 'Dondi', 'dondi', NULL, NULL, NULL, NULL, 'active', 'PHP', '2026-08-22 01:31:34', '2026-08-22 01:31:34');

-- --------------------------------------------------------

--
-- Table structure for table `business_users`
--

CREATE TABLE `business_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `role` enum('owner','admin','loan_officer','cashier','staff') NOT NULL DEFAULT 'staff',
  `status` enum('active','inactive','invited','suspended') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `business_users`
--

INSERT INTO `business_users` (`id`, `business_id`, `user_id`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'owner', 'active', '2026-08-21 00:28:50', '2026-08-21 00:28:50'),
(2, 1, 3, 'staff', 'active', '2026-08-21 01:08:35', '2026-08-21 01:08:35'),
(3, 2, 4, 'owner', 'active', '2026-08-21 01:19:55', '2026-08-21 01:19:55'),
(4, 3, 5, 'owner', 'active', '2026-08-22 01:31:34', '2026-08-22 01:31:34');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `type` enum('expense','loan','both') NOT NULL DEFAULT 'both',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `business_id`, `name`, `description`, `type`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(3, 1, 'Confidential', NULL, 'loan', 'active', 2, '2026-08-26 08:26:20', '2026-08-26 08:26:20');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `account_id` int(10) UNSIGNED DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `expense_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('active','void') NOT NULL DEFAULT 'active',
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` bigint(20) UNSIGNED NOT NULL,
  `borrower_id` int(10) UNSIGNED NOT NULL,
  `account_id` int(10) UNSIGNED DEFAULT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `loan_number` varchar(50) NOT NULL,
  `principal_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `interest_rate` decimal(8,2) NOT NULL DEFAULT 0.00,
  `interest_type` enum('flat','reducing_balance') NOT NULL DEFAULT 'flat',
  `payment_type` enum('installment','full_payment') NOT NULL DEFAULT 'installment',
  `term` int(11) NOT NULL DEFAULT 1,
  `term_period` enum('days','weeks','months','years') NOT NULL DEFAULT 'months',
  `processing_fee` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_interest` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_payable` decimal(15,2) NOT NULL DEFAULT 0.00,
  `release_date` date DEFAULT NULL,
  `first_payment_date` date DEFAULT NULL,
  `status` enum('pending','approved','active','completed','overdue','cancelled','rejected') NOT NULL DEFAULT 'pending',
  `purpose` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`id`, `business_id`, `borrower_id`, `account_id`, `category_id`, `loan_number`, `principal_amount`, `interest_rate`, `interest_type`, `payment_type`, `term`, `term_period`, `processing_fee`, `total_interest`, `total_payable`, `release_date`, `first_payment_date`, `status`, `purpose`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(9, 1, 3, NULL, 3, 'LN-20260826-18DF55', 50000.00, 15.00, 'flat', 'full_payment', 1, 'days', 0.00, 7500.00, 57500.00, '2026-08-15', '2026-09-30', 'active', '', '', 2, '2026-08-26 08:27:26', '2026-08-26 10:00:31'),
(10, 1, 4, NULL, 3, 'LN-20260826-58838D', 58898.00, 0.00, 'flat', 'installment', 1, 'months', 0.00, 0.00, 58898.00, NULL, NULL, 'active', '', '', 2, '2026-08-26 10:05:09', '2026-08-26 10:05:26');

-- --------------------------------------------------------

--
-- Table structure for table `loan_payments`
--

CREATE TABLE `loan_payments` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` bigint(20) UNSIGNED NOT NULL,
  `loan_id` int(10) UNSIGNED NOT NULL,
  `schedule_id` int(10) UNSIGNED DEFAULT NULL,
  `account_id` int(10) UNSIGNED NOT NULL,
  `payment_number` varchar(50) DEFAULT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `principal_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `interest_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `penalty_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `status` enum('posted','void') NOT NULL DEFAULT 'posted',
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_penalties`
--

CREATE TABLE `loan_penalties` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` bigint(20) UNSIGNED NOT NULL,
  `loan_id` bigint(20) UNSIGNED NOT NULL,
  `schedule_id` bigint(20) UNSIGNED NOT NULL,
  `penalty_type` enum('fixed','percentage') NOT NULL,
  `penalty_base` enum('principal','total_due','overdue_amount') DEFAULT NULL,
  `rate` decimal(15,2) NOT NULL DEFAULT 0.00,
  `base_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `penalty_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `reason` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_schedules`
--

CREATE TABLE `loan_schedules` (
  `id` int(10) UNSIGNED NOT NULL,
  `loan_id` int(10) UNSIGNED NOT NULL,
  `installment_number` int(11) NOT NULL,
  `due_date` date NOT NULL,
  `principal_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `interest_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_due` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','paid','partial','overdue','cancelled') NOT NULL DEFAULT 'pending',
  `paid_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `paid_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `loan_schedules`
--

INSERT INTO `loan_schedules` (`id`, `loan_id`, `installment_number`, `due_date`, `principal_amount`, `interest_amount`, `total_due`, `status`, `paid_amount`, `paid_date`, `created_at`, `updated_at`) VALUES
(18, 9, 1, '2026-09-30', 50000.00, 7500.00, 57500.00, 'pending', 0.00, NULL, '2026-08-26 08:27:26', '2026-08-26 08:27:26'),
(19, 10, 1, '2026-08-26', 58898.00, 0.00, 58898.00, 'pending', 0.00, NULL, '2026-08-26 10:05:09', '2026-08-26 10:05:09');

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `billing_cycle` enum('monthly','yearly','lifetime') NOT NULL DEFAULT 'monthly',
  `max_users` int(10) UNSIGNED DEFAULT NULL,
  `max_borrowers` int(10) UNSIGNED DEFAULT NULL,
  `max_active_loans` int(10) UNSIGNED DEFAULT NULL,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`id`, `name`, `slug`, `description`, `price`, `billing_cycle`, `max_users`, `max_borrowers`, `max_active_loans`, `features`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Starter', 'starter', 'Basic loan management for small lending businesses.', 0.00, 'monthly', 2, 500, 100, NULL, 'active', '2026-08-20 22:20:11', '2026-08-20 22:20:11'),
(2, 'Professional', 'professional', 'Advanced loan management for growing lending businesses.', 999.00, 'monthly', 10, 5000, 1000, NULL, 'active', '2026-08-20 22:20:11', '2026-08-20 22:20:11'),
(3, 'Enterprise', 'enterprise', 'Full loan management solution for larger organizations.', 2499.00, 'monthly', NULL, NULL, NULL, NULL, 'active', '2026-08-20 22:20:11', '2026-08-20 22:20:11');

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` bigint(20) UNSIGNED NOT NULL,
  `plan_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('trial','active','past_due','cancelled','expired') NOT NULL DEFAULT 'trial',
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `trial_ends_at` datetime DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` bigint(20) UNSIGNED NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `role` enum('super_admin','admin','staff') NOT NULL DEFAULT 'staff',
  `status` enum('pending','approved','rejected','inactive','locked') NOT NULL DEFAULT 'approved',
  `failed_login_attempts` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `status`, `failed_login_attempts`, `locked_until`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', 'superadmin@gmail.com', '$2y$10$r7wubJjlYNzs7Hwvf3UVQO6CiInOxL00.dLwHFxJK11FJiRX7xTUe', 'System Administrator', 'super_admin', '', 0, NULL, '2026-08-20 22:36:25', '2026-08-25 16:21:00'),
(2, 'mrubinos', 'mrubinos@azpired.net', '$2y$10$LkgWVckDDNPU0znd7oFXpOov73A6ZJe/Vf0LVV7VIQT.0fio2KN5a', 'Marldohn Rubinos', 'admin', 'approved', 0, NULL, '2026-08-21 00:28:50', '2026-08-25 17:24:58'),
(3, 'sardillo', 'ardilloshelou@gmail.com', '$2y$10$hmZOuZgv/DmnSkJuJjYNN.a7jyE0r9ELnmd04dHuSK.s9zk3/Bclq', 'March Shelou Ardillo', 'staff', 'approved', 0, NULL, '2026-08-21 01:08:35', '2026-08-24 23:22:56'),
(4, 'mardonio1104', 'marldohncrubinos11@gmail.com', '$2y$10$E2rsODQgncoWsy5sBF9LuuHn6Fq.NWlY0NumDhgf.iyLdYX1FSXoa', 'March Shelou', 'admin', 'approved', 0, NULL, '2026-08-21 01:19:55', '2026-08-21 01:19:55'),
(5, 'drubinos', 'drubinos@gmail.com', '$2y$10$bZ78GqH5dGy4UbvpAeKcz.VZqheopHr4ZZtXsVMqsJVBWFNM5A9KO', 'Dondi Rubinos', 'admin', 'approved', 0, NULL, '2026-08-22 01:31:34', '2026-08-22 01:31:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_accounts_business` (`business_id`),
  ADD KEY `idx_accounts_created_by` (`created_by`),
  ADD KEY `idx_accounts_status` (`status`);

--
-- Indexes for table `borrowers`
--
ALTER TABLE `borrowers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_borrower_business_code` (`business_id`,`borrower_code`),
  ADD KEY `idx_borrowers_business` (`business_id`),
  ADD KEY `idx_borrowers_name` (`last_name`,`first_name`),
  ADD KEY `idx_borrowers_email` (`email`),
  ADD KEY `idx_borrowers_phone` (`phone`),
  ADD KEY `idx_borrowers_created_by` (`created_by`),
  ADD KEY `idx_borrowers_status` (`status`);

--
-- Indexes for table `businesses`
--
ALTER TABLE `businesses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_business_status` (`status`),
  ADD KEY `idx_business_slug` (`slug`);

--
-- Indexes for table `business_users`
--
ALTER TABLE `business_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_business_user` (`business_id`,`user_id`),
  ADD KEY `idx_business_users_business` (`business_id`),
  ADD KEY `idx_business_users_user` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_category_name_type` (`name`,`type`),
  ADD KEY `fk_categories_created_by` (`created_by`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_expenses_business` (`business_id`),
  ADD KEY `idx_expenses_category` (`category_id`),
  ADD KEY `idx_expenses_created_by` (`created_by`),
  ADD KEY `idx_expenses_date` (`expense_date`),
  ADD KEY `idx_expenses_account_id` (`account_id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_loan_number` (`loan_number`),
  ADD KEY `idx_borrower_id` (`borrower_id`),
  ADD KEY `idx_category_id` (`category_id`),
  ADD KEY `idx_created_by` (`created_by`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_loans_business_id` (`business_id`),
  ADD KEY `idx_loans_account_id` (`account_id`);

--
-- Indexes for table `loan_payments`
--
ALTER TABLE `loan_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_loan_payments_business_id` (`business_id`),
  ADD KEY `idx_loan_payments_loan_id` (`loan_id`),
  ADD KEY `idx_loan_payments_schedule_id` (`schedule_id`),
  ADD KEY `idx_loan_payments_account_id` (`account_id`),
  ADD KEY `idx_loan_payments_payment_date` (`payment_date`),
  ADD KEY `idx_loan_payments_status` (`status`);

--
-- Indexes for table `loan_penalties`
--
ALTER TABLE `loan_penalties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_penalties_business` (`business_id`),
  ADD KEY `idx_penalties_loan` (`loan_id`),
  ADD KEY `idx_penalties_schedule` (`schedule_id`);

--
-- Indexes for table `loan_schedules`
--
ALTER TABLE `loan_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_loan_schedules_loan_id` (`loan_id`),
  ADD KEY `idx_loan_schedules_due_date` (`due_date`),
  ADD KEY `idx_loan_schedules_status` (`status`);

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `unique_plan_slug` (`slug`),
  ADD KEY `idx_plans_status` (`status`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_subscriptions_business` (`business_id`),
  ADD KEY `idx_subscriptions_plan` (`plan_id`),
  ADD KEY `idx_subscriptions_status` (`status`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_business_setting` (`business_id`,`setting_key`),
  ADD KEY `idx_business_id` (`business_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `borrowers`
--
ALTER TABLE `borrowers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `businesses`
--
ALTER TABLE `businesses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `business_users`
--
ALTER TABLE `business_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `loan_payments`
--
ALTER TABLE `loan_payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `loan_penalties`
--
ALTER TABLE `loan_penalties`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loan_schedules`
--
ALTER TABLE `loan_schedules`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=525;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `fk_accounts_business` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_accounts_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `business_users`
--
ALTER TABLE `business_users`
  ADD CONSTRAINT `fk_business_users_business` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_business_users_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_categories_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `fk_expenses_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `fk_loans_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_loans_borrower` FOREIGN KEY (`borrower_id`) REFERENCES `borrowers` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_loans_business` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_loans_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
