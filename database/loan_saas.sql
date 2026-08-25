-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 25, 2026 at 07:45 AM
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
(1, 1, 'Gcash', 'asset', 300.00, 'active', 2, '2026-08-22 00:06:29', '2026-08-24 21:29:22'),
(2, 1, 'Maribank', 'asset', 1300.00, 'active', 2, '2026-08-22 00:28:41', '2026-08-24 21:28:17'),
(3, 1, 'Cash', 'asset', 0.00, 'active', 2, '2026-08-22 00:35:34', '2026-08-23 01:34:54');

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
(1, 1, 'BRW-20260821-84515C', 'Jerryniel', '', 'Lauronal', '', '', NULL, 'male', '', '', '', '', '', '', 0.00, 'active', '', 3, '2026-08-21 01:18:43', '2026-08-21 01:18:43'),
(2, 1, 'BRW-20260824-423BF6', 'March Shelou', 'Goc-ong', 'Ardillo', 'ardilloshelou@gmail.com', '09059626063', '2004-03-19', 'female', 'Jakosalem Street', '', '', '6000', 'Student', '', 10000.00, 'active', '', 2, '2026-08-24 21:22:54', '2026-08-24 21:22:54');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `businesses`
--

INSERT INTO `businesses` (`id`, `name`, `slug`, `email`, `phone`, `address`, `logo`, `status`, `created_at`, `updated_at`) VALUES
(1, 'ShelDohns Financial', 'sheldohns-financial', 'sheldohn@gmail.com', '09061941138', 'Jakosalem Street', NULL, 'active', '2026-08-21 00:28:50', '2026-08-21 00:28:50'),
(2, 'Secret', 'secret', NULL, NULL, NULL, NULL, 'active', '2026-08-21 01:19:55', '2026-08-21 01:19:55'),
(3, 'Dondi', 'dondi', NULL, NULL, NULL, NULL, 'active', '2026-08-22 01:31:34', '2026-08-22 01:31:34');

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
(1, 1, 'Food', NULL, 'expense', 'active', 4, '2026-08-21 09:55:37', '2026-08-23 02:52:15'),
(2, 1, 'Emergency', 'Emergency', 'loan', 'active', 2, '2026-08-23 03:05:39', '2026-08-23 03:05:39');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
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
(1, 1, 1, NULL, 1, 'LN-20260823-C46F36', 1000.00, 10.00, 'flat', 'installment', 1, 'months', 10.00, 100.00, 1110.00, '2026-08-23', '0002-09-23', 'pending', '', '', 2, '2026-08-22 23:19:00', '2026-08-22 23:19:00'),
(2, 1, 1, NULL, 1, 'LN-20260823-C0BE0D', 1000.00, 10.00, 'flat', 'installment', 1, 'months', 10.00, 100.00, 1110.00, '2026-08-23', '0002-09-23', 'pending', '', '', 2, '2026-08-22 23:22:08', '2026-08-22 23:22:08'),
(3, 1, 1, NULL, 1, 'LN-20260823-5E5A88', 5000.00, 10.00, 'flat', 'full_payment', 1, 'months', 0.00, 500.00, 5500.00, '2026-08-23', '2026-09-23', 'active', '', '', 2, '2026-08-23 01:27:51', '2026-08-24 19:45:43'),
(4, 1, 1, NULL, 1, 'LN-20260823-6DAA05', 5000.00, 10.00, 'flat', 'installment', 1, 'months', 0.00, 500.00, 5500.00, '2026-08-23', '2026-08-23', 'active', '', '', 2, '2026-08-23 01:34:54', '2026-08-23 01:50:15'),
(5, 1, 1, NULL, 2, 'LN-20260823-519AB2', 1000.00, 10.00, 'flat', 'installment', 5, 'months', 0.00, 500.00, 1500.00, '2026-08-23', '2027-01-23', 'pending', '', '', 2, '2026-08-23 03:38:40', '2026-08-23 03:38:40'),
(6, 1, 1, NULL, 2, 'LN-20260823-8C01A2', 1000.00, 10.00, 'flat', 'installment', 3, 'months', 0.00, 300.00, 1300.00, '2026-08-23', '2026-11-23', 'active', '', '', 2, '2026-08-23 03:39:10', '2026-08-24 19:32:33'),
(7, 1, 1, NULL, 2, 'LN-20260823-30CC0C', 1000.00, 10.00, 'flat', 'installment', 5, 'months', 0.00, 500.00, 1500.00, '2026-08-23', '2026-09-23', 'active', '', '', 2, '2026-08-23 03:40:45', '2026-08-24 19:33:06'),
(8, 1, 1, NULL, 2, 'LN-20260824-3913E0', 6000.00, 100.00, 'flat', 'full_payment', 1, 'months', 1000.00, 6000.00, 13000.00, '2026-08-25', '2026-09-25', 'pending', '', '', 2, '2026-08-24 19:49:35', '2026-08-24 19:49:35');

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

