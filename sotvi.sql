-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 02, 2025 at 02:11 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sotvi`
--

-- --------------------------------------------------------

--
-- Table structure for table `audiences`
--

CREATE TABLE `audiences` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `conference_id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `paper_title` varchar(255) DEFAULT NULL,
  `institution` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `country` varchar(255) NOT NULL,
  `presentation_type` varchar(255) NOT NULL,
  `paid_fee` decimal(10,2) DEFAULT NULL,
  `payment_status` enum('pending_payment','paid','cancelled','refunded') NOT NULL DEFAULT 'pending_payment',
  `payment_method` enum('transfer_bank','payment_gateway') DEFAULT NULL,
  `payment_proof_path` varchar(255) DEFAULT NULL,
  `full_paper_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audiences`
--

INSERT INTO `audiences` (`id`, `conference_id`, `first_name`, `last_name`, `paper_title`, `institution`, `email`, `phone_number`, `country`, `presentation_type`, `paid_fee`, `payment_status`, `payment_method`, `payment_proof_path`, `full_paper_path`, `created_at`, `updated_at`) VALUES
(1, 5, 'Aldo', 'Erianda', 'Pengaruh', 'PNP', 'erianda90@gmail.com', '6285263791200', 'Indonesia', 'online_author', '1000000.00', 'pending_payment', NULL, NULL, NULL, '2025-06-10 21:46:11', '2025-06-10 21:46:11'),
(2, 6, 'Hidra', 'Amnur', 'Test 123 di coba', 'PNP', 'hidra@pnp.ac.id', '08593539535', 'Malaysia', 'online_author', '100000.00', 'pending_payment', NULL, NULL, NULL, '2025-06-10 22:04:03', '2025-06-10 22:04:03'),
(3, 5, 'Rahmat', 'Hidayat', 'Test Paper', 'PNP', 'rahmat@pnp.ac.id', '34593053053', '112', 'online_author', '555000.00', 'pending_payment', NULL, NULL, NULL, '2025-06-10 22:19:41', '2025-06-10 22:19:41'),
(4, 7, 'Alde', 'Alanda', 'testing', 'PNP', 'alde@pnp.ac.id', '834583593450', '112', 'online_author', '900000.00', 'pending_payment', NULL, NULL, NULL, '2025-06-10 22:24:58', '2025-06-10 22:24:58'),
(5, 7, 'Indri', 'Rahmayuni', 'Testing', 'Test 123', 'indri@pnp.ac.id', '3240394242094', '1', 'online_author', '900000.00', 'pending_payment', NULL, NULL, NULL, '2025-06-11 00:51:40', '2025-06-11 00:51:40'),
(6, 6, 'Yance', 'Sonatha', 'belum ada judul', 'PNP', 'yance@gmail.com', '0824274817', 'Indonesia', 'online_author', '555000.00', 'pending_payment', NULL, NULL, NULL, '2025-06-11 01:24:50', '2025-06-11 01:24:50'),
(7, 6, 'Aulisa', 'Rahmi', 'belum ada judul', 'PNP', 'ica@gmail.com', '0938539583958', 'Indonesia', 'online_author', '555000.00', 'pending_payment', NULL, NULL, NULL, '2025-06-11 01:27:31', '2025-06-11 01:27:31'),
(8, 6, 'Narendra', 'Ibrahim', 'Lorem ipsum', 'PNP', 'naren@gmail.com', '320482490820384', '1', 'online_author', '555000.00', 'pending_payment', NULL, NULL, NULL, '2025-06-11 01:48:23', '2025-06-11 01:48:23'),
(9, 6, 'Rian', 'Kurnia', 'Lorem ipsum', 'PNP', 'rian@pnp.ac.id', '0234829482934', '112', 'online_author', '555000.00', 'pending_payment', NULL, NULL, 'audience_full_papers/lxrpXuP73dm1PG4q0SkO68VbmQNcmWGMuHMcsamF.docx', '2025-06-11 02:12:12', '2025-06-11 02:12:12'),
(10, 8, 'Taufik', 'Gusman', 'Resep memasak rendang gurih', 'PNP', 'taufik@pnp.ac.id', '435345935834', '261', 'online_author', '10000000.00', 'pending_payment', NULL, NULL, 'audience_full_papers/YMV8bOOq1aeSn0t1Bxj7ahaWbNibcQxIPInE9FDn.docx', '2025-06-11 02:16:13', '2025-06-11 02:16:13'),
(11, 9, 'Hidayatul', 'Ummi', 'Pengaruh kakao terhadap Hama', 'PNP', 'hidayatul@pnp.ac.id', '3453945835948', '112', 'onsite', '1000000.00', 'pending_payment', NULL, NULL, NULL, '2025-06-13 22:58:58', '2025-06-13 22:58:58'),
(12, 8, 'Tio', 'Nugros', 'test judul', 'PNP', 'tio@gmail.com', '34234234', '112', 'online_author', '10000000.00', 'pending_payment', NULL, NULL, NULL, '2025-06-18 00:01:04', '2025-06-18 00:01:04'),
(13, 7, 'Muhammad', 'Fahri', 'Test Parte', 'PNP', 'fahri@gmail.com', '3423424243', '112', 'online_author', '900000.00', 'pending_payment', NULL, NULL, NULL, '2025-06-18 00:03:03', '2025-06-18 00:03:03');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conferences`
--

CREATE TABLE `conferences` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `initial` varchar(255) DEFAULT NULL,
  `cover_poster_path` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `city` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `year` int(11) NOT NULL,
  `online_fee` decimal(10,2) NOT NULL,
  `onsite_fee` decimal(10,2) NOT NULL,
  `participant_fee` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `conferences`
--

INSERT INTO `conferences` (`id`, `name`, `initial`, `cover_poster_path`, `date`, `city`, `country`, `year`, `online_fee`, `onsite_fee`, `participant_fee`, `created_at`, `updated_at`) VALUES
(5, 'Software & Technologies, Visual Informatics & Applications (SOTVIA) Conference', 'Sotvia2025', NULL, '2025-03-05', 'Nanjing', 'China', 2025, '555000.00', '555000.00', '51000.00', '2025-06-10 11:43:36', '2025-06-10 12:05:48'),
(6, 'The 3rd Asia-Europe Conference on Applied Information Technology 2025', 'AETECH2025', NULL, '2025-06-04', 'Corfu', 'Greece', 2025, '555000.00', '555000.00', '123000.00', '2025-06-10 11:54:03', '2025-06-10 11:54:03'),
(7, 'Software & Technologies, Visual Informatics & Applications (SOTVIA) Conference', 'SOTVIA2026', NULL, '2025-06-11', 'Casablanca', 'Moroko', 2026, '900000.00', '600000.00', '70000.00', '2025-06-10 22:23:43', '2025-06-10 22:24:15'),
(8, 'The 5st 2026 Software & Technologies, Visual Informatics & Applications (SOTVIA) Conference', 'SOTVIA2027', NULL, '2027-02-12', 'Bairut', 'Kuwait', 2027, '10000000.00', '10000000.00', '500000.00', '2025-06-11 02:15:06', '2025-06-11 02:15:06'),
(9, 'Konferensi Mahasisa Politeknik Negeri Padang 2025', 'KMPNP2025', NULL, '2025-06-14', 'Padang', 'Indonesia', 2025, '1000000.00', '1000000.00', '600000.00', '2025-06-13 22:57:30', '2025-06-13 22:57:30');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_06_10_175259_add_initial_cover_date_to_conferences_table', 1),
(5, '2025_06_10_181226_create_conferences_table', 2),
(6, '2025_06_10_191717_create_registrations_table', 3),
(7, '2025_06_11_025208_create_audiences_table', 4),
(8, '2025_06_11_085805_add_payment_method_proof_to_audiences_table', 5);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `conference_id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `paper_title` varchar(255) NOT NULL,
  `institution` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `presentation_type` varchar(255) NOT NULL,
  `paid_fee` decimal(10,2) DEFAULT NULL,
  `full_paper_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('b8BYqxM2bxfGy44TKmhFtAp2YI173f7nZkHBX8fJ', NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.4 Safari/605.1.15', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVzd5TVZlMTdtZkhTTU0wRFBGa0dNZG5BOTUyb0lNOHBFSUJ5TUxnSyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9ob21lL2F1ZGllbmNlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1750238825),
('pGf4fZen7SUjeeZzwR67Fm380eE6n4yKLsoCQnKj', NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.4 Safari/605.1.15', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibnl3MVM3ZmE4SG03Rmd6dldBdkx0WERYNmVEc0wyM0xLOUtJbUJXRSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9ob21lL2F1ZGllbmNlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1749640234),
('X0RmbqcxVdVWOWC7DxV12fMDEyvEkULC6p0q1mMU', NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.4 Safari/605.1.15', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZUVWV1I0aWhBSXh3d1hsb09ja1RuR0tHSzBQR3FzSnZQN0dudzlGWCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9ob21lL2F1ZGllbmNlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1749880974),
('Xva0drYbV60oCzAuy5QniDvdoxs9pfmXB6AyuL1s', NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.4 Safari/605.1.15', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiS0xJS29VcTVBVzhhQ0VoaEFlR3hyZzlad2dKU0c4ZHZpY05jZ1JBRSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9ob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1750231040);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audiences`
--
ALTER TABLE `audiences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `audiences_email_unique` (`email`),
  ADD KEY `audiences_conference_id_foreign` (`conference_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `conferences`
--
ALTER TABLE `conferences`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `registrations_conference_id_foreign` (`conference_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audiences`
--
ALTER TABLE `audiences`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `conferences`
--
ALTER TABLE `conferences`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audiences`
--
ALTER TABLE `audiences`
  ADD CONSTRAINT `audiences_conference_id_foreign` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `registrations_conference_id_foreign` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
