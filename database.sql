-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 06, 2018 at 05:06 PM
-- Server version: 5.6.38
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `amiaarfz_numu`
--

-- --------------------------------------------------------

--
-- Table structure for table `v2_artist`
--

CREATE TABLE `v2_artist` (
  `artist_id` int(11) NOT NULL,
  `artist_mbid` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_name` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disambiguation` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_updated` date NOT NULL DEFAULT '0000-00-00',
  `art` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `last_art_check` date NOT NULL DEFAULT '0000-00-00',
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `v2_artist_aka`
--

CREATE TABLE `v2_artist_aka` (
  `artist_id` int(11) NOT NULL,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `v2_fresh`
--

CREATE TABLE `v2_fresh` (
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL,
  `release_id` int(11) NOT NULL,
  `type` varchar(10) COLLATE utf8mb4_bin NOT NULL,
  `read_state` int(11) NOT NULL DEFAULT '0',
  `sent_state` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `v2_imported_artists`
--

CREATE TABLE `v2_imported_artists` (
  `user_id` int(11) NOT NULL,
  `artist_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `found_artist_id` int(11) DEFAULT '0',
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_check` date NOT NULL DEFAULT '0000-00-00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `v2_notifications_release_days`
--

CREATE TABLE `v2_notifications_release_days` (
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `v2_release_group`
--

CREATE TABLE `v2_release_group` (
  `release_id` int(11) NOT NULL,
  `artist_id` int(11) NOT NULL,
  `release_mbid` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `is_deleted` tinyint(1) NOT NULL,
  `art` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `last_updated` date NOT NULL DEFAULT '0000-00-00',
  `last_art_check` date NOT NULL DEFAULT '0000-00-00',
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `v2_users`
--

CREATE TABLE `v2_users` (
  `user_id` int(11) NOT NULL,
  `password` varchar(512) COLLATE utf8mb4_bin NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_bin NOT NULL,
  `username` varchar(10) COLLATE utf8mb4_bin NOT NULL,
  `lastfm` varchar(30) COLLATE utf8mb4_bin NOT NULL,
  `register_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `album` tinyint(1) NOT NULL DEFAULT '1',
  `single` tinyint(1) NOT NULL DEFAULT '0',
  `ep` tinyint(1) NOT NULL DEFAULT '1',
  `live` tinyint(1) NOT NULL DEFAULT '0',
  `soundtrack` tinyint(1) NOT NULL DEFAULT '0',
  `remix` tinyint(1) NOT NULL DEFAULT '0',
  `other` tinyint(1) NOT NULL DEFAULT '0',
  `subscription_end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `v2_users_purchase_history`
--

CREATE TABLE `v2_users_purchase_history` (
  `purchase_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `old_expiration_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `new_expiration_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `v2_user_artist`
--

CREATE TABLE `v2_user_artist` (
  `user_id` int(11) NOT NULL,
  `artist_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `v2_user_auth_log`
--

CREATE TABLE `v2_user_auth_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `success` tinyint(1) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `v2_user_listen`
--

CREATE TABLE `v2_user_listen` (
  `release_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `read_status` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `v2_artist`
--
ALTER TABLE `v2_artist`
  ADD PRIMARY KEY (`artist_id`),
  ADD UNIQUE KEY `mbid` (`artist_mbid`),
  ADD KEY `sort_by` (`last_updated`),
  ADD KEY `sort_name` (`sort_name`(191));

--
-- Indexes for table `v2_artist_aka`
--
ALTER TABLE `v2_artist_aka`
  ADD PRIMARY KEY (`artist_id`,`name`),
  ADD KEY `artist_id_indx` (`artist_id`);

--
-- Indexes for table `v2_fresh`
--
ALTER TABLE `v2_fresh`
  ADD PRIMARY KEY (`user_id`,`type`,`release_id`),
  ADD UNIQUE KEY `user_release_unique` (`user_id`,`release_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `release_id` (`release_id`);

--
-- Indexes for table `v2_imported_artists`
--
ALTER TABLE `v2_imported_artists`
  ADD PRIMARY KEY (`user_id`,`artist_name`),
  ADD KEY `artist_user_id_index` (`user_id`,`artist_name`);

--
-- Indexes for table `v2_notifications_release_days`
--
ALTER TABLE `v2_notifications_release_days`
  ADD PRIMARY KEY (`user_id`,`date`);

--
-- Indexes for table `v2_release_group`
--
ALTER TABLE `v2_release_group`
  ADD PRIMARY KEY (`release_id`),
  ADD UNIQUE KEY `artist_release` (`artist_id`,`release_mbid`),
  ADD KEY `artist_release_index` (`release_mbid`,`artist_id`),
  ADD KEY `release_date` (`date`),
  ADD KEY `type` (`type`),
  ADD KEY `is_deleted` (`is_deleted`);

--
-- Indexes for table `v2_users`
--
ALTER TABLE `v2_users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`),
  ADD KEY `id` (`user_id`);

--
-- Indexes for table `v2_users_purchase_history`
--
ALTER TABLE `v2_users_purchase_history`
  ADD PRIMARY KEY (`purchase_id`),
  ADD KEY `purchase_user_idx` (`user_id`);

--
-- Indexes for table `v2_user_artist`
--
ALTER TABLE `v2_user_artist`
  ADD PRIMARY KEY (`user_id`,`artist_id`),
  ADD KEY `user_artist_fk_idx` (`artist_id`),
  ADD KEY `user_artist_index` (`artist_id`,`user_id`),
  ADD KEY `user_artist_usr_id_index` (`user_id`);

--
-- Indexes for table `v2_user_auth_log`
--
ALTER TABLE `v2_user_auth_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_auth_fk_idx` (`user_id`);

--
-- Indexes for table `v2_user_listen`
--
ALTER TABLE `v2_user_listen`
  ADD PRIMARY KEY (`user_id`,`release_id`),
  ADD KEY `user_listen_release_id_idx` (`release_id`),
  ADD KEY `user_listen_index` (`user_id`,`release_id`),
  ADD KEY `user_listen_tri_index` (`release_id`,`user_id`,`read_status`),
  ADD KEY `user_read` (`user_id`,`read_status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `v2_artist`
--
ALTER TABLE `v2_artist`
  MODIFY `artist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66772;

--
-- AUTO_INCREMENT for table `v2_release_group`
--
ALTER TABLE `v2_release_group`
  MODIFY `release_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21775036;

--
-- AUTO_INCREMENT for table `v2_users`
--
ALTER TABLE `v2_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145;

--
-- AUTO_INCREMENT for table `v2_users_purchase_history`
--
ALTER TABLE `v2_users_purchase_history`
  MODIFY `purchase_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `v2_user_auth_log`
--
ALTER TABLE `v2_user_auth_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1217;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `v2_artist_aka`
--
ALTER TABLE `v2_artist_aka`
  ADD CONSTRAINT `artist_id_aka` FOREIGN KEY (`artist_id`) REFERENCES `v2_artist` (`artist_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `v2_fresh`
--
ALTER TABLE `v2_fresh`
  ADD CONSTRAINT `fresh_release_Id` FOREIGN KEY (`release_id`) REFERENCES `v2_release_group` (`release_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fresh_user_id` FOREIGN KEY (`user_id`) REFERENCES `v2_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `v2_imported_artists`
--
ALTER TABLE `v2_imported_artists`
  ADD CONSTRAINT `imported_artists_fk` FOREIGN KEY (`user_id`) REFERENCES `v2_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `v2_notifications_release_days`
--
ALTER TABLE `v2_notifications_release_days`
  ADD CONSTRAINT `release_days_fk` FOREIGN KEY (`user_id`) REFERENCES `v2_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `v2_release_group`
--
ALTER TABLE `v2_release_group`
  ADD CONSTRAINT `release_artist_fk` FOREIGN KEY (`artist_id`) REFERENCES `v2_artist` (`artist_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `v2_user_artist`
--
ALTER TABLE `v2_user_artist`
  ADD CONSTRAINT `user_artist_fk` FOREIGN KEY (`artist_id`) REFERENCES `v2_artist` (`artist_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_artist_user_fk` FOREIGN KEY (`user_id`) REFERENCES `v2_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `v2_user_auth_log`
--
ALTER TABLE `v2_user_auth_log`
  ADD CONSTRAINT `user_auth_fk` FOREIGN KEY (`user_id`) REFERENCES `v2_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `v2_user_listen`
--
ALTER TABLE `v2_user_listen`
  ADD CONSTRAINT `user_listen_release_id` FOREIGN KEY (`release_id`) REFERENCES `v2_release_group` (`release_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_listen_user_id` FOREIGN KEY (`user_id`) REFERENCES `v2_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
