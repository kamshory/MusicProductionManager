-- phpMyAdmin SQL Dump
-- version 4.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 22, 2024 at 05:58 PM
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
  `producer_id` varchar(40) DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `number_of_song` int(11) DEFAULT NULL,
  `duration` float DEFAULT NULL,
  `image_path` text,
  `sort_order` int(11) DEFAULT NULL,
  `time_create` timestamp NULL DEFAULT NULL,
  `time_edit` timestamp NULL DEFAULT NULL,
  `admin_create` varchar(40) DEFAULT NULL,
  `admin_edit` varchar(40) DEFAULT NULL,
  `ip_create` varchar(50) DEFAULT NULL,
  `ip_edit` varchar(50) DEFAULT NULL,
  `locked` tinyint(1) DEFAULT '0',
  `as_draft` tinyint(1) DEFAULT '1',
  `active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `article`
--

CREATE TABLE IF NOT EXISTS `article` (
  `article_id` varchar(40) NOT NULL,
  `type` varchar(20) DEFAULT NULL,
  `title` text,
  `content` longtext,
  `time_create` timestamp NULL DEFAULT NULL,
  `time_edit` timestamp NULL DEFAULT NULL,
  `admin_create` varchar(40) DEFAULT NULL,
  `admin_edit` varchar(40) DEFAULT NULL,
  `ip_create` varchar(50) DEFAULT NULL,
  `ip_edit` varchar(50) DEFAULT NULL,
  `draft` tinyint(1) DEFAULT '1',
  `active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `phone` varchar(50) DEFAULT NULL,
  `phone2` varchar(50) DEFAULT NULL,
  `phone3` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `email2` varchar(100) DEFAULT NULL,
  `email3` varchar(100) DEFAULT NULL,
  `website` text,
  `address` text,
  `picture` tinyint(1) DEFAULT NULL,
  `image_path` text,
  `image_update` timestamp NULL DEFAULT NULL,
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
-- Table structure for table `draft_rating`
--

