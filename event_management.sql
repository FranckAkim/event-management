-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 07, 2026 at 10:50 PM
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
-- Database: `event_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `BookingID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `VenueID` int(11) NOT NULL,
  `EventID` int(11) DEFAULT NULL,
  `Status` varchar(20) NOT NULL DEFAULT 'PENDING',
  `RequestedAt` datetime NOT NULL DEFAULT current_timestamp(),
  `DepositAmount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Notes` text DEFAULT NULL
) ;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`BookingID`, `UserID`, `VenueID`, `EventID`, `Status`, `RequestedAt`, `DepositAmount`, `Notes`) VALUES
(1, 3, 1, 178, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(2, 7, 1, 178, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(3, 8, 1, 178, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(4, 54, 1, 178, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(5, 55, 1, 178, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(6, 56, 1, 178, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(7, 57, 1, 178, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(8, 58, 1, 178, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(9, 59, 1, 178, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(10, 60, 1, 178, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(11, 61, 1, 178, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(12, 62, 1, 178, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(13, 63, 1, 178, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(14, 64, 1, 178, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(15, 65, 1, 178, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(16, 3, 2, 180, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(17, 7, 2, 180, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(18, 8, 2, 180, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(19, 54, 2, 180, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(20, 55, 2, 180, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(21, 56, 2, 180, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(22, 57, 2, 180, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(23, 58, 2, 180, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(24, 59, 2, 180, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(25, 60, 2, 180, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(26, 61, 2, 180, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(27, 62, 2, 180, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(28, 63, 2, 180, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(31, 3, 5, 183, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(32, 7, 5, 183, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(33, 8, 5, 183, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(34, 54, 5, 183, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(35, 55, 5, 183, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(36, 56, 5, 183, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(37, 57, 5, 183, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(38, 58, 5, 183, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(39, 59, 5, 183, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(40, 60, 5, 183, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(41, 61, 5, 183, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(42, 62, 5, 183, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(43, 63, 5, 183, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(44, 64, 5, 183, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(45, 65, 5, 183, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(46, 3, 11, 186, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(47, 7, 11, 186, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(48, 8, 11, 186, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(49, 54, 11, 186, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(50, 55, 11, 186, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(53, 3, 1, 187, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(54, 7, 1, 187, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(55, 8, 1, 187, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(56, 54, 1, 187, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(57, 55, 1, 187, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(58, 56, 1, 187, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(59, 57, 1, 187, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(60, 58, 1, 187, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(61, 59, 1, 187, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(62, 60, 1, 187, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(63, 61, 1, 187, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(64, 62, 1, 187, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(65, 63, 1, 187, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(66, 64, 1, 187, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(68, 3, 5, 188, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(69, 7, 5, 188, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(70, 8, 5, 188, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(71, 54, 5, 188, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(72, 55, 5, 188, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(73, 56, 5, 188, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(74, 57, 5, 188, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(75, 3, 2, 189, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(76, 7, 2, 189, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(77, 8, 2, 189, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(78, 54, 2, 189, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(79, 55, 2, 189, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(80, 56, 2, 189, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(82, 3, 1, 191, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(83, 7, 1, 191, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(84, 8, 1, 191, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(85, 54, 1, 191, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(86, 55, 1, 191, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(87, 56, 1, 191, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(88, 57, 1, 191, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(89, 58, 1, 191, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(90, 59, 1, 191, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(91, 60, 1, 191, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(92, 61, 1, 191, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(93, 62, 1, 191, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(94, 63, 1, 191, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(95, 64, 1, 191, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(96, 65, 1, 191, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(97, 3, 5, 196, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(98, 7, 5, 196, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(99, 8, 5, 196, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(100, 54, 5, 196, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(101, 55, 5, 196, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(102, 56, 5, 196, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(103, 57, 5, 196, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(104, 58, 5, 196, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(105, 59, 5, 196, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(106, 60, 5, 196, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(107, 61, 5, 196, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(108, 62, 5, 196, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(109, 63, 5, 196, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(110, 64, 5, 196, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(111, 65, 5, 196, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(112, 66, 5, 196, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(113, 67, 5, 196, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(114, 68, 5, 196, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(128, 3, 1, 197, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(129, 7, 1, 197, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(130, 8, 1, 197, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(131, 54, 1, 197, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(132, 55, 1, 197, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(133, 56, 1, 197, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(134, 57, 1, 197, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(135, 58, 1, 197, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(136, 59, 1, 197, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(137, 60, 1, 197, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(138, 61, 1, 197, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(139, 62, 1, 197, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(140, 63, 1, 197, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(141, 64, 1, 197, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(142, 65, 1, 197, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(143, 54, 3, 190, 'PENDING', '2026-04-07 03:59:18', 0.00, ''),
(144, 55, 3, 190, 'PENDING', '2026-04-07 03:59:18', 0.00, ''),
(145, 56, 4, 193, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(146, 57, 4, 193, 'CANCELLED', '2026-04-07 03:59:18', 0.00, ''),
(147, 58, 13, 194, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(148, 59, 12, 195, 'PENDING', '2026-04-07 03:59:18', 0.00, ''),
(149, 60, 2, 200, 'APPROVED', '2026-04-07 03:59:18', 0.00, ''),
(150, 61, 1, 181, 'REJECTED', '2026-04-07 03:59:18', 0.00, 'Rejected due to late payment'),
(152, 62, 4, 193, 'PENDING', '2026-04-07 04:01:02', 0.00, '');

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `EventID` int(11) NOT NULL,
  `Title` varchar(150) NOT NULL,
  `Description` text DEFAULT NULL,
  `OrganizerID` int(11) NOT NULL,
  `CapacityLimit` int(11) NOT NULL CHECK (`CapacityLimit` > 0),
  `Status` varchar(20) NOT NULL DEFAULT 'DRAFT'
) ;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`EventID`, `Title`, `Description`, `OrganizerID`, `CapacityLimit`, `Status`) VALUES
(1, 'Sarah & Michael Wedding Reception', 'Elegant wedding reception with 150 guests, dinner, and live band.', 1, 150, 'CONFIRMED'),
(2, 'Emily\'s 5th Birthday Party', 'Princess-themed birthday celebration with games, cake, and photo booth.', 1, 40, 'CONFIRMED'),
(5, 'Sarah & Michael Wedding Reception', 'Elegant evening wedding reception with dinner, live music, and dancing.', 2, 180, 'CONFIRMED'),
(12, 'Franck\'s Wedding', 'Let the fun begin', 1, 45, 'CONFIRMED'),
(13, 'Team Building Event', 'Cheers', 1, 290, 'CONFIRMED'),
(16, 'Birthday Gala - 50th', 'Milestone birthday celebration with live music.', 2, 150, 'CONFIRMED'),
(17, 'Charity Fundraiser Dinner', 'Black-tie charity dinner with auction.', 4, 250, 'CONFIRMED'),
(18, 'Product Launch Event', 'New product reveal with press and stakeholders.', 3, 100, 'CONFIRMED'),
(19, 'Graduation Ceremony', 'University graduation celebration and reception.', 2, 300, 'CONFIRMED'),
(20, 'Anniversary Banquet', '25th wedding anniversary dinner celebration.', 4, 120, 'CONFIRMED'),
(21, 'Networking Mixer', 'Professional networking event for local businesses.', 3, 180, 'CONFIRMED'),
(23, 'Luncheon Awards Ceremony', 'Annual staff awards and recognition lunch.', 4, 120, 'CONFIRMED'),
(24, 'Evening Gala Dinner', 'Formal gala dinner for VIP guests.', 2, 200, 'CONFIRMED'),
(25, 'Baby Shower Brunch', 'Garden baby shower with brunch and games.', 3, 60, 'CONFIRMED'),
(26, 'Retirement Party', 'Retirement celebration for long-serving staff.', 4, 90, 'CONFIRMED'),
(27, 'Tech Conference Day 1', 'Annual technology conference - first day.', 2, 250, 'CONFIRMED'),
(28, 'Tech Conference Day 2', 'Annual technology conference - second day.', 3, 250, 'CONFIRMED'),
(29, 'Kids Birthday Bash', 'Fun-filled kids party with entertainment.', 4, 50, 'CONFIRMED'),
(30, 'Wine Tasting Evening', 'Curated wine tasting with sommelier.', 2, 70, 'CONFIRMED'),
(31, 'Yoga & Wellness Retreat', 'Full-day wellness retreat with yoga and meditation.', 3, 45, 'CONFIRMED'),
(32, 'Sold-Out Wedding', 'Very popular wedding event, near full capacity.', 2, 100, 'CONFIRMED'),
(33, 'Popular Music Night', 'Live music event almost at capacity.', 4, 150, 'CONFIRMED'),
(36, 'Overlapping Booking Test', 'This event overlaps with Baby Shower to test conflicts.', 4, 60, 'CONFIRMED'),
(39, 'Birthday Gala - 50th', 'Milestone birthday celebration with live music.', 2, 150, 'CONFIRMED'),
(40, 'Charity Fundraiser Dinner', 'Black-tie charity dinner with auction.', 4, 250, 'CONFIRMED'),
(41, 'Product Launch Event', 'New product reveal with press and stakeholders.', 3, 100, 'CONFIRMED'),
(42, 'Graduation Ceremony', 'University graduation celebration and reception.', 2, 300, 'CONFIRMED'),
(43, 'Anniversary Banquet', '25th wedding anniversary dinner celebration.', 4, 120, 'CONFIRMED'),
(44, 'Networking Mixer', 'Professional networking event for local businesses.', 3, 180, 'CONFIRMED'),
(46, 'Luncheon Awards Ceremony', 'Annual staff awards and recognition lunch.', 4, 120, 'CONFIRMED'),
(47, 'Evening Gala Dinner', 'Formal gala dinner for VIP guests.', 2, 200, 'CONFIRMED'),
(48, 'Baby Shower Brunch', 'Garden baby shower with brunch and games.', 3, 60, 'CONFIRMED'),
(49, 'Retirement Party', 'Retirement celebration for long-serving staff.', 4, 90, 'CONFIRMED'),
(50, 'Tech Conference Day 1', 'Annual technology conference - first day.', 2, 250, 'CONFIRMED'),
(51, 'Tech Conference Day 2', 'Annual technology conference - second day.', 3, 250, 'CONFIRMED'),
(52, 'Kids Birthday Bash', 'Fun-filled kids party with entertainment.', 4, 50, 'CONFIRMED'),
(53, 'Wine Tasting Evening', 'Curated wine tasting with sommelier.', 2, 70, 'CONFIRMED'),
(54, 'Yoga & Wellness Retreat', 'Full-day wellness retreat with yoga and meditation.', 3, 45, 'CONFIRMED'),
(55, 'Sold-Out Wedding', 'Very popular wedding event, near full capacity.', 2, 100, 'CONFIRMED'),
(56, 'Popular Music Night', 'Live music event almost at capacity.', 4, 150, 'CONFIRMED'),
(59, 'Overlapping Booking Test', 'This event overlaps with Baby Shower to test conflicts.', 4, 60, 'CONFIRMED'),
(62, 'Birthday Gala - 50th', 'Milestone birthday celebration with live music.', 2, 150, 'CONFIRMED'),
(63, 'Charity Fundraiser Dinner', 'Black-tie charity dinner with auction.', 4, 250, 'CONFIRMED'),
(64, 'Product Launch Event', 'New product reveal with press and stakeholders.', 3, 100, 'CONFIRMED'),
(65, 'Graduation Ceremony', 'University graduation celebration and reception.', 2, 300, 'CONFIRMED'),
(66, 'Anniversary Banquet', '25th wedding anniversary dinner celebration.', 4, 120, 'CONFIRMED'),
(67, 'Networking Mixer', 'Professional networking event for local businesses.', 3, 180, 'CONFIRMED'),
(69, 'Luncheon Awards Ceremony', 'Annual staff awards and recognition lunch.', 4, 120, 'CONFIRMED'),
(70, 'Evening Gala Dinner', 'Formal gala dinner for VIP guests.', 2, 200, 'CONFIRMED'),
(71, 'Baby Shower Brunch', 'Garden baby shower with brunch and games.', 3, 60, 'CONFIRMED'),
(72, 'Retirement Party', 'Retirement celebration for long-serving staff.', 4, 90, 'CONFIRMED'),
(73, 'Tech Conference Day 1', 'Annual technology conference - first day.', 2, 250, 'CONFIRMED'),
(74, 'Tech Conference Day 2', 'Annual technology conference - second day.', 3, 250, 'CONFIRMED'),
(75, 'Kids Birthday Bash', 'Fun-filled kids party with entertainment.', 4, 50, 'CONFIRMED'),
(76, 'Wine Tasting Evening', 'Curated wine tasting with sommelier.', 2, 70, 'CONFIRMED'),
(77, 'Yoga & Wellness Retreat', 'Full-day wellness retreat with yoga and meditation.', 3, 45, 'CONFIRMED'),
(78, 'Sold-Out Wedding', 'Very popular wedding event, near full capacity.', 2, 100, 'CONFIRMED'),
(79, 'Popular Music Night', 'Live music event almost at capacity.', 4, 150, 'CONFIRMED'),
(82, 'Overlapping Booking Test', 'This event overlaps with Baby Shower to test conflicts.', 4, 60, 'CONFIRMED'),
(85, 'Birthday Gala', '50th birthday.', 2, 150, 'CONFIRMED'),
(88, 'Birthday Gala 50th', 'Milestone birthday with live music.', 2, 150, 'CONFIRMED'),
(89, 'Charity Fundraiser Dinner', 'Black-tie charity dinner with auction.', 4, 250, 'CONFIRMED'),
(90, 'Product Launch Event', 'New product reveal with press and stakeholders.', 3, 100, 'CONFIRMED'),
(91, 'Graduation Ceremony', 'University graduation celebration.', 2, 300, 'CONFIRMED'),
(92, 'Anniversary Banquet', '25th wedding anniversary dinner.', 4, 120, 'CONFIRMED'),
(93, 'Networking Mixer', 'Professional networking for local businesses.', 3, 180, 'CONFIRMED'),
(95, 'Luncheon Awards Ceremony', 'Annual staff awards and recognition lunch.', 4, 120, 'CONFIRMED'),
(96, 'Evening Gala Dinner', 'Formal gala dinner for VIP guests.', 2, 200, 'CONFIRMED'),
(97, 'Baby Shower Brunch', 'Garden baby shower with brunch and games.', 3, 60, 'CONFIRMED'),
(98, 'Retirement Party', 'Retirement celebration for long-serving staff.', 4, 90, 'CONFIRMED'),
(99, 'Tech Conference Day 1', 'Annual technology conference - day one.', 2, 250, 'CONFIRMED'),
(100, 'Tech Conference Day 2', 'Annual technology conference - day two.', 3, 250, 'CONFIRMED'),
(101, 'Kids Birthday Bash', 'Fun-filled kids party with entertainment.', 4, 50, 'CONFIRMED'),
(102, 'Wine Tasting Evening', 'Curated wine tasting with sommelier.', 2, 70, 'CONFIRMED'),
(103, 'Yoga Wellness Retreat', 'Full-day wellness retreat with yoga.', 3, 45, 'CONFIRMED'),
(104, 'Sold-Out Wedding', 'Very popular wedding event near capacity.', 2, 100, 'CONFIRMED'),
(105, 'Popular Music Night', 'Live music event almost at capacity.', 4, 150, 'CONFIRMED'),
(108, 'Conflicting Booking Test', 'Overlaps with Baby Shower to test conflict filter.', 4, 60, 'CONFIRMED'),
(109, 'Spring Wedding Reception', 'Elegant spring wedding with dinner and dancing.', 51, 200, 'CONFIRMED'),
(110, 'Corporate Team Building', 'Full-day outdoor team activities and workshops.', 52, 80, 'CONFIRMED'),
(111, 'Birthday Gala 50th', 'Milestone birthday with live music.', 51, 150, 'CONFIRMED'),
(112, 'Charity Fundraiser Dinner', 'Black-tie charity dinner with auction.', 53, 250, 'CONFIRMED'),
(113, 'Product Launch Event', 'New product reveal with press and stakeholders.', 52, 100, 'CONFIRMED'),
(114, 'Graduation Ceremony', 'University graduation celebration.', 51, 300, 'CONFIRMED'),
(115, 'Anniversary Banquet', '25th wedding anniversary dinner.', 53, 120, 'CONFIRMED'),
(116, 'Networking Mixer', 'Professional networking for local businesses.', 52, 180, 'CONFIRMED'),
(117, 'Morning Board Meeting', 'Quarterly board meeting with breakfast.', 51, 40, 'CONFIRMED'),
(118, 'Luncheon Awards Ceremony', 'Annual staff awards and recognition lunch.', 53, 120, 'CONFIRMED'),
(119, 'Evening Gala Dinner', 'Formal gala dinner for VIP guests.', 51, 200, 'CONFIRMED'),
(120, 'Baby Shower Brunch', 'Garden baby shower with brunch and games.', 52, 60, 'CONFIRMED'),
(121, 'Retirement Party', 'Retirement celebration for long-serving staff.', 53, 90, 'CONFIRMED'),
(122, 'Tech Conference Day 1', 'Annual technology conference - day one.', 51, 250, 'CONFIRMED'),
(123, 'Tech Conference Day 2', 'Annual technology conference - day two.', 52, 250, 'CONFIRMED'),
(124, 'Kids Birthday Bash', 'Fun-filled kids party with entertainment.', 53, 50, 'CONFIRMED'),
(125, 'Wine Tasting Evening', 'Curated wine tasting with sommelier.', 51, 70, 'CONFIRMED'),
(126, 'Yoga Wellness Retreat', 'Full-day wellness retreat with yoga.', 52, 45, 'CONFIRMED'),
(127, 'Sold-Out Wedding', 'Very popular wedding event near capacity.', 51, 100, 'CONFIRMED'),
(128, 'Popular Music Night', 'Live music event almost at capacity.', 53, 150, 'CONFIRMED'),
(131, 'Conflicting Booking Test', 'Overlaps with Baby Shower to test conflict filter.', 53, 60, 'CONFIRMED'),
(132, 'Spring Wedding Reception', 'Elegant spring wedding with dinner and dancing.', 51, 200, 'CONFIRMED'),
(133, 'Corporate Team Building', 'Full-day outdoor team activities and workshops.', 52, 80, 'CONFIRMED'),
(134, 'Birthday Gala 50th', 'Milestone birthday with live music.', 51, 150, 'CONFIRMED'),
(135, 'Charity Fundraiser Dinner', 'Black-tie charity dinner with auction.', 53, 250, 'CONFIRMED'),
(136, 'Product Launch Event', 'New product reveal with press and stakeholders.', 52, 100, 'CONFIRMED'),
(137, 'Graduation Ceremony', 'University graduation celebration.', 51, 300, 'CONFIRMED'),
(138, 'Anniversary Banquet', '25th wedding anniversary dinner.', 53, 120, 'CONFIRMED'),
(139, 'Networking Mixer', 'Professional networking for local businesses.', 52, 180, 'CONFIRMED'),
(140, 'Morning Board Meeting', 'Quarterly board meeting with breakfast.', 51, 40, 'CONFIRMED'),
(141, 'Luncheon Awards Ceremony', 'Annual staff awards and recognition lunch.', 53, 120, 'CONFIRMED'),
(142, 'Evening Gala Dinner', 'Formal gala dinner for VIP guests.', 51, 200, 'CONFIRMED'),
(143, 'Baby Shower Brunch', 'Garden baby shower with brunch and games.', 52, 60, 'CONFIRMED'),
(144, 'Retirement Party', 'Retirement celebration for long-serving staff.', 53, 90, 'CONFIRMED'),
(145, 'Tech Conference Day 1', 'Annual technology conference - day one.', 51, 250, 'CONFIRMED'),
(146, 'Tech Conference Day 2', 'Annual technology conference - day two.', 52, 250, 'CONFIRMED'),
(147, 'Kids Birthday Bash', 'Fun-filled kids party with entertainment.', 53, 50, 'CONFIRMED'),
(148, 'Wine Tasting Evening', 'Curated wine tasting with sommelier.', 51, 70, 'CONFIRMED'),
(149, 'Yoga Wellness Retreat', 'Full-day wellness retreat with yoga.', 52, 45, 'CONFIRMED'),
(150, 'Sold-Out Wedding', 'Very popular wedding event near capacity.', 51, 100, 'CONFIRMED'),
(151, 'Popular Music Night', 'Live music event almost at capacity.', 53, 150, 'CONFIRMED'),
(154, 'Conflicting Booking Test', 'Overlaps with Baby Shower to test conflict filter.', 53, 60, 'CONFIRMED'),
(155, 'Spring Wedding Reception', 'Elegant spring wedding with dinner and dancing.', 51, 200, 'CONFIRMED'),
(156, 'Corporate Team Building', 'Full-day outdoor team activities and workshops.', 52, 80, 'CONFIRMED'),
(157, 'Birthday Gala 50th', 'Milestone birthday with live music.', 51, 150, 'CONFIRMED'),
(158, 'Charity Fundraiser Dinner', 'Black-tie charity dinner with auction.', 53, 250, 'CONFIRMED'),
(159, 'Product Launch Event', 'New product reveal with press and stakeholders.', 52, 100, 'CONFIRMED'),
(160, 'Graduation Ceremony', 'University graduation celebration.', 51, 300, 'CONFIRMED'),
(161, 'Anniversary Banquet', '25th wedding anniversary dinner.', 53, 120, 'CONFIRMED'),
(162, 'Networking Mixer', 'Professional networking for local businesses.', 52, 180, 'CONFIRMED'),
(163, 'Morning Board Meeting', 'Quarterly board meeting with breakfast.', 51, 40, 'CONFIRMED'),
(164, 'Luncheon Awards Ceremony', 'Annual staff awards and recognition lunch.', 53, 120, 'CONFIRMED'),
(165, 'Evening Gala Dinner', 'Formal gala dinner for VIP guests.', 51, 200, 'CONFIRMED'),
(166, 'Baby Shower Brunch', 'Garden baby shower with brunch and games.', 52, 60, 'CONFIRMED'),
(167, 'Retirement Party', 'Retirement celebration for long-serving staff.', 53, 90, 'CONFIRMED'),
(168, 'Tech Conference Day 1', 'Annual technology conference - day one.', 51, 250, 'CONFIRMED'),
(169, 'Tech Conference Day 2', 'Annual technology conference - day two.', 52, 250, 'CONFIRMED'),
(170, 'Kids Birthday Bash', 'Fun-filled kids party with entertainment.', 53, 50, 'CONFIRMED'),
(171, 'Wine Tasting Evening', 'Curated wine tasting with sommelier.', 51, 70, 'CONFIRMED'),
(172, 'Yoga Wellness Retreat', 'Full-day wellness retreat with yoga.', 52, 45, 'CONFIRMED'),
(173, 'Sold-Out Wedding', 'Very popular wedding event near capacity.', 51, 100, 'CONFIRMED'),
(174, 'Popular Music Night', 'Live music event almost at capacity.', 53, 150, 'CONFIRMED'),
(175, 'Proposed Christmas Party', 'Tentative Christmas celebration pending approval.', 52, 200, 'DRAFT'),
(176, 'Possible Team Offsite', 'Draft offsite, venue TBD.', 51, 80, 'DRAFT'),
(177, 'Conflicting Booking Test', 'Overlaps with Baby Shower to test conflict filter.', 53, 60, 'CONFIRMED'),
(178, 'Spring Wedding Reception', 'Elegant spring wedding with dinner and dancing.', 51, 200, 'CONFIRMED'),
(179, 'Corporate Team Building', 'Full-day outdoor team activities.', 52, 80, 'CONFIRMED'),
(180, 'Birthday Gala 50th', 'Milestone birthday with live music.', 51, 150, 'CONFIRMED'),
(181, 'Charity Fundraiser Dinner', 'Black-tie charity dinner with auction.', 53, 250, 'CONFIRMED'),
(182, 'Product Launch Event', 'New product reveal with press.', 52, 100, 'CONFIRMED'),
(183, 'Graduation Ceremony', 'University graduation celebration.', 51, 300, 'CONFIRMED'),
(184, 'Anniversary Banquet', '25th wedding anniversary dinner.', 53, 120, 'CONFIRMED'),
(185, 'Networking Mixer', 'Professional networking event.', 52, 180, 'CONFIRMED'),
(186, 'Morning Board Meeting', 'Quarterly board meeting with breakfast.', 51, 40, 'CONFIRMED'),
(187, 'Luncheon Awards Ceremony', 'Annual staff awards lunch.', 53, 120, 'CONFIRMED'),
(188, 'Evening Gala Dinner', 'Formal gala dinner for VIP guests.', 51, 200, 'CONFIRMED'),
(189, 'Baby Shower Brunch', 'Garden baby shower with brunch.', 52, 60, 'CONFIRMED'),
(190, 'Retirement Party', 'Retirement celebration for long-serving staff.', 53, 90, 'CONFIRMED'),
(191, 'Tech Conference Day 1', 'Annual technology conference day one.', 51, 250, 'CONFIRMED'),
(192, 'Tech Conference Day 2', 'Annual technology conference day two.', 52, 250, 'CONFIRMED'),
(193, 'Kids Birthday Bash', 'Fun kids party with entertainment.', 53, 50, 'CONFIRMED'),
(194, 'Wine Tasting Evening', 'Curated wine tasting with sommelier.', 51, 70, 'CONFIRMED'),
(195, 'Yoga Wellness Retreat', 'Full-day wellness retreat.', 52, 45, 'CONFIRMED'),
(196, 'Sold-Out Wedding', 'Popular wedding event near capacity.', 51, 20, 'CONFIRMED'),
(197, 'Popular Music Night', 'Live music event almost at capacity.', 53, 30, 'CONFIRMED'),
(198, 'Proposed Christmas Party', 'Tentative Christmas celebration.', 52, 200, 'DRAFT'),
(199, 'Possible Team Offsite', 'Draft offsite event.', 51, 80, 'DRAFT'),
(200, 'Conflicting Booking Test', 'Overlaps with Baby Shower to test conflicts.', 53, 60, 'CONFIRMED');

-- --------------------------------------------------------

--
-- Table structure for table `event_resource`
--

CREATE TABLE `event_resource` (
  `EventID` int(11) NOT NULL,
  `ResourceID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL CHECK (`Quantity` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_resource`
--

INSERT INTO `event_resource` (`EventID`, `ResourceID`, `Quantity`) VALUES
(178, 3, 20),
(178, 4, 15),
(179, 1, 2),
(180, 2, 4),
(180, 6, 8),
(181, 3, 30),
(181, 4, 20),
(183, 2, 8),
(183, 6, 10),
(186, 1, 1),
(187, 3, 15),
(187, 4, 10),
(188, 6, 6),
(189, 3, 8),
(189, 5, 1),
(190, 3, 10),
(190, 4, 8),
(191, 1, 6),
(191, 2, 10),
(192, 1, 4),
(193, 5, 1),
(194, 2, 2),
(196, 3, 12),
(196, 6, 6),
(197, 2, 6),
(197, 6, 12),
(197, 7, 1);

-- --------------------------------------------------------

--
-- Table structure for table `event_schedule`
--

CREATE TABLE `event_schedule` (
  `VenueID` int(11) NOT NULL,
  `SlotID` int(11) NOT NULL,
  `EventID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_schedule`
--

INSERT INTO `event_schedule` (`VenueID`, `SlotID`, `EventID`) VALUES
(1, 156, 109),
(3, 157, 110),
(2, 158, 111),
(1, 160, 112),
(4, 161, 113),
(5, 163, 114),
(3, 159, 115),
(12, 162, 116),
(11, 164, 117),
(1, 165, 118),
(5, 166, 119),
(2, 167, 120),
(3, 168, 121),
(1, 170, 122),
(1, 171, 123),
(4, 172, 124),
(13, 173, 125),
(12, 174, 126),
(5, 175, 127),
(1, 176, 128),
(2, 169, 131),
(1, 180, 132),
(3, 181, 133),
(2, 182, 134),
(1, 184, 135),
(4, 185, 136),
(5, 187, 137),
(3, 183, 138),
(12, 186, 139),
(11, 188, 140),
(1, 189, 141),
(5, 190, 142),
(2, 191, 143),
(3, 192, 144),
(1, 194, 145),
(1, 195, 146),
(4, 196, 147),
(13, 197, 148),
(12, 198, 149),
(5, 199, 150),
(1, 200, 151),
(2, 193, 154),
(1, 204, 155),
(3, 205, 156),
(2, 206, 157),
(1, 208, 158),
(4, 209, 159),
(5, 211, 160),
(3, 207, 161),
(12, 210, 162),
(11, 212, 163),
(1, 213, 164),
(5, 214, 165),
(2, 215, 166),
(3, 216, 167),
(1, 218, 168),
(1, 219, 169),
(4, 220, 170),
(13, 221, 171),
(12, 222, 172),
(5, 223, 173),
(1, 224, 174),
(3, 225, 175),
(2, 226, 176),
(2, 217, 177),
(1, 228, 178),
(3, 229, 179),
(2, 230, 180),
(1, 232, 181),
(4, 233, 182),
(5, 235, 183),
(3, 231, 184),
(12, 234, 185),
(11, 236, 186),
(1, 237, 187),
(5, 238, 188),
(2, 239, 189),
(3, 240, 190),
(1, 242, 191),
(1, 243, 192),
(4, 244, 193),
(13, 245, 194),
(12, 246, 195),
(5, 247, 196),
(1, 248, 197),
(3, 249, 198),
(2, 250, 199),
(2, 241, 200);

-- --------------------------------------------------------

--
-- Table structure for table `resource`
--

CREATE TABLE `resource` (
  `ResourceID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Type` varchar(50) NOT NULL
) ;

--
-- Dumping data for table `resource`
--

INSERT INTO `resource` (`ResourceID`, `Name`, `Type`) VALUES
(1, 'Projector & Screen', 'AV'),
(2, 'PA System & Microphones', 'AV'),
(3, 'Round Tables & Chairs', 'Furniture'),
(4, 'Buffet Serving Stations', 'Catering'),
(5, 'Photo Booth', 'Other'),
(6, 'Stage Lighting', 'AV'),
(7, 'Dance Floor', 'Furniture'),
(8, 'Catering Set', 'Catering');

-- --------------------------------------------------------

--
-- Table structure for table `timeslot`
--

CREATE TABLE `timeslot` (
  `SlotID` int(11) NOT NULL,
  `EventDate` date NOT NULL,
  `StartTime` time NOT NULL,
  `EndTime` time NOT NULL
) ;

--
-- Dumping data for table `timeslot`
--

INSERT INTO `timeslot` (`SlotID`, `EventDate`, `StartTime`, `EndTime`) VALUES
(1, '2026-04-12', '14:00:00', '18:00:00'),
(2, '2026-04-19', '16:00:00', '22:00:00'),
(3, '2026-04-25', '10:00:00', '14:00:00'),
(4, '2026-05-03', '18:00:00', '23:00:00'),
(5, '2026-05-10', '12:00:00', '16:00:00'),
(6, '2026-05-15', '19:00:00', '23:00:00'),
(7, '2026-12-11', '16:03:00', '23:03:00'),
(8, '2026-04-17', '20:34:00', '23:34:00'),
(9, '2026-03-01', '10:00:00', '14:00:00'),
(10, '2026-03-01', '15:00:00', '19:00:00'),
(11, '2026-03-05', '09:00:00', '12:00:00'),
(12, '2026-03-05', '13:00:00', '17:00:00'),
(13, '2026-03-10', '11:00:00', '15:00:00'),
(14, '2026-03-10', '16:00:00', '20:00:00'),
(15, '2026-03-15', '10:00:00', '13:00:00'),
(16, '2026-03-15', '14:00:00', '18:00:00'),
(17, '2026-03-20', '09:00:00', '12:00:00'),
(18, '2026-03-20', '13:00:00', '16:00:00'),
(19, '2026-03-25', '10:00:00', '14:00:00'),
(20, '2026-03-25', '18:00:00', '22:00:00'),
(21, '2026-04-06', '09:00:00', '12:00:00'),
(22, '2026-04-06', '13:00:00', '17:00:00'),
(23, '2026-04-06', '18:00:00', '21:00:00'),
(24, '2026-04-07', '10:00:00', '14:00:00'),
(25, '2026-04-07', '15:00:00', '19:00:00'),
(26, '2026-04-08', '09:00:00', '13:00:00'),
(27, '2026-04-08', '14:00:00', '18:00:00'),
(28, '2026-04-09', '10:00:00', '15:00:00'),
(29, '2026-04-09', '16:00:00', '20:00:00'),
(30, '2026-04-10', '11:00:00', '14:00:00'),
(31, '2026-04-10', '15:00:00', '19:00:00'),
(32, '2026-04-11', '09:00:00', '12:00:00'),
(33, '2026-04-11', '13:00:00', '17:00:00'),
(34, '2026-04-12', '10:00:00', '14:00:00'),
(35, '2026-04-12', '18:00:00', '22:00:00'),
(36, '2026-04-13', '09:00:00', '13:00:00'),
(37, '2026-04-13', '14:00:00', '18:00:00'),
(38, '2026-04-07', '12:00:00', '16:00:00'),
(39, '2026-04-20', '10:00:00', '14:00:00'),
(40, '2026-04-27', '11:00:00', '15:00:00'),
(41, '2026-05-06', '09:00:00', '17:00:00'),
(42, '2026-03-01', '10:00:00', '14:00:00'),
(43, '2026-03-01', '15:00:00', '19:00:00'),
(44, '2026-03-05', '09:00:00', '12:00:00'),
(45, '2026-03-05', '13:00:00', '17:00:00'),
(46, '2026-03-10', '11:00:00', '15:00:00'),
(47, '2026-03-10', '16:00:00', '20:00:00'),
(48, '2026-03-15', '10:00:00', '13:00:00'),
(49, '2026-03-15', '14:00:00', '18:00:00'),
(50, '2026-03-20', '09:00:00', '12:00:00'),
(51, '2026-03-20', '13:00:00', '16:00:00'),
(52, '2026-03-25', '10:00:00', '14:00:00'),
(53, '2026-03-25', '18:00:00', '22:00:00'),
(54, '2026-04-06', '09:00:00', '12:00:00'),
(55, '2026-04-06', '13:00:00', '17:00:00'),
(56, '2026-04-06', '18:00:00', '21:00:00'),
(57, '2026-04-07', '10:00:00', '14:00:00'),
(58, '2026-04-07', '15:00:00', '19:00:00'),
(59, '2026-04-08', '09:00:00', '13:00:00'),
(60, '2026-04-08', '14:00:00', '18:00:00'),
(61, '2026-04-09', '10:00:00', '15:00:00'),
(62, '2026-04-09', '16:00:00', '20:00:00'),
(63, '2026-04-10', '11:00:00', '14:00:00'),
(64, '2026-04-10', '15:00:00', '19:00:00'),
(65, '2026-04-11', '09:00:00', '12:00:00'),
(66, '2026-04-11', '13:00:00', '17:00:00'),
(67, '2026-04-12', '10:00:00', '14:00:00'),
(68, '2026-04-12', '18:00:00', '22:00:00'),
(69, '2026-04-13', '09:00:00', '13:00:00'),
(70, '2026-04-13', '14:00:00', '18:00:00'),
(71, '2026-04-07', '12:00:00', '16:00:00'),
(72, '2026-04-20', '10:00:00', '14:00:00'),
(73, '2026-04-27', '11:00:00', '15:00:00'),
(74, '2026-05-06', '09:00:00', '17:00:00'),
(75, '2026-03-01', '10:00:00', '14:00:00'),
(76, '2026-03-01', '15:00:00', '19:00:00'),
(77, '2026-03-05', '09:00:00', '12:00:00'),
(78, '2026-03-05', '13:00:00', '17:00:00'),
(79, '2026-03-10', '11:00:00', '15:00:00'),
(80, '2026-03-10', '16:00:00', '20:00:00'),
(81, '2026-03-15', '10:00:00', '13:00:00'),
(82, '2026-03-15', '14:00:00', '18:00:00'),
(83, '2026-03-20', '09:00:00', '12:00:00'),
(84, '2026-03-20', '13:00:00', '16:00:00'),
(85, '2026-03-25', '10:00:00', '14:00:00'),
(86, '2026-03-25', '18:00:00', '22:00:00'),
(87, '2026-04-06', '09:00:00', '12:00:00'),
(88, '2026-04-06', '13:00:00', '17:00:00'),
(89, '2026-04-06', '18:00:00', '21:00:00'),
(90, '2026-04-07', '10:00:00', '14:00:00'),
(91, '2026-04-07', '15:00:00', '19:00:00'),
(92, '2026-04-08', '09:00:00', '13:00:00'),
(93, '2026-04-08', '14:00:00', '18:00:00'),
(94, '2026-04-09', '10:00:00', '15:00:00'),
(95, '2026-04-09', '16:00:00', '20:00:00'),
(96, '2026-04-10', '11:00:00', '14:00:00'),
(97, '2026-04-10', '15:00:00', '19:00:00'),
(98, '2026-04-11', '09:00:00', '12:00:00'),
(99, '2026-04-11', '13:00:00', '17:00:00'),
(100, '2026-04-12', '10:00:00', '14:00:00'),
(101, '2026-04-12', '18:00:00', '22:00:00'),
(102, '2026-04-13', '09:00:00', '13:00:00'),
(103, '2026-04-13', '14:00:00', '18:00:00'),
(104, '2026-04-07', '12:00:00', '16:00:00'),
(105, '2026-04-20', '10:00:00', '14:00:00'),
(106, '2026-04-27', '11:00:00', '15:00:00'),
(107, '2026-05-06', '09:00:00', '17:00:00'),
(108, '2026-03-01', '10:00:00', '14:00:00'),
(109, '2026-03-01', '15:00:00', '19:00:00'),
(110, '2026-03-05', '09:00:00', '12:00:00'),
(111, '2026-03-05', '13:00:00', '17:00:00'),
(112, '2026-03-10', '11:00:00', '15:00:00'),
(113, '2026-03-10', '16:00:00', '20:00:00'),
(114, '2026-03-15', '10:00:00', '13:00:00'),
(115, '2026-03-15', '14:00:00', '18:00:00'),
(116, '2026-03-20', '09:00:00', '12:00:00'),
(117, '2026-03-20', '13:00:00', '16:00:00'),
(118, '2026-03-25', '10:00:00', '14:00:00'),
(119, '2026-03-25', '18:00:00', '22:00:00'),
(120, '2026-04-06', '09:00:00', '12:00:00'),
(121, '2026-04-06', '13:00:00', '17:00:00'),
(122, '2026-04-06', '18:00:00', '21:00:00'),
(123, '2026-03-01', '10:00:00', '14:00:00'),
(124, '2026-03-01', '15:00:00', '19:00:00'),
(125, '2026-03-05', '09:00:00', '12:00:00'),
(126, '2026-03-05', '13:00:00', '17:00:00'),
(127, '2026-03-10', '11:00:00', '15:00:00'),
(128, '2026-03-10', '16:00:00', '20:00:00'),
(129, '2026-03-15', '10:00:00', '13:00:00'),
(130, '2026-03-15', '14:00:00', '18:00:00'),
(131, '2026-03-20', '09:00:00', '12:00:00'),
(132, '2026-03-20', '13:00:00', '16:00:00'),
(133, '2026-03-25', '10:00:00', '14:00:00'),
(134, '2026-03-25', '18:00:00', '22:00:00'),
(135, '2026-04-07', '09:00:00', '12:00:00'),
(136, '2026-04-07', '13:00:00', '17:00:00'),
(137, '2026-04-07', '18:00:00', '21:00:00'),
(138, '2026-04-08', '10:00:00', '14:00:00'),
(139, '2026-04-08', '15:00:00', '19:00:00'),
(140, '2026-04-08', '12:00:00', '16:00:00'),
(141, '2026-04-09', '09:00:00', '13:00:00'),
(142, '2026-04-09', '14:00:00', '18:00:00'),
(143, '2026-04-10', '10:00:00', '15:00:00'),
(144, '2026-04-10', '16:00:00', '20:00:00'),
(145, '2026-04-11', '11:00:00', '14:00:00'),
(146, '2026-04-11', '15:00:00', '19:00:00'),
(147, '2026-04-12', '09:00:00', '12:00:00'),
(148, '2026-04-12', '13:00:00', '17:00:00'),
(149, '2026-04-13', '10:00:00', '14:00:00'),
(150, '2026-04-13', '18:00:00', '22:00:00'),
(151, '2026-04-14', '09:00:00', '13:00:00'),
(152, '2026-04-14', '14:00:00', '18:00:00'),
(153, '2026-04-21', '10:00:00', '14:00:00'),
(154, '2026-04-28', '11:00:00', '15:00:00'),
(155, '2026-05-07', '09:00:00', '17:00:00'),
(156, '2026-03-01', '10:00:00', '14:00:00'),
(157, '2026-03-05', '09:00:00', '12:00:00'),
(158, '2026-03-10', '11:00:00', '15:00:00'),
(159, '2026-03-15', '10:00:00', '13:00:00'),
(160, '2026-03-15', '14:00:00', '18:00:00'),
(161, '2026-03-20', '09:00:00', '12:00:00'),
(162, '2026-03-20', '13:00:00', '16:00:00'),
(163, '2026-03-25', '10:00:00', '14:00:00'),
(164, '2026-04-07', '09:00:00', '12:00:00'),
(165, '2026-04-07', '13:00:00', '17:00:00'),
(166, '2026-04-07', '18:00:00', '21:00:00'),
(167, '2026-04-08', '10:00:00', '14:00:00'),
(168, '2026-04-08', '15:00:00', '19:00:00'),
(169, '2026-04-08', '12:00:00', '16:00:00'),
(170, '2026-04-09', '09:00:00', '13:00:00'),
(171, '2026-04-09', '14:00:00', '18:00:00'),
(172, '2026-04-10', '10:00:00', '15:00:00'),
(173, '2026-04-11', '11:00:00', '14:00:00'),
(174, '2026-04-12', '09:00:00', '12:00:00'),
(175, '2026-04-13', '10:00:00', '14:00:00'),
(176, '2026-04-14', '09:00:00', '13:00:00'),
(177, '2026-04-21', '10:00:00', '14:00:00'),
(178, '2026-04-28', '11:00:00', '15:00:00'),
(179, '2026-05-07', '09:00:00', '17:00:00'),
(180, '2026-03-01', '10:00:00', '14:00:00'),
(181, '2026-03-05', '09:00:00', '12:00:00'),
(182, '2026-03-10', '11:00:00', '15:00:00'),
(183, '2026-03-15', '10:00:00', '13:00:00'),
(184, '2026-03-15', '14:00:00', '18:00:00'),
(185, '2026-03-20', '09:00:00', '12:00:00'),
(186, '2026-03-20', '13:00:00', '16:00:00'),
(187, '2026-03-25', '10:00:00', '14:00:00'),
(188, '2026-04-07', '09:00:00', '12:00:00'),
(189, '2026-04-07', '13:00:00', '17:00:00'),
(190, '2026-04-07', '18:00:00', '21:00:00'),
(191, '2026-04-08', '10:00:00', '14:00:00'),
(192, '2026-04-08', '15:00:00', '19:00:00'),
(193, '2026-04-08', '12:00:00', '16:00:00'),
(194, '2026-04-09', '09:00:00', '13:00:00'),
(195, '2026-04-09', '14:00:00', '18:00:00'),
(196, '2026-04-10', '10:00:00', '15:00:00'),
(197, '2026-04-11', '11:00:00', '14:00:00'),
(198, '2026-04-12', '09:00:00', '12:00:00'),
(199, '2026-04-13', '10:00:00', '14:00:00'),
(200, '2026-04-14', '09:00:00', '13:00:00'),
(201, '2026-04-21', '10:00:00', '14:00:00'),
(202, '2026-04-28', '11:00:00', '15:00:00'),
(203, '2026-05-07', '09:00:00', '17:00:00'),
(204, '2026-03-01', '10:00:00', '14:00:00'),
(205, '2026-03-05', '09:00:00', '12:00:00'),
(206, '2026-03-10', '11:00:00', '15:00:00'),
(207, '2026-03-15', '10:00:00', '13:00:00'),
(208, '2026-03-15', '14:00:00', '18:00:00'),
(209, '2026-03-20', '09:00:00', '12:00:00'),
(210, '2026-03-20', '13:00:00', '16:00:00'),
(211, '2026-03-25', '10:00:00', '14:00:00'),
(212, '2026-04-07', '09:00:00', '12:00:00'),
(213, '2026-04-07', '13:00:00', '17:00:00'),
(214, '2026-04-07', '18:00:00', '21:00:00'),
(215, '2026-04-08', '10:00:00', '14:00:00'),
(216, '2026-04-08', '15:00:00', '19:00:00'),
(217, '2026-04-08', '12:00:00', '16:00:00'),
(218, '2026-04-09', '09:00:00', '13:00:00'),
(219, '2026-04-09', '14:00:00', '18:00:00'),
(220, '2026-04-10', '10:00:00', '15:00:00'),
(221, '2026-04-11', '11:00:00', '14:00:00'),
(222, '2026-04-12', '09:00:00', '12:00:00'),
(223, '2026-04-13', '10:00:00', '14:00:00'),
(224, '2026-04-14', '09:00:00', '13:00:00'),
(225, '2026-04-21', '10:00:00', '14:00:00'),
(226, '2026-04-28', '11:00:00', '15:00:00'),
(227, '2026-05-07', '09:00:00', '17:00:00'),
(228, '2026-03-01', '10:00:00', '14:00:00'),
(229, '2026-03-05', '09:00:00', '12:00:00'),
(230, '2026-03-10', '11:00:00', '15:00:00'),
(231, '2026-03-15', '10:00:00', '13:00:00'),
(232, '2026-03-15', '14:00:00', '18:00:00'),
(233, '2026-03-20', '09:00:00', '12:00:00'),
(234, '2026-03-20', '13:00:00', '16:00:00'),
(235, '2026-03-25', '10:00:00', '14:00:00'),
(236, '2026-04-07', '09:00:00', '12:00:00'),
(237, '2026-04-07', '13:00:00', '17:00:00'),
(238, '2026-04-07', '18:00:00', '21:00:00'),
(239, '2026-04-08', '10:00:00', '14:00:00'),
(240, '2026-04-08', '15:00:00', '19:00:00'),
(241, '2026-04-08', '12:00:00', '16:00:00'),
(242, '2026-04-09', '09:00:00', '13:00:00'),
(243, '2026-04-09', '14:00:00', '18:00:00'),
(244, '2026-04-10', '10:00:00', '15:00:00'),
(245, '2026-04-11', '11:00:00', '14:00:00'),
(246, '2026-04-12', '09:00:00', '12:00:00'),
(247, '2026-04-13', '10:00:00', '14:00:00'),
(248, '2026-04-14', '09:00:00', '13:00:00'),
(249, '2026-04-21', '10:00:00', '14:00:00'),
(250, '2026-04-28', '11:00:00', '15:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `RoleName` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL
) ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`UserID`, `Name`, `Email`, `RoleName`, `password`) VALUES
(1, 'Admin User', 'admin@festival.edu', 'Admin', 'mali123'),
(2, 'John Organizer', 'organizer@festival.edu', 'Organiser', 'mali1234'),
(3, 'Sarah Student', 'student@festival.edu', 'Requester', 'mali12'),
(4, 'Admin User', 'admin@celebratehub.com', 'Admin', 'admin123'),
(5, 'Sarah Johnson', 'sarah.organiser@gmail.com', 'Organiser', 'organiser123'),
(6, 'Mike Thompson', 'mike.organiser@gmail.com', 'Organiser', 'organiser456'),
(7, 'Emily Davis', 'emily.requester@yahoo.com', 'Requester', 'requester123'),
(8, 'David Wilson', 'david.requester@gmail.com', 'Requester', 'requester456'),
(9, 'mins', 'mims@gmail.com', 'Organiser', 'mali123'),
(50, 'Admin User', 'admin1@festival.edu', 'admin', 'admin123'),
(51, 'John Organizer', 'john@festival.edu', 'organiser', 'org123'),
(52, 'Sarah Planner', 'sarah@festival.edu', 'organiser', 'org123'),
(53, 'Mike Events', 'mike@festival.edu', 'organiser', 'org123'),
(54, 'Alice Johnson', 'alice@festival.edu', 'requester', 'att123'),
(55, 'Bob Smith', 'bob@festival.edu', 'requester', 'att123'),
(56, 'Carol White', 'carol@festival.edu', 'requester', 'att123'),
(57, 'David Brown', 'david@festival.edu', 'requester', 'att123'),
(58, 'Emma Davis', 'emma@festival.edu', 'requester', 'att123'),
(59, 'Frank Miller', 'frank@festival.edu', 'requester', 'att123'),
(60, 'Grace Wilson', 'grace@festival.edu', 'requester', 'att123'),
(61, 'Henry Moore', 'henry@festival.edu', 'requester', 'att123'),
(62, 'Iris Taylor', 'iris@festival.edu', 'requester', 'att123'),
(63, 'Jack Anderson', 'jack@festival.edu', 'requester', 'att123'),
(64, 'Karen Thomas', 'karen@festival.edu', 'requester', 'att123'),
(65, 'Leo Jackson', 'leo@festival.edu', 'requester', 'att123'),
(66, 'Mia Harris', 'mia@festival.edu', 'requester', 'att123'),
(67, 'Noah Martinez', 'noah@festival.edu', 'requester', 'att123'),
(68, 'Olivia Garcia', 'olivia@festival.edu', 'requester', 'att123'),
(69, 'Paul Robinson', 'paul@festival.edu', 'requester', 'att123');

-- --------------------------------------------------------

--
-- Table structure for table `venue`
--

CREATE TABLE `venue` (
  `VenueID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Address` varchar(255) NOT NULL,
  `MaxCapacity` int(11) NOT NULL CHECK (`MaxCapacity` > 0),
  `HireFeePerSlot` decimal(10,2) NOT NULL DEFAULT 0.00,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `venue`
--

INSERT INTO `venue` (`VenueID`, `Name`, `Address`, `MaxCapacity`, `HireFeePerSlot`, `IsActive`) VALUES
(1, 'Grand Ballroom', '123 Luxury Ave, Houston, TX', 300, 850.00, 1),
(2, 'Garden Pavilion', '456 Park Lane, Houston, TX', 150, 450.00, 1),
(3, 'Riverside Hall', '789 River Rd, Houston, TX', 200, 650.00, 1),
(4, 'Cozy Chapel Hall', '101 Chapel St, Houston, TX', 80, 250.00, 1),
(5, 'Outdoor Meadow', '202 Country Rd, Houston, TX', 120, 300.00, 1),
(6, 'Grand Ballroom', '', 300, 0.00, 1),
(7, 'Garden Pavilion', '', 150, 0.00, 1),
(8, 'Riverside Hall', '', 200, 0.00, 1),
(9, 'Cozy Chapel Hall', '', 100, 0.00, 1),
(10, 'Outdoor Meadow', '', 300, 0.00, 1),
(11, 'Executive Suite', '', 50, 0.00, 1),
(12, 'Rooftop Terrace', '', 120, 0.00, 1),
(13, 'Lakeside Lounge', '', 80, 0.00, 1),
(14, 'Grand Ballroom', '', 300, 0.00, 1),
(15, 'Garden Pavilion', '', 150, 0.00, 1),
(16, 'Riverside Hall', '', 200, 0.00, 1),
(17, 'Cozy Chapel Hall', '', 100, 0.00, 1),
(18, 'Outdoor Meadow', '', 300, 0.00, 1),
(19, 'Executive Suite', '', 50, 0.00, 1),
(20, 'Rooftop Terrace', '', 120, 0.00, 1),
(21, 'Lakeside Lounge', '', 80, 0.00, 1),
(22, 'Grand Ballroom', '', 300, 0.00, 1),
(23, 'Garden Pavilion', '', 150, 0.00, 1),
(24, 'Riverside Hall', '', 200, 0.00, 1),
(25, 'Cozy Chapel Hall', '', 100, 0.00, 1),
(26, 'Outdoor Meadow', '', 300, 0.00, 1),
(27, 'Executive Suite', '', 50, 0.00, 1),
(28, 'Rooftop Terrace', '', 120, 0.00, 1),
(29, 'Lakeside Lounge', '', 80, 0.00, 1),
(30, 'Grand Ballroom', '', 300, 0.00, 1),
(31, 'Garden Pavilion', '', 150, 0.00, 1),
(32, 'Riverside Hall', '', 200, 0.00, 1),
(33, 'Cozy Chapel Hall', '', 100, 0.00, 1),
(34, 'Outdoor Meadow', '', 300, 0.00, 1),
(35, 'Executive Suite', '', 50, 0.00, 1),
(36, 'Rooftop Terrace', '', 120, 0.00, 1),
(37, 'Lakeside Lounge', '', 80, 0.00, 1),
(38, 'Grand Ballroom', '', 300, 0.00, 1),
(39, 'Garden Pavilion', '', 150, 0.00, 1),
(40, 'Riverside Hall', '', 200, 0.00, 1),
(41, 'Cozy Chapel Hall', '', 100, 0.00, 1),
(42, 'Outdoor Meadow', '', 300, 0.00, 1),
(43, 'Executive Suite', '', 50, 0.00, 1),
(44, 'Rooftop Terrace', '', 120, 0.00, 1),
(45, 'Lakeside Lounge', '', 80, 0.00, 1),
(46, 'Grand Ballroom', '', 300, 0.00, 1),
(47, 'Garden Pavilion', '', 150, 0.00, 1),
(48, 'Riverside Hall', '', 200, 0.00, 1),
(49, 'Cozy Chapel Hall', '', 100, 0.00, 1),
(50, 'Outdoor Meadow', '', 300, 0.00, 1),
(51, 'Executive Suite', '', 50, 0.00, 1),
(52, 'Rooftop Terrace', '', 120, 0.00, 1),
(53, 'Lakeside Lounge', '', 80, 0.00, 1),
(54, 'Grand Ballroom', '', 300, 0.00, 1),
(55, 'Garden Pavilion', '', 150, 0.00, 1),
(56, 'Riverside Hall', '', 200, 0.00, 1),
(57, 'Cozy Chapel Hall', '', 100, 0.00, 1),
(58, 'Outdoor Meadow', '', 300, 0.00, 1),
(59, 'Executive Suite', '', 50, 0.00, 1),
(60, 'Rooftop Terrace', '', 120, 0.00, 1),
(61, 'Lakeside Lounge', '', 80, 0.00, 1),
(62, 'Grand Ballroom', '', 300, 0.00, 1),
(63, 'Garden Pavilion', '', 150, 0.00, 1),
(64, 'Riverside Hall', '', 200, 0.00, 1),
(65, 'Cozy Chapel Hall', '', 100, 0.00, 1),
(66, 'Outdoor Meadow', '', 300, 0.00, 1),
(67, 'Executive Suite', '', 50, 0.00, 1),
(68, 'Rooftop Terrace', '', 120, 0.00, 1),
(69, 'Lakeside Lounge', '', 80, 0.00, 1),
(70, 'Grand Ballroom', '', 300, 0.00, 1),
(71, 'Garden Pavilion', '', 150, 0.00, 1),
(72, 'Riverside Hall', '', 200, 0.00, 1),
(73, 'Cozy Chapel Hall', '', 100, 0.00, 1),
(74, 'Outdoor Meadow', '', 300, 0.00, 1),
(75, 'Executive Suite', '', 50, 0.00, 1),
(76, 'Rooftop Terrace', '', 120, 0.00, 1),
(77, 'Lakeside Lounge', '', 80, 0.00, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`BookingID`),
  ADD KEY `fk_booking_event` (`EventID`),
  ADD KEY `idx_booking_user` (`UserID`),
  ADD KEY `idx_booking_venue` (`VenueID`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`EventID`),
  ADD KEY `idx_event_organizer` (`OrganizerID`);

--
-- Indexes for table `event_resource`
--
ALTER TABLE `event_resource`
  ADD PRIMARY KEY (`EventID`,`ResourceID`),
  ADD KEY `fk_er_resource` (`ResourceID`);

--
-- Indexes for table `event_schedule`
--
ALTER TABLE `event_schedule`
  ADD PRIMARY KEY (`VenueID`,`SlotID`),
  ADD KEY `fk_es_slot` (`SlotID`),
  ADD KEY `idx_event_schedule_event` (`EventID`);

--
-- Indexes for table `resource`
--
ALTER TABLE `resource`
  ADD PRIMARY KEY (`ResourceID`);

--
-- Indexes for table `timeslot`
--
ALTER TABLE `timeslot`
  ADD PRIMARY KEY (`SlotID`),
  ADD KEY `idx_timeslot_date` (`EventDate`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `venue`
--
ALTER TABLE `venue`
  ADD PRIMARY KEY (`VenueID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `BookingID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `EventID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resource`
--
ALTER TABLE `resource`
  MODIFY `ResourceID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `timeslot`
--
ALTER TABLE `timeslot`
  MODIFY `SlotID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `venue`
--
ALTER TABLE `venue`
  MODIFY `VenueID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `fk_booking_event` FOREIGN KEY (`EventID`) REFERENCES `event` (`EventID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_booking_user` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_booking_venue` FOREIGN KEY (`VenueID`) REFERENCES `venue` (`VenueID`) ON UPDATE CASCADE;

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `fk_event_organizer` FOREIGN KEY (`OrganizerID`) REFERENCES `user` (`UserID`) ON UPDATE CASCADE;

--
-- Constraints for table `event_resource`
--
ALTER TABLE `event_resource`
  ADD CONSTRAINT `fk_er_event` FOREIGN KEY (`EventID`) REFERENCES `event` (`EventID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_er_resource` FOREIGN KEY (`ResourceID`) REFERENCES `resource` (`ResourceID`) ON UPDATE CASCADE;

--
-- Constraints for table `event_schedule`
--
ALTER TABLE `event_schedule`
  ADD CONSTRAINT `fk_es_event` FOREIGN KEY (`EventID`) REFERENCES `event` (`EventID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_es_slot` FOREIGN KEY (`SlotID`) REFERENCES `timeslot` (`SlotID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_es_venue` FOREIGN KEY (`VenueID`) REFERENCES `venue` (`VenueID`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
