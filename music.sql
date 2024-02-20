-- phpMyAdmin SQL Dump
-- version 4.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 19, 2024 at 07:39 AM
-- Server version: 5.5.68-MariaDB
-- PHP Version: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `music`
--

-- --------------------------------------------------------

--
-- Table structure for table `album`
--

CREATE TABLE IF NOT EXISTS `album` (
  `album_id` varchar(50) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `description` longtext,
  `release_date` date DEFAULT NULL,
  `number_of_song` int(11) DEFAULT NULL,
  `duration` float DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `time_create` timestamp NULL DEFAULT NULL,
  `time_edit` timestamp NULL DEFAULT NULL,
  `admin_create` varchar(40) DEFAULT NULL,
  `admin_edit` varchar(40) DEFAULT NULL,
  `ip_create` varchar(50) DEFAULT NULL,
  `ip_edit` varchar(50) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `as_draft` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `artist`
--

CREATE TABLE IF NOT EXISTS `artist` (
  `artist_id` varchar(40) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `stage_name` varchar(100) DEFAULT NULL,
  `gender` varchar(2) DEFAULT NULL,
  `birth_day` date DEFAULT NULL,
  `time_create` timestamp NULL DEFAULT NULL,
  `time_edit` timestamp NULL DEFAULT NULL,
  `admin_create` varchar(40) DEFAULT NULL,
  `admin_edit` varchar(40) DEFAULT NULL,
  `ip_create` varchar(50) DEFAULT NULL,
  `ip_edit` varchar(50) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `genre`
--

CREATE TABLE IF NOT EXISTS `genre` (
  `genre_id` varchar(50) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `time_create` timestamp NULL DEFAULT NULL,
  `time_edit` timestamp NULL DEFAULT NULL,
  `admin_create` varchar(40) DEFAULT NULL,
  `admin_edit` varchar(40) DEFAULT NULL,
  `ip_create` varchar(50) DEFAULT NULL,
  `ip_edit` varchar(50) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `midi`
--

CREATE TABLE IF NOT EXISTS `midi` (
  `midi_id` varchar(50) NOT NULL,
  `random_midi_id` varchar(50) DEFAULT NULL,
  `title` text,
  `album_id` varchar(50) DEFAULT NULL,
  `artist_vocal` varchar(50) DEFAULT NULL,
  `artist_composer` varchar(50) DEFAULT NULL,
  `artist_arranger` varchar(50) DEFAULT NULL,
  `file_path` text,
  `file_name` varchar(100) DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `file_extension` varchar(20) DEFAULT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `file_md5` varchar(32) DEFAULT NULL,
  `file_upload_time` timestamp NULL DEFAULT NULL,
  `duration` float DEFAULT NULL,
  `genre_id` varchar(50) DEFAULT NULL,
  `lyric` longtext,
  `comment` longtext,
  `time_create` timestamp NULL DEFAULT NULL,
  `time_edit` timestamp NULL DEFAULT NULL,
  `ip_create` varchar(50) DEFAULT NULL,
  `ip_edit` varchar(50) DEFAULT NULL,
  `admin_create` varchar(50) DEFAULT NULL,
  `admin_edit` varchar(50) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `reference`
--

CREATE TABLE IF NOT EXISTS `reference` (
  `reference_id` varchar(50) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `genre_id` varchar(50) DEFAULT NULL,
  `album` varchar(255) DEFAULT NULL,
  `artist_id` varchar(50) DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `url` text,
  `url_provider` varchar(100) DEFAULT NULL,
  `lyric` text,
  `description` longtext,
  `time_create` timestamp NULL DEFAULT NULL,
  `time_edit` timestamp NULL DEFAULT NULL,
  `ip_create` varchar(50) DEFAULT NULL,
  `ip_edit` varchar(50) DEFAULT NULL,
  `admin_create` varchar(50) DEFAULT NULL,
  `admin_edit` varchar(50) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `song`
--

CREATE TABLE IF NOT EXISTS `song` (
  `song_id` varchar(50) NOT NULL,
  `random_song_id` varchar(50) DEFAULT NULL,
  `title` text,
  `album_id` varchar(50) DEFAULT NULL,
  `track_number` int(11) DEFAULT NULL,
  `artist_vocal` varchar(50) DEFAULT NULL,
  `artist_composer` varchar(50) DEFAULT NULL,
  `artist_arranger` varchar(50) DEFAULT NULL,
  `file_path` text,
  `file_name` varchar(100) DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `file_extension` varchar(20) DEFAULT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `file_md5` varchar(32) DEFAULT NULL,
  `file_upload_time` timestamp NULL DEFAULT NULL,
  `first_upload_time` timestamp NULL DEFAULT NULL,
  `last_upload_time` timestamp NULL DEFAULT NULL,
  `last_upload_time_midi` timestamp NULL DEFAULT NULL,
  `last_upload_time_xml` timestamp NULL DEFAULT NULL,
  `last_upload_time_pdf` timestamp NULL DEFAULT NULL,
  `file_path_midi` text,
  `file_path_xml` text,
  `file_path_pdf` text,
  `duration` float DEFAULT NULL,
  `genre_id` varchar(50) DEFAULT NULL,
  `lyric` longtext,
  `lyric_complete` tinyint(1) DEFAULT '0',
  `lyric_midi` longtext,
  `lyric_midi_raw` longtext,
  `vocal` tinyint(1) DEFAULT '0',
  `instrument` longtext,
  `rating` int(11) DEFAULT NULL,
  `comment` longtext,
  `time_create` timestamp NULL DEFAULT NULL,
  `time_edit` timestamp NULL DEFAULT NULL,
  `ip_create` varchar(50) DEFAULT NULL,
  `ip_edit` varchar(50) DEFAULT NULL,
  `admin_create` varchar(50) DEFAULT NULL,
  `admin_edit` varchar(50) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `song_attachment`
--

CREATE TABLE IF NOT EXISTS `song_attachment` (
  `song_attachment_id` varchar(40) NOT NULL,
  `song_id` varchar(40) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `path` text,
  `file_size` bigint(20) DEFAULT NULL,
  `time_create` timestamp NULL DEFAULT NULL,
  `time_edit` timestamp NULL DEFAULT NULL,
  `admin_create` varchar(40) DEFAULT NULL,
  `admin_edit` varchar(40) DEFAULT NULL,
  `ip_create` varchar(50) DEFAULT NULL,
  `ip_edit` varchar(50) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `song_comment`
--

CREATE TABLE IF NOT EXISTS `song_comment` (
  `song_comment_id` varchar(40) NOT NULL,
  `song_id` varchar(40) DEFAULT NULL,
  `comment` longtext,
  `time_create` timestamp NULL DEFAULT NULL,
  `time_edit` timestamp NULL DEFAULT NULL,
  `admin_create` varchar(40) DEFAULT NULL,
  `admin_edit` varchar(40) DEFAULT NULL,
  `ip_create` varchar(50) DEFAULT NULL,
  `ip_edit` varchar(50) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` varchar(40) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `admin` tinyint(1) DEFAULT '0',
  `name` varchar(100) DEFAULT NULL,
  `birth_day` varchar(100) DEFAULT NULL,
  `gender` varchar(2) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `user_type_id` varchar(40) DEFAULT NULL,
  `associated_artist` varchar(40) DEFAULT NULL,
  `time_create` timestamp NULL DEFAULT NULL,
  `time_edit` timestamp NULL DEFAULT NULL,
  `admin_create` varchar(40) DEFAULT NULL,
  `admin_edit` varchar(40) DEFAULT NULL,
  `ip_create` varchar(50) DEFAULT NULL,
  `ip_edit` varchar(50) DEFAULT NULL,
  `reset_password_hash` varchar(256) DEFAULT NULL,
  `last_reset_password` timestamp NULL DEFAULT NULL,
  `blocked` tinyint(1) DEFAULT '0',
  `active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_type`
--

CREATE TABLE IF NOT EXISTS `user_type` (
  `user_type_id` varchar(50) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `admin` tinyint(1) DEFAULT '0',
  `sort_order` int(11) DEFAULT NULL,
  `time_create` timestamp NULL DEFAULT NULL,
  `time_edit` timestamp NULL DEFAULT NULL,
  `admin_create` varchar(40) DEFAULT NULL,
  `admin_edit` varchar(40) DEFAULT NULL,
  `ip_create` varchar(50) DEFAULT NULL,
  `ip_edit` varchar(50) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `album`
--
ALTER TABLE `album`
  ADD PRIMARY KEY (`album_id`);

--
-- Indexes for table `artist`
--
ALTER TABLE `artist`
  ADD PRIMARY KEY (`artist_id`);

--
-- Indexes for table `genre`
--
ALTER TABLE `genre`
  ADD PRIMARY KEY (`genre_id`);

--
-- Indexes for table `midi`
--
ALTER TABLE `midi`
  ADD PRIMARY KEY (`midi_id`);

--
-- Indexes for table `song`
--
ALTER TABLE `song`
  ADD PRIMARY KEY (`song_id`);

--
-- Indexes for table `song_attachment`
--
ALTER TABLE `song_attachment`
  ADD PRIMARY KEY (`song_attachment_id`);

--
-- Indexes for table `song_comment`
--
ALTER TABLE `song_comment`
  ADD PRIMARY KEY (`song_comment_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_type`
--
ALTER TABLE `user_type`
  ADD PRIMARY KEY (`user_type_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