CREATE TABLE IF NOT EXISTS `draft_rating` (
  `draft_rating_id` varchar(40) NOT NULL,
  `user_id` varchar(40) DEFAULT NULL,
  `song_draft_id` varchar(40) DEFAULT NULL,
  `rating` float DEFAULT NULL,
  `time_create` timestamp NULL DEFAULT NULL,
  `time_edit` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `genre`
--

CREATE TABLE IF NOT EXISTS `genre` (
  `genre_id` varchar(50) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `image_path` text,
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
-- Table structure for table `producer`
--

CREATE TABLE IF NOT EXISTS `producer` (
  `producer_id` varchar(40) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `gender` varchar(2) DEFAULT NULL,
  `birth_day` date DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `phone2` varchar(50) DEFAULT NULL,
  `phone3` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `email2` varchar(100) DEFAULT NULL,
  `email3` varchar(100) DEFAULT NULL,
  `website` text,
  `address` text,
  `picture` tinyint(1) DEFAULT NULL,
  `image_path` text,
  `image_update` timestamp NULL DEFAULT NULL,
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
-- Table structure for table `rating`
--

CREATE TABLE IF NOT EXISTS `rating` (
  `rating_id` varchar(40) NOT NULL,
  `user_id` varchar(40) DEFAULT NULL,
  `song_id` varchar(40) DEFAULT NULL,
  `rating` float DEFAULT NULL,
  `time_create` timestamp NULL DEFAULT NULL,
  `time_edit` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `subtitle` text,
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
  `name` varchar(100) DEFAULT NULL,
  `title` text,
  `album_id` varchar(50) DEFAULT NULL,
  `track_number` int(11) DEFAULT NULL,
  `producer_id` varchar(40) DEFAULT NULL,
  `artist_vocalist` varchar(50) DEFAULT NULL,
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
  `file_path_midi` text,
  `last_upload_time_midi` timestamp NULL DEFAULT NULL,
  `file_path_xml` text,
  `last_upload_time_xml` timestamp NULL DEFAULT NULL,
  `file_path_pdf` text,
  `last_upload_time_pdf` timestamp NULL DEFAULT NULL,
  `duration` float DEFAULT NULL,
  `genre_id` varchar(50) DEFAULT NULL,
  `bpm` float DEFAULT NULL,
  `time_signature` varchar(40) DEFAULT NULL,
  `subtitle` longtext,
  `subtitle_complete` tinyint(1) DEFAULT '0',
  `lyric_midi` longtext,
  `lyric_midi_raw` longtext,
  `vocal` tinyint(1) DEFAULT '0',
  `instrument` longtext,
  `midi_vocal_channel` int(11) DEFAULT NULL,
  `rating` float DEFAULT NULL,
  `comment` longtext,
  `image_path` text,
  `last_upload_time_image` timestamp NULL DEFAULT NULL,
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
-- Table structure for table `song_copy`
--

CREATE TABLE IF NOT EXISTS `song_copy` (
  `song_id` varchar(50) NOT NULL,
  `random_song_id` varchar(50) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `title` text,
  `album_id` varchar(50) DEFAULT NULL,
  `track_number` int(11) DEFAULT NULL,
  `producer_id` varchar(40) DEFAULT NULL,
  `artist_vocalist` varchar(50) DEFAULT NULL,
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
  `file_path_midi` text,
  `last_upload_time_midi` timestamp NULL DEFAULT NULL,
  `file_path_xml` text,
  `last_upload_time_xml` timestamp NULL DEFAULT NULL,
  `file_path_pdf` text,
  `last_upload_time_pdf` timestamp NULL DEFAULT NULL,
  `duration` float DEFAULT NULL,
  `genre_id` varchar(50) DEFAULT NULL,
  `bpm` float DEFAULT NULL,
  `time_signature` varchar(40) DEFAULT NULL,
  `subtitle` longtext,
  `subtitle_complete` tinyint(1) DEFAULT '0',
  `lyric_midi` longtext,
  `lyric_midi_raw` longtext,
  `vocal` tinyint(1) DEFAULT '0',
  `instrument` longtext,
  `midi_vocal_channel` int(11) DEFAULT NULL,
  `rating` float DEFAULT NULL,
  `comment` longtext,
  `image_path` text,
  `last_upload_time_image` timestamp NULL DEFAULT NULL,
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
-- Table structure for table `song_draft`
--

CREATE TABLE IF NOT EXISTS `song_draft` (
  `song_draft_id` varchar(40) NOT NULL,
  `parent_id` varchar(40) DEFAULT NULL,
  `random_id` varchar(40) DEFAULT NULL,
  `artist_id` varchar(40) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `title` text,
  `lyric` longtext,
  `rating` float DEFAULT NULL,
  `duration` float DEFAULT NULL,
  `file_path` text,
  `file_size` bigint(20) DEFAULT NULL,
  `sha1_file` varchar(40) NOT NULL,
  `read_count` int(11) NOT NULL DEFAULT '0',
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
-- Table structure for table `song_draft_comment`
--

CREATE TABLE IF NOT EXISTS `song_draft_comment` (
  `song_draft_comment_id` varchar(40) NOT NULL,
  `song_draft_id` varchar(40) DEFAULT NULL,
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
-- Table structure for table `song_update_history`
--

CREATE TABLE IF NOT EXISTS `song_update_history` (
  `song_update_history_id` varchar(40) NOT NULL,
  `song_id` varchar(40) DEFAULT NULL,
  `user_id` varchar(40) DEFAULT NULL,
  `user_activity_id` varchar(40) DEFAULT NULL,
  `action` varchar(20) DEFAULT NULL,
  `time_update` timestamp NULL DEFAULT NULL,
  `ip_update` varchar(50) DEFAULT NULL
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
  `time_zone` varchar(255) DEFAULT NULL,
  `user_type_id` varchar(40) DEFAULT NULL,
  `associated_artist` varchar(40) DEFAULT NULL,
  `associated_producer` varchar(40) DEFAULT NULL,
  `current_role` varchar(40) DEFAULT NULL,
  `image_path` text,
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
-- Table structure for table `user_activity`
--

CREATE TABLE IF NOT EXISTS `user_activity` (
  `user_activity_id` varchar(40) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `user_id` varchar(40) DEFAULT NULL,
  `path` text,
  `method` varchar(10) DEFAULT NULL,
  `get_data` longtext,
  `post_data` longtext,
  `request_body` longtext,
  `time_create` timestamp NULL DEFAULT NULL,
  `ip_create` varchar(50) DEFAULT NULL
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
-- Indexes for table `draft_rating`
--
ALTER TABLE `draft_rating`
  ADD PRIMARY KEY (`draft_rating_id`);

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
-- Indexes for table `producer`
--
ALTER TABLE `producer`
  ADD PRIMARY KEY (`producer_id`);

--
-- Indexes for table `rating`
--
ALTER TABLE `rating`
  ADD PRIMARY KEY (`rating_id`);

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
-- Indexes for table `song_copy`
--
ALTER TABLE `song_copy`
  ADD PRIMARY KEY (`song_id`);

--
-- Indexes for table `song_draft`
--
ALTER TABLE `song_draft`
  ADD PRIMARY KEY (`song_draft_id`);

--
-- Indexes for table `song_draft_comment`
--
ALTER TABLE `song_draft_comment`
  ADD PRIMARY KEY (`song_draft_comment_id`);

--
-- Indexes for table `song_update_history`
--
ALTER TABLE `song_update_history`
  ADD PRIMARY KEY (`song_update_history_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_activity`
--
ALTER TABLE `user_activity`
  ADD PRIMARY KEY (`user_activity_id`);

--
-- Indexes for table `user_type`
--
ALTER TABLE `user_type`
  ADD PRIMARY KEY (`user_type_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