--
-- Dumping data for table `loan_payments`
--

INSERT INTO `loan_payments` (`id`, `business_id`, `loan_id`, `schedule_id`, `account_id`, `payment_number`, `payment_date`, `amount`, `principal_amount`, `interest_amount`, `penalty_amount`, `notes`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(2, 1, 7, 14, 2, 'PAY-20260824-C1A0BB', '2026-08-24', 300.00, 200.00, 100.00, 0.00, 'Paid', 'posted', 2, '2026-08-24 21:28:17', '2026-08-24 21:28:17'),
(3, 1, 7, 12, 1, 'PAY-20260824-72C9C0', '2026-08-24', 300.00, 200.00, 100.00, 0.00, '', 'posted', 2, '2026-08-24 21:29:21', '2026-08-24 21:29:21');

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
(1, 2, 1, '0002-09-23', 1000.00, 100.00, 1100.00, 'pending', 0.00, NULL, '2026-08-22 23:22:08', '2026-08-22 23:22:08'),
(2, 3, 1, '2026-09-23', 5000.00, 500.00, 5500.00, 'pending', 0.00, NULL, '2026-08-23 01:27:51', '2026-08-23 01:27:51'),
(3, 4, 1, '2026-08-23', 5000.00, 500.00, 5500.00, 'pending', 0.00, NULL, '2026-08-23 01:34:54', '2026-08-23 01:34:54'),
(4, 5, 1, '2027-01-23', 200.00, 100.00, 300.00, 'pending', 0.00, NULL, '2026-08-23 03:38:40', '2026-08-23 03:38:40'),
(5, 5, 2, '2027-02-23', 200.00, 100.00, 300.00, 'pending', 0.00, NULL, '2026-08-23 03:38:40', '2026-08-23 03:38:40'),
(6, 5, 3, '2027-03-23', 200.00, 100.00, 300.00, 'pending', 0.00, NULL, '2026-08-23 03:38:40', '2026-08-23 03:38:40'),
(7, 5, 4, '2027-04-23', 200.00, 100.00, 300.00, 'pending', 0.00, NULL, '2026-08-23 03:38:40', '2026-08-23 03:38:40'),
(8, 5, 5, '2027-05-23', 200.00, 100.00, 300.00, 'pending', 0.00, NULL, '2026-08-23 03:38:40', '2026-08-23 03:38:40'),
(9, 6, 1, '2026-11-23', 333.33, 100.00, 433.33, 'pending', 0.00, NULL, '2026-08-23 03:39:10', '2026-08-23 03:39:10'),
(10, 6, 2, '2026-12-23', 333.33, 100.00, 433.33, 'pending', 0.00, NULL, '2026-08-23 03:39:10', '2026-08-23 03:39:10'),
(11, 6, 3, '2027-01-23', 333.33, 100.00, 433.33, 'pending', 0.00, NULL, '2026-08-23 03:39:10', '2026-08-23 03:39:10'),
(12, 7, 1, '2026-09-23', 200.00, 100.00, 300.00, 'paid', 300.00, '2026-08-24', '2026-08-23 03:40:46', '2026-08-24 21:29:22'),
(13, 7, 2, '2026-10-23', 200.00, 100.00, 300.00, 'pending', 0.00, NULL, '2026-08-23 03:40:46', '2026-08-23 03:40:46'),
(14, 7, 3, '2026-11-23', 200.00, 100.00, 300.00, 'paid', 300.00, '2026-08-24', '2026-08-23 03:40:46', '2026-08-24 21:28:17'),
(15, 7, 4, '2026-12-23', 200.00, 100.00, 300.00, 'pending', 0.00, NULL, '2026-08-23 03:40:46', '2026-08-23 03:40:46'),
(16, 7, 5, '2027-01-23', 200.00, 100.00, 300.00, 'pending', 0.00, NULL, '2026-08-23 03:40:46', '2026-08-23 03:40:46'),
(17, 8, 1, '2026-09-25', 6000.00, 6000.00, 12000.00, 'pending', 0.00, NULL, '2026-08-24 19:49:35', '2026-08-24 19:49:35');

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

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `business_id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 1, 'system_name', 'Loan Management System', '2026-08-25 05:39:21', '2026-08-25 05:39:55'),
(2, 1, 'system_tagline', '', '2026-08-25 05:39:21', '2026-08-25 05:39:55'),
(3, 1, 'currency', 'PHP', '2026-08-25 05:39:21', '2026-08-25 05:39:55'),
(4, 1, 'currency_symbol', '₱', '2026-08-25 05:39:21', '2026-08-25 05:39:55'),
(5, 1, 'date_format', 'Y-m-d', '2026-08-25 05:39:22', '2026-08-25 05:39:55'),
(6, 1, 'timezone', 'Asia/Manila', '2026-08-25 05:39:22', '2026-08-25 05:39:55'),
(7, 1, 'primary_color', '#2563eb', '2026-08-25 05:39:22', '2026-08-25 05:39:55'),
(8, 1, 'loan_number_prefix', 'LN', '2026-08-25 05:39:22', '2026-08-25 05:39:55'),
(9, 1, 'payment_number_prefix', 'PAY', '2026-08-25 05:39:22', '2026-08-25 05:39:55'),
(10, 1, 'default_interest_type', 'flat', '2026-08-25 05:39:22', '2026-08-25 05:39:55'),
(11, 1, 'default_payment_type', 'installment', '2026-08-25 05:39:22', '2026-08-25 05:39:55'),
(12, 1, 'default_term', '1', '2026-08-25 05:39:22', '2026-08-25 05:39:55'),
(13, 1, 'default_term_period', 'months', '2026-08-25 05:39:22', '2026-08-25 05:39:55'),
(14, 1, 'default_interest_rate', '0', '2026-08-25 05:39:22', '2026-08-25 05:39:55'),
(15, 1, 'default_processing_fee', '0', '2026-08-25 05:39:22', '2026-08-25 05:39:55'),
(16, 1, 'enable_penalty', '0', '2026-08-25 05:39:22', '2026-08-25 05:39:55'),
(17, 1, 'penalty_type', 'fixed', '2026-08-25 05:39:22', '2026-08-25 05:39:55'),
(18, 1, 'penalty_rate', '0', '2026-08-25 05:39:22', '2026-08-25 05:39:55'),
(19, 1, 'penalty_amount', '0', '2026-08-25 05:39:22', '2026-08-25 05:39:55'),
(20, 1, 'overdue_reminders', '0', '2026-08-25 05:39:22', '2026-08-25 05:39:55'),
(21, 1, 'payment_reminders', '0', '2026-08-25 05:39:22', '2026-08-25 05:39:55'),
(22, 1, 'maintenance_mode', '0', '2026-08-25 05:39:22', '2026-08-25 05:39:55'),
(23, 1, 'allow_registration', '0', '2026-08-25 05:39:22', '2026-08-25 05:39:55');

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
  `status` enum('pending','approved','rejected','inactive') NOT NULL DEFAULT 'approved',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', 'superadmin@gmail.com', '$2y$10$r7wubJjlYNzs7Hwvf3UVQO6CiInOxL00.dLwHFxJK11FJiRX7xTUe', 'System Administrator', 'super_admin', 'approved', '2026-08-20 22:36:25', '2026-08-20 22:41:15'),
(2, 'mrubinos', 'mrubinos@azpired.net', '$2y$10$LkgWVckDDNPU0znd7oFXpOov73A6ZJe/Vf0LVV7VIQT.0fio2KN5a', 'Marldohn Rubinos', 'admin', 'approved', '2026-08-21 00:28:50', '2026-08-21 00:28:50'),
(3, 'sardillo', 'ardilloshelou@gmail.com', '$2y$10$hmZOuZgv/DmnSkJuJjYNN.a7jyE0r9ELnmd04dHuSK.s9zk3/Bclq', 'March Shelou Ardillo', 'staff', 'approved', '2026-08-21 01:08:35', '2026-08-24 23:22:56'),
(4, 'mardonio1104', 'marldohncrubinos11@gmail.com', '$2y$10$E2rsODQgncoWsy5sBF9LuuHn6Fq.NWlY0NumDhgf.iyLdYX1FSXoa', 'March Shelou', 'admin', 'approved', '2026-08-21 01:19:55', '2026-08-21 01:19:55'),
(5, 'drubinos', 'drubinos@gmail.com', '$2y$10$bZ78GqH5dGy4UbvpAeKcz.VZqheopHr4ZZtXsVMqsJVBWFNM5A9KO', 'Dondi Rubinos', 'admin', 'approved', '2026-08-22 01:31:34', '2026-08-22 01:31:34');

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
  ADD KEY `idx_expenses_date` (`expense_date`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `borrowers`
--
ALTER TABLE `borrowers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `loan_payments`
--
ALTER TABLE `loan_payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `loan_schedules`
--
ALTER TABLE `loan_schedules`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

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
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `fk_loans_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_loans_borrower` FOREIGN KEY (`borrower_id`) REFERENCES `borrowers` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_loans_business` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_loans_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `loan_payments`
--
ALTER TABLE `loan_payments`
  ADD CONSTRAINT `fk_loan_payments_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_loan_payments_loan` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_loan_payments_schedule` FOREIGN KEY (`schedule_id`) REFERENCES `loan_schedules` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `fk_subscriptions_business` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_subscriptions_plan` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD CONSTRAINT `fk_system_settings_business` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
